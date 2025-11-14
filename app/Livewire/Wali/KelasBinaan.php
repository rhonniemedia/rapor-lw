<?php

namespace App\Livewire\Wali;

use App\Models\Rombel;
use App\Models\Jurusan;
use App\Models\Pelajar;
use Livewire\Component;
use App\Models\OrangTuaWali;
use Livewire\WithPagination;
use App\Models\RombelPelajar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KelasBinaan extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Properti untuk rombel
    public $rombel;
    public $rombelId;

    // Properti pencarian & pagination
    public $search = '';
    public $perPage = 10;

    public $selectedStudent = null;
    public $jurusansList = [];

    public $studentData = [];
    public $ayahData = [];
    public $ibuData = [];
    public $waliData = [];

    public function mount()
    {
        $user = Auth::user();

        $this->rombel = Rombel::where('wali_kelas_slug', $user->slug)
            ->with(['jurusan', 'tahunAjaranKurikulum.tahunAjaran', 'tahunAjaranKurikulum.kurikulum'])
            ->first();

        if (!$this->rombel) {
            abort(403, 'Anda belum memiliki kelas binaan.');
        }

        $this->rombelId = $this->rombel->id;
        $this->jurusansList = Jurusan::select('id', 'nama')->get();
    }

    public function openDetailModal($pelajarId)
    {
        $this->selectedStudent = Pelajar::with('orangTuaWalis')
            ->find($pelajarId);

        if (!$this->selectedStudent) {
            session()->flash('error', 'Data pelajar tidak ditemukan.');
            return;
        }

        $this->reset(['studentData', 'ayahData', 'ibuData', 'waliData']);
        $this->dispatch('show-detail-modal');
    }

    public function openEditModal($pelajarId)
    {
        $student = Pelajar::with('orangTuaWalis')
            ->find($pelajarId);

        if (!$student) {
            session()->flash('error', 'Data pelajar tidak ditemukan.');
            return;
        }

        $this->selectedStudent = $student;
        $this->studentData = $student->toArray();

        // Mapping data Orang Tua/Wali
        $ayah = $student->orangTuaWalis->where('hubungan', 'Ayah')->first();
        $ibu = $student->orangTuaWalis->where('hubungan', 'Ibu')->first();
        $wali = $student->orangTuaWalis->where('hubungan', 'Wali')->first();

        $this->ayahData = $ayah ? $ayah->toArray() : ['hubungan' => 'Ayah'];
        $this->ibuData = $ibu ? $ibu->toArray() : ['hubungan' => 'Ibu'];
        $this->waliData = $wali ? $wali->toArray() : ['hubungan' => 'Wali'];

        $this->dispatch('show-edit-modal');
    }

    public function saveStudent()
    {
        // Validasi data pelajar
        $validatedStudent = $this->validate([
            'studentData.nama_lengkap' => 'required|string|max:255',
            'studentData.nomor_induk' => 'nullable|string|max:50',
            'studentData.nisn' => 'nullable|string|max:10',
            'studentData.tempat_lahir' => 'nullable|string|max:255',
            'studentData.tanggal_lahir' => 'nullable|date',
            'studentData.jenis_kelamin' => 'nullable|in:L,P',
            'studentData.agama' => 'nullable|string',
            'studentData.status_dalam_keluarga' => 'nullable|string|max:100',
            'studentData.anak_ke' => 'nullable|integer',
            'studentData.alamat' => 'nullable|string',
            'studentData.telepon' => 'nullable|string|max:20',
            'studentData.sekolah_asal' => 'nullable|string|max:255',
            'studentData.diterima_di_kelas' => 'nullable|string|max:100',
            'studentData.pada_tanggal' => 'nullable|date',
        ], [
            'studentData.nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'studentData.jenis_kelamin.in' => 'Jenis kelamin harus L atau P.',
            'studentData.tanggal_lahir.date' => 'Format tanggal lahir tidak valid.',
        ]);

        // Validasi data orang tua (optional)
        $this->validate([
            'ayahData.nama' => 'nullable|string|max:255',
            'ayahData.pekerjaan' => 'nullable|string|max:255',
            'ayahData.telepon' => 'nullable|string|max:20',
            'ayahData.alamat' => 'nullable|string',
            'ayahData.status' => 'nullable|in:masih-hidup,sudah-meninggal',

            'ibuData.nama' => 'nullable|string|max:255',
            'ibuData.pekerjaan' => 'nullable|string|max:255',
            'ibuData.telepon' => 'nullable|string|max:20',
            'ibuData.alamat' => 'nullable|string',
            'ibuData.status' => 'nullable|in:masih-hidup,sudah-meninggal',

            'waliData.nama' => 'nullable|string|max:255',
            'waliData.pekerjaan' => 'nullable|string|max:255',
            'waliData.telepon' => 'nullable|string|max:20',
            'waliData.alamat' => 'nullable|string',
            'waliData.status' => 'nullable|in:masih-hidup,sudah-meninggal',
        ]);

        DB::beginTransaction();

        try {
            // Update data pelajar
            $pelajar = Pelajar::find($this->selectedStudent->id);

            if (!$pelajar) {
                throw new \Exception('Data pelajar tidak ditemukan.');
            }

            // Update field biasa
            $pelajar->nama_lengkap = $this->studentData['nama_lengkap'];
            $pelajar->nomor_induk = $this->studentData['nomor_induk'] ?? null;
            $pelajar->tempat_lahir = $this->studentData['tempat_lahir'] ?? null;
            $pelajar->jenis_kelamin = $this->studentData['jenis_kelamin'] ?? null;
            $pelajar->status_dalam_keluarga = $this->studentData['status_dalam_keluarga'] ?? null;
            $pelajar->anak_ke = $this->studentData['anak_ke'] ?? null;
            $pelajar->sekolah_asal = $this->studentData['sekolah_asal'] ?? null;
            $pelajar->diterima_di_kelas = $this->studentData['diterima_di_kelas'] ?? null;

            // Handle tanggal
            if (!empty($this->studentData['tanggal_lahir'])) {
                $pelajar->tanggal_lahir = encrypt($this->studentData['tanggal_lahir']);
            }

            if (!empty($this->studentData['pada_tanggal'])) {
                $pelajar->pada_tanggal = $this->studentData['pada_tanggal'];
            }

            // Handle field terenkripsi dengan hash
            if (!empty($this->studentData['nisn'])) {
                $pelajar->nisn = encrypt($this->studentData['nisn']);
                $pelajar->nisn_hash = hash('sha256', $this->studentData['nisn']);
            }

            if (!empty($this->studentData['agama'])) {
                $pelajar->agama = encrypt($this->studentData['agama']);
                $pelajar->agama_hash = hash('sha256', $this->studentData['agama']);
            }

            if (!empty($this->studentData['alamat'])) {
                $pelajar->alamat = encrypt($this->studentData['alamat']);
            }

            if (!empty($this->studentData['telepon'])) {
                $pelajar->telepon = encrypt($this->studentData['telepon']);
            }

            $pelajar->save();

            // Update/Create data Ayah
            $this->saveOrUpdateOrangTua($pelajar->id, $this->ayahData, 'Ayah');

            // Update/Create data Ibu
            $this->saveOrUpdateOrangTua($pelajar->id, $this->ibuData, 'Ibu');

            // Update/Create data Wali
            $this->saveOrUpdateOrangTua($pelajar->id, $this->waliData, 'Wali');

            DB::commit();

            session()->flash('success', 'Data pelajar berhasil diperbarui.');

            // Close modal
            $this->dispatch('close-edit-modal');

            // Reset selected student
            $this->reset(['selectedStudent', 'studentData', 'ayahData', 'ibuData', 'waliData']);
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    protected function saveOrUpdateOrangTua($pelajarId, $data, $hubungan)
    {
        // Skip jika nama kosong
        if (empty($data['nama'])) {
            return;
        }

        // Cek apakah sudah ada data orang tua dengan hubungan ini
        $orangTua = OrangTuaWali::where('pelajar_id', $pelajarId)
            ->where('hubungan', $hubungan)
            ->first();

        if ($orangTua) {
            // Update existing
            $orangTua->nama = !empty($data['nama']) ? encrypt($data['nama']) : $orangTua->nama;
            $orangTua->pekerjaan = $data['pekerjaan'] ?? null;
            $orangTua->status = $data['status'] ?? 'masih-hidup';

            if (!empty($data['telepon'])) {
                $orangTua->telepon = encrypt($data['telepon']);
            }

            if (!empty($data['alamat'])) {
                $orangTua->alamat = encrypt($data['alamat']);
            }

            $orangTua->save();
        } else {
            // Create new
            OrangTuaWali::create([
                'pelajar_id' => $pelajarId,
                'nama' => encrypt($data['nama']),
                'hubungan' => $hubungan,
                'status' => $data['status'] ?? 'masih-hidup',
                'pekerjaan' => $data['pekerjaan'] ?? null,
                'telepon' => !empty($data['telepon']) ? encrypt($data['telepon']) : null,
                'alamat' => !empty($data['alamat']) ? encrypt($data['alamat']) : null,
            ]);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getStatsProperty()
    {
        $totalSiswa = RombelPelajar::where('rombel_id', $this->rombelId)
            ->count();

        return [
            'total_siswa' => $totalSiswa,
        ];
    }

    public function render()
    {
        $query = RombelPelajar::where('rombel_id', $this->rombelId)
            ->with(['pelajar']);

        if (!empty($this->search)) {
            $query->whereHas('pelajar', function ($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                    ->orWhere('nomor_induk', 'like', '%' . $this->search . '%');
            });
        }

        $pelajars = $query->orderBy('created_at', 'asc')->paginate($this->perPage);

        return view('livewire.wali.kelas-binaan', [
            'pelajars' => $pelajars,
            'stats' => $this->stats,
        ]);
    }
}
