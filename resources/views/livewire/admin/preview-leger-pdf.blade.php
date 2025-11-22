<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Leger Kelas {{ $kelas ?? 'N/A' }}</title>
    <style>
        @page {
            margin: 5mm;
            size: landscape;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            margin: 0;
            padding: 0;
        }

        .leger-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .title-section {
            text-align: center;
            margin-bottom: 10px;
        }

        .title-main {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .title-sub {
            font-size: 11pt;
        }

        /* Info Header Table */
        .info-table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 9pt;
        }

        .info-table td {
            padding: 2px 0;
        }

        /* Main Data Table */
        .header-row th {
            border: 1px solid #000;
            background-color: #e0e0e0;
            text-align: center;
            vertical-align: middle;
            font-size: 8pt;
            padding: 2px;
        }

        /* Vertical Text logic for Mapel Headers */
        .th-mapel {
            height: 100px;
            /* Tinggi header agar teks vertikal muat */
            white-space: nowrap;
            position: relative;
            padding: 0 !important;
            width: 22px;
            /* Lebar kolom nilai dipersempit */
        }

        .vertical-text {
            transform: rotate(-90deg);
            transform-origin: center center;
            white-space: nowrap;
            display: block;
            width: 100px;
            /* Sama dengan tinggi th */
            margin-left: -39px;
            /* Adjust positioning manual */
            text-align: center;
        }

        /* Fallback simple rotation if precise CSS fails in DOMPDF */
        .mapel-code {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            height: 90px;
            padding: 5px 0;
            font-size: 8pt;
        }

        .data-row td {
            border: 1px solid #000;
            padding: 3px 2px;
            text-align: center;
            font-size: 8pt;
            vertical-align: middle;
        }

        .text-left {
            text-align: left !important;
            padding-left: 5px !important;
        }

        .signature-section {
            margin-top: 20px;
            width: 100%;
            display: table;
        }

        .sign-box {
            display: table-cell;
            width: 33%;
            text-align: center;
            vertical-align: top;
        }

        .sign-name {
            margin-top: 60px;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="title-section">
        <div class="title-main">LEGER NILAI HASIL BELAJAR PESERTA DIDIK</div>
        <div class="title-sub">{{ strtoupper($sekolah['nama_sekolah'] ?? 'SEKOLAH') }}</div>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">Tahun Ajaran</td>
            <td width="35%">: {{ $tahun_ajaran ?? '-' }}</td>
            <td width="15%">Kelas</td>
            <td width="35%">: {{ $kelas ?? '-' }}</td>
        </tr>
        <tr>
            <td>Semester</td>
            <td>: {{ strtoupper($semester_nama ?? '-') }}</td>
            <td>Wali Kelas</td>
            <td>: {{ $wali_kelas['nama'] ?? '-' }}</td>
        </tr>
    </table>

    <table class="leger-table">
        <thead>
            <tr class="header-row">
                <th rowspan="2" width="25">No</th>
                <th rowspan="2" width="60">NIS</th>
                <th rowspan="2">Nama Siswa</th>
                <th rowspan="2" width="25">L/P</th>

                <th colspan="{{ count($mata_pelajaran) }}">Mata Pelajaran</th>

                <th rowspan="2" width="25">KK</th>
                <th rowspan="2" width="35">Jml</th>
                <th rowspan="2" width="35">Rata</th>
                <th rowspan="2" width="25">Rank</th>
                <th colspan="3" width="75">Absensi</th>
            </tr>
            <tr class="header-row">
                @foreach($mata_pelajaran as $mapel)
                <th class="th-mapel">
                    <div class="mapel-code">{{ $mapel['kode'] }}</div>
                </th>
                @endforeach

                <th width="25">S</th>
                <th width="25">I</th>
                <th width="25">A</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
            <tr class="data-row">
                <td>{{ $student['no'] }}</td>
                <td>{{ $student['nis'] }}</td>
                <td class="text-left">{{ $student['nama'] }}</td>
                <td>{{ $student['jenis_kelamin'] }}</td>

                @foreach($mata_pelajaran as $mapel)
                <td>
                    {{ (isset($student['nilai_per_mapel'][$mapel['id']]) && $student['nilai_per_mapel'][$mapel['id']] > 0) 
                        ? $student['nilai_per_mapel'][$mapel['id']] 
                        : '-' 
                    }}
                </td>
                @endforeach

                <td>{{ $student['kokurikuler'] }}</td>
                <td>{{ $student['jumlah_nilai'] }}</td>
                <td>{{ number_format($student['rata_rata'], 1, ',', '.') }}</td>
                <td><strong>{{ $student['peringkat'] }}</strong></td>

                <td>{{ $student['sakit'] }}</td>
                <td>{{ $student['izin'] }}</td>
                <td>{{ $student['tanpa_keterangan'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section">
        <div class="sign-box">
            <br>
            Mengetahui,<br>
            Kepala Sekolah<br>
            <div class="sign-name">{{ $kepala_sekolah['nama'] }}</div>
            NIP. {{ $kepala_sekolah['nip'] }}
        </div>
        <div class="sign-box"></div>
        <div class="sign-box">
            {{ $sekolah['kota_kabupaten'] }}, {{ \Carbon\Carbon::parse($tanggal_rapor)->isoFormat('D MMMM Y') }}<br>
            Wali Kelas<br>
            <div class="sign-name">{{ $wali_kelas['nama'] }}</div>
            NIP. {{ $wali_kelas['nip'] }}
        </div>
    </div>

</body>

</html>