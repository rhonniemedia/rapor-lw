<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Kurikulum;
use App\Models\TahunAjaranSemester;

class InfoTahunAjaran extends Component
{
    public $tahunAjaran;
    public $kurikulum;

    public function mount()
    {
        $this->tahunAjaran = TahunAjaranSemester::with('tahunAjaran')
            ->where('status', 'aktif')
            ->first();

        $this->kurikulum = Kurikulum::first();
    }

    public function render()
    {
        return view('livewire.admin.info-tahun-ajaran');
    }
}
