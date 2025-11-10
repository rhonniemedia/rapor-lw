<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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

        // 3. Pisahkan nilai berdasarkan kelompok
        $nilaiKelompokA = [];
        $nilaiKelompokB = [];

        if (isset($data['nilai']) && is_array($data['nilai'])) {
            foreach ($data['nilai'] as $nilai) {
                if ($nilai['kelompok'] === 'A. Kelompok Mata Pelajaran') {
                    $nilaiKelompokA[] = $nilai;
                } else {
                    $nilaiKelompokB[] = $nilai;
                }
            }
        }

        $data['nilai_kelompok_a'] = $nilaiKelompokA;
        $data['nilai_kelompok_b'] = $nilaiKelompokB;

        // 4. Render PDF menggunakan Laravel-DomPDF
        $pdf = Pdf::loadView('livewire.admin.preview-file-pdf', $data);

        // 5. Set paper size
        $pdf->setPaper('A4', 'portrait');

        // 6. Stream PDF ke browser
        return $pdf->stream('raport_' . $data['nama'] . '.pdf');
    }
}
