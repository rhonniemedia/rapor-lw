<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function generatePdf(Request $request)
    {
        // 1. Ambil data dari query string (parameter 'data' DAN 'view')
        $dataParam = $request->query('data');
        // Ambil parameter 'view'. Default ke 'cover' (biodata) jika tidak ada.
        $selectedView = $request->query('view', 'cover');

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

        // 4. Tentukan View yang akan di-Render
        $viewPath = '';
        if ($selectedView === 'content') {
            // Jika 'content' (Halaman Nilai/Rapor) dipilih
            $viewPath = 'livewire.admin.preview-pdf-rapor';
        } else {
            // Default: 'cover' (Halaman Sampul/Biodata)
            $viewPath = 'livewire.admin.preview-pdf-biodata';
        }

        // 5. Render PDF menggunakan Laravel-DomPDF
        $pdf = Pdf::loadView($viewPath, $data);

        // 6. Set paper size
        $pdf->setPaper('A4', 'portrait');

        // 7. Stream PDF ke browser
        return $pdf->stream('raport_' . ($data['nama'] ?? 'siswa') . '.pdf');
    }
}
