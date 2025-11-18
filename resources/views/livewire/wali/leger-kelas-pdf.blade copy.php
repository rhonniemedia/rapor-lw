<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Leger Kelas {{ $kelas ?? 'N/A' }}</title>
    <style>
        @page {
            margin: 0.5cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            line-height: 1.3;
        }

        .leger-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .leger-title {
            text-align: center;
            font-weight: bold;
            padding: 8px 0;
            font-size: 12pt;
            border: 1px solid #000;
            background-color: #f0f0f0;
        }

        .leger-subtitle {
            font-size: 10pt;
            font-weight: normal;
            margin-top: 3px;
        }

        .info-row td {
            padding: 3px 8px;
            font-size: 8pt;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
        }

        .info-row:first-of-type td {
            border-top: 1px solid #000;
        }

        .info-row:last-of-type td {
            border-bottom: 1px solid #000;
        }

        .header-content th {
            text-align: center;
            vertical-align: middle;
            padding: 4px 2px;
            border: 1px solid #000;
            font-weight: bold;
            background-color: #e0e0e0;
            font-size: 7pt;
        }

        .mapel-header {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            white-space: nowrap;
            min-width: 18px;
            max-width: 18px;
            padding: 3px 1px !important;
            font-size: 6pt;
            transform: rotate(180deg);
        }

        .data-row td {
            border: 1px solid #666;
            padding: 2px 3px;
            text-align: center;
            vertical-align: middle;
            font-size: 7pt;
        }

        .data-row td.text-left {
            text-align: left;
            padding-left: 5px;
        }

        .nilai-cell {
            min-width: 20px;
            max-width: 25px;
        }

        .signature-section {
            margin-top: 30px;
            padding: 0 20px;
        }

        .signature-container {
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 48%;
            text-align: center;
            vertical-align: top;
        }

        .signature-name {
            margin-top: 60px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            display: inline-block;
            padding: 0 30px;
        }

        .signature-nip {
            margin-top: 3px;
            font-size: 7pt;
        }

        .signature-title {
            margin-bottom: 5px;
            font-size: 7pt;
        }

        .signature-date {
            margin-bottom: 50px;
            font-size: 7pt;
        }

        /* Untuk tabel dengan banyak kolom */
        .compact .header-content th {
            font-size: 6pt;
            padding: 2px 1px;
        }

        .compact .data-row td {
            font-size: 6pt;
            padding: 1px 2px;
        }

        .compact .mapel-header {
            font-size: 5pt;
            min-width: 15px;
            max-width: 15px;
        }
    </style>
</head>

<body>
    <?php
    $jumlahMapel = count($mata_pelajaran ?? []);
    $compactClass = $jumlahMapel > 12 ? 'compact' : '';
    ?>

    <table class="leger-table {{ $compactClass }}">
        <thead>
            {{-- JUDUL --}}
            <tr>
                <th colspan="{{ 11 + $jumlahMapel }}" class="leger-title">
                    LEGER NILAI HASIL BELAJAR PESERTA DIDIK
                    <div class="leger-subtitle">{{ $sekolah['nama_sekolah'] ?? 'N/A' }}</div>
                </th>
            </tr>

            {{-- INFO SEKOLAH & KELAS --}}
            <tr class="info-row">
                <td colspan="3" style="width: 12%;">Tahun Ajaran</td>
                <td colspan="{{ 8 + $jumlahMapel }}">: {{ $tahun_ajaran ?? 'N/A' }}</td>
            </tr>
            <tr class="info-row">
                <td colspan="3">Semester</td>
                <td colspan="{{ 8 + $jumlahMapel }}">: {{ $semester_nama ?? 'N/A' }}</td>
            </tr>
            <tr class="info-row">
                <td colspan="3">Kelas</td>
                <td colspan="{{ 7 + $jumlahMapel }}">: {{ $kelas ?? 'N/A' }}</td>
                <td style="text-align: right;">KKM: {{ $kkm ?? 75 }}</td>
            </tr>

            {{-- HEADER KOLOM --}}
            <tr class="header-content">
                <th rowspan="2" style="width: 2%;">NO</th>
                <th rowspan="2" style="width: 6%;">NIS</th>
                <th rowspan="2" style="width: 12%;">NAMA SISWA</th>
                <th rowspan="2" style="width: 7%;">NISN</th>
                <th rowspan="2" style="width: 2%;">JK</th>

                <th colspan="{{ $jumlahMapel }}" style="border-bottom: 1px solid #000;">
                    MATA PELAJARAN
                </th>

                <th rowspan="2" style="width: 2%;">KK</th>
                <th rowspan="2" style="width: 3%;">JML</th>
                <th rowspan="2" style="width: 3%;">RATA</th>
                <th rowspan="2" style="width: 2%;">PRKT</th>
                <th colspan="3" style="width: 6%;">KEHADIRAN</th>
            </tr>

            <tr class="header-content">
                @foreach($mata_pelajaran as $mapel)
                <th class="mapel-header" title="{{ $mapel->nama }}">
                    {{ $mapel->kode ?? substr($mapel->nama, 0, 4) }}
                </th>
                @endforeach

                <th style="width: 2%;">S</th>
                <th style="width: 2%;">I</th>
                <th style="width: 2%;">A</th>
            </tr>
        </thead>

        <tbody>
            @foreach($students as $student)
            <tr class="data-row">
                <td>{{ $student['no'] }}</td>
                <td>{{ $student['nis'] }}</td>
                <td class="text-left">{{ $student['nama'] }}</td>
                <td>{{ $student['nisn'] }}</td>
                <td>{{ $student['jenis_kelamin'] }}</td>

                @foreach($mata_pelajaran as $mapel)
                <td class="nilai-cell">
                    @if(isset($student['nilai_per_mapel'][$mapel->id]) && $student['nilai_per_mapel'][$mapel->id] > 0)
                    {{ $student['nilai_per_mapel'][$mapel->id] }}
                    @else
                    -
                    @endif
                </td>
                @endforeach

                <td>{{ $student['ketuntasan'] }}</td>
                <td>{{ $student['jumlah_nilai'] }}</td>
                <td>{{ $student['rata_rata'] }}</td>
                <td><strong>{{ $student['predikat'] }}</strong></td>
                <td>{{ $student['sakit'] }}</td>
                <td>{{ $student['izin'] }}</td>
                <td>{{ $student['tanpa_keterangan'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TANDA TANGAN --}}
    <div class="signature-section">
        <div class="signature-container">
            <div class="signature-box" style="text-align: left;">
                <div class="signature-title">Wali Kelas</div>
                <div class="signature-name">{{ $wali_kelas['nama'] ?? 'N/A' }}</div>
                <div class="signature-nip">NIP. {{ $wali_kelas['nip'] ?? '-' }}</div>
            </div>

            <div class="signature-box" style="text-align: right;">
                <?php
                $tanggalRapor = $tanggal_rapor ?? date('Y-m-d');
                $tanggalFormat = \Carbon\Carbon::parse($tanggalRapor)->locale('id')->isoFormat('D MMMM Y');
                ?>
                <div class="signature-date">
                    {{ $sekolah['kota_kabupaten'] ?? 'Kota' }}, {{ $tanggalFormat }}
                </div>
                <div class="signature-title">Kepala Sekolah</div>
                <div class="signature-name">{{ $kepala_sekolah['nama'] ?? 'N/A' }}</div>
                <div class="signature-nip">NIP. {{ $kepala_sekolah['nip'] ?? '-' }}</div>
            </div>
        </div>
    </div>

    {{-- Keterangan di footer --}}
    <div style="margin-top: 15px; padding: 0 20px; font-size: 6pt; border-top: 1px solid #ccc; padding-top: 5px;">
        <strong>Keterangan:</strong>
        KK = Ketuntasan Kompetensi |
        JML = Jumlah Total Nilai |
        RATA = Rata-rata Nilai |
        PRKT = Predikat (A: 90-100, B: 80-89, C: 70-79, D: 60-69, E: <60) |
            S=Sakit, I=Izin, A=Tanpa Keterangan
            </div>
</body>

</html>