<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rapor Siswa</title>
    <style>
        /* --- 1. SETTING HALAMAN --- */
        @page {
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 15mm;
            padding-top: 0;
        }

        /* --- 2. CSS WATERMARK (PERBAIKAN) --- */
        header.watermark {
            position: fixed;
            /* Tetap di tempat */
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: -1000;
            /* Di belakang teks */
            opacity: 0.25;
            /* Transparansi */
            width: 50%;
            /* Lebar area watermark */
            text-align: center;
        }

        header.watermark img {
            width: 100%;
            height: auto;
        }

        /* --- 3. CSS LAINNYA (SAMA SEPERTI SEBELUMNYA) --- */
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
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        table {
            border-collapse: collapse;
            margin-bottom: 10px;
            width: 100%;
        }

        th {
            background-color: #e9e7e7ff;
        }

        td {
            padding: 4px;
            vertical-align: top;
            line-height: 1rem;
        }

        /* Tabel Identitas */
        table.identitas td {
            padding: 4px;
            vertical-align: top;
            line-height: 0.9rem;
        }

        /* Bordered & Nilai */
        table.bordered td,
        table.bordered th {
            padding: 4px;
            border: 1px solid black;
        }

        table.bordered.nilai td {
            line-height: 1rem;
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
            /* Opsional: agar teks di tengah secara vertikal */
        }

        /* Tanda Tangan */
        .signature-table {
            width: 100%;
        }

        .signature-table .signature td {
            padding-left: 3.5rem;
            line-height: 0.8rem;
        }

        .signature-table .signature-space td {
            height: 2cm;
            min-height: 2cm;
            padding: 0 !important;
        }

        .kepala-sekolah td[colspan="2"] {
            text-align: left;
            padding-left: 40%;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    {{-- ==============================
          FIX: GUNAKAN TAG <HEADER> 
          DAN TARUH PALING ATAS
    =============================== --}}
    <header class="watermark">
        <img src="{{ public_path('assets/images/logo-sekolah.png') }}" alt="Watermark">
    </header>

    {{-- ==============================
          KONTEN UTAMA (MAIN)
    =============================== --}}
    <main>
        {{-- ==============================
              1. TABEL IDENTITAS MURID
        =============================== --}}
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
                {{-- ... SISA ROW IDENTITAS ANDA ... --}}
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
            <tbody>
                <tr>
                    <td colspan="6">
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
                                    <td class="center">{{ $nilai['nilai'] ?? '-' }}</td>
                                    <td class="justify">{{ $nilai['capaian'] ?? '-' }}</td>
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
                                    @if(isset($ekstrakurikuler) && count($ekstrakurikuler) > 0)
                                    @foreach($ekstrakurikuler as $index => $ekskul)
                                    <tr>
                                        <td class="center">{{ $index + 1 }}</td>
                                        <td>{{ $ekskul['nama'] }}</td>
                                        <td>{{ $ekskul['keterangan'] }}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="3" class="center">Tidak mengikuti ekstrakurikuler</td>
                                    </tr>
                                    @endif
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

                        <div class="tanggapan">
                            <table class="bordered">
                                <tbody>
                                    <tr>
                                        <th class="center"><strong>Tanggapan Orang Tua/Wali Murid</strong></th>
                                    </tr>
                                    <tr>
                                        <td style="height: 60px;">{{ $tanggapan_ortu ?? 'Tidak ada tanggapan.' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="body-signature">
                            <table class="no-border signature-table" style="margin-top: 20px;">
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
                    </td>
                </tr>
            </tbody>
        </table>
    </main>
</body>

</html>