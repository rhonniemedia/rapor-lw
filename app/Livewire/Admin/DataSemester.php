<?php

namespace App\Livewire\Admin;

use App\Models\Semester;
use Livewire\Component;
use Livewire\WithPagination;

class DataSemester extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔹 Pencarian & pagination
    public $search = '';
    public $perPage = 10;

    // 🔹 Form field
    public $semester_id;
    public $nama;
    public $urutan;

    public $isEdit = false;

    // 🔹 Aturan validasi dasar
    protected $baseRules = [
        'nama' => 'required|string|min:3|unique:semesters,nama',
        'urutan' => 'nullable|integer|min:1|max:2',
    ];

    // 🔹 Listener event konfirmasi hapus
    protected $listeners = ['deleteConfirmedSemester' => 'deleteConfirmedSemester'];

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
        if ($this->isEdit && $this->semester_id) {
            $rules['nama'] = 'required|string|min:3|unique:semesters,nama,' . $this->semester_id . ',id';
        }
        return $rules;
    }

    // 🔹 Buka modal tambah
    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->dispatch('openModalSemester');
    }

    // 🔹 Simpan data baru
    public function store()
    {
        $this->validate($this->getRules());

        Semester::create([
            'nama' => $this->nama,
            'urutan' => $this->urutan,
        ]);

        $this->resetForm();
        $this->dispatch('closeModalSemester');
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data semester berhasil ditambahkan!',
        ]);
    }

    // 🔹 Edit data
    public function edit($id)
    {
        $semester = Semester::findOrFail($id);

        $this->semester_id = $semester->id;
        $this->nama = $semester->nama;
        $this->urutan = $semester->urutan;

        $this->isEdit = true;
        $this->dispatch('openModalSemester');
    }

    // 🔹 Update data
    public function update()
    {
        $this->validate($this->getRules());

        $semester = Semester::findOrFail($this->semester_id);
        $semester->update([
            'nama' => $this->nama,
            'urutan' => $this->urutan,
        ]);

        $this->resetForm();
        $this->dispatch('closeModalSemester');
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data semester berhasil diperbarui!',
        ]);
    }

    // 🔹 Konfirmasi hapus
    public function confirmDeleteSemester($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Hapus Data?',
            'text' => 'Data semester ini akan dihapus permanen!',
            'nextEvent' => 'deleteConfirmedSemester',
            'id' => $id,
        ]);
    }

    // 🔹 Hapus data
    public function deleteConfirmedSemester($id)
    {
        Semester::findOrFail($id)->delete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data semester telah dihapus!',
        ]);
    }

    // 🔹 Reset form
    private function resetForm()
    {
        $this->semester_id = null;
        $this->nama = '';
        $this->urutan = null;
        $this->resetErrorBag();
    }

    // 🔹 Render data
    public function render()
    {
        $query = Semester::query();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                    ->orWhere('urutan', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.admin.data-semester', [
            'semesters' => $query->orderBy('urutan', 'asc')->paginate($this->perPage),
        ]);
    }
}
