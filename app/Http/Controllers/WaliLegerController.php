<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class WaliLegerController extends Controller
{
    /**
     * Generate PDF Leger Kelas
     */
    public function generateLeger(Request $request)
    {
        // Decode data dari query parameter
        $encodedData = $request->query('data');

        if (!$encodedData) {
            abort(400, 'Data tidak ditemukan');
        }

        $data = json_decode(base64_decode($encodedData), true);

        if (!$data) {
            abort(400, 'Data tidak valid');
        }

        // Load view untuk PDF
        $pdf = Pdf::loadView('livewire.wali.leger-kelas-pdf', $data);

        // Set paper ke landscape A4 atau A3 tergantung jumlah mata pelajaran
        $jumlahMapel = count($data['mata_pelajaran'] ?? []);

        if ($jumlahMapel > 10) {
            // Jika mata pelajaran banyak, gunakan A3 landscape
            $pdf->setPaper('a3', 'landscape');
        } else {
            // Jika mata pelajaran sedikit, gunakan A4 landscape
            $pdf->setPaper('a4', 'landscape');
        }

        // Set options untuk PDF
        $pdf->setOption('margin-top', 10);
        $pdf->setOption('margin-right', 10);
        $pdf->setOption('margin-bottom', 10);
        $pdf->setOption('margin-left', 10);

        // Generate nama file
        $namaFile = 'Leger_' .
            str_replace(' ', '_', $data['kelas'] ?? 'Kelas') . '_' .
            str_replace(' ', '_', $data['semester_nama'] ?? 'Semester') . '_' .
            str_replace(['/', ' '], '_', $data['tahun_ajaran'] ?? 'TA') . '.pdf';

        // Return PDF untuk di-download
        return $pdf->stream($namaFile);
    }
}
