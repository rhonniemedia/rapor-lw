<?php

namespace App\Livewire;

use App\Models\MataPelajaranKelompok; // Asumsi model telah dibuat
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class DataKelompokMapel extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔹 Properti pencarian & pagination
    public $search = '';
    public $perPage = 10;

    // 🔹 Field form
    public $kelompok_id;
    public $nama;
    public $kode;

    public $isEdit = false;

    // 🔹 Event listener
    protected $listeners = ['deleteConfirmedKelompok' => 'deleteConfirmedKelompok'];

    // 🔹 Validasi dasar
    protected $baseRules = [
        'nama' => 'required|string|min:2',
        'kode' => 'required|string|min:1|max:10|unique:mata_pelajaran_kelompoks,kode',
    ];

    // 🔹 Reset pagination saat search berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // 🔹 Reset pagination saat perPage berubah
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    // 🔹 Rules dinamis (saat edit)
    public function getRules()
    {
        $rules = $this->baseRules;

        if ($this->isEdit && $this->kelompok_id) {
            // Perbarui rule 'kode' untuk mengabaikan kode saat ini
            $rules['kode'] = [
                'required',
                'string',
                'min:1',
                'max:10',
                Rule::unique('mata_pelajaran_kelompoks', 'kode')->ignore($this->kelompok_id, 'id'),
            ];
        }

        return $rules;
    }

    // 🔹 Buka modal tambah
    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->dispatch('openModalKelompok');
    }

    // 🔹 Simpan data baru
    public function store()
    {
        $this->validate($this->getRules());

        MataPelajaranKelompok::create([
            'nama' => $this->nama,
            'kode' => $this->kode,
        ]);

        $this->dispatch('closeModalKelompok');
        $this->resetForm();
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data kelompok mata pelajaran berhasil ditambahkan!',
        ]);
    }

    // 🔹 Edit data
    public function edit($id)
    {
        $kelompok = MataPelajaranKelompok::findOrFail($id);

        $this->kelompok_id = $kelompok->id;
        $this->nama = $kelompok->nama;
        $this->kode = $kelompok->kode;

        $this->isEdit = true;
        $this->dispatch('openModalKelompok');
    }

    // 🔹 Update data
    public function update()
    {
        $this->validate($this->getRules());

        $kelompok = MataPelajaranKelompok::findOrFail($this->kelompok_id);

        $kelompok->update([
            'nama' => $this->nama,
            'kode' => $this->kode,
        ]);

        $this->dispatch('closeModalKelompok');
        $this->resetForm();
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data kelompok mata pelajaran berhasil diperbarui!',
        ]);
    }

    // 🔹 Konfirmasi hapus
    public function confirmDeleteKelompok($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Hapus Data?',
            'text' => 'Data kelompok mata pelajaran ini akan dihapus secara permanen!',
            'nextEvent' => 'deleteConfirmedKelompok',
            'id' => $id,
        ]);
    }

    // 🔹 Hapus data
    public function deleteConfirmedKelompok($id)
    {
        MataPelajaranKelompok::findOrFail($id)->delete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data kelompok mata pelajaran berhasil dihapus!',
        ]);
    }

    // 🔹 Reset form input
    private function resetForm()
    {
        $this->kelompok_id = null;
        $this->nama = '';
        $this->kode = '';
        $this->resetErrorBag();
    }

    // 🔹 Render tabel data
    public function render()
    {
        $query = MataPelajaranKelompok::query();

        // Filter pencarian jika ada
        if (!empty($this->search)) {
            $query->where('nama', 'like', '%' . $this->search . '%')
                ->orWhere('kode', 'like', '%' . $this->search . '%');
        }

        return view('livewire.data-kelompok-mapel', [
            'kelompoks' => $query->orderBy('kode', 'asc')->paginate($this->perPage),
        ]);
    }
}
