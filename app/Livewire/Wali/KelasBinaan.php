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

    // ✅ DATA AWAL UNTUK DIRTY CHECKING
    public $originalStudentData = [];
    public $originalAyahData = [];
    public $originalIbuData = [];
    public $originalWaliData = [];

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
            $this->dispatch('show-alert', [
                'type' => 'error',
                'message' => 'Data pelajar tidak ditemukan.'
            ]);
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
            $this->dispatch('show-alert', [
                'type' => 'error',
                'message' => 'Data pelajar tidak ditemukan.'
            ]);
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

        // ✅ SIMPAN DATA AWAL UNTUK DIRTY CHECKING
        $this->originalStudentData = $this->studentData;

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
            'hubungan' => 'ayah',
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
            'hubungan' => 'ibu',
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
            'hubungan' => 'wali',
            'status' => 'masih-hidup',
            'nama' => '',
            'pekerjaan' => '',
            'telepon' => '',
            'alamat' => '',
        ];

        // ✅ SIMPAN DATA AWAL ORANG TUA UNTUK DIRTY CHECKING
        $this->originalAyahData = $this->ayahData;
        $this->originalIbuData = $this->ibuData;
        $this->originalWaliData = $this->waliData;

        $this->dispatch('show-edit-modal');
    }

    public function saveStudent()
    {
        // ✅ VALIDASI DENGAN ENUM DAN FORMAT ANGKA
        $validatedStudent = $this->validate([
            'studentData.nama_lengkap' => 'required|string|max:255',
            'studentData.nomor_induk' => 'nullable|numeric|digits_between:1,20',
            'studentData.nisn' => 'nullable|numeric|digits:10',
            'studentData.tempat_lahir' => 'nullable|string|max:255',
            'studentData.tanggal_lahir' => 'nullable|date',
            'studentData.jenis_kelamin' => 'nullable|in:L,P',
            'studentData.agama' => enum_validation_rule('agama', true),
            'studentData.status_dalam_keluarga' => 'nullable|string|max:100',
            'studentData.anak_ke' => 'nullable|integer',
            'studentData.alamat' => 'nullable|string',
            'studentData.telepon' => 'nullable|numeric|digits_between:10,15',
            'studentData.sekolah_asal' => 'nullable|string|max:255',
            'studentData.diterima_di_kelas' => 'nullable|string|max:100',
            'studentData.pada_tanggal' => 'nullable|date',
        ], [
            'studentData.nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'studentData.nomor_induk.numeric' => 'Nomor induk harus berupa angka.',
            'studentData.nomor_induk.digits_between' => 'Nomor induk maksimal 20 digit.',
            'studentData.nisn.numeric' => 'NISN harus berupa angka.',
            'studentData.nisn.digits' => 'NISN harus 10 digit.',
            'studentData.jenis_kelamin.in' => 'Jenis kelamin harus L atau P.',
            'studentData.tanggal_lahir.date' => 'Format tanggal lahir tidak valid.',
            'studentData.agama.in' => 'Agama yang dipilih tidak valid.',
            'studentData.telepon.numeric' => 'Telepon harus berupa angka.',
            'studentData.telepon.digits_between' => 'Telepon harus 10-15 digit.',
        ]);

        // ✅ VALIDASI DATA ORANG TUA DENGAN ENUM
        $this->validate([
            'ayahData.nama' => 'nullable|string|max:255',
            'ayahData.pekerjaan' => enum_validation_rule('pekerjaan', true),
            'ayahData.telepon' => 'nullable|numeric|digits_between:10,15',
            'ayahData.alamat' => 'nullable|string',
            'ayahData.status' => 'nullable|in:masih-hidup,sudah-meninggal',

            'ibuData.nama' => 'nullable|string|max:255',
            'ibuData.pekerjaan' => enum_validation_rule('pekerjaan', true),
            'ibuData.telepon' => 'nullable|numeric|digits_between:10,15',
            'ibuData.alamat' => 'nullable|string',
            'ibuData.status' => 'nullable|in:masih-hidup,sudah-meninggal',

            'waliData.nama' => 'nullable|string|max:255',
            'waliData.pekerjaan' => enum_validation_rule('pekerjaan', true),
            'waliData.telepon' => 'nullable|numeric|digits_between:10,15',
            'waliData.alamat' => 'nullable|string',
            'waliData.status' => 'nullable|in:masih-hidup,sudah-meninggal',
        ], [
            'ayahData.telepon.numeric' => 'Telepon ayah harus berupa angka.',
            'ayahData.telepon.digits_between' => 'Telepon ayah harus 10-15 digit.',
            'ibuData.telepon.numeric' => 'Telepon ibu harus berupa angka.',
            'ibuData.telepon.digits_between' => 'Telepon ibu harus 10-15 digit.',
            'waliData.telepon.numeric' => 'Telepon wali harus berupa angka.',
            'waliData.telepon.digits_between' => 'Telepon wali harus 10-15 digit.',
        ]);

        DB::beginTransaction();

        try {
            $pelajar = Pelajar::find($this->selectedStudent->id);

            if (!$pelajar) {
                throw new \Exception('Data pelajar tidak ditemukan.');
            }

            // ✅ UPDATE HANYA FIELD YANG BERUBAH (DIRTY CHECKING)
            $hasChanges = false;

            // Cek perubahan nama_lengkap
            if (
                isset($this->studentData['nama_lengkap']) &&
                $this->studentData['nama_lengkap'] !== $this->originalStudentData['nama_lengkap']
            ) {
                $pelajar->nama_lengkap = $this->studentData['nama_lengkap'];
                $hasChanges = true;
            }

            // Cek perubahan nomor_induk
            if (
                isset($this->studentData['nomor_induk']) &&
                $this->studentData['nomor_induk'] !== $this->originalStudentData['nomor_induk']
            ) {
                $pelajar->nomor_induk = !empty($this->studentData['nomor_induk']) ? $this->studentData['nomor_induk'] : null;
                $hasChanges = true;
            }

            // Cek perubahan NISN
            if (
                isset($this->studentData['nisn']) &&
                $this->studentData['nisn'] !== $this->originalStudentData['nisn']
            ) {
                if (!empty($this->studentData['nisn'])) {
                    $pelajar->nisn = $this->studentData['nisn'];
                    $pelajar->nisn_hash = hash('sha256', $this->studentData['nisn']);
                } else {
                    $pelajar->nisn = null;
                    $pelajar->nisn_hash = null;
                }
                $hasChanges = true;
            }

            // Cek perubahan tempat_lahir
            if (
                isset($this->studentData['tempat_lahir']) &&
                $this->studentData['tempat_lahir'] !== $this->originalStudentData['tempat_lahir']
            ) {
                $pelajar->tempat_lahir = !empty($this->studentData['tempat_lahir']) ? $this->studentData['tempat_lahir'] : null;
                $hasChanges = true;
            }

            // Cek perubahan tanggal_lahir
            if (
                isset($this->studentData['tanggal_lahir']) &&
                $this->studentData['tanggal_lahir'] !== $this->originalStudentData['tanggal_lahir']
            ) {
                $pelajar->tanggal_lahir = !empty($this->studentData['tanggal_lahir']) ? $this->studentData['tanggal_lahir'] : null;
                $hasChanges = true;
            }

            // Cek perubahan jenis_kelamin
            if (
                isset($this->studentData['jenis_kelamin']) &&
                $this->studentData['jenis_kelamin'] !== $this->originalStudentData['jenis_kelamin']
            ) {
                $pelajar->jenis_kelamin = !empty($this->studentData['jenis_kelamin']) ? $this->studentData['jenis_kelamin'] : null;
                $hasChanges = true;
            }

            // Cek perubahan agama
            if (
                isset($this->studentData['agama']) &&
                $this->studentData['agama'] !== $this->originalStudentData['agama']
            ) {
                if (!empty($this->studentData['agama'])) {
                    $pelajar->agama = $this->studentData['agama'];
                    $pelajar->agama_hash = hash('sha256', strtolower($this->studentData['agama']));
                } else {
                    $pelajar->agama = null;
                    $pelajar->agama_hash = null;
                }
                $hasChanges = true;
            }

            // Cek perubahan status_dalam_keluarga
            if (
                isset($this->studentData['status_dalam_keluarga']) &&
                $this->studentData['status_dalam_keluarga'] !== $this->originalStudentData['status_dalam_keluarga']
            ) {
                $pelajar->status_dalam_keluarga = !empty($this->studentData['status_dalam_keluarga']) ? $this->studentData['status_dalam_keluarga'] : null;
                $hasChanges = true;
            }

            // Cek perubahan anak_ke
            if (
                isset($this->studentData['anak_ke']) &&
                $this->studentData['anak_ke'] !== $this->originalStudentData['anak_ke']
            ) {
                $pelajar->anak_ke = !empty($this->studentData['anak_ke']) ? $this->studentData['anak_ke'] : null;
                $hasChanges = true;
            }

            // Cek perubahan alamat
            if (
                isset($this->studentData['alamat']) &&
                $this->studentData['alamat'] !== $this->originalStudentData['alamat']
            ) {
                $pelajar->alamat = !empty($this->studentData['alamat']) ? $this->studentData['alamat'] : null;
                $hasChanges = true;
            }

            // Cek perubahan telepon
            if (
                isset($this->studentData['telepon']) &&
                $this->studentData['telepon'] !== $this->originalStudentData['telepon']
            ) {
                $pelajar->telepon = !empty($this->studentData['telepon']) ? $this->studentData['telepon'] : null;
                $hasChanges = true;
            }

            // Cek perubahan sekolah_asal
            if (
                isset($this->studentData['sekolah_asal']) &&
                $this->studentData['sekolah_asal'] !== $this->originalStudentData['sekolah_asal']
            ) {
                $pelajar->sekolah_asal = !empty($this->studentData['sekolah_asal']) ? $this->studentData['sekolah_asal'] : null;
                $hasChanges = true;
            }

            // Cek perubahan diterima_di_kelas
            if (
                isset($this->studentData['diterima_di_kelas']) &&
                $this->studentData['diterima_di_kelas'] !== $this->originalStudentData['diterima_di_kelas']
            ) {
                $pelajar->diterima_di_kelas = !empty($this->studentData['diterima_di_kelas']) ? $this->studentData['diterima_di_kelas'] : null;
                $hasChanges = true;
            }

            // Cek perubahan pada_tanggal
            if (
                isset($this->studentData['pada_tanggal']) &&
                $this->studentData['pada_tanggal'] !== $this->originalStudentData['pada_tanggal']
            ) {
                $pelajar->pada_tanggal = !empty($this->studentData['pada_tanggal']) ? $this->studentData['pada_tanggal'] : null;
                $hasChanges = true;
            }

            // Simpan jika ada perubahan
            if ($hasChanges) {
                $pelajar->save();
            }

            // Update/Create data Orang Tua dengan dirty checking
            $ayahChanged = $this->saveOrUpdateOrangTua($pelajar->id, $this->ayahData, $this->originalAyahData, 'ayah');
            $ibuChanged = $this->saveOrUpdateOrangTua($pelajar->id, $this->ibuData, $this->originalIbuData, 'ibu');
            $waliChanged = $this->saveOrUpdateOrangTua($pelajar->id, $this->waliData, $this->originalWaliData, 'wali');

            DB::commit();

            // ✅ SWEETALERT NOTIFICATION
            if ($hasChanges || $ayahChanged || $ibuChanged || $waliChanged) {
                $this->dispatch('show-alert', [
                    'type' => 'success',
                    'message' => 'Data pelajar berhasil diperbarui.'
                ]);
            } else {
                $this->dispatch('show-alert', [
                    'type' => 'info',
                    'message' => 'Tidak ada perubahan data.'
                ]);
            }

            $this->dispatch('close-edit-modal');
            $this->reset(['selectedStudent', 'studentData', 'ayahData', 'ibuData', 'waliData', 'originalStudentData', 'originalAyahData', 'originalIbuData', 'originalWaliData']);
        } catch (\Exception $e) {
            DB::rollBack();

            // ✅ SWEETALERT ERROR
            $this->dispatch('show-alert', [
                'type' => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ]);
        }
    }

    protected function saveOrUpdateOrangTua($pelajarId, $data, $originalData, $hubungan)
    {
        // Jika nama kosong, skip
        if (empty($data['nama'])) {
            return false;
        }

        $orangTua = OrangTuaWali::where('pelajar_id', $pelajarId)
            ->where('hubungan', $hubungan)
            ->first();

        $hasChanges = false;

        if ($orangTua) {
            // ✅ UPDATE HANYA FIELD YANG BERUBAH
            if (isset($data['nama']) && $data['nama'] !== $originalData['nama']) {
                $orangTua->nama = $data['nama'];
                $hasChanges = true;
            }

            if (isset($data['pekerjaan']) && $data['pekerjaan'] !== $originalData['pekerjaan']) {
                $orangTua->pekerjaan = !empty($data['pekerjaan']) ? $data['pekerjaan'] : null;
                $hasChanges = true;
            }

            if (isset($data['status']) && $data['status'] !== $originalData['status']) {
                $orangTua->status = $data['status'] ?? 'masih-hidup';
                $hasChanges = true;
            }

            if (isset($data['telepon']) && $data['telepon'] !== $originalData['telepon']) {
                $orangTua->telepon = !empty($data['telepon']) ? $data['telepon'] : null;
                $hasChanges = true;
            }

            if (isset($data['alamat']) && $data['alamat'] !== $originalData['alamat']) {
                $orangTua->alamat = !empty($data['alamat']) ? $data['alamat'] : null;
                $hasChanges = true;
            }

            if ($hasChanges) {
                $orangTua->save();
            }
        } else {
            // Create new
            OrangTuaWali::create([
                'pelajar_id' => $pelajarId,
                'nama' => $data['nama'],
                'hubungan' => $hubungan,
                'status' => $data['status'] ?? 'masih-hidup',
                'pekerjaan' => !empty($data['pekerjaan']) ? $data['pekerjaan'] : null,
                'telepon' => !empty($data['telepon']) ? $data['telepon'] : null,
                'alamat' => !empty($data['alamat']) ? $data['alamat'] : null,
            ]);
            $hasChanges = true;
        }

        return $hasChanges;
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
