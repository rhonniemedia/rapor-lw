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

class KelasBinaan extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $rombel;
    public $rombelId;
    public $search = '';
    public $perPage = 10;
    public $selectedStudent = null;
    public $jurusansList = [];

    public $studentData = [];
    public $ayahData = [];
    public $ibuData = [];
    public $waliData = [];

    // ✅ PROPERTY UNTUK ENUM OPTIONS
    public $agamaOptions = [];
    public $pekerjaanOptions = [];
    public $hubunganOptions = [];

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

        // ✅ LOAD ENUM OPTIONS
        $this->agamaOptions = enum_options('agama', true);
        $this->pekerjaanOptions = enum_options('pekerjaan', true);
        $this->hubunganOptions = enum('hubungan');
    }

    public function openDetailModal($pelajarId)
    {
        $this->selectedStudent = Pelajar::with('orangTuaWalis')
            ->find($pelajarId);

        if (!$this->selectedStudent) {
            session()->flash('error', 'Data pelajar tidak ditemukan.');
            return;
        }

        // ✅ Gunakan huruf kapital atau key enum
        // Jika database menggunakan key enum (lowercase): 'ayah', 'ibu', 'wali'
        $ayah = $this->selectedStudent->orangTuaWalis->where('hubungan', 'ayah')->first();
        $ibu = $this->selectedStudent->orangTuaWalis->where('hubungan', 'ibu')->first();
        $wali = $this->selectedStudent->orangTuaWalis->where('hubungan', 'wali')->first();

        $this->ayahData = $ayah ? [
            'id' => $ayah->id,
            'nama' => $ayah->nama,
            'hubungan' => $ayah->hubungan,
            'status' => $ayah->status ?? 'masih-hidup',
            'pekerjaan' => $ayah->pekerjaan,
            'telepon' => $ayah->telepon,
            'alamat' => $ayah->alamat,
        ] : [];

        $this->ibuData = $ibu ? [
            'id' => $ibu->id,
            'nama' => $ibu->nama,
            'hubungan' => $ibu->hubungan,
            'status' => $ibu->status ?? 'masih-hidup',
            'pekerjaan' => $ibu->pekerjaan,
            'telepon' => $ibu->telepon,
            'alamat' => $ibu->alamat,
        ] : [];

        $this->waliData = $wali ? [
            'id' => $wali->id,
            'nama' => $wali->nama,
            'hubungan' => $wali->hubungan,
            'status' => $wali->status ?? 'masih-hidup',
            'pekerjaan' => $wali->pekerjaan,
            'telepon' => $wali->telepon,
            'alamat' => $wali->alamat,
        ] : [];

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

        // ✅ PERBAIKAN: Manual mapping dengan format tanggal yang benar
        $this->studentData = [
            'id' => $student->id,
            'nama_lengkap' => $student->nama_lengkap,
            'nomor_induk' => $student->nomor_induk,
            'nisn' => $student->nisn,
            'tempat_lahir' => $student->tempat_lahir,

            // ✅ Format untuk input type="date" (Y-m-d)
            'tanggal_lahir' => $student->tanggal_lahir_input,

            'jenis_kelamin' => $student->jenis_kelamin,
            'agama' => $student->agama,
            'status_dalam_keluarga' => $student->status_dalam_keluarga,
            'anak_ke' => $student->anak_ke,
            'alamat' => $student->alamat,
            'telepon' => $student->telepon,
            'sekolah_asal' => $student->sekolah_asal,
            'diterima_di_kelas' => $student->diterima_di_kelas,

            // ✅ Format untuk input type="date" (Y-m-d)
            'pada_tanggal' => $student->pada_tanggal,
        ];

        // ✅ Gunakan key enum (lowercase)
        $ayah = $student->orangTuaWalis->where('hubungan', 'ayah')->first();
        $ibu = $student->orangTuaWalis->where('hubungan', 'ibu')->first();
        $wali = $student->orangTuaWalis->where('hubungan', 'wali')->first();

        $this->ayahData = $ayah ? [
            'id' => $ayah->id,
            'nama' => $ayah->nama,
            'hubungan' => $ayah->hubungan,
            'status' => $ayah->status ?? 'masih-hidup',
            'pekerjaan' => $ayah->pekerjaan,
            'telepon' => $ayah->telepon,
            'alamat' => $ayah->alamat,
        ] : [
            'hubungan' => 'ayah', // ✅ gunakan key enum
            'status' => 'masih-hidup',
            'nama' => '',
            'pekerjaan' => '',
            'telepon' => '',
            'alamat' => '',
        ];

        $this->ibuData = $ibu ? [
            'id' => $ibu->id,
            'nama' => $ibu->nama,
            'hubungan' => $ibu->hubungan,
            'status' => $ibu->status ?? 'masih-hidup',
            'pekerjaan' => $ibu->pekerjaan,
            'telepon' => $ibu->telepon,
            'alamat' => $ibu->alamat,
        ] : [
            'hubungan' => 'ibu', // ✅ gunakan key enum
            'status' => 'masih-hidup',
            'nama' => '',
            'pekerjaan' => '',
            'telepon' => '',
            'alamat' => '',
        ];

        $this->waliData = $wali ? [
            'id' => $wali->id,
            'nama' => $wali->nama,
            'hubungan' => $wali->hubungan,
            'status' => $wali->status ?? 'masih-hidup',
            'pekerjaan' => $wali->pekerjaan,
            'telepon' => $wali->telepon,
            'alamat' => $wali->alamat,
        ] : [
            'hubungan' => 'wali', // ✅ gunakan key enum
            'status' => 'masih-hidup',
            'nama' => '',
            'pekerjaan' => '',
            'telepon' => '',
            'alamat' => '',
        ];

        $this->dispatch('show-edit-modal');
    }

    public function saveStudent()
    {
        // ✅ VALIDASI DENGAN ENUM
        $validatedStudent = $this->validate([
            'studentData.nama_lengkap' => 'required|string|max:255',
            'studentData.nomor_induk' => 'nullable|string|max:50',
            'studentData.nisn' => 'nullable|string|max:10',
            'studentData.tempat_lahir' => 'nullable|string|max:255',
            'studentData.tanggal_lahir' => 'nullable|date',
            'studentData.jenis_kelamin' => 'nullable|in:L,P',
            'studentData.agama' => enum_validation_rule('agama', true), // ✅ Validasi enum
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
            'studentData.agama.in' => 'Agama yang dipilih tidak valid.',
        ]);

        // ✅ VALIDASI DATA ORANG TUA DENGAN ENUM
        $this->validate([
            'ayahData.nama' => 'nullable|string|max:255',
            'ayahData.pekerjaan' => enum_validation_rule('pekerjaan', true), // ✅ Validasi enum
            'ayahData.telepon' => 'nullable|string|max:20',
            'ayahData.alamat' => 'nullable|string',
            'ayahData.status' => 'nullable|in:masih-hidup,sudah-meninggal',

            'ibuData.nama' => 'nullable|string|max:255',
            'ibuData.pekerjaan' => enum_validation_rule('pekerjaan', true), // ✅ Validasi enum
            'ibuData.telepon' => 'nullable|string|max:20',
            'ibuData.alamat' => 'nullable|string',
            'ibuData.status' => 'nullable|in:masih-hidup,sudah-meninggal',

            'waliData.nama' => 'nullable|string|max:255',
            'waliData.pekerjaan' => enum_validation_rule('pekerjaan', true), // ✅ Validasi enum
            'waliData.telepon' => 'nullable|string|max:20',
            'waliData.alamat' => 'nullable|string',
            'waliData.status' => 'nullable|in:masih-hidup,sudah-meninggal',
        ]);

        DB::beginTransaction();

        try {
            $pelajar = Pelajar::find($this->selectedStudent->id);

            if (!$pelajar) {
                throw new \Exception('Data pelajar tidak ditemukan.');
            }

            // Update field - biarkan cast yang handle encryption
            $pelajar->nama_lengkap = $this->studentData['nama_lengkap'];
            $pelajar->nomor_induk = $this->studentData['nomor_induk'] ?? null;
            $pelajar->tempat_lahir = $this->studentData['tempat_lahir'] ?? null;
            $pelajar->jenis_kelamin = $this->studentData['jenis_kelamin'] ?? null;
            $pelajar->status_dalam_keluarga = $this->studentData['status_dalam_keluarga'] ?? null;
            $pelajar->anak_ke = $this->studentData['anak_ke'] ?? null;
            $pelajar->sekolah_asal = $this->studentData['sekolah_asal'] ?? null;
            $pelajar->diterima_di_kelas = $this->studentData['diterima_di_kelas'] ?? null;

            if (!empty($this->studentData['tanggal_lahir'])) {
                $pelajar->tanggal_lahir = $this->studentData['tanggal_lahir'];
            }

            if (!empty($this->studentData['pada_tanggal'])) {
                $pelajar->pada_tanggal = $this->studentData['pada_tanggal'];
            }

            if (!empty($this->studentData['nisn'])) {
                $pelajar->nisn = $this->studentData['nisn'];
                $pelajar->nisn_hash = hash('sha256', $this->studentData['nisn']);
            }

            // ✅ SIMPAN KEY ENUM (lowercase) ke database
            if (!empty($this->studentData['agama'])) {
                $pelajar->agama = $this->studentData['agama']; // Simpan key: 'islam', 'kristen', dll
                $pelajar->agama_hash = hash('sha256', strtolower($this->studentData['agama']));
            }

            if (!empty($this->studentData['alamat'])) {
                $pelajar->alamat = $this->studentData['alamat'];
            }

            if (!empty($this->studentData['telepon'])) {
                $pelajar->telepon = $this->studentData['telepon'];
            }

            $pelajar->save();

            // Update/Create data Orang Tua
            $this->saveOrUpdateOrangTua($pelajar->id, $this->ayahData, 'ayah');
            $this->saveOrUpdateOrangTua($pelajar->id, $this->ibuData, 'ibu');
            $this->saveOrUpdateOrangTua($pelajar->id, $this->waliData, 'wali');

            DB::commit();

            session()->flash('success', 'Data pelajar berhasil diperbarui.');
            $this->dispatch('close-edit-modal');
            $this->reset(['selectedStudent', 'studentData', 'ayahData', 'ibuData', 'waliData']);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    protected function saveOrUpdateOrangTua($pelajarId, $data, $hubungan)
    {
        if (empty($data['nama'])) {
            return;
        }

        $orangTua = OrangTuaWali::where('pelajar_id', $pelajarId)
            ->where('hubungan', $hubungan) // ✅ Simpan key enum: 'ayah', 'ibu', 'wali'
            ->first();

        if ($orangTua) {
            // Update existing
            $orangTua->nama = $data['nama'];
            $orangTua->pekerjaan = $data['pekerjaan'] ?? null; // ✅ Simpan key enum pekerjaan
            $orangTua->status = $data['status'] ?? 'masih-hidup';
            $orangTua->telepon = $data['telepon'] ?? null;
            $orangTua->alamat = $data['alamat'] ?? null;
            $orangTua->save();
        } else {
            // Create new
            OrangTuaWali::create([
                'pelajar_id' => $pelajarId,
                'nama' => $data['nama'],
                'hubungan' => $hubungan, // ✅ Simpan key enum: 'ayah', 'ibu', 'wali'
                'status' => $data['status'] ?? 'masih-hidup',
                'pekerjaan' => $data['pekerjaan'] ?? null, // ✅ Simpan key enum pekerjaan
                'telepon' => $data['telepon'] ?? null,
                'alamat' => $data['alamat'] ?? null,
            ]);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getStatsProperty()
    {
        $totalSiswa = RombelPelajar::where('rombel_id', $this->rombelId)->count();
        return ['total_siswa' => $totalSiswa];
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
