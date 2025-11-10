<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class PreviewPdf extends Component
{
    // Filter properties
    public $tahunAjaranId = 1;
    public $semesterId = 1;
    public $rombonganBelajarId = 1;

    // Navigation properties
    public $currentIndex = 0;
    public $totalStudents = 5;

    // Data properties
    public $students = [];
    public $currentStudent;
    public $pdfUrl;

    public function mount()
    {
        // Load dummy data
        $this->loadDummyData();

        // Set first student
        $this->currentIndex = 0;
        $this->loadCurrentStudent();
    }

    public function loadDummyData()
    {
        $this->students = [
            [
                'id' => 1,
                'nis' => '123456',
                'nisn' => '0058745623',
                'nama' => 'AHMAD FIKRI',
                'kelas' => 'X RPL 1',
                'fase' => 'E',
                'nilai' => [
                    ['no' => 1, 'mapel' => 'Pendidikan Agama dan Budi Pekerti', 'kelompok' => 'A. Kelompok Mata Pelajaran', 'nilai' => 88, 'capaian' => 'Sangat baik dalam memahami nilai-nilai keagamaan dan moral.'],
                    ['no' => 2, 'mapel' => 'Bahasa Indonesia', 'kelompok' => 'A. Kelompok Mata Pelajaran', 'nilai' => 85, 'capaian' => 'Mampu menulis dan berbicara dengan baik serta aktif berdiskusi.'],
                    ['no' => 3, 'mapel' => 'Matematika', 'kelompok' => 'A. Kelompok Mata Pelajaran', 'nilai' => 79, 'capaian' => 'Perlu meningkatkan ketelitian dalam berhitung dan memahami konsep.'],
                    ['no' => 1, 'mapel' => 'Pemrograman Dasar', 'kelompok' => 'B. Mata Pelajaran Kejuruan', 'nilai' => 92, 'capaian' => 'Mampu memahami logika pemrograman dan membuat program sederhana.'],
                    ['no' => 2, 'mapel' => 'Sistem Komputer', 'kelompok' => 'B. Mata Pelajaran Kejuruan', 'nilai' => 87, 'capaian' => 'Menunjukkan pemahaman baik dalam konsep perangkat keras dan lunak.'],
                ],
                'kokurikuler' => 'Aktif mengikuti kegiatan pramuka dan literasi sekolah.',
                'ekstrakurikuler' => [
                    ['nama' => 'Pramuka', 'keterangan' => 'Baik'],
                    ['nama' => 'Futsal', 'keterangan' => 'Sangat Baik'],
                ],
                'ketidakhadiran' => ['sakit' => 2, 'izin' => 3, 'tanpa_keterangan' => 0],
                'catatan_wali' => 'Siswa menunjukkan kedisiplinan yang baik dan aktif dalam kegiatan kelas.',
                'tanggapan_ortu' => 'Kami bangga dengan hasil belajar anak kami, semoga terus berkembang dan menjadi pribadi yang lebih baik.',
            ],
            [
                'id' => 2,
                'nis' => '123457',
                'nisn' => '0058745624',
                'nama' => 'SITI AMINAH',
                'kelas' => 'X RPL 1',
                'fase' => 'E',
                'nilai' => [
                    ['no' => 1, 'mapel' => 'Pendidikan Agama dan Budi Pekerti', 'kelompok' => 'A. Kelompok Mata Pelajaran', 'nilai' => 90, 'capaian' => 'Sangat memahami dan mengamalkan nilai-nilai agama dalam kehidupan sehari-hari.'],
                    ['no' => 2, 'mapel' => 'Bahasa Indonesia', 'kelompok' => 'A. Kelompok Mata Pelajaran', 'nilai' => 88, 'capaian' => 'Memiliki kemampuan menulis dan berbicara yang sangat baik.'],
                    ['no' => 3, 'mapel' => 'Matematika', 'kelompok' => 'A. Kelompok Mata Pelajaran', 'nilai' => 85, 'capaian' => 'Menunjukkan pemahaman yang baik dalam konsep matematika.'],
                    ['no' => 1, 'mapel' => 'Pemrograman Dasar', 'kelompok' => 'B. Mata Pelajaran Kejuruan', 'nilai' => 89, 'capaian' => 'Mampu membuat program sederhana dengan baik.'],
                    ['no' => 2, 'mapel' => 'Sistem Komputer', 'kelompok' => 'B. Mata Pelajaran Kejuruan', 'nilai' => 91, 'capaian' => 'Sangat memahami arsitektur dan komponen komputer.'],
                ],
                'kokurikuler' => 'Aktif dalam kegiatan literasi dan mengikuti program kewirausahaan.',
                'ekstrakurikuler' => [
                    ['nama' => 'Pramuka', 'keterangan' => 'Sangat Baik'],
                    ['nama' => 'English Club', 'keterangan' => 'Baik'],
                ],
                'ketidakhadiran' => ['sakit' => 1, 'izin' => 2, 'tanpa_keterangan' => 0],
                'catatan_wali' => 'Siswa sangat disiplin, rajin, dan menjadi teladan bagi teman-temannya.',
                'tanggapan_ortu' => 'Terima kasih atas bimbingan guru, kami akan terus mendukung perkembangan anak kami.',
            ],
            [
                'id' => 3,
                'nis' => '123458',
                'nisn' => '0058745625',
                'nama' => 'BUDI HARTONO',
                'kelas' => 'X RPL 1',
                'fase' => 'E',
                'nilai' => [
                    ['no' => 1, 'mapel' => 'Pendidikan Agama dan Budi Pekerti', 'kelompok' => 'A. Kelompok Mata Pelajaran', 'nilai' => 82, 'capaian' => 'Memahami nilai-nilai keagamaan dengan baik.'],
                    ['no' => 2, 'mapel' => 'Bahasa Indonesia', 'kelompok' => 'A. Kelompok Mata Pelajaran', 'nilai' => 80, 'capaian' => 'Cukup baik dalam berkomunikasi lisan maupun tulisan.'],
                    ['no' => 3, 'mapel' => 'Matematika', 'kelompok' => 'A. Kelompok Mata Pelajaran', 'nilai' => 78, 'capaian' => 'Perlu lebih banyak latihan dalam menyelesaikan soal matematika.'],
                    ['no' => 1, 'mapel' => 'Pemrograman Dasar', 'kelompok' => 'B. Mata Pelajaran Kejuruan', 'nilai' => 85, 'capaian' => 'Menunjukkan minat yang baik dalam pemrograman.'],
                    ['no' => 2, 'mapel' => 'Sistem Komputer', 'kelompok' => 'B. Mata Pelajaran Kejuruan', 'nilai' => 83, 'capaian' => 'Memahami konsep dasar sistem komputer.'],
                ],
                'kokurikuler' => 'Mengikuti kegiatan pramuka dengan cukup baik.',
                'ekstrakurikuler' => [
                    ['nama' => 'Futsal', 'keterangan' => 'Sangat Baik'],
                    ['nama' => 'Pramuka', 'keterangan' => 'Baik'],
                ],
                'ketidakhadiran' => ['sakit' => 3, 'izin' => 4, 'tanpa_keterangan' => 1],
                'catatan_wali' => 'Siswa perlu meningkatkan kedisiplinan dan kehadiran di kelas.',
                'tanggapan_ortu' => 'Kami akan lebih memperhatikan kedisiplinan anak di rumah.',
            ],
            [
                'id' => 4,
                'nis' => '123459',
                'nisn' => '0058745626',
                'nama' => 'DEWI LESTARI',
                'kelas' => 'X RPL 1',
                'fase' => 'E',
                'nilai' => [
                    ['no' => 1, 'mapel' => 'Pendidikan Agama dan Budi Pekerti', 'kelompok' => 'A. Kelompok Mata Pelajaran', 'nilai' => 91, 'capaian' => 'Sangat baik dan menjadi teladan dalam mengamalkan nilai agama.'],
                    ['no' => 2, 'mapel' => 'Bahasa Indonesia', 'kelompok' => 'A. Kelompok Mata Pelajaran', 'nilai' => 89, 'capaian' => 'Memiliki kemampuan berbahasa yang sangat baik dan kreatif.'],
                    ['no' => 3, 'mapel' => 'Matematika', 'kelompok' => 'A. Kelompok Mata Pelajaran', 'nilai' => 87, 'capaian' => 'Menunjukkan kemampuan analisis matematika yang baik.'],
                    ['no' => 1, 'mapel' => 'Pemrograman Dasar', 'kelompok' => 'B. Mata Pelajaran Kejuruan', 'nilai' => 90, 'capaian' => 'Sangat kreatif dalam membuat program dan menyelesaikan masalah.'],
                    ['no' => 2, 'mapel' => 'Sistem Komputer', 'kelompok' => 'B. Mata Pelajaran Kejuruan', 'nilai' => 88, 'capaian' => 'Memahami konsep sistem komputer dengan sangat baik.'],
                ],
                'kokurikuler' => 'Aktif dan antusias dalam semua kegiatan kokurikuler.',
                'ekstrakurikuler' => [
                    ['nama' => 'English Club', 'keterangan' => 'Sangat Baik'],
                    ['nama' => 'Pramuka', 'keterangan' => 'Baik'],
                ],
                'ketidakhadiran' => ['sakit' => 1, 'izin' => 1, 'tanpa_keterangan' => 0],
                'catatan_wali' => 'Siswa sangat berprestasi, disiplin, dan menjadi contoh yang baik.',
                'tanggapan_ortu' => 'Alhamdulillah, kami sangat bangga dengan prestasi anak kami.',
            ],
            [
                'id' => 5,
                'nis' => '123460',
                'nisn' => '0058745627',
                'nama' => 'EKO PRASETYO',
                'kelas' => 'X RPL 1',
                'fase' => 'E',
                'nilai' => [
                    ['no' => 1, 'mapel' => 'Pendidikan Agama dan Budi Pekerti', 'kelompok' => 'A. Kelompok Mata Pelajaran', 'nilai' => 84, 'capaian' => 'Memahami dan menjalankan ajaran agama dengan baik.'],
                    ['no' => 2, 'mapel' => 'Bahasa Indonesia', 'kelompok' => 'A. Kelompok Mata Pelajaran', 'nilai' => 82, 'capaian' => 'Kemampuan berbahasa cukup baik, perlu lebih percaya diri.'],
                    ['no' => 3, 'mapel' => 'Matematika', 'kelompok' => 'A. Kelompok Mata Pelajaran', 'nilai' => 81, 'capaian' => 'Menunjukkan usaha yang baik dalam belajar matematika.'],
                    ['no' => 1, 'mapel' => 'Pemrograman Dasar', 'kelompok' => 'B. Mata Pelajaran Kejuruan', 'nilai' => 86, 'capaian' => 'Mampu membuat program sederhana dengan bimbingan.'],
                    ['no' => 2, 'mapel' => 'Sistem Komputer', 'kelompok' => 'B. Mata Pelajaran Kejuruan', 'nilai' => 84, 'capaian' => 'Memahami konsep dasar dengan baik.'],
                ],
                'kokurikuler' => 'Mengikuti kegiatan pramuka dan olahraga.',
                'ekstrakurikuler' => [
                    ['nama' => 'Futsal', 'keterangan' => 'Baik'],
                    ['nama' => 'Pramuka', 'keterangan' => 'Baik'],
                ],
                'ketidakhadiran' => ['sakit' => 2, 'izin' => 3, 'tanpa_keterangan' => 0],
                'catatan_wali' => 'Siswa cukup baik, perlu meningkatkan kepercayaan diri.',
                'tanggapan_ortu' => 'Kami akan terus mendukung dan memotivasi anak kami.',
            ],
        ];

        $this->totalStudents = count($this->students);
    }

    public function loadCurrentStudent()
    {
        if (isset($this->students[$this->currentIndex])) {
            $this->currentStudent = $this->students[$this->currentIndex];
            $this->generatePdfUrl();
        } else {
            $this->currentStudent = null;
            $this->pdfUrl = '';
        }
    }

    public function generatePdfUrl()
    {
        if (!$this->currentStudent) {
            $this->pdfUrl = '';
            return;
        }

        // Prepare data for PDF
        $pdfData = [
            'nama' => $this->currentStudent['nama'],
            'nis' => $this->currentStudent['nis'],
            'nisn' => $this->currentStudent['nisn'],
            'kelas' => $this->currentStudent['kelas'],
            'fase' => $this->currentStudent['fase'],
            'sekolah' => 'SMKN 1 Rejang Lebong',
            'alamat' => 'Jl. Merdeka No. 10, Curup',
            'semester' => 'Ganjil (1)',
            'tahun_ajaran' => '2024/2025',
            'nilai' => $this->currentStudent['nilai'],
            'kokurikuler' => $this->currentStudent['kokurikuler'],
            'ekstrakurikuler' => $this->currentStudent['ekstrakurikuler'],
            'ketidakhadiran' => $this->currentStudent['ketidakhadiran'],
            'catatan_wali' => $this->currentStudent['catatan_wali'],
            'tanggapan_ortu' => $this->currentStudent['tanggapan_ortu'],
            'wali_kelas' => ['nama' => 'Siti Nurhaliza, S.Pd', 'nip' => '19850715 201001 2 002'],
            'kepala_sekolah' => ['nama' => 'Drs. Bambang Sudarno, M.M', 'nip' => '19670505 199003 1 001'],
            'orang_tua' => 'Sudirman',
        ];

        // Encode data
        $encodedData = base64_encode(json_encode($pdfData));

        // Generate URL
        $this->pdfUrl = route('pdf.generate') . '?data=' . $encodedData;
    }

    public function nextStudent()
    {
        if ($this->currentIndex < $this->totalStudents - 1) {
            $this->currentIndex++;
            $this->loadCurrentStudent();

            // Livewire 3: dispatch event using new method
            $this->dispatch(
                'student-changed',
                direction: 'next',
                student: $this->currentStudent['nama']
            );
        }
    }

    public function previousStudent()
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
            $this->loadCurrentStudent();

            // Livewire 3: dispatch event using new method
            $this->dispatch(
                'student-changed',
                direction: 'previous',
                student: $this->currentStudent['nama']
            );
        }
    }

    public function selectStudent($index)
    {
        $this->currentIndex = (int)$index;
        $this->loadCurrentStudent();
    }

    public function render()
    {
        return view('livewire.admin.preview-pdf');
    }
}
