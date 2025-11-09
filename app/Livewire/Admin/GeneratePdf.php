<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class GeneratePdf extends Component
{
    public function render()
    {
        return view('livewire.admin.generate-pdf');
    }
}
