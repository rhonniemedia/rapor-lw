<div>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 20px;
        }

        table {
            border-collapse: collapse;
            margin-bottom: 10px;
            width: 100%;
        }

        td {
            padding: 4px;
            vertical-align: top;
            /* td tetap top untuk content */
        }

        th {
            padding: 4px;
            vertical-align: middle;
            /* th di tengah vertikal */
            text-align: center;
            /* th di tengah horizontal */
            background-color: #e9e7e7ff;
        }

        .bordered,
        .bordered td,
        .bordered th {
            border: 1px solid black;
        }

        .no-border td,
        .no-border th {
            border: none;
        }

        .center {
            text-align: center;
        }

        /* --- Tambahan perataan dan layout --- */
        .table-half {
            width: 49%;
            display: inline-block;
            vertical-align: top;
        }

        .table-space {
            width: 2%;
            display: inline-block;
        }

        /* ===== STYLE UNTUK TITLE HEADER ===== */
        .title-header {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
    </style>

    {{-- ==============================
          1. TABEL IDENTITAS MURID
    =============================== --}}
    <p class="title-header"><strong>Laporan Hasil Belajar (Rapor)</strong></p>

    <table>
        <tbody>
            <tr>
                <td style="width: 15%;"><b>Nama Murid</b></td>
                <td style="width: 45%;">: AHMAD FIKRI</td>
                <td style="width: 15%;"><b>Kelas</b></td>
                <td style="width: 25%;">: X RPL 1</td>
            </tr>
            <tr>
                <td><b>NIS/NISN</b></td>
                <td>: 123456 / 0058745623</td>
                <td><b>Fase</b></td>
                <td>: E</td>
            </tr>
            <tr>
                <td><b>Sekolah</b></td>
                <td>: SMKN 1 Rejang Lebong</td>
                <td><b>Semester</b></td>
                <td>: 1 (Satu)</td>
            </tr>
            <tr>
                <td><b>Alamat</b></td>
                <td>: Jl. Merdeka No. 10, Curup</td>
                <td><b>Tahun Ajaran</b></td>
                <td>: 2025/2026</td>
            </tr>
        </tbody>
    </table>

    {{-- ==============================
          2. TABEL NILAI & CATATAN
    =============================== --}}
    <table class="bordered">
        <tbody>
            <tr class="center" style="vertical-align: middle;">
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">Mata Pelajaran</th>
                <th style="width: 8%;">Nilai Akhir</th>
                <th style="width: 57%;">Capaian Kompetensi</th>
            </tr>
            <tr>
                <td colspan="4"><strong>A. Kelompok Mata Pelajaran</strong></td>
            </tr>
            <tr>
                <td class="center">1</td>
                <td>Pendidikan Agama dan Budi Pekerti</td>
                <td class="center">88</td>
                <td>Sangat baik dalam memahami nilai-nilai keagamaan dan moral.</td>
            </tr>
            <tr>
                <td class="center">2</td>
                <td>Bahasa Indonesia</td>
                <td class="center">85</td>
                <td>Mampu menulis dan berbicara dengan baik serta aktif berdiskusi.</td>
            </tr>
            <tr>
                <td class="center">3</td>
                <td>Matematika</td>
                <td class="center">79</td>
                <td>Perlu meningkatkan ketelitian dalam berhitung dan memahami konsep.</td>
            </tr>
            <tr>
                <td colspan="4"><strong>B. Mata Pelajaran Kejuruan</strong></td>
            </tr>
            <tr>
                <td class="center">1</td>
                <td>Pemrograman Dasar</td>
                <td class="center">92</td>
                <td>Mampu memahami logika pemrograman dan membuat program sederhana.</td>
            </tr>
            <tr>
                <td class="center">2</td>
                <td>Sistem Komputer</td>
                <td class="center">87</td>
                <td>Menunjukkan pemahaman baik dalam konsep perangkat keras dan lunak.</td>
            </tr>
        </tbody>
    </table>

    <table class="bordered">
        <tbody>
            <tr>
                <th class="center">Kokurikuler</th>
            </tr>
            <tr>
                <td>Aktif mengikuti kegiatan pramuka dan literasi sekolah.</td>
            </tr>
        </tbody>
    </table>

    <table class="bordered">
        <tbody>
            <tr class="center">
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Ekstrakurikuler</th>
                <th style="width: 70%;">Keterangan</th>
            </tr>
            <tr>
                <td class="center">1</td>
                <td>Pramuka</td>
                <td>Baik</td>
            </tr>
            <tr>
                <td class="center">2</td>
                <td>Futsal</td>
                <td>Sangat Baik</td>
            </tr>
        </tbody>
    </table>

    {{-- ==============================
          3. KETIDAKHADIRAN & CATATAN WALI KELAS
    =============================== --}}
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <!-- Header Ketidakhadiran -->
            <th colspan="2" class="center" style="width: 49%; border: 1px solid #000; padding: 5px; font-weight: bold;">
                Ketidakhadiran
            </th>
            <th style="width: 2%; border: none;">&nbsp;</th>
            <!-- Header Catatan -->
            <th class="center" style="width: 49%; border: 1px solid #000; padding: 5px; font-weight: bold;">
                Catatan Wali Kelas
            </th>
        </tr>
        <tr>
            <!-- Baris 1 Ketidakhadiran -->
            <td style="width: 24%; border: 1px solid #000; padding: 5px;">Sakit</td>
            <td style="width: 24%; border: 1px solid #000; padding: 5px;">2 Hari</td>
            <td style="border: none;">&nbsp;</td>
            <!-- Catatan dengan rowspan -->
            <td rowspan="3" style="border: 1px solid #000; padding: 5px; vertical-align: top;">
                Siswa menunjukkan kedisiplinan yang baik dan aktif dalam kegiatan kelas.
            </td>
        </tr>
        <tr>
            <!-- Baris 2 Ketidakhadiran -->
            <td style="border: 1px solid #000; padding: 5px;">Izin</td>
            <td style="border: 1px solid #000; padding: 5px;">3 Hari</td>
            <td style="border: none;">&nbsp;</td>
        </tr>
        <tr>
            <!-- Baris 3 Ketidakhadiran -->
            <td style="border: 1px solid #000; padding: 5px;">Tanpa Keterangan</td>
            <td style="border: 1px solid #000; padding: 5px;">0 Hari</td>
            <td style="border: none;">&nbsp;</td>
        </tr>
    </table>

    {{-- ==============================
          4. TANGGAPAN ORANG TUA
    =============================== --}}
    <table class="bordered" style="margin-top: 10px;">
        <tbody>
            <tr>
                <th class="center"><strong>Tanggapan Orang Tua/Wali Murid</strong></th>
            </tr>
            <tr>
                <td>Kami bangga dengan hasil belajar anak kami, semoga terus berkembang dan menjadi pribadi yang lebih baik.</td>
            </tr>
        </tbody>
    </table>

    {{-- ==============================
          5. TANDA TANGAN
    =============================== --}}
    <table class="no-border" style="margin-top: 20px;">
        <tbody>
            <tr>
                <td style="width: 33%;"></td>
                <td style="width: 33%;"></td>
                <td style="width: 33%;">Rejang Lebong, 20 Desember 2025</td>
            </tr>
            <tr>
                <td>Orang Tua/Wali Murid</td>
                <td>Kepala Sekolah</td>
                <td>Wali Kelas</td>
            </tr>
            <tr style="height: 60px;">
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>(Sudirman)</td>
                <td>(Drs. Bambang Sudarno, M.M)</td>
                <td>(Siti Nurhaliza, S.Pd)</td>
            </tr>
            <tr>
                <td></td>
                <td>NIP. 19670505 199003 1 001</td>
                <td>NIP. 19850715 201001 2 002</td>
            </tr>
        </tbody>
    </table>
</div>