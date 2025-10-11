<?php

namespace App\Livewire;

use App\Models\TahunAjaran;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class DataTahunAjaran extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔹 Properti pencarian & pagination
    public $search = '';
    public $perPage = 10;

    // 🔹 Field form
    public $tahun_id;
    public $nama;
    public $tgl_mulai;
    public $tgl_selesai;
    public $status = 'aktif';

    public $isEdit = false;

    // 🔹 Event listener - DIPERBAIKI
    protected $listeners = ['deleteConfirmedTahunAjaran' => 'deleteConfirmedTahunAjaran'];

    // 🔹 Validasi dasar
    protected $baseRules = [
        'nama' => 'required|string|min:4|unique:tahun_ajarans,nama',
        'tgl_mulai' => 'required|date',
        'tgl_selesai' => 'required|date|after:tgl_mulai',
        'status' => 'required|in:aktif,arsip',
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

        if ($this->isEdit && $this->tahun_id) {
            $rules['nama'] = [
                'required',
                'string',
                'min:4',
                Rule::unique('tahun_ajarans', 'nama')->ignore($this->tahun_id, 'id'),
            ];
        }

        return $rules;
    }

    // 🔹 Buka modal tambah
    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->dispatch('openModalTahunAjaran');
    }

    // 🔹 Simpan data baru
    public function store()
    {
        $this->validate($this->getRules());

        TahunAjaran::create([
            'nama' => $this->nama,
            'tgl_mulai' => $this->tgl_mulai,
            'tgl_selesai' => $this->tgl_selesai,
            'status' => $this->status,
        ]);

        $this->dispatch('closeModalTahunAjaran');
        $this->resetForm();
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data tahun ajaran berhasil ditambahkan!',
        ]);
    }

    // 🔹 Edit data
    public function edit($id)
    {
        $tahun = TahunAjaran::findOrFail($id);

        $this->tahun_id = $tahun->id;
        $this->nama = $tahun->nama;
        $this->tgl_mulai = $tahun->tgl_mulai;
        $this->tgl_selesai = $tahun->tgl_selesai;
        $this->status = $tahun->status;

        $this->isEdit = true;
        $this->dispatch('openModalTahunAjaran');
    }

    // 🔹 Update data
    public function update()
    {
        $this->validate($this->getRules());

        $tahun = TahunAjaran::findOrFail($this->tahun_id);

        $tahun->update([
            'nama' => $this->nama,
            'tgl_mulai' => $this->tgl_mulai,
            'tgl_selesai' => $this->tgl_selesai,
            'status' => $this->status,
        ]);

        $this->dispatch('closeModalTahunAjaran');
        $this->resetForm();
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data tahun ajaran berhasil diperbarui!',
        ]);
    }

    // 🔹 Konfirmasi hapus
    public function confirmDeleteTahunAjaran($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Hapus Data?',
            'text' => 'Data tahun ajaran ini akan dihapus secara permanen!',
            'nextEvent' => 'deleteConfirmedTahunAjaran', // DIPERBAIKI
            'id' => $id,
        ]);
    }

    // 🔹 Hapus data - NAMA METHOD SUDAH BENAR
    public function deleteConfirmedTahunAjaran($id)
    {
        TahunAjaran::findOrFail($id)->delete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data tahun ajaran berhasil dihapus!',
        ]);
    }

    // 🔹 Reset form input
    private function resetForm()
    {
        $this->tahun_id = null;
        $this->nama = '';
        $this->tgl_mulai = '';
        $this->tgl_selesai = '';
        $this->status = '';
        $this->resetErrorBag();
    }

    // 🔹 Render tabel data
    public function render()
    {
        $query = TahunAjaran::query();

        // Filter pencarian jika ada
        if (!empty($this->search)) {
            $query->where('nama', 'like', '%' . $this->search . '%');
        }

        return view('livewire.data-tahun-ajaran', [
            'tahunAjarans' => $query->orderBy('nama', 'asc')->paginate($this->perPage),
        ]);
    }
}
