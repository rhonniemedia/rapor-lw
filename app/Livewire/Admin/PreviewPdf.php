<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class PreviewPdf extends Component
{
    // Menggunakan helper method untuk mengambil data dummy agar kode lebih bersih
    private function getDummyData()
    {
        // Definisi data dummy Anda yang sebelumnya ada di render()
        return [
            'murid' => [
                'nama' => 'Ahmad Fikri',
                'nisn' => '0058745623',
                'kelas' => 'X RPL 1',
                'semester' => '1',
                'tahun' => '2025/2026',
                'wali_kelas' => 'Siti Nurhaliza, S.Pd',
                'kepala_sekolah' => 'Drs. H. Bambang Sudarno, M.M',
                'tanggapan_ortu' => 'Kami sangat senang dengan hasil belajar anak kami. Terima kasih kepada seluruh guru yang telah membimbing dengan sabar.',
            ],
            'nilai' => [
                'Kelompok A (Umum)' => [
                    ['mapel' => 'Pendidikan Agama dan Budi Pekerti', 'nilai' => 88, 'predikat' => 'A'],
                    ['mapel' => 'Pendidikan Pancasila', 'nilai' => 84, 'predikat' => 'B'],
                    ['mapel' => 'Bahasa Indonesia', 'nilai' => 90, 'predikat' => 'A'],
                    ['mapel' => 'Matematika', 'nilai' => 79, 'predikat' => 'B'],
                    ['mapel' => 'Bahasa Inggris', 'nilai' => 85, 'predikat' => 'B'],
                ],
                'Kelompok B (Umum)' => [
                    ['mapel' => 'Seni Budaya', 'nilai' => 88, 'predikat' => 'A'],
                    ['mapel' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan', 'nilai' => 86, 'predikat' => 'B'],
                ],
                'Kelompok C (Peminatan Kejuruan)' => [
                    ['mapel' => 'Pemrograman Dasar', 'nilai' => 92, 'predikat' => 'A'],
                    ['mapel' => 'Sistem Komputer', 'nilai' => 87, 'predikat' => 'B'],
                    ['mapel' => 'Desain Multimedia', 'nilai' => 90, 'predikat' => 'A'],
                ],
            ],
            'kokurikuler' => 'Aktif mengikuti kegiatan pramuka, literasi sekolah, dan lomba desain poster digital.',
            'ekstrakurikuler' => [
                ['nama' => 'Pramuka', 'keterangan' => 'Baik'],
                ['nama' => 'Futsal', 'keterangan' => 'Sangat Baik'],
            ],
            'kehadiran' => ['sakit' => 2, 'izin' => 1, 'alpa' => 0],
            'catatan_wali' => 'Siswa memiliki semangat belajar tinggi, aktif bertanya, dan disiplin. Perlu ditingkatkan kerapian dalam mengerjakan tugas.',
        ];
    }

    // Computed Property: Menghasilkan URL untuk iframe
    public function getPdfUrlProperty()
    {
        $data = $this->getDummyData();

        // 1. Serialize data ke JSON
        $json_data = json_encode($data);

        // 2. Encode ke Base64 agar aman ditransfer melalui URL
        $base64_data = base64_encode($json_data);

        // 3. Buat URL dengan data sebagai parameter query
        return route('report.dummy', ['data' => $base64_data]);
    }

    public function render()
    {
        return view('livewire.admin.preview-pdf');
    }
}
