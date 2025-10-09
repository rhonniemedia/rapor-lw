<?php

namespace App\Livewire;

use App\Models\MataPelajaran;
use Livewire\Component;
use Livewire\WithPagination;

class DataMataPelajaran extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔹 Pencarian & pagination
    public $search = '';
    public $perPage = 10;

    // 🔹 Form field
    public $mapel_id;
    public $nama;
    public $kode;
    public $status = 'aktif';

    public $isEdit = false;

    protected $baseRules = [
        'nama' => 'required|min:3',
        'kode' => 'required|unique:mata_pelajarans,kode|max:10',
        'status' => 'required|in:aktif,arsip',
    ];

    // 🔹 Event listener
    protected $listeners = [
        'deleteConfirmed',
        'createMapel' => 'create',
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

        if ($this->isEdit && $this->mapel_id) {
            $rules['kode'] = 'required|unique:mata_pelajarans,kode,' . $this->mapel_id . ',id|max:10';
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

        MataPelajaran::create([
            'nama' => $this->nama,
            'kode' => strtoupper($this->kode),
            'status' => $this->status,
        ]);

        $this->resetForm();
        $this->dispatch('closeModal');
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data mata pelajaran berhasil ditambahkan!',
        ]);
    }

    // 🔹 Edit data
    public function edit($id)
    {
        $mapel = MataPelajaran::findOrFail($id);

        $this->mapel_id = $mapel->id;
        $this->nama = $mapel->nama;
        $this->kode = $mapel->kode;
        $this->status = $mapel->status;

        $this->isEdit = true;
        $this->dispatch('openModal');
    }

    // 🔹 Update data
    public function update()
    {
        $this->validate($this->getRules());

        $mapel = MataPelajaran::findOrFail($this->mapel_id);

        $mapel->update([
            'nama' => $this->nama,
            'kode' => strtoupper($this->kode),
            'status' => $this->status,
        ]);

        $this->resetForm();
        $this->dispatch('closeModal');
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data mata pelajaran berhasil diperbarui!',
        ]);
    }

    // 🔹 Konfirmasi hapus
    public function confirmDelete($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Hapus Data?',
            'text' => 'Data mata pelajaran ini akan dihapus secara permanen!',
            'nextEvent' => 'deleteConfirmed',
            'id' => $id,
        ]);
    }

    // 🔹 Hapus data
    public function deleteConfirmed($id)
    {
        MataPelajaran::findOrFail($id)->delete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data mata pelajaran berhasil dihapus!',
        ]);
    }

    // 🔹 Reset form
    private function resetForm()
    {
        $this->mapel_id = null;
        $this->nama = '';
        $this->kode = '';
        $this->status = '';
        $this->resetErrorBag();
    }

    // 🔹 Render data
    public function render()
    {
        $query = MataPelajaran::query();

        // Filter pencarian (jika ada)
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('kode', 'like', '%' . $this->search . '%')
                    ->orWhere('status', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.data-mata-pelajaran', [
            'mata_pelajarans' => $query->orderBy('nama', 'asc')->paginate($this->perPage)
        ]);
    }
}
