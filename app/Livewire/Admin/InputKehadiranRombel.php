<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Rombel;
use App\Models\Kehadiran;
use App\Models\RombelPelajar;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InputKehadiranRombel extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 📹 Properti utama
    public $rombelId;
    public $rombel;

    // 📹 Properti pencarian & pagination
    public $searchPelajar = '';
    public $perPagePelajar = 1000;

    // 📹 Input data kehadiran
    public $kehadiranInput = []; // Format: ['pelajar_id' => ['sakit' => 0, 'izin' => 0, 'tanpa_keterangan' => 0]]

    // 📹 Query string untuk persistensi state
    protected $queryString = [
        'searchPelajar' => ['except' => ''],
    ];

    // 📹 Event listener
    protected $listeners = [
        'saveKehadiranConfirmed' => 'saveKehadiran',
        'resetKehadiranConfirmed' => 'resetKehadiran',
    ];

    // 📹 Validation rules
    protected $rules = [
        'kehadiranInput.*.sakit' => 'nullable|integer|min:0',
        'kehadiranInput.*.izin' => 'nullable|integer|min:0',
        'kehadiranInput.*.tanpa_keterangan' => 'nullable|integer|min:0',
    ];

    protected $messages = [
        'kehadiranInput.*.sakit.integer' => 'Jumlah sakit harus berupa angka',
        'kehadiranInput.*.sakit.min' => 'Jumlah sakit minimal adalah 0',
        'kehadiranInput.*.izin.integer' => 'Jumlah izin harus berupa angka',
        'kehadiranInput.*.izin.min' => 'Jumlah izin minimal adalah 0',
        'kehadiranInput.*.tanpa_keterangan.integer' => 'Jumlah tanpa keterangan harus berupa angka',
        'kehadiranInput.*.tanpa_keterangan.min' => 'Jumlah tanpa keterangan minimal adalah 0',
    ];

    public function mount($rombelId)
    {
        $this->rombelId = $rombelId;
        $this->rombel = Rombel::with(['tahunAjaranKurikulum.tahunAjaran'])->findOrFail($rombelId);
        $this->loadKehadiranPelajar();
    }

    public function updatingSearchPelajar()
    {
        $this->resetPage();
    }

    private function loadKehadiranPelajar()
    {
        $tahunAjaranSemesterId = $this->getTahunAjaranSemesterId();

        // Ambil kehadiran yang sudah ada untuk rombel ini
        $kehadiranExist = Kehadiran::where('rombel_id', $this->rombelId)
            ->where('tahun_ajaran_semester_id', $tahunAjaranSemesterId)
            ->get()
            ->keyBy('pelajar_id');

        $this->kehadiranInput = [];

        // Populate kehadiran input dengan data existing
        foreach ($kehadiranExist as $pelajarId => $kehadiran) {
            $this->kehadiranInput[$pelajarId] = [
                'sakit' => $kehadiran->jumlah_sakit ?? 0,
                'izin' => $kehadiran->jumlah_izin ?? 0,
                'tanpa_keterangan' => $kehadiran->jumlah_tanpa_keterangan ?? 0,
            ];
        }
    }

    private function getTahunAjaranSemesterId()
    {
        $sessionSemesterId = session('tahun_ajaran_semester_id');

        if ($sessionSemesterId) {
            return $sessionSemesterId;
        }

        $activeSemester = TahunAjaranSemester::where('status', 'aktif')->first();

        if ($activeSemester) {
            return $activeSemester->id;
        }

        if ($this->rombel && $this->rombel->tahunAjaranKurikulum) {
            $semester = TahunAjaranSemester::where('tahun_ajaran_id', $this->rombel->tahunAjaranKurikulum->tahun_ajaran_id)
                ->first();

            if ($semester) {
                return $semester->id;
            }
        }

        return null;
    }

    private function getPelajarQuery()
    {
        $query = RombelPelajar::where('rombel_id', $this->rombelId)
            ->with(['pelajar']);

        if (!empty($this->searchPelajar)) {
            $query->whereHas('pelajar', function ($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->searchPelajar . '%')
                    ->orWhere('nisn', 'like', '%' . $this->searchPelajar . '%')
                    ->orWhere('nomor_induk', 'like', '%' . $this->searchPelajar . '%');
            });
        }

        return $query;
    }

    public function confirmSaveKehadiran()
    {
        $this->validate();

        $this->dispatch('swal:confirm', [
            'title' => 'Simpan Kehadiran?',
            'text' => 'Semua data kehadiran yang diinput akan disimpan.',
            'nextEvent' => 'saveKehadiranConfirmed',
        ]);
    }

    public function saveKehadiran()
    {
        $tahunAjaranSemesterId = $this->getTahunAjaranSemesterId();

        if (!$tahunAjaranSemesterId) {
            $this->dispatch('swal:error', [
                'title' => 'Error!',
                'text' => 'Tidak dapat menemukan tahun ajaran semester yang aktif.',
            ]);
            return;
        }

        DB::beginTransaction();
        try {
            $savedCount = 0;

            foreach ($this->kehadiranInput as $pelajarId => $kehadiran) {
                // Skip jika semua nilai kosong atau null
                if (empty($kehadiran['sakit']) && empty($kehadiran['izin']) && empty($kehadiran['tanpa_keterangan'])) {
                    continue;
                }

                $sakit = is_numeric($kehadiran['sakit']) ? intval($kehadiran['sakit']) : 0;
                $izin = is_numeric($kehadiran['izin']) ? intval($kehadiran['izin']) : 0;
                $tanpaKeterangan = is_numeric($kehadiran['tanpa_keterangan']) ? intval($kehadiran['tanpa_keterangan']) : 0;

                // Cek apakah kehadiran sudah ada
                $kehadiranExist = Kehadiran::where('pelajar_id', $pelajarId)
                    ->where('rombel_id', $this->rombelId)
                    ->where('tahun_ajaran_semester_id', $tahunAjaranSemesterId)
                    ->first();

                if ($kehadiranExist) {
                    $kehadiranExist->update([
                        'jumlah_sakit' => $sakit,
                        'jumlah_izin' => $izin,
                        'jumlah_tanpa_keterangan' => $tanpaKeterangan,
                    ]);
                } else {
                    Kehadiran::create([
                        'id' => Str::uuid(),
                        'pelajar_id' => $pelajarId,
                        'rombel_id' => $this->rombelId,
                        'tahun_ajaran_semester_id' => $tahunAjaranSemesterId,
                        'jumlah_sakit' => $sakit,
                        'jumlah_izin' => $izin,
                        'jumlah_tanpa_keterangan' => $tanpaKeterangan,
                    ]);
                }

                $savedCount++;
            }

            DB::commit();

            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => "Berhasil menyimpan $savedCount data kehadiran.",
            ]);

            $this->loadKehadiranPelajar();
        } catch (\Exception $e) {
            DB::rollback();

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Gagal menyimpan kehadiran: ' . $e->getMessage(),
            ]);
        }
    }

    public function confirmResetKehadiran()
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Reset Input Kehadiran?',
            'text' => 'Semua input kehadiran akan dikosongkan (belum disimpan).',
            'nextEvent' => 'resetKehadiranConfirmed',
        ]);
    }

    public function resetKehadiran()
    {
        foreach ($this->kehadiranInput as $pelajarId => $data) {
            $this->kehadiranInput[$pelajarId] = [
                'sakit' => 0,
                'izin' => 0,
                'tanpa_keterangan' => 0,
            ];
        }

        $this->dispatch('swal:info', [
            'title' => 'Direset!',
            'text' => 'Semua kolom input kehadiran telah dikosongkan.',
        ]);
    }

    public function render()
    {
        $tahunAjaranSemesterId = $this->getTahunAjaranSemesterId();

        $kehadiranExist = Kehadiran::where('rombel_id', $this->rombelId)
            ->where('tahun_ajaran_semester_id', $tahunAjaranSemesterId)
            ->get()
            ->keyBy('pelajar_id');

        $pelajarPaginated = $this->getPelajarQuery()
            ->orderBy('id', 'asc')
            ->paginate($this->perPagePelajar);

        $pelajarData = $pelajarPaginated->through(function ($rombelPelajar) use ($kehadiranExist) {
            $pelajarId = $rombelPelajar->pelajar_id;

            // Inisialisasi input kehadiran jika belum ada
            if (!isset($this->kehadiranInput[$pelajarId])) {
                $existingKehadiran = $kehadiranExist->get($pelajarId);
                $this->kehadiranInput[$pelajarId] = [
                    'sakit' => $existingKehadiran->jumlah_sakit ?? 0,
                    'izin' => $existingKehadiran->jumlah_izin ?? 0,
                    'tanpa_keterangan' => $existingKehadiran->jumlah_tanpa_keterangan ?? 0,
                ];
            }

            return (object) [
                'rombel_pelajar_id' => $rombelPelajar->id,
                'pelajar_id' => $pelajarId,
                'nama_lengkap' => $rombelPelajar->pelajar->nama_lengkap,
                'nomor_induk' => $rombelPelajar->pelajar->nomor_induk,
                'nisn' => $rombelPelajar->pelajar->nisn,
                'kehadiran_sekarang' => $kehadiranExist->get($pelajarId),
            ];
        });

        return view('livewire.admin.input-kehadiran-rombel', [
            'pelajarData' => $pelajarData,
        ]);
    }
}
