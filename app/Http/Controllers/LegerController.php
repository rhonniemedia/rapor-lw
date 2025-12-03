<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;

class LegerController extends Controller
{


    public function cetakLeger(Request $request)
    {
        $key = $request->query('key');
        $action = $request->query('action'); // 'download' atau 'stream'

        // 1. Ambil data dari Cache server
        $data = Cache::get($key);

        if (!$data) {
            return abort(404, 'Data kadaluarsa atau tidak ditemukan. Silakan refresh halaman leger.');
        }

        // 2. Load View PDF
        $pdf = Pdf::loadView('livewire.pdf.leger-kelas-pdf', $data);

        // 3. Set Ukuran Kertas (Landscape untuk Leger)
        $pdf->setPaper('a4', 'landscape');

        $namaFile = 'Leger_' . ($data['kelas'] ?? 'Kelas') . '.pdf';

        // 4. Return sesuai aksi
        if ($action === 'download') {
            return $pdf->download($namaFile);
        }

        return $pdf->stream($namaFile);
    }
}
