<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;

class PdfController extends Controller
{
    public function generateDummyPdf(Request $request)
    {
        // 1. Ambil data dari query string (parameter 'data')
        $dataParam = $request->query('data');

        if (empty($dataParam)) {
            return response('Data PDF tidak ditemukan.', 400);
        }

        // 2. Dekode data: Base64 Decode, lalu JSON Decode
        try {
            $data = json_decode(base64_decode($dataParam), true);
        } catch (\Exception $e) {
            return response('Error saat mendekode data: ' . $e->getMessage(), 500);
        }

        // 3. Render PDF menggunakan Laravel-DomPDF
        $pdf = Pdf::loadView('Livewire.admin.preview-file-pdf', $data);

        // 4. Set paper size
        $pdf->setPaper('A4', 'portrait');

        // 5. Stream PDF ke browser
        return $pdf->stream('dummy_raport.pdf');
    }
}
