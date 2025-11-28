<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Nilai;
use App\Models\Rombel;
use App\Models\Pelajar;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\OrangTuaWali;
use Livewire\WithPagination;
use App\Models\MataPelajaran;
use App\Models\RombelPelajar;
use App\Models\RombelPengajar;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\JurusanMataPelajaran;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class DataRombelPelajar extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Properti utama
    public $rombelId;
    public $rombel;

    // Properti untuk form mata pelajaran
    public $rombel_pengajar_id;
    public $mata_pelajaran_id;
    public $guru_id;
    public $isEdit = false;

    // Properti untuk autocomplete guru
    public $guruSearch = '';
    public $selectedGuruName = '';

    // Properti pencarian & pagination
    public $search = '';
    public $perPage = 10;

    // Tab aktif
    public $activeTab = 'pelajar'; // 'pelajar' atau 'mapel'

    // === PROPERTI BARU: CRUD PELAJAR ===
    public $selectedStudentId;
    public $selectedStudent; // Model Pelajar (untuk Detail)
    public $isEditStudent = false;

    // Data Binding Form Pelajar
    public $studentData = [];
    public $ayahData = [];
    public $ibuData = [];
    public $waliData = [];

    // Event listener
    protected $listeners = [
        'deleteConfirmedRombelPelajar' => 'deleteConfirmedRombelPelajar',
        'deleteConfirmedRombelPengajar' => 'deleteConfirmedRombelPengajar'
    ];

    public function mount($rombelId)
    {
        $this->rombelId = $rombelId;
        $this->rombel = Rombel::findOrFail($rombelId);
    }

    // Computed properties untuk dropdown (Logika pemfilteran kurikulum/jurusan tetap sama)
    public function getMataPelajaranListProperty()
    {
        $tahunAjaranKurikulum = $this->rombel->tahunAjaranKurikulum;
        if (!$tahunAjaranKurikulum) return collect();

        $kurikulumId = $tahunAjaranKurikulum->kurikulum_id;
        $tingkat = $this->rombel->tingkat;
        $jurusanId = $this->rombel->jurusan_id;

        // Step 1: Ambil ID mapel dari kurikulum_mata_pelajarans (mapel umum)
        $mapelUmumIds = MataPelajaran::whereHas('kurikulumMataPelajarans', function ($query) use ($kurikulumId, $tingkat) {
            $query->where('kurikulum_id', $kurikulumId)
                ->where('tingkat', $tingkat);
        })->pluck('id')->toArray();

        // Step 2: Jika tidak ada jurusan, return mapel umum saja
        if (!$jurusanId) {
            return MataPelajaran::whereIn('id', $mapelUmumIds)
                ->orderBy('nama')->get();
        }

        // Step 3: Ambil ID mapel khusus jurusan
        $mapelJurusanIds = JurusanMataPelajaran::where('jurusan_id', $jurusanId)
            ->where('kurikulum_id', $kurikulumId)
            ->pluck('mata_pelajaran_id')
            ->toArray();

        // Step 4: Ambil SEMUA mapel yang ada pembatasan jurusan (untuk filtering)
        $semuaMapelDenganJurusan = JurusanMataPelajaran::where('kurikulum_id', $kurikulumId)
            ->pluck('mata_pelajaran_id')
            ->unique()
            ->toArray();

        // Step 5: Filter mapel umum - hapus yang punya pembatasan jurusan tapi bukan untuk jurusan ini
        $mapelUmumFiltered = array_diff($mapelUmumIds, $semuaMapelDenganJurusan);

        // Step 6: Gabungkan mapel umum (yang sudah difilter) + mapel khusus jurusan ini
        $finalIds = array_merge($mapelUmumFiltered, $mapelJurusanIds);

        // Step 7: Ambil data mata pelajaran dan return
        return MataPelajaran::whereIn('id', $finalIds)
            ->orderBy('nama')->get();
    }

    public function getGuruListProperty()
    {
        // Mengambil semua guru, nanti divalidasi saat penugasan
        return User::where('is_teacher', true)
            ->orderBy('name', 'asc')
            ->get();
    }

    // Computed property untuk filtered guru list (autocomplete)
    public function getFilteredGuruListProperty()
    {
        if (empty($this->guruSearch)) {
            return collect();
        }

        // Hashing input pencarian TIDAK diperlukan di sini karena 'name' tidak dienkripsi
        return User::where('is_teacher', true)
            ->where('name', 'like', '%' . $this->guruSearch . '%')
            ->orderBy('name', 'asc')
            ->limit(10)
            ->get();
    }

    // Method untuk select guru dari autocomplete
    public function selectGuru($guruId)
    {
        $guru = User::findOrFail($guruId);
        $this->guru_id = $guruId;
        $this->selectedGuruName = $guru->name;
        $this->guruSearch = '';
    }

    // Method untuk clear selected guru
    public function clearGuru()
    {
        $this->guru_id = null;
        $this->selectedGuruName = '';
        $this->guruSearch = '';
    }

    // Reset pagination saat search berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Switch tab
    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->search = '';
        $this->resetPage();
    }

    public function openDetailModal($id)
    {
        $this->selectedStudent = Pelajar::with('orangTuaWalis')->findOrFail($id);
        $this->dispatch('openModalDetail');
    }

    /**
     * Membuka Modal Tambah Pelajar Baru
     */
    public function createPelajar()
    {
        // === PROTEKSI BACKEND ===
        if (!Auth::user()->hasRole('superadmin')) {
            $this->dispatch('swal:error', [
                'title' => 'Akses Ditolak!',
                'text' => 'Fitur ini hanya untuk Superadmin.',
            ]);
            return;
        }

        // ========================

        $this->resetStudentForm();
        $this->isEditStudent = false;

        // Inisialisasi default array
        $this->studentData = [
            'jenis_kelamin' => '',
            'agama' => '',
            // SET DEFAULT DI SINI
            'status_dalam_keluarga' => ''
        ];

        $this->ayahData = ['pekerjaan' => '', 'status' => 'masih-hidup'];
        $this->ibuData = ['pekerjaan' => '', 'status' => 'masih-hidup'];
        $this->waliData = ['pekerjaan' => '', 'status' => 'masih-hidup'];

        $this->dispatch('openModalEditStudent');
    }

    /**
     * Membuka Modal Edit Pelajar
     */
    public function openEditModal($id)
    {
        $this->isEditStudent = true;
        $this->selectedStudentId = $id;
        $pelajar = Pelajar::with('orangTuaWalis')->findOrFail($id);
        $this->selectedStudent = $pelajar; // Untuk keperluan view jika ada

        // Isi form data pelajar
        $this->studentData = [
            'nama_lengkap' => $pelajar->nama_lengkap,
            'nomor_induk' => $pelajar->nomor_induk,
            'nisn' => $pelajar->nisn,
            'jenis_kelamin' => $pelajar->jenis_kelamin,
            'tempat_lahir' => $pelajar->tempat_lahir,
            // 'tanggal_lahir' => $pelajar->tanggal_lahir ? $pelajar->tanggal_lahir->format('Y-m-d') : null,
            'tanggal_lahir' => $pelajar->tanggal_lahir ? Carbon::parse($pelajar->tanggal_lahir)->format('Y-m-d') : null,
            'agama' => $pelajar->agama,
            // 'status_dalam_keluarga' => $pelajar->status_dalam_keluarga,
            'status_dalam_keluarga' => $pelajar->status_dalam_keluarga ?? '',
            'anak_ke' => $pelajar->anak_ke,
            'telepon' => $pelajar->telepon,
            'alamat' => $pelajar->alamat,
            'sekolah_asal' => $pelajar->sekolah_asal,
            'diterima_di_kelas' => $pelajar->diterima_di_kelas,
            // 'pada_tanggal' => $pelajar->pada_tanggal ? $pelajar->pada_tanggal->format('Y-m-d') : null,
            'pada_tanggal' => $pelajar->pada_tanggal ? Carbon::parse($pelajar->pada_tanggal)->format('Y-m-d') : null
        ];

        // Isi form data orang tua/wali
        $ayah = $pelajar->orangTuaWalis->where('hubungan', 'ayah')->first();
        $ibu = $pelajar->orangTuaWalis->where('hubungan', 'ibu')->first();
        $wali = $pelajar->orangTuaWalis->where('hubungan', 'wali')->first();

        $this->ayahData = $ayah ? $ayah->toArray() : ['status' => 'masih-hidup'];
        $this->ibuData = $ibu ? $ibu->toArray() : ['status' => 'masih-hidup'];
        $this->waliData = $wali ? $wali->toArray() : ['status' => 'masih-hidup'];

        $this->dispatch('openModalEditStudent');
    }

    /**
     * Menyimpan Data Pelajar (Create & Update)
     */
    public function saveStudent()
    {
        // 1. Validasi
        $rules = [
            'studentData.nama_lengkap' => 'required|string|max:255',
            'studentData.jenis_kelamin' => 'required|in:L,P',
            'studentData.status_dalam_keluarga' => 'nullable|string|max:50',
            'studentData.nomor_induk' => [
                'nullable',
                Rule::unique('pelajars', 'nomor_induk')->ignore($this->selectedStudentId)
            ],
            'studentData.nisn' => [
                'nullable',
                'digits:10',
                Rule::unique('pelajars', 'nisn')->ignore($this->selectedStudentId)
            ],
            // Validasi nama orang tua opsional
            'ayahData.nama' => 'nullable|string|max:255',
            'ibuData.nama' => 'nullable|string|max:255',
        ];

        $this->validate($rules);

        DB::beginTransaction();
        try {
            $message = '';
            $statusType = 'success';
            $isAnythingUpdated = false;

            // Bersihkan data pelajar (Convert "" ke NULL)
            $cleanStudentData = $this->sanitizeData($this->studentData);

            if ($this->isEditStudent) {
                // === LOGIKA UPDATE ===
                $pelajar = Pelajar::findOrFail($this->selectedStudentId);

                // Fill data baru
                $pelajar->fill($cleanStudentData);

                // Cek apakah data pelajar berubah?
                if ($pelajar->isDirty()) {
                    $pelajar->save();
                    $isAnythingUpdated = true;
                }
            } else {
                // === LOGIKA CREATE (Selalu dianggap update/baru) ===
                $pelajar = Pelajar::create($cleanStudentData);
                RombelPelajar::create([
                    'rombel_id' => $this->rombelId,
                    'pelajar_id' => $pelajar->id
                ]);
                $isAnythingUpdated = true;
            }

            // 3. Simpan Data Orang Tua/Wali
            // Variabel $cek... akan bernilai TRUE jika ada perubahan di DB
            $cekAyah = $this->saveOrangTua($pelajar, 'ayah', $this->ayahData);
            $cekIbu  = $this->saveOrangTua($pelajar, 'ibu', $this->ibuData);
            $cekWali = $this->saveOrangTua($pelajar, 'wali', $this->waliData);

            if ($cekAyah || $cekIbu || $cekWali) {
                $isAnythingUpdated = true;
            }

            DB::commit();

            // LOGIKA PESAN NOTIFIKASI
            if ($isAnythingUpdated) {
                // Jika ada perubahan (Entah di pelajar ATAU di orang tua)
                $this->dispatch('closeModalEditStudent');
                $this->resetStudentForm();

                $this->dispatch('swal:success', [
                    'title' => 'Berhasil!',
                    'text' => $this->isEditStudent ? 'Data berhasil diperbarui.' : 'Pelajar baru ditambahkan.',
                ]);
            } else {
                // Jika TIDAK ADA perubahan sama sekali
                // Opsional: Tutup modal atau biarkan terbuka
                // $this->dispatch('closeModalEditStudent'); 

                $this->dispatch('swal:info', [
                    'title' => 'Info',
                    'text' => 'Tidak ada perubahan data yang disimpan.',
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Helper: Ubah string kosong jadi NULL
     * Berguna agar field tanggal/opsional tidak error atau menyimpan string kosong
     */
    private function sanitizeData($array)
    {
        return array_map(function ($value) {
            return $value === '' ? null : $value;
        }, $array);
    }

    /**
     * Helper simpan Orang Tua (Update agar me-return boolean jika ada perubahan)
     */
    private function saveOrangTua($pelajar, $hubungan, $data)
    {
        // 1. Jika form nama kosong, anggap user tidak mengisi data -> return false
        if (empty($data['nama'])) return false;

        // 2. Bersihkan data (convert "" jadi null)
        $cleanData = $this->sanitizeData($data);

        // 3. PENTING: Buang kolom sistem yang terbawa oleh toArray() saat load data
        // Ini penyebab utama isDirty() selalu true sebelumnya
        $dataToSave = Arr::except($cleanData, ['id', 'created_at', 'updated_at', 'pelajar_id', 'hubungan']);

        $dataToSave['hubungan'] = $hubungan; // Pastikan hubungan tetap diset

        // Cek data eksisting di DB
        $existing = $pelajar->orangTuaWalis()->where('hubungan', $hubungan)->first();

        if ($existing) {
            $existing->fill($dataToSave);

            if ($existing->isDirty()) {
                $existing->save();
                return true; // Ada perubahan
            }
            return false; // Tidak ada perubahan
        } else {
            // Jika data baru, create
            $pelajar->orangTuaWalis()->create($dataToSave);
            return true;
        }
    }

    private function resetStudentForm()
    {
        $this->selectedStudentId = null;
        $this->selectedStudent = null;
        $this->studentData = [];
        // Set default status orang tua
        $this->ayahData = ['status' => 'masih-hidup'];
        $this->ibuData = ['status' => 'masih-hidup'];
        $this->waliData = ['status' => 'masih-hidup'];
        $this->resetErrorBag();
    }

    // === CRUD MATA PELAJARAN & PENGAJAR ===

    public function createMapel()
    {
        $this->resetFormMapel();
        $this->isEdit = false;
        $this->dispatch('openModalRombelPengajar');
    }

    // Fungsi baru untuk validasi agama
    private function validateAgamaCompatibility()
    {
        if (empty($this->mata_pelajaran_id)) {
            return true; // Mata pelajaran wajib diisi, jika kosong biarkan rules yang menangani
        }

        // 🚨 MODIFIKASI: Jika guru_id kosong/null, anggap valid (diizinkan tanpa guru)
        if (empty($this->guru_id)) {
            return true;
        }

        $mapel = MataPelajaran::findOrFail($this->mata_pelajaran_id);
        $guru = User::findOrFail($this->guru_id);

        if ($mapel->is_mapel_agama) {
            // Jika Mapel adalah Agama, cek kompatibilitas Guru

            // 1. Guru WAJIB ditandai sebagai guru agama
            if (!$guru->is_guru_agama) {
                $this->addError('guru_id', 'Guru ini harus ditandai sebagai Guru Agama untuk mengajar mata pelajaran agama.');
                return false;
            }

            // 2. Hash Agama Guru harus sama dengan Hash Agama Mapel
            // Gunakan hash yang sudah dinormalisasi dari Model
            if ($guru->spesialisasi_agama_hash !== $mapel->agama_terkait_hash) {
                // Tampilkan pesan error dengan nama agama yang didekripsi
                $this->addError('guru_id', 'Spesialisasi agama guru tidak cocok dengan mata pelajaran: ' . ucfirst($mapel->agama_terkait));
                return false;
            }
        }

        // Jika Mapel BUKAN Agama (Mapel Umum), cek Guru BUKAN Guru Agama Spesifik
        if (!$mapel->is_mapel_agama && $guru->is_guru_agama && !empty($guru->spesialisasi_agama)) {
            // Opsional: Batasi Guru Agama hanya mengajar Mapel Agama. 
            // Namun, demi fleksibilitas, kita anggap Guru Agama boleh mengajar mapel umum.
            // Tidak perlu menambahkan error di sini.
        }

        return true;
    }

    public function storeMapel()
    {
        $this->validate($this->getRulesMapel());

        // Validasi Kustom Kompatibilitas Agama
        if (!$this->validateAgamaCompatibility()) {
            return;
        }

        // 🚨 SOLUSI: Konversi string kosong menjadi NULL sebelum create
        $guruIdToStore = $this->guru_id === '' ? null : $this->guru_id;

        RombelPengajar::create([
            'rombel_id' => $this->rombelId,
            'mata_pelajaran_id' => $this->mata_pelajaran_id,
            'guru_id' => $guruIdToStore,
        ]);

        $this->dispatch('closeModalRombelPengajar');
        $this->resetFormMapel();
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Mata pelajaran dan pengajar berhasil ditambahkan!',
        ]);
    }

    public function editMapel($id)
    {
        $data = RombelPengajar::findOrFail($id);

        $this->rombel_pengajar_id = $data->id;
        $this->mata_pelajaran_id = $data->mata_pelajaran_id;
        $this->guru_id = $data->guru_id;

        // Set selected guru name untuk ditampilkan
        if ($this->guru_id) {
            $guru = User::find($this->guru_id);
            $this->selectedGuruName = $guru ? $guru->name : '';
        }

        $this->isEdit = true;
        $this->dispatch('openModalRombelPengajar');
    }

    public function updateMapel()
    {
        $this->validate($this->getRulesMapel());

        // Validasi Kustom Kompatibilitas Agama
        if (!$this->validateAgamaCompatibility()) {
            return;
        }

        $data = RombelPengajar::findOrFail($this->rombel_pengajar_id);

        // 🚨 SOLUSI: Konversi string kosong menjadi NULL sebelum update
        $guruIdToUpdate = $this->guru_id === '' ? null : $this->guru_id;

        $data->update([
            'mata_pelajaran_id' => $this->mata_pelajaran_id,
            'guru_id' => $guruIdToUpdate,
        ]);

        $this->dispatch('closeModalRombelPengajar');
        $this->resetFormMapel();
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data pengajar berhasil diperbarui!',
        ]);
    }

    public function confirmDeleteRombelPengajar($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Hapus Mata Pelajaran?',
            'text' => 'Mata pelajaran ini akan dihapus dari rombel!',
            'nextEvent' => 'deleteConfirmedRombelPengajar',
            'id' => $id,
        ]);
    }

    public function deleteConfirmedRombelPengajar($id)
    {
        Nilai::where('rombel_pengajar_id', $id)->delete();

        RombelPengajar::findOrFail($id)->delete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Mata pelajaran berhasil dihapus!',
        ]);
    }

    protected function getRulesMapel()
    {
        $rules = [
            'mata_pelajaran_id' => [
                'required',
                'uuid',
                'exists:mata_pelajarans,id',
            ],
            'guru_id' => 'nullable|uuid|exists:users,id',
        ];

        // Unique validation untuk kombinasi rombel + mata pelajaran
        $uniqueRule = Rule::unique('rombel_pengajars')
            ->where('rombel_id', $this->rombelId)
            ->where('mata_pelajaran_id', $this->mata_pelajaran_id);

        if ($this->isEdit && $this->rombel_pengajar_id) {
            $rules['mata_pelajaran_id'][] = $uniqueRule->ignore($this->rombel_pengajar_id);
        } else {
            $rules['mata_pelajaran_id'][] = $uniqueRule;
        }

        return $rules;
    }

    private function resetFormMapel()
    {
        $this->rombel_pengajar_id = null;
        $this->mata_pelajaran_id = '';
        $this->guru_id = '';
        $this->guruSearch = '';
        $this->selectedGuruName = '';
        $this->resetErrorBag();
    }

    // === CRUD PELAJAR (Hapus saja) ===

    public function confirmDeleteRombelPelajar($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Hapus Pelajar dari Rombel?',
            'text' => 'Pelajar ini akan dikeluarkan dari rombongan belajar!',
            'nextEvent' => 'deleteConfirmedRombelPelajar',
            'id' => $id,
        ]);
    }

    public function deleteConfirmedRombelPelajar($id)
    {
        RombelPelajar::findOrFail($id)->delete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Pelajar berhasil dikeluarkan dari rombel!',
        ]);
    }

    // Render view
    public function render()
    {
        if ($this->activeTab === 'mapel') {
            // Query untuk mata pelajaran & pengajar
            $query = RombelPengajar::where('rombel_id', $this->rombelId)
                ->with(['mataPelajaran', 'guru']);

            if (!empty($this->search)) {
                $query->where(function ($q) {
                    $q->whereHas('mataPelajaran', function ($subQ) {
                        $subQ->where('nama', 'like', '%' . $this->search . '%');
                    })
                        ->orWhereHas('guru', function ($subQ) {
                            $subQ->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            }

            $data = $query->orderBy('created_at', 'asc')->paginate($this->perPage);
        } else {
            // Query untuk pelajar
            $query = RombelPelajar::where('rombel_id', $this->rombelId)
                ->with(['pelajar']);

            if (!empty($this->search)) {
                $query->whereHas('pelajar', function ($q) {
                    // Filter pelajar berdasarkan nama_lengkap atau hash NISN (jika diperlukan)
                    $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                        // Pencarian NISN menggunakan hash
                        ->orWhere('nisn_hash', 'like', '%' . hash('sha256', Str::lower($this->search)) . '%');
                });
            }

            $data = $query
                ->with(['pelajar'])
                ->whereHas('pelajar')
                ->orderBy(
                    Pelajar::select('nama_lengkap')
                        ->whereColumn('pelajars.id', 'rombel_pelajars.pelajar_id'),
                    'asc'
                )
                ->paginate($this->perPage);
        }

        return view('livewire.admin.data-rombel-pelajar', [
            'data' => $data,
            'mataPelajaranList' => $this->mataPelajaranList,
            'guruList' => $this->guruList,
            'filteredGuruList' => $this->filteredGuruList,
        ]);
    }
}
