<?php

namespace App\Livewire\Admin;

use App\Models\Ekstrakurikuler;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class DataEkstrakurikuler extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔹 Pencarian & pagination
    public $search = '';
    public $perPage = 10;

    // 🔹 Form field
    public $ekstrakurikuler_id;
    public $nama;
    public $deskripsi;
    public $status = 'aktif';
    public $pembina_id;

    public $isEdit = false;

    protected $baseRules = [
        'nama' => 'required|min:3|unique:ekstrakurikulers,nama',
        'deskripsi' => 'nullable|string',
        'status' => 'required|in:aktif,arsip',
        'pembina_id' => 'required|exists:users,id',
    ];

    protected $listeners = [
        'deleteConfirmed',
        'createEkstrakurikuler' => 'create',
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

    // 🔹 Rules dinamis untuk edit
    public function getRules()
    {
        $rules = $this->baseRules;

        if ($this->isEdit && $this->ekstrakurikuler_id) {
            $rules['nama'] = 'required|min:3|unique:ekstrakurikulers,nama,' . $this->ekstrakurikuler_id . ',id';
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

        Ekstrakurikuler::create([
            'nama' => $this->nama,
            'deskripsi' => $this->deskripsi,
            'status' => $this->status,
            'pembina_id' => $this->pembina_id,
        ]);

        $this->resetForm();
        $this->dispatch('closeModal');
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data ekstrakurikuler berhasil ditambahkan!',
        ]);
    }

    // 🔹 Edit data
    public function edit($id)
    {
        $ekstra = Ekstrakurikuler::findOrFail($id);

        $this->ekstrakurikuler_id = $ekstra->id;
        $this->nama = $ekstra->nama;
        $this->deskripsi = $ekstra->deskripsi;
        $this->status = $ekstra->status;
        $this->pembina_id = $ekstra->pembina_id;

        $this->isEdit = true;
        $this->dispatch('openModal');
    }

    // 🔹 Update data
    public function update()
    {
        $this->validate($this->getRules());

        $ekstra = Ekstrakurikuler::findOrFail($this->ekstrakurikuler_id);

        $ekstra->update([
            'nama' => $this->nama,
            'deskripsi' => $this->deskripsi,
            'status' => $this->status,
            'pembina_id' => $this->pembina_id,
        ]);

        $this->resetForm();
        $this->dispatch('closeModal');
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data ekstrakurikuler berhasil diperbarui!',
        ]);
    }

    // 🔹 Konfirmasi hapus
    public function confirmDelete($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Hapus Data?',
            'text' => 'Data ekstrakurikuler ini akan dihapus secara permanen!',
            'nextEvent' => 'deleteConfirmed',
            'id' => $id,
        ]);
    }

    // 🔹 Hapus data
    public function deleteConfirmed($id)
    {
        Ekstrakurikuler::findOrFail($id)->delete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data ekstrakurikuler berhasil dihapus!',
        ]);
    }

    // 🔹 Reset form
    private function resetForm()
    {
        $this->ekstrakurikuler_id = null;
        $this->nama = '';
        $this->deskripsi = '';
        $this->status = 'aktif';
        $this->pembina_id = '';
        $this->resetErrorBag();
    }

    // 🔹 Render data
    public function render()
    {
        $query = Ekstrakurikuler::with('pembina');

        // Filter pencarian (jika ada)
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.admin.data-ekstrakurikuler', [
            'ekstrakurikulers' => $query->orderBy('nama', 'asc')->paginate($this->perPage),
            'pembinas' => User::orderBy('name', 'asc')->get(),
        ]);
    }
}
