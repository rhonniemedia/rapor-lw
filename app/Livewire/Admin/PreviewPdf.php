<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class PreviewPdf extends Component
{
    public function render()
    {
        return view('livewire.admin.preview-pdf');
    }
}
