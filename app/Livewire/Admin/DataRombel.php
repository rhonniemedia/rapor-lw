<?php

namespace App\Livewire\Admin;

use App\Models\Rombel;
use App\Models\Jurusan;
use App\Models\TahunAjaranKurikulum;
use App\Models\User;
use App\Models\Pelajar;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class DataRombel extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;

    public $rombel_id;
    public $jurusan_id;
    public $tahun_ajaran_kurikulum_id;
    public $wali_kelas_slug;
    public $tingkat;
    public $nama;

    public $isEdit = false;

    // Data dropdown - akan diakses via computed property

    protected $listeners = [
        'deleteConfirmedRombel' => 'deleteConfirmedRombel',
        'createRombel' => 'create',
    ];

    // ✅ Ubah mount() menjadi lebih ringan atau hapus sama sekali
    public function mount()
    {
        // Kosongkan atau hapus method ini
    }

    // ✅ Buat computed property untuk dropdown data
    public function getJurusanListProperty()
    {
        return Jurusan::orderBy('nama', 'asc')->get();
    }

    public function getTahunAjaranKurikulumListProperty()
    {
        return TahunAjaranKurikulum::with(['kurikulum', 'tahunAjaran'])
            ->get()
            ->map(function ($item) {
                $item->display_name = $item->tahunAjaran->nama . ' (' . ($item->kurikulum->nama ?? 'Tanpa Kurikulum') . ')';
                return $item;
            });
    }

    public function getWalikelasListProperty()
    {
        return User::orderBy('name', 'asc')
            ->select('id', 'slug', 'name')
            ->get();
    }

    protected $baseRules = [
        'jurusan_id' => 'required|uuid|exists:jurusans,id',
        'tahun_ajaran_kurikulum_id' => 'nullable|uuid|exists:tahun_ajaran_kurikulums,id',
        'wali_kelas_slug' => 'nullable|string|exists:users,slug',
        'tingkat' => 'required|integer|in:10,11,12',
        'nama' => 'required|string|min:2',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getRules()
    {
        $rules = $this->baseRules;

        $uniqueRule = Rule::unique('rombels', 'nama')
            ->where(fn($query) => $query->where('tingkat', $this->tingkat)
                ->where('jurusan_id', $this->jurusan_id));

        if ($this->isEdit && $this->rombel_id) {
            $rules['nama'] = ['required', 'string', 'min:2', $uniqueRule->ignore($this->rombel_id, 'id')];
        } elseif (!$this->isEdit) {
            $rules['nama'] = ['required', 'string', 'min:2', $uniqueRule];
        }

        return $rules;
    }

    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->dispatch('openModalRombel');
    }

    public function store()
    {
        $this->validate($this->getRules());

        Rombel::create([
            'jurusan_id' => $this->jurusan_id,
            'tahun_ajaran_kurikulum_id' => $this->tahun_ajaran_kurikulum_id ?: null,
            'wali_kelas_slug' => $this->wali_kelas_slug ?: null,
            'tingkat' => $this->tingkat,
            'nama' => $this->nama,
        ]);

        $this->dispatch('closeModalRombel');
        $this->resetForm();
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data rombongan belajar berhasil ditambahkan!',
        ]);
    }

    public function edit($id)
    {
        $data = Rombel::findOrFail($id);

        $this->rombel_id = $data->id;
        $this->jurusan_id = $data->jurusan_id;
        $this->tahun_ajaran_kurikulum_id = $data->tahun_ajaran_kurikulum_id;
        $this->wali_kelas_slug = $data->wali_kelas_slug;
        $this->tingkat = $data->tingkat;
        $this->nama = $data->nama;

        $this->isEdit = true;
        $this->dispatch('openModalRombel');
    }

    public function update()
    {
        $this->validate($this->getRules());

        $data = Rombel::findOrFail($this->rombel_id);

        $data->update([
            'jurusan_id' => $this->jurusan_id,
            'tahun_ajaran_kurikulum_id' => $this->tahun_ajaran_kurikulum_id ?: null,
            'wali_kelas_slug' => $this->wali_kelas_slug ?: null,
            'tingkat' => $this->tingkat,
            'nama' => $this->nama,
        ]);

        $this->dispatch('closeModalRombel');
        $this->resetForm();
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data rombongan belajar berhasil diperbarui!',
        ]);
    }

    public function confirmDeleteRombel($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Hapus Data?',
            'text' => 'Rombel ini akan dihapus secara permanen!',
            'nextEvent' => 'deleteConfirmedRombel',
            'id' => $id,
        ]);
    }

    public function deleteConfirmedRombel($id)
    {
        Rombel::findOrFail($id)->delete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data rombongan belajar berhasil dihapus!',
        ]);
    }

    private function resetForm()
    {
        $this->rombel_id = null;
        $this->jurusan_id = '';
        $this->tahun_ajaran_kurikulum_id = '';
        $this->wali_kelas_slug = '';
        $this->tingkat = '';
        $this->nama = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = Rombel::with([
            'jurusan',
            'tahunAjaranKurikulum.kurikulum',
            'tahunAjaranKurikulum.tahunAjaran'
        ])
            ->withCount([
                'pelajars as total_pelajar',
                'pelajars as total_laki' => function ($q) {
                    $q->where('pelajars.jenis_kelamin', 'L');
                },
                'pelajars as total_perempuan' => function ($q) {
                    $q->where('pelajars.jenis_kelamin', 'P');
                }
            ]);

        $query->leftJoin('users as u', 'u.slug', '=', 'rombels.wali_kelas_slug');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('rombels.nama', 'like', '%' . $this->search . '%')
                    ->orWhere('rombels.tingkat', 'like', '%' . $this->search . '%')
                    ->orWhereHas('jurusan', function ($subQ) {
                        $subQ->where('nama', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('u.name', 'like', '%' . $this->search . '%');
            });
        }

        $rombels = $query
            ->select('rombels.*', 'u.name as walikelas_name')
            ->orderBy('rombels.tingkat', 'asc')
            ->orderBy('rombels.nama', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.data-rombel', [
            'rombels' => $rombels,
            'jurusanList' => $this->jurusanList,
            'tahunAjaranKurikulumList' => $this->tahunAjaranKurikulumList,
            'walikelasList' => $this->walikelasList,
        ]);
    }
}
