<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class DataPendidik extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Properties untuk filter dan pencarian
    public $perPage = 10;
    public $search = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';

    // Properties untuk modal edit
    public $showEditModal = false;
    public $editingUserId = null;
    public $editStatus = '';
    public $editIsGuruAgama = '';
    public $editSpesialisasiAgama = '';

    // Listener untuk refresh (tetap sama)
    protected $listeners = ['refreshDataPendidik' => '$refresh'];

    // Reset pagination saat search berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Reset pagination saat perPage berubah
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    // Method untuk sorting
    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    // Method untuk membuka modal edit
    // Akan menggunakan Livewire-nya sendiri, jadi perlu dispatch event untuk buka modal Bootstrap
    public function edit($userId)
    {
        $user = User::find($userId);

        if ($user) {
            $this->editingUserId = $userId;
            $this->editStatus = $user->status ?? 'aktif';
            // Pastikan nilai adalah string '1' atau '0' untuk select
            $this->editIsGuruAgama = $user->is_guru_agama ? '1' : '0';
            $this->editSpesialisasiAgama = $user->spesialisasi_agama ?? '';
            $this->showEditModal = true;

            // Dispatch event untuk membuka modal Bootstrap
            $this->dispatch('show-edit-modal');
        }
    }

    // Method untuk update data
    public function update()
    {
        $this->validate([
            'editStatus' => 'required|in:aktif,nonaktif',
            'editIsGuruAgama' => 'required|in:0,1',
            // Validasi 'nullable' di sini lebih baik daripada di rules
            'editSpesialisasiAgama' => $this->editIsGuruAgama == '1' ? 'required|string|max:255' : 'nullable',
        ], [
            'editStatus.required' => 'Status harus dipilih',
            'editIsGuruAgama.required' => 'Pilihan guru agama harus dipilih',
            'editSpesialisasiAgama.required' => 'Mata pelajaran agama harus dipilih jika guru agama',
        ]);

        try {
            DB::beginTransaction();

            $user = User::find($this->editingUserId);

            if ($user) {
                $user->update([
                    'status' => $this->editStatus,
                    'is_guru_agama' => $this->editIsGuruAgama == '1',
                    'spesialisasi_agama' => $this->editIsGuruAgama == '1' ? $this->editSpesialisasiAgama : null,
                ]);

                DB::commit();

                session()->flash('success', 'Data tenaga pendidik berhasil diperbarui.');
                // Dispatch event untuk menutup modal Bootstrap
                $this->dispatch('hide-edit-modal');
                $this->closeEditModal(); // Reset properti Livewire
                $this->dispatch('refreshDataPendidik');
            }
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    // Method untuk menutup modal (reset properti Livewire)
    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['editingUserId', 'editStatus', 'editIsGuruAgama', 'editSpesialisasiAgama']);
    }

    // Method untuk mendapatkan data (tetap sama)
    public function getTenagaPendidikProperty()
    {
        // ... (Logika yang sama)
        return User::where('is_teacher', true)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $searchTerm = $this->search;

                    // Search by name (not encrypted)
                    $q->where('name', 'like', '%' . $searchTerm . '%');

                    // Search by email hash
                    if (filter_var($searchTerm, FILTER_VALIDATE_EMAIL)) {
                        $emailHash = hash('sha256', strtolower($searchTerm));
                        $q->orWhere('email_hash', $emailHash);
                    }

                    // Search by NIP hash
                    if (is_numeric($searchTerm)) {
                        $nipHash = hash('sha256', $searchTerm);
                        $q->orWhere('nip_hash', $nipHash);
                    }

                    // Search by spesialisasi agama hash
                    // Asumsi User model punya accessor/mutator untuk menangani enkripsi/dekripsi
                    // Jika tidak, Anda mungkin perlu melakukan pencarian langsung ke kolom plaintext/hash, seperti di bawah:
                    $agamaHash = hash('sha256', strtolower($searchTerm));
                    $q->orWhere('spesialisasi_agama_hash', $agamaHash);
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.admin.data-pendidik', [
            'tenagaPendidik' => $this->tenagaPendidik
        ]);
    }
}
