<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Leger Kelas {{ $kelas ?? 'N/A' }}</title>
    <?php
    // Hitung jumlah pelajar
    $jumlahPelajar = count($students ?? []);

    // Tentukan line-height berdasarkan jumlah pelajar
    if ($jumlahPelajar > 36) {
        $lineHeight = '0.85';
    } else {
        $lineHeight = '0.9';
    }

    // ... kode PHP Anda yang sudah ada ...
    ?>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 10mm;
            padding-top: 0;
        }

        .leger-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;

            line-height: <?php echo $lineHeight; ?>rem;
        }

        .leger-title {
            text-align: center;
            font-weight: bold;
            padding: 0px 0;
            font-size: 12pt;
            /* border: 1px solid #000; */
            /* background-color: #f0f0f0; */
        }

        .leger-subtitle {
            font-size: 11pt;
            font-weight: normal;
            margin-top: 5px;
        }

        table.info-row {
            width: 100%;
            line-height: 0.7rem;
        }

        .info-row td {
            padding: 3px 0px;
            font-size: 9pt;
            /* border-left: 1px solid #000; */
            /* border-right: 1px solid #000; */
        }

        .info-row:first-of-type td {
            border-top: none;
        }

        .info-row:last-of-type td {
            border-bottom: none;
        }

        .header-content th {
            text-align: center;
            vertical-align: middle;
            padding: 4px 2px;
            border: 1px solid #000;
            font-weight: bold;
            background-color: #e0e0e0;
            font-size: 9pt;
        }

        .mapel-header {
            text-orientation: mixed;
            white-space: nowrap;
            padding: 3px 1px !important;
            font-size: 9pt;
        }

        .mapel-header-wrapper {
            /* width: 18px; */
            /* Lebar cell tetap sempit */
            height: 40px;
            /* Berikan tinggi yang cukup */
            position: relative;
            padding: 0;
            overflow: hidden;
            /* Sembunyikan jika ada yang meluber */
        }

        /* CSS untuk teks yang akan dirotasi */
        .mapel-text-rotated {
            /* Rotasi */
            transform: rotate(-90deg);
        }

        .data-row td {
            border: 1px solid #666;
            padding: 2px 3px;
            text-align: center;
            vertical-align: middle;
            font-size: 9pt;
        }

        .data-row td.text-left {
            text-align: left;
            padding-left: 5px;
        }

        .nilai-cell {
            min-width: 20px;
            max-width: 25px;
        }

        .signature-table {
            width: 100%;
            font-size: 10pt;
            margin-top: 5px;
            border-collapse: collapse;
        }

        .signature-cell {
            vertical-align: top;
            width: 50%;
            padding-top: 5px;
        }

        .signature-left {
            text-align: left;
            padding-left: 10rem;
        }

        .signature-right {
            text-align: left;
            padding-left: 15rem;
        }

        /* Ruang tanda tangan */
        .signature-space {
            height: 60px;
        }

        /* Nama dan NIP */
        .signature-name {
            font-weight: bold;
            margin-top: 0.5rem;
        }

        /* Untuk tabel dengan banyak kolom */
        .compact .header-content th {
            font-size: 9pt;
            padding: 2px 1px;
        }

        .compact .data-row td {
            font-size: 9pt;
            padding: 1px 2px;
        }

        .compact .mapel-header {
            font-size: 9pt;
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
                <th colspan="{{ 12 + $jumlahMapel }}" class="leger-title">
                    LEGER NILAI HASIL BELAJAR PESERTA DIDIK
                    <div class="leger-subtitle">
                        {{ strtoupper($sekolah['nama_sekolah'] ?? 'N/A') }}
                    </div>
                </th>
            </tr>

            {{-- INFO SEKOLAH & KELAS --}}
            <tr>
                <td colspan="{{ 12 + $jumlahMapel }}">
                    <table class="info-row">
                        <tr class="info-row">
                            <td style="width: 20%;">TAHUN AJARAN / SEMESTER</td>
                            <td style="width: 80%;">: {{ $tahun_ajaran ?? 'N/A' }} ~ {{ strtoupper($semester_nama ?? 'N/A') }}</td>
                        </tr>
                        <tr class="info-row">
                            <td>KELAS</td>
                            <td>: {{ $kelas ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>


            {{-- HEADER KOLOM --}}
            <tr class="header-content">
                <th rowspan="2" style="width: 2%;">NO</th>
                <th rowspan="2" style="width: 5%;">NIS</th>
                <th rowspan="2" style="width: 20%;">NAMA SISWA</th>
                <th rowspan="2" style="width: 7%;">NISN</th>
                <th rowspan="2" style="width: 3%;">JK</th>

                <th colspan="{{ $jumlahMapel }}" style="border-bottom: 1px solid #000; width: 41%;">
                    MATA PELAJARAN
                </th>

                <th rowspan="2" style="width: 3%;">KK</th>
                <th rowspan="2" style="width: 3%;">JML</th>
                <th rowspan="2" style="width: 3%;">RATA</th>
                <th rowspan="2" style="width: 3%;">PRKT</th>
                <th colspan="3" style="width: 9%;">KEHADIRAN</th>
            </tr>

            <tr class="header-content">
                @foreach($mata_pelajaran as $mapel)
                <th class="mapel-header-wrapper" title="{{ $mapel['nama'] }}">
                    <div class="mapel-text-rotated">
                        {{ $mapel['kode'] ?? substr($mapel['nama'], 0, 4) }}
                    </div>
                </th>
                @endforeach

                <th style="width: 3%;">S</th>
                <th style="width: 3%;">I</th>
                <th style="width: 3%;">A</th>
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
                    @if(isset($student['nilai_per_mapel'][$mapel['id']]) && $student['nilai_per_mapel'][$mapel['id']] > 0)
                    {{ $student['nilai_per_mapel'][$mapel['id']] }}
                    @else
                    -
                    @endif
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

    {{-- TANDA TANGAN --}}
    <table class="signature-table">
        <tr>
            <!-- Kepala Sekolah -->
            <td class="signature-cell signature-left">
                <div>Mengetahui</div>
                <div>Kepala Sekolah,</div>

                <div class="signature-space"></div>

                <div class="signature-name">{{ $kepala_sekolah['nama'] ?? 'N/A' }}</div>
                <div>NIP {{ $kepala_sekolah['nip'] ?? '-' }}</div>
            </td>

            <!-- Wali Kelas -->
            <td class="signature-cell signature-right">
                <?php
                $tanggalRapor = $tanggal_rapor ?? date('Y-m-d');
                $tanggalFormat = \Carbon\Carbon::parse($tanggalRapor)->locale('id')->isoFormat('D MMMM Y');
                ?>

                <div>{{ $sekolah['kota_kabupaten'] ?? 'Kota' }}, {{ $tanggalFormat }}</div>
                <div>Wali Kelas,</div>

                <div class="signature-space"></div>

                <div class="signature-name">{{ $wali_kelas['nama'] ?? 'N/A' }}</div>
                <div>NIP {{ $wali_kelas['nip'] ?? '-' }}</div>
            </td>
        </tr>
    </table>

    {{-- Keterangan di footer --}}
    <div style="margin-top: 15px; padding: 0 20px; font-size: 8pt; border-top: 1px solid #ccc; padding-top: 5px;">
        <strong>Keterangan:</strong>
        KK = Kokurikuler |
        JML = Jumlah Total Nilai |
        RATA = Rata-rata Nilai |
        PRKT = Peringkat |
        S = Sakit, I = Izin, A = Tanpa Keterangan
    </div>
</body>

</html>