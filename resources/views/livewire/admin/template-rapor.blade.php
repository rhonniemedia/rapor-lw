<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Belajar (Rapor)</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h3 {
            text-align: center;
            margin-bottom: 10px;
        }

        /* Group 1 - Identitas Murid */
        .identitas table {
            width: 100%;
            border: none;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .identitas td {
            padding: 4px 6px;
            vertical-align: top;
        }

        /* Group 2 - Tabel Nilai dan Keterangan */
        .nilai table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .nilai th,
        .nilai td {
            border: 1px solid #000;
            padding: 4px;
        }

        .nilai th {
            text-align: center;
        }

        /* Sub bagian kecil seperti kehadiran */
        .nilai .small-table {
            width: 40%;
        }

        /* Group 3 - Tanda Tangan */
        .ttd table {
            width: 100%;
            border: none;
            margin-top: 25px;
        }

        .ttd td {
            padding: 6px;
            vertical-align: top;
            text-align: center;
        }

        .space {
            height: 50px;
        }
    </style>
</head>

<body>

    <h3>Laporan Hasil Belajar (Rapor)</h3>

    <!-- ==================== GROUP 1: IDENTITAS MURID ==================== -->
    <div class="identitas">
        <table>
            <tbody>
                <tr>
                    <td>Nama Murid</td>
                    <td></td>
                    <td>Kelas</td>
                    <td></td>
                </tr>
                <tr>
                    <td>NIS/NISN</td>
                    <td></td>
                    <td>Fase</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Sekolah</td>
                    <td></td>
                    <td>Semester</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td></td>
                    <td>Tahun Ajaran</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ==================== GROUP 2: NILAI, KEGIATAN, CATATAN ==================== -->
    <div class="nilai">
        <!-- Nilai Pelajaran -->
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Mata Pelajaran</th>
                    <th>Nilai Akhir</th>
                    <th>Capaian Kompetensi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="4"><strong>A. Kelompok Mata Pelajaran</strong></td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Bahasa Indonesia</td>
                    <td>90</td>
                    <td>Sangat Baik</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Matematika</td>
                    <td>85</td>
                    <td>Baik</td>
                </tr>
                <tr>
                    <td colspan="4"><strong>B. Mata Pelajaran Kejuruan</strong></td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Desain Grafis</td>
                    <td>88</td>
                    <td>Baik</td>
                </tr>
            </tbody>
        </table>

        <!-- Kokurikuler -->
        <table>
            <tbody>
                <tr>
                    <th>Kokurikuler</th>
                </tr>
                <tr>
                    <td>Mengikuti kegiatan literasi sekolah</td>
                </tr>
            </tbody>
        </table>

        <!-- Ekstrakurikuler -->
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Ekstrakurikuler</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Pramuka</td>
                    <td>Aktif</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Paskibra</td>
                    <td>Baik</td>
                </tr>
            </tbody>
        </table>

        <!-- Kehadiran -->
        <table class="small-table">
            <tbody>
                <tr>
                    <th colspan="2">Ketidakhadiran</th>
                </tr>
                <tr>
                    <td>Sakit</td>
                    <td>2 Hari</td>
                </tr>
                <tr>
                    <td>Izin</td>
                    <td>1 Hari</td>
                </tr>
                <tr>
                    <td>Tanpa Keterangan</td>
                    <td>0 Hari</td>
                </tr>
            </tbody>
        </table>

        <!-- Catatan Wali Kelas -->
        <table>
            <tbody>
                <tr>
                    <th>Catatan Wali Kelas</th>
                </tr>
                <tr>
                    <td>Perkembangan belajar sangat baik dan aktif dalam kegiatan sekolah.</td>
                </tr>
            </tbody>
        </table>

        <!-- Tanggapan Orang Tua -->
        <table>
            <tbody>
                <tr>
                    <th>Tanggapan Orang Tua/Wali Murid</th>
                </tr>
                <tr>
                    <td>Sudah membaca hasil belajar dan akan mendukung peningkatan prestasi anak.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ==================== GROUP 3: TANDA TANGAN ==================== -->
    <div class="ttd">
        <table>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td>Rejang Lebong, Desember 2025</td>
                </tr>
                <tr>
                    <td>Orang Tua/Wali Murid</td>
                    <td>Kepala Sekolah</td>
                    <td>Wali Kelas</td>
                </tr>
                <tr class="space">
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>(................................)</td>
                    <td>HHHHH</td>
                    <td>HHHHHH</td>
                </tr>
                <tr>
                    <td></td>
                    <td>NIP</td>
                    <td>NIP</td>
                </tr>
            </tbody>
        </table>
    </div>

</body>

</html>