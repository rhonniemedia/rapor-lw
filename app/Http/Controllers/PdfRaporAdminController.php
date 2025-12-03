<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;

class PdfRaporAdminController extends Controller
{
    public function generatePdf(Request $request)
    {
        // 1. Ambil Parameter Key
        $key = $request->query('key');
        $selectedView = $request->query('view', 'cover');

        if (!$key) {
            return response('Parameter key tidak ditemukan.', 400);
        }

        // 2. Ambil Data dari Cache
        $data = Cache::get($key);

        if (!$data) {
            return response('Sesi cetak rapor telah berakhir (kadaluarsa). Silakan tutup tab ini, lalu klik tombol cetak ulang di halaman aplikasi.', 404);
        }

        // 3. Tentukan View Blade
        $viewPath = ($selectedView === 'content')
            ? 'livewire.admin.preview-pdf-rapor'
            : 'livewire.admin.preview-pdf-biodata';

        // 4. Render PDF
        $pdf = Pdf::loadView($viewPath, $data);

        // 5. Setup Kertas & Stream
        $pdf->setPaper('A4', 'portrait');

        $namaFile = 'Raport_' . preg_replace('/[^A-Za-z0-9\-]/', '_', $data['nama'] ?? 'Siswa') . '.pdf';

        return $pdf->stream($namaFile);
    }
}
