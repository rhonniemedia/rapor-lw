<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rapor Siswa</title>
    <style>
        /* --- 1. SETTING HALAMAN (DIUBAH) --- */
        @page {
            /* KITA BIKIN MARGIN ATAS LEBIH BESAR UNTUK TEMPAT TABEL IDENTITAS */
            margin-top: 4cm;
            margin-left: 15mm;
            margin-right: 15mm;
            margin-bottom: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 0.88rem;
            /* Margin body dinolkan karena sudah dihandle @page */
            margin: 0;
            padding-top: 0;
        }

        /* --- 2. HEADER TETAP (Fixed Header Identitas) --- */
        header.header-identitas {
            position: fixed;
            top: -2.5cm;
            /* Tarik ke atas ke area margin yang kosong */
            left: 0;
            right: 0;
            height: 5cm;
            /* Tentukan tinggi area header */
            /* background-color: white; Opsional, biar tidak tembus pandang */
        }

        /* --- CSS LAINNYA TETAP SAMA (Watermark, dll) --- */
        header.watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: -1000;
            opacity: 0.25;
            width: 50%;
            text-align: center;
        }

        header.watermark img {
            width: 100%;
            height: auto;
        }

        /* --- 3. CSS UMUM & PAGE BREAK FIX --- */
        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .title-header {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-top: 5px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        hr {
            border: none;
            border-top: 1px solid #000;
            margin: 3px 0;
        }

        table {
            border-collapse: collapse;
            margin-bottom: 10px;
            width: 100%;
            /* FIX PAGE BREAK: Izinkan tabel terpotong ke halaman baru */
            page-break-inside: auto;
        }

        tr {
            /* FIX PAGE BREAK: Usahakan baris tidak terpotong di tengah huruf */
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th {
            background-color: #e9e7e7ff;
        }

        td {
            padding: 4px;
            vertical-align: top;
        }

        /* =======================================================
           4. LOGIKA PEMISAH FASE E DAN F (TIDAK DIUBAH)
        ======================================================= */
        @if(isset($fase) && strtoupper($fase)=='E')

        /* --- SETTING KHUSUS FASE E --- */
        table.identitas td {
            line-height: 0.9rem;
        }

        table.identitas thead td {
            line-height: 0.75rem;
        }

        table.bordered.nilai td {
            line-height: 0.95rem;
            padding-top: 3px;
            padding-bottom: 3px;
        }

        @else

        /* --- SETTING KHUSUS FASE F (DEFAULT) --- */
        table.identitas td {
            line-height: 0.9rem;
        }

        table.identitas thead td {
            line-height: 0.75rem;
        }

        table.bordered.nilai td {
            line-height: 1rem;
        }

        @endif
        /* ======================================================= */

        /* --- 5. STYLE SPESIFIK TABEL --- */
        table.bordered td,
        table.bordered th {
            padding: 4px;
            border: 1px solid black;
        }

        .bordered {
            border: 1px solid black;
            padding: 4px 0;
        }

        .no-border td,
        .no-border th {
            border: none;
        }

        /* Kehadiran */
        .section-kehadiran table.kehadiran td {
            padding: 0 4px;
            vertical-align: middle;
        }

        td.bordered.kehadiran {
            padding: 3px 4px !important;
            vertical-align: middle;
        }

        /* --- 6. TANDA TANGAN --- */
        .signature-table {
            width: 100%;
        }

        .signature-table .signature td {
            padding-left: 3.5rem;
            line-height: 0.7rem !important;
        }

        .signature-table .signature-space td {
            height: 2cm;
            min-height: 2cm;
            padding: 0 !important;
        }

        .kepala-sekolah td[colspan="2"] {
            text-align: left;
            padding-left: 40%;
            line-height: 0.7rem !important;
        }

        .page-break {
            page-break-after: always;
        }

        /* Agar Header Tabel (No, Mapel, Nilai) muncul lagi di halaman baru */
        table.nilai thead {
            display: table-header-group;
        }

        /* FIX PAGE BREAK */
        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }
    </style>
</head>

<body>

    <header class="watermark">
        <img src="{{ public_path('assets/images/logo-sekolah.png') }}" alt="Watermark">
    </header>

    <header class="watermark">
        <img src="{{ public_path('assets/images/logo-sekolah.png') }}" alt="Watermark">
    </header>

    <header class="header-identitas">
        <table class="identitas">
            <thead>
                <tr>
                    <td style="width: 14%;"><b>Nama Murid</b></td>
                    <td style="width: 1%;">:</td>
                    <td style="width: 45%;">{{ strtoupper($nama ?? '-') }}</td>
                    <td style="width: 15%;"><b>Kelas</b></td>
                    <td style="width: 1%;">:</td>
                    <td style="width: 24%;">{{ $kelas ?? '-' }}</td>
                </tr>
                <tr>
                    <td><b>NIS/NISN</b></td>
                    <td>:</td>
                    <td> {{ $nis ?? '-' }} / {{ $nisn ?? '-' }}</td>
                    <td><b>Fase</b></td>
                    <td>:</td>
                    <td> {{ $fase ?? '-' }}</td>
                </tr>
                <tr>
                    <td><b>Sekolah</b></td>
                    <td>:</td>
                    <td> {{ $sekolah['nama_sekolah'] ?? '-' }}</td>
                    <td><b>Semester</b></td>
                    <td>:</td>
                    <td> {{ $semester_urutan ?? '-' }} ({{ $semester_nama ?? '-' }})</td>
                </tr>
                <tr>
                    <td><b>Alamat</b></td>
                    <td>:</td>
                    <td> {{ $sekolah['alamat'] ?? '-' }}, {{ $sekolah['kelurahan'] ?? '-' }}</td>
                    <td><b>Tahun Ajaran</b></td>
                    <td>:</td>
                    <td> {{ $tahun_ajaran ?? '-' }}</td>
                </tr>
                <tr>
                    <td colspan="6">
                        <hr>
                    </td>
                </tr>
            </thead>
        </table>
    </header>

    <main>
        {{-- ==============================
              2. KONTEN RAPOR (YANG BISA MULTI-PAGE)
        =============================== --}}

        <p class="title-header"><strong>LAPORAN HASIL BELAJAR</strong></p>

        <table class="bordered nilai">
            <thead>
                <tr class="center" style="vertical-align: middle;">
                    <th style="width: 5%;">No</th>
                    <th style="width: 30%;">Mata Pelajaran</th>
                    <th style="width: 8%;">Nilai Akhir</th>
                    <th style="width: 57%;">Capaian Kompetensi</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($nilai_grouped) && count($nilai_grouped) > 0)
                @foreach($nilai_grouped as $kelompokNama => $dataKelompok)
                @php
                $kelompokKode = $dataKelompok['kode'] ?? 'Z';
                $nilaiList = $dataKelompok['items'] ?? [];
                $mapelCounter = 1;
                @endphp

                <tr>
                    <td colspan="4" style="padding-left: 12px;"><strong>{{ $kelompokKode }}. {{ $kelompokNama }}</strong></td>
                </tr>

                @foreach($nilaiList as $nilai)
                <tr>
                    <td class="center">{{ $mapelCounter++ }}</td>
                    <td>{{ $nilai['mapel'] ?? '-' }}</td>
                    <td class="center"><strong>{{ $nilai['nilai'] ?? '-' }}</strong></td>
                    <td>{{ $nilai['capaian'] ?? '-' }}</td>
                </tr>
                @endforeach
                @endforeach
                @else
                <tr>
                    <td colspan="4" class="center"><em>Belum ada data nilai.</em></td>
                </tr>
                @endif
            </tbody>
        </table>

        {{-- PAGE BREAK UNTUK HALAMAN BERIKUTNYA --}}
        <div class="page-break"></div>

        <div class="kokurikuler">
            <table class="bordered">
                <tbody>
                    <tr>
                        <th class="center">Kokurikuler</th>
                    </tr>
                    <tr>
                        <td>{{ $kokurikuler ?? 'Tidak ada catatan kokurikuler.' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="ekstrakurikuler">
            <table class="bordered">
                <tbody>
                    <tr class="center">
                        <th style="width: 5%;">No</th>
                        <th style="width: 25%;">Ekstrakurikuler</th>
                        <th style="width: 70%;">Keterangan</th>
                    </tr>
                    @php
                    $dataEkskul = $ekstrakurikuler ?? [];
                    $totalData = count($dataEkskul);
                    $minRows = 3;
                    $loopCount = max($totalData, $minRows);
                    @endphp

                    @for ($i = 0; $i < $loopCount; $i++)
                        <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        @if (isset($dataEkskul[$i]))
                        <td>{{ $dataEkskul[$i]['nama'] }}</td>
                        <td>{{ $dataEkskul[$i]['keterangan'] }}</td>
                        @else
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        @endif
                        </tr>
                        @endfor
                </tbody>
            </table>
        </div>

        <div class="section-kehadiran">
            <table class="kehadiran">
                <tr>
                    <th colspan="2" class="center bordered" style="width: 39%;">Ketidakhadiran</th>
                    <td style="width: 2%;"></td>
                    <th class="center bordered" style="width: 59%;">Catatan Wali Kelas</th>
                </tr>
                <tr>
                    <td class="bordered kehadiran">Sakit</td>
                    <td class="bordered kehadiran">{{ $ketidakhadiran['sakit'] ?? 0 }} Hari</td>
                    <td></td>
                    <td class="bordered catatan" rowspan="3">{{ $catatan_wali ?? 'Tidak ada catatan.' }}</td>
                </tr>
                <tr>
                    <td class="bordered kehadiran">Izin</td>
                    <td class="bordered kehadiran">{{ $ketidakhadiran['izin'] ?? 0 }} Hari</td>
                    <td></td>
                </tr>
                <tr>
                    <td class="bordered kehadiran">Tanpa Keterangan</td>
                    <td class="bordered kehadiran">{{ $ketidakhadiran['tanpa_keterangan'] ?? 0 }} Hari</td>
                    <td></td>
                </tr>
            </table>
        </div>

        @if(($semester_urutan ?? 0) == 2)
        <div class="tanggapan">
            <table class="bordered">
                <tbody>
                    @if(($tingkat ?? 0) < 12)
                        {{-- Bagian Kenaikan Kelas untuk Kelas 10 & 11 --}}
                        <tr>
                        <th class="center"><strong>Kenaikan Kelas</strong></th>
                        </tr>
                        @php
                        $kelasTujuan = '';
                        if (($tingkat ?? 0) == 10) {
                        $kelasTujuan = 'XI (sebelas)';
                        } elseif (($tingkat ?? 0) == 11) {
                        $kelasTujuan = 'XII (dua belas)';
                        }
                        @endphp
                        <tr>
                            <td style="height: 40px;">
                                Berdasarkan hasil yang dicapai pada semester ganjil dan genap, maka peserta didik dinyatakan
                                <strong>dapat / tidak dapat</strong> melanjutkan ke kelas
                                <strong>{{ $kelasTujuan }}</strong>
                            </td>
                        </tr>
                        @else
                        {{-- Bagian Kelulusan untuk Kelas 12 --}}
                        <tr>
                            <td style="height: 30px; vertical-align: middle;" class="center">
                                <strong>Keterangan Kelulusan : Lulus</strong>
                            </td>
                        </tr>
                        @endif
                </tbody>
            </table>
        </div>
        @endif

        <div class="tanggapan">
            <table class="bordered">
                <tbody>
                    <tr>
                        <th class="center"><strong>Tanggapan Orang Tua/Wali Murid</strong></th>
                    </tr>
                    <tr>
                        <td style="height: 50px;">{{ $tanggapan_ortu ?? 'Tidak ada tanggapan.' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="body-signature">
            <table class="no-border signature-table" style="margin-top: 5px;">
                <tbody>
                    <tr class="signature">
                        <td style="width: 50%;"></td>
                        <td style="width: 50%;">
                            {{ $sekolah['kota_kabupaten'] ?? '-' }},
                            @if(isset($tanggal_rapor))
                            {{ \Carbon\Carbon::parse($tanggal_rapor)->translatedFormat('d F Y') }}
                            @else
                            {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                            @endif
                        </td>
                    </tr>
                    <tr class="signature">
                        <td>Orang Tua/Wali Murid,</td>
                        <td>Wali Kelas,</td>
                    </tr>
                    <tr class="signature signature-space">
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="signature">
                        <td>(__________________)</td>
                        <td class="bold">{{ $wali_kelas['nama'] ?? '-' }}</td>
                    </tr>
                    <tr class="signature">
                        <td></td>
                        <td>NIP {{ $wali_kelas['nip'] ?? '-' }}</td>
                    </tr>
                    <tr class="kepala-sekolah">
                        <td colspan="2" style="height: 1cm; vertical-align: bottom;">Mengetahui</td>
                    </tr>
                    <tr class="kepala-sekolah">
                        <td colspan="2">Kepala Sekolah,</td>
                    </tr>
                    <tr class="kepala-sekolah signature-space">
                        <td colspan="2"></td>
                    </tr>
                    <tr class="kepala-sekolah">
                        <td class="bold" colspan="2">{{ $kepala_sekolah['nama'] ?? '-' }}</td>
                    </tr>
                    <tr class="kepala-sekolah">
                        <td colspan="2">NIP {{ $kepala_sekolah['nip'] ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </main>
</body>

</html>