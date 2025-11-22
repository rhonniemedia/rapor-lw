<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AdminLegerController extends Controller
{
    /**
     * Generate PDF Leger Kelas untuk Admin
     */
    public function generateLegerPdf(Request $request)
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

        // Ensure Carbon instance for tanggal_rapor
        if (isset($data['tanggal_rapor'])) {
            Carbon::setLocale('id');
        }

        // Load view untuk PDF (menggunakan view leger yang sudah ada)
        $pdf = Pdf::loadView('livewire.admin.preview-leger-pdf', $data);

        // Set paper size berdasarkan jumlah mata pelajaran
        $jumlahMapel = count($data['mata_pelajaran'] ?? []);

        if ($jumlahMapel > 15) {
            // Jika mata pelajaran sangat banyak, gunakan A3 landscape
            $pdf->setPaper('a3', 'landscape');
        } elseif ($jumlahMapel > 10) {
            // Jika mata pelajaran sedang, gunakan A4 landscape dengan margin lebih kecil
            $pdf->setPaper('a4', 'landscape');
            $pdf->setOption('margin-top', 8);
            $pdf->setOption('margin-right', 8);
            $pdf->setOption('margin-bottom', 8);
            $pdf->setOption('margin-left', 8);
        } else {
            // Jika mata pelajaran sedikit, gunakan A4 landscape normal
            $pdf->setPaper('a4', 'landscape');
            $pdf->setOption('margin-top', 10);
            $pdf->setOption('margin-right', 10);
            $pdf->setOption('margin-bottom', 10);
            $pdf->setOption('margin-left', 10);
        }

        // Set options untuk PDF
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('defaultFont', 'Arial');
        $pdf->setOption('dpi', 96);

        // Generate nama file
        $kelas = str_replace(['/', '\\', ' '], '_', $data['kelas'] ?? 'Kelas');
        $semester = str_replace(' ', '_', $data['semester_nama'] ?? 'Semester');
        $tahunAjaran = str_replace(['/', ' '], '_', $data['tahun_ajaran'] ?? 'TA');
        $timestamp = date('Ymd_His');

        $namaFile = 'Leger_' . $kelas . '_' . $semester . '_' . $tahunAjaran . '_' . $timestamp . '.pdf';

        // Return PDF untuk stream (preview) atau download
        // Jika ada parameter download=true, maka download. Jika tidak, maka stream (preview)
        if ($request->has('download') && $request->download == 'true') {
            return $pdf->download($namaFile);
        }

        return $pdf->stream($namaFile);
    }

    /**
     * Download Leger dalam format Excel
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadLegerExcel(Request $request)
    {
        // Decode data dari query parameter
        $encodedData = $request->query('data');

        if (!$encodedData) {
            return back()->with('error', 'Data tidak ditemukan');
        }

        $data = json_decode(base64_decode($encodedData), true);

        if (!$data) {
            return back()->with('error', 'Data tidak valid');
        }

        // TODO: Implementasi export ke Excel menggunakan Laravel Excel atau PHPSpreadsheet
        // Untuk sementara, redirect dengan pesan info
        return back()->with('info', 'Fitur export Excel sedang dalam pengembangan');
    }

    /**
     * Preview Leger (method untuk menampilkan halaman preview)
     */
    public function previewLeger()
    {
        return view('admin.preview-leger');
    }
}
