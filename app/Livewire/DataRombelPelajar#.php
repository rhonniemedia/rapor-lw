<?php

namespace App\Livewire;

use App\Models\Rombel; // Asumsi model Rombel ada
use App\Models\Pelajar; // Asumsi model Pelajar ada
use App\Models\RombelPelajar; // Asumsi model RombelPelajar ada

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class DataRombelPelajar extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔹 Properti utama: ID Rombel yang sedang dilihat
    public $rombelId;
    public $rombel;

    // 🔹 Properti untuk Tambah Pelajar
    public $pelajar_id;

    // 🔹 Properti pencarian & pagination
    public $search = '';
    public $perPage = 10;

    // Properti untuk daftar pelajar yang tersedia
    public $availablePelajars = [];

    // 🔹 Event listener
    protected $listeners = ['deleteConfirmedRombelPelajar' => 'deleteConfirmedRombelPelajar'];

    public function mount($rombelId)
    {
        $this->rombelId = $rombelId;
        $this->rombel = Rombel::findOrFail($rombelId);
    }

    // 🔹 Reset pagination saat search berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // 🔹 Muat daftar pelajar yang belum masuk ke rombel mana pun (atau belum masuk rombel ini)
    public function loadAvailablePelajars()
    {
        // Mendapatkan ID pelajar yang sudah terdaftar di Rombel ini atau Rombel lain
        $registeredPelajarsIds = RombelPelajar::pluck('pelajar_id')->toArray();

        // Ambil pelajar yang TIDAK termasuk dalam daftar registeredPelajarsIds
        $this->availablePelajars = Pelajar::whereNotIn('id', $registeredPelajarsIds)
            ->orderBy('nama_lengkap', 'asc')
            ->get();
    }

    // 🔹 Buka modal tambah
    public function create()
    {
        $this->resetForm();
        $this->loadAvailablePelajars(); // Muat daftar pelajar yang tersedia
        $this->dispatch('openModalRombelPelajar');
    }

    // 🔹 Simpan data baru
    public function store()
    {
        // Validasi: pelajar_id wajib dan harus ada di tabel 'pelajars'
        $this->validate([
            'pelajar_id' => [
                'required',
                'uuid',
                'exists:pelajars,id',
                // Pastikan pelajar belum terdaftar di rombel mana pun (atau rombel ini)
                Rule::unique('rombel_pelajars')->where(function ($query) {
                    return $query->where('pelajar_id', $this->pelajar_id);
                })
            ],
        ], [
            'pelajar_id.unique' => 'Pelajar ini sudah terdaftar di rombel lain atau rombel ini.',
        ]);

        RombelPelajar::create([
            'pelajar_id' => $this->pelajar_id,
            'rombel_id' => $this->rombelId,
        ]);

        $this->dispatch('closeModalRombelPelajar');
        $this->resetForm();
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Pelajar berhasil ditambahkan ke rombel!',
        ]);
    }

    // 🔹 Konfirmasi hapus
    public function confirmDeleteRombelPelajar($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Hapus Pelajar dari Rombel?',
            'text' => 'Pelajar ini akan dikeluarkan dari rombongan belajar!',
            'nextEvent' => 'deleteConfirmedRombelPelajar',
            'id' => $id,
        ]);
    }

    // 🔹 Hapus data
    public function deleteConfirmedRombelPelajar($id)
    {
        RombelPelajar::findOrFail($id)->delete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Pelajar berhasil dikeluarkan dari rombel!',
        ]);
    }

    // 🔹 Reset form input
    private function resetForm()
    {
        $this->pelajar_id = '';
        $this->resetErrorBag();
    }

    // 🔹 Render tabel data (Anggota Rombel)
    public function render()
    {
        $query = RombelPelajar::where('rombel_id', $this->rombelId)
            ->with(['pelajar']); // Eager load data Pelajar

        // Filter pencarian berdasarkan nama/NISN Pelajar
        if (!empty($this->search)) {
            $query->whereHas('pelajar', function ($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                    ->orWhere('nisn', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.data-rombel-pelajar', [
            'rombelPelajars' => $query->orderBy('created_at', 'asc')->paginate($this->perPage),
        ]);
    }
}
