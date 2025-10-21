<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class DataPendidik extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap'; // agar tampilan pagination pakai Bootstrap

    public $search = ''; // opsional: untuk pencarian nama/email

    public function updatingSearch()
    {
        $this->resetPage(); // reset halaman saat pencarian berubah
    }

    public function render()
    {
        $tendiks = User::query()
            ->where('status', 'aktif')
            ->where('is_teacher', true)
            ->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('nip', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.data-pendidik', [
            'tendiks' => $tendiks,
        ]);
    }
}
