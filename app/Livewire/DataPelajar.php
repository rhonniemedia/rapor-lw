<?php

namespace App\Livewire;

use App\Models\Pelajar;
use Livewire\Component;
use Livewire\WithPagination;

class DataPelajar extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;

    // Form properties
    public $pelajar_id;
    public $nis;
    public $nik;
    public $nama;
    public $tempat_lahir;
    public $tgl_lahir;
    public $jk;
    public $alamat = '';

    public $isEdit = false;

    protected $rules = [
        'nis' => 'required|unique:data_pelajars,nis',
        'nik' => 'required|numeric|digits:16|unique:data_pelajars,nik',
        'nama' => 'required|min:3',
        'tempat_lahir' => 'required',
        'tgl_lahir' => 'required|date',
        'jk' => 'required|in:L,P',
    ];

    protected $listeners = ['deleteConfirmed'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->dispatch('openModal');
    }

    public function store()
    {
        $this->validate();

        Pelajar::create([
            'nis' => $this->nis,
            'nik' => $this->nik,
            'nama' => $this->nama,
            'tempat_lahir' => $this->tempat_lahir,
            'tgl_lahir' => $this->tgl_lahir,
            'jk' => $this->jk,
        ]);

        $this->resetForm();
        $this->dispatch('closeModal');

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data pelajar berhasil ditambahkan!',
        ]);
    }

    public function edit($id)
    {
        $pelajar = Pelajar::findOrFail($id);

        $this->pelajar_id = $pelajar->id;
        $this->nis = $pelajar->nis;
        $this->nik = $pelajar->nik;
        $this->nama = $pelajar->nama;
        $this->tempat_lahir = $pelajar->tempat_lahir;
        $this->tgl_lahir = $pelajar->tgl_lahir;
        $this->jk = $pelajar->jk;

        $this->isEdit = true;
        $this->dispatch('openModal');
    }

    public function update()
    {
        $this->validate([
            'nis' => 'required|unique:data_pelajars,nis,' . $this->pelajar_id . ',id',
            'nik' => 'required|numeric|digits:16|unique:data_pelajars,nik,' . $this->pelajar_id . ',id',
            'nama' => 'required|min:3',
            'tempat_lahir' => 'required',
            'tgl_lahir' => 'required|date',
            'jk' => 'required|in:L,P',
        ]);

        $pelajar = Pelajar::findOrFail($this->pelajar_id);

        $pelajar->update([
            'nis' => $this->nis,
            'nik' => $this->nik,
            'nama' => $this->nama,
            'tempat_lahir' => $this->tempat_lahir,
            'tgl_lahir' => $this->tgl_lahir,
            'jk' => $this->jk,
        ]);

        $this->resetForm();
        $this->dispatch('closeModal');

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data pelajar berhasil diperbarui!',
        ]);
    }

    public function confirmDelete($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Hapus Data?',
            'text' => 'Data pelajar ini akan dihapus secara permanen!',
            'nextEvent' => 'deleteConfirmed',
            'id' => $id
        ]);
    }

    public function deleteConfirmed($id)
    {
        Pelajar::findOrFail($id)->delete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data pelajar berhasil dihapus!',
        ]);
    }

    private function resetForm()
    {
        $this->pelajar_id = null;
        $this->nis = '';
        $this->nik = '';
        $this->nama = '';
        $this->tempat_lahir = '';
        $this->tgl_lahir = '';
        $this->jk = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.data-pelajar', [
            'pelajars' => Pelajar::where('nama', 'like', '%' . $this->search . '%')
                ->orWhere('nis', 'like', '%' . $this->search . '%')
                ->orWhere('tempat_lahir', 'like', '%' . $this->search . '%')
                ->orderBy('nama', 'asc')
                ->paginate($this->perPage)
        ]);
    }
}
