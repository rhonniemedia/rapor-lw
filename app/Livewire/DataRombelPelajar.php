<?php

namespace App\Livewire;

use Log;
use App\Models\User;
use App\Models\Rombel;
use App\Models\Pelajar;
use Livewire\Component;
use Livewire\WithPagination;

use App\Models\MataPelajaran;
use App\Models\RombelPelajar;
use App\Models\RombelPengajar;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

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
    public $jam_pelajaran;
    public $isEdit = false;

    // Properti untuk autocomplete guru
    public $guruSearch = '';
    public $selectedGuruName = '';

    // Properti pencarian & pagination
    public $search = '';
    public $perPage = 10;

    // Tab aktif
    public $activeTab = 'pelajar'; // 'pelajar' atau 'mapel'

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

    // Computed properties untuk dropdown
    // public function getMataPelajaranListProperty()
    // {
    //     $tahunAjaranKurikulum = $this->rombel->tahunAjaranKurikulum;

    //     if (!$tahunAjaranKurikulum) {
    //         return collect();
    //     }

    //     $kurikulumId = $tahunAjaranKurikulum->kurikulum_id;
    //     $tingkat = $this->rombel->tingkat;

    //     return MataPelajaran::whereHas('kurikulumMataPelajarans', function ($query) use ($kurikulumId, $tingkat) {
    //         $query->where('kurikulum_id', $kurikulumId)
    //             ->where('tingkat', $tingkat);
    //     })
    //         ->orderBy('nama', 'asc')
    //         ->get();
    // }

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
        $mapelJurusanIds = \DB::table('jurusan_mata_pelajarans')
            ->where('jurusan_id', $jurusanId)
            ->where('kurikulum_id', $kurikulumId)
            ->pluck('mata_pelajaran_id')
            ->toArray();

        // Step 4: Ambil SEMUA mapel yang ada pembatasan jurusan (untuk filtering)
        $semuaMapelDenganJurusan = \DB::table('jurusan_mata_pelajarans')
            ->where('kurikulum_id', $kurikulumId)
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

    // === CRUD MATA PELAJARAN & PENGAJAR ===

    public function createMapel()
    {
        $this->resetFormMapel();
        $this->isEdit = false;
        $this->dispatch('openModalRombelPengajar');
    }

    public function storeMapel()
    {
        $this->validate($this->getRulesMapel());

        RombelPengajar::create([
            'rombel_id' => $this->rombelId,
            'mata_pelajaran_id' => $this->mata_pelajaran_id,
            'guru_id' => $this->guru_id,
            'jam_pelajaran' => $this->jam_pelajaran ?: null,
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
        $this->jam_pelajaran = $data->jam_pelajaran;

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

        $data = RombelPengajar::findOrFail($this->rombel_pengajar_id);

        $data->update([
            'mata_pelajaran_id' => $this->mata_pelajaran_id,
            'guru_id' => $this->guru_id,
            'jam_pelajaran' => $this->jam_pelajaran ?: null,
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
            'guru_id' => 'required|uuid|exists:users,id',
            'jam_pelajaran' => 'nullable|integer|min:1|max:10',
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
        $this->jam_pelajaran = '';
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
                    $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                        ->orWhere('nisn', 'like', '%' . $this->search . '%');
                });
            }

            $data = $query->orderBy('created_at', 'asc')->paginate($this->perPage);
        }

        return view('livewire.data-rombel-pelajar', [
            'data' => $data,
            'mataPelajaranList' => $this->mataPelajaranList,
            'guruList' => $this->guruList,
            'filteredGuruList' => $this->filteredGuruList,
        ]);
    }
}
