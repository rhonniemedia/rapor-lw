<?php

namespace App\Livewire\Wali;

use App\Models\Rombel;
use App\Models\RombelPelajar;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class KelasBinaan extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Properti untuk rombel
    public $rombel;
    public $rombelId;

    // Properti pencarian & pagination
    public $search = '';
    public $perPage = 10;

    public function mount()
    {
        // Ambil user yang login
        $user = Auth::user();

        // Cari rombel yang diampu oleh wali kelas ini berdasarkan slug
        $this->rombel = Rombel::where('wali_kelas_slug', $user->slug)
            ->with(['jurusan', 'tahunAjaranKurikulum.tahunAjaran', 'tahunAjaranKurikulum.kurikulum'])
            ->first();

        // Jika tidak ada rombel yang diampu, redirect atau abort
        if (!$this->rombel) {
            abort(403, 'Anda belum memiliki kelas binaan.');
        }

        $this->rombelId = $this->rombel->id;
    }

    // Reset pagination saat search berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Method untuk mendapatkan statistik quick info
    public function getStatsProperty()
    {
        $totalSiswa = RombelPelajar::where('rombel_id', $this->rombelId)
            ->count();

        return [
            'total_siswa' => $totalSiswa,
        ];
    }

    public function render()
    {
        // Query untuk pelajar di rombel
        $query = RombelPelajar::where('rombel_id', $this->rombelId)
            ->with(['pelajar']);

        // Pencarian
        if (!empty($this->search)) {
            $query->whereHas('pelajar', function ($q) {
                $q->where('nama_lengkap', 'like', '%' . $this->search . '%')
                    ->orWhere('nisn', 'like', '%' . $this->search . '%')
                    ->orWhere('nomor_induk', 'like', '%' . $this->search . '%');
            });
        }

        $pelajars = $query->orderBy('created_at', 'asc')->paginate($this->perPage);

        return view('livewire.wali.kelas-binaan', [
            'pelajars' => $pelajars,
            'stats' => $this->stats,
        ]);
    }
}
