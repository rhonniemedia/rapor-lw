<?php

namespace App\Livewire;

use App\Models\Jurusan;
use Livewire\Component;
use Livewire\WithPagination;

class DataJurusan extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔹 Pencarian & pagination
    public $search = '';
    public $perPage = 10;

    // 🔹 Form field
    public $jurusan_id;
    public $nama;
    public $alias;
    public $kode;

    public $isEdit = false;

    protected $baseRules = [
        'nama' => 'required|min:3',
        'alias' => 'required|unique:jurusans,alias',
        'kode' => 'required|unique:jurusans,kode|max:10',
    ];

    // 🔹 Event listener
    protected $listeners = [
        'deleteConfirmed',
        'createJurusan' => 'create',
    ];

    // 🔹 Reset pagination saat search/perPage berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    // 🔹 Rules dinamis untuk edit vs tambah
    public function getRules()
    {
        $rules = $this->baseRules;

        if ($this->isEdit && $this->jurusan_id) {
            $rules['alias'] = 'required|unique:jurusans,alias,' . $this->jurusan_id . ',id';
            $rules['kode'] = 'required|unique:jurusans,kode,' . $this->jurusan_id . ',id|max:10';
        }

        return $rules;
    }

    // 🔹 Buka modal tambah
    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->dispatch('openModal');
    }

    // 🔹 Simpan data baru
    public function store()
    {
        $this->validate($this->getRules());

        Jurusan::create([
            'nama' => $this->nama,
            'alias' => $this->alias,
            'kode' => strtoupper($this->kode),
        ]);

        $this->resetForm();
        $this->dispatch('closeModal');
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data jurusan berhasil ditambahkan!',
        ]);
    }

    // 🔹 Edit data
    public function edit($id)
    {
        $jurusan = Jurusan::findOrFail($id);

        $this->jurusan_id = $jurusan->id;
        $this->nama = $jurusan->nama;
        $this->alias = $jurusan->alias;
        $this->kode = $jurusan->kode;

        $this->isEdit = true;
        $this->dispatch('openModal');
    }

    // 🔹 Update data
    public function update()
    {
        $this->validate($this->getRules());

        $jurusan = Jurusan::findOrFail($this->jurusan_id);

        $jurusan->update([
            'nama' => $this->nama,
            'alias' => $this->alias,
            'kode' => strtoupper($this->kode),
        ]);

        $this->resetForm();
        $this->dispatch('closeModal');
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data jurusan berhasil diperbarui!',
        ]);
    }

    // 🔹 Konfirmasi hapus
    public function confirmDelete($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Hapus Data?',
            'text' => 'Data jurusan ini akan dihapus secara permanen!',
            'nextEvent' => 'deleteConfirmed',
            'id' => $id,
        ]);
    }

    // 🔹 Hapus data
    public function deleteConfirmed($id)
    {
        Jurusan::findOrFail($id)->delete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data jurusan berhasil dihapus!',
        ]);
    }

    // 🔹 Reset form
    private function resetForm()
    {
        $this->jurusan_id = null;
        $this->nama = '';
        $this->alias = '';
        $this->kode = '';
        $this->resetErrorBag();
    }

    // 🔹 Render data
    public function render()
    {
        return view('livewire.data-jurusan', [
            'jurusans' => Jurusan::where('nama', 'like', '%' . $this->search . '%')
                ->orWhere('alias', 'like', '%' . $this->search . '%')
                ->orWhere('kode', 'like', '%' . $this->search . '%')
                ->orderBy('nama', 'asc')
                ->paginate($this->perPage)
        ]);
    }
}
