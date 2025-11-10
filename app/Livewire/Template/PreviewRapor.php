<?php

namespace App\Livewire\Template;

use Livewire\Component;

class PreviewRapor extends Component
{
    public function render()
    {
        // Data dummy
        $murid = [
            'nama' => 'Ahmad Fikri',
            'nisn' => '0058745623',
            'kelas' => 'X RPL 1',
            'semester' => '1',
            'tahun' => '2025/2026',
            'kokurikuler' => 'Aktif dalam kegiatan Pramuka dan Literasi Sekolah.',
            'kehadiran' => [
                'sakit' => 2,
                'izin' => 1,
                'alpa' => 0,
            ],
            'catatan' => 'Siswa menunjukkan perkembangan baik dan disiplin.',
            'tanggapan_ortu' => 'Kami sangat senang dengan hasil belajar anak kami.',
            'wali_kelas' => 'Siti Nurhaliza, S.Pd',
            'nilai' => [
                ['mapel' => 'Pendidikan Agama dan Budi Pekerti', 'nilai' => 88, 'predikat' => 'A'],
                ['mapel' => 'Pendidikan Pancasila', 'nilai' => 84, 'predikat' => 'B'],
                ['mapel' => 'Bahasa Indonesia', 'nilai' => 90, 'predikat' => 'A'],
                ['mapel' => 'Matematika', 'nilai' => 79, 'predikat' => 'B'],
                ['mapel' => 'Bahasa Inggris', 'nilai' => 85, 'predikat' => 'B'],
                ['mapel' => 'Pemrograman Dasar', 'nilai' => 92, 'predikat' => 'A'],
            ],
            'ekskul' => [
                ['nama' => 'Pramuka', 'keterangan' => 'Baik'],
                ['nama' => 'Futsal', 'keterangan' => 'Sangat Baik'],
            ],
        ];

        return view('livewire.template.preview-rapor', compact('murid'));
    }
}
