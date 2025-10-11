<?php

namespace App\Livewire;

use App\Models\Kurikulum;
use Livewire\Component;
use Livewire\WithPagination;

class DataKurikulum extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔹 Pencarian & pagination
    public $search = '';
    public $perPage = 10;

    // 🔹 Form field
    public $kurikulum_id;
    public $nama;
    public $kode;
    public $deskripsi;
    public $status = 'aktif';

    public $isEdit = false;

    // 🔹 Aturan dasar
    protected $baseRules = [
        'nama' => 'required|min:3',
        'kode' => 'required|max:10|unique:kurikulums,kode',
        'deskripsi' => 'nullable|string|max:255',
        'status' => 'required|in:aktif,arsip',
    ];

    // 🔹 Listener event konfirmasi hapus - DIPERBAIKI
    protected $listeners = ['deleteConfirmedKurikulum' => 'deleteConfirmedKurikulum'];

    // 🔹 Reset pagination jika search/perPage berubah
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
        if ($this->isEdit && $this->kurikulum_id) {
            $rules['kode'] = 'required|max:10|unique:kurikulums,kode,' . $this->kurikulum_id . ',id';
        }
        return $rules;
    }

    // 🔹 Buka modal tambah
    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->dispatch('openModalKurikulum');
    }

    // 🔹 Simpan data baru
    public function store()
    {
        $this->validate($this->getRules());

        Kurikulum::create([
            'nama' => $this->nama,
            'kode' => strtoupper($this->kode),
            'deskripsi' => $this->deskripsi,
            'status' => $this->status,
        ]);

        $this->resetForm();
        $this->dispatch('closeModalKurikulum');
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Kurikulum berhasil ditambahkan!',
        ]);
    }

    // 🔹 Edit data
    public function edit($id)
    {
        $kurikulum = Kurikulum::findOrFail($id);

        $this->kurikulum_id = $kurikulum->id;
        $this->nama = $kurikulum->nama;
        $this->kode = $kurikulum->kode;
        $this->deskripsi = $kurikulum->deskripsi;
        $this->status = $kurikulum->status;

        $this->isEdit = true;
        $this->dispatch('openModalKurikulum');
    }

    // 🔹 Update data
    public function update()
    {
        $this->validate($this->getRules());

        $kurikulum = Kurikulum::findOrFail($this->kurikulum_id);
        $kurikulum->update([
            'nama' => $this->nama,
            'kode' => strtoupper($this->kode),
            'deskripsi' => $this->deskripsi,
            'status' => $this->status,
        ]);

        $this->resetForm();
        $this->dispatch('closeModalKurikulum');
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data kurikulum berhasil diperbarui!',
        ]);
    }

    // 🔹 Konfirmasi hapus
    public function confirmDeleteKurikulum($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Hapus Data?',
            'text' => 'Data kurikulum ini akan dihapus permanen!',
            'nextEvent' => 'deleteConfirmedKurikulum', // DIPERBAIKI
            'id' => $id,
        ]);
    }

    // 🔹 Hapus data - NAMA METHOD SUDAH BENAR
    public function deleteConfirmedKurikulum($id)
    {
        Kurikulum::findOrFail($id)->delete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Kurikulum telah dihapus!',
        ]);
    }

    // 🔹 Reset form
    private function resetForm()
    {
        $this->kurikulum_id = null;
        $this->nama = '';
        $this->kode = '';
        $this->deskripsi = '';
        $this->status = '';
        $this->resetErrorBag();
    }

    // 🔹 Render data
    public function render()
    {
        $query = Kurikulum::query();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                    ->orWhere('kode', 'like', "%{$this->search}%")
                    ->orWhere('deskripsi', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.data-kurikulum', [
            'kurikulums' => $query->orderBy('nama', 'asc')->paginate($this->perPage),
        ]);
    }
}
