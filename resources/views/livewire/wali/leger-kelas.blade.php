<div>
    {{-- Header dengan tombol download --}}

    <div class="row g-4">
        <div class="col-lg-12">
            <div class="page-header pb-3 mb-4 border-bottom">

                <div class="d-flex justify-content-between align-items-center w-100">

                    <!-- Kiri: Ikon + Judul -->
                    <div class="d-flex align-items-center">
                        <span class="bg-gradient-primary p-2 rounded-3 shadow-sm me-3 d-inline-flex align-items-center justify-content-center">
                            <i class="mdi mdi-file-document-box mdi-24px text-white"></i>
                        </span>

                        <div>
                            <h4 class="mb-1 text-dark fw-bold">
                                Leger Kelas {{ $rombel->nama ?? 'N/A' }}
                            </h4>
                            <small class="text-muted">
                                {{ $semesterAktif->tahunAjaran->nama ?? 'N/A' }}
                                - Semester {{ $semesterAktif->semester->nama ?? 'N/A' }}
                            </small>
                        </div>
                    </div>

                    <!-- Kanan: Tombol -->
                    <div class="d-flex align-items-center gap-2 ms-auto">

                        @if($pdfUrl)
                        <button
                            type="button"
                            class="btn btn-labeled btn-success"
                            onclick="window.open('{{ $pdfUrl }}', '_blank')">

                            <span class="btn-label">
                                <i class="mdi mdi-file-pdf-box"></i>
                            </span>

                            Download PDF
                        </button>
                        @endif

                        <button
                            type="button"
                            class="btn btn-labeled btn-primary"
                            onclick="window.print()">
                            <span class="btn-label">
                                <i class="mdi mdi-printer"></i>
                            </span>
                            Cetak
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Leger --}}
    <div id="tabel-leger">
        <div class="bg-white overflow-hidden">
            <div class="overflow-x-auto">
                <style>
                    @media print {
                        @page {
                            @if(count($mataPelajaranList) > 10) size: A3 landscape;
                            /* Banyak mapel = A3 */
                            @else size: A4 landscape;
                            /* Sedikit mapel = A4 */
                            @endif margin: 10mm;
                        }

                        /* Sembunyikan semua elemen */
                        body * {
                            visibility: hidden;
                        }

                        /* Tampilkan hanya tabel leger dan isinya */
                        #tabel-leger,
                        #tabel-leger * {
                            visibility: visible;
                        }

                        /* Posisikan tabel di pojok kiri atas */
                        #tabel-leger {
                            position: absolute;
                            left: 0;
                            top: 0;
                            width: 100%;
                        }

                        /* Sembunyikan tombol cetak */
                        .btn,
                        button,
                        .no-print {
                            display: none !important;
                        }

                        /* Font size lebih kecil untuk print */
                        .leger-table {
                            font-size: 8pt;
                        }

                        .header-content th {
                            font-size: 7pt;
                        }

                        .data-row td {
                            font-size: 7pt;
                        }
                    }

                    /* Style regular (non-print) tetap sama */
                    .leger-table {
                        width: 100%;
                        border-collapse: collapse;
                        font-family: Arial, sans-serif;
                        font-size: 9pt;
                    }

                    .leger-title {
                        text-align: center;
                        font-weight: bold;
                        padding: 10px 0;
                        font-size: 13pt;
                        /* background-color: #f3f4f6; */
                    }

                    .info-row td {
                        padding: 3px 8px;
                        /* background-color: #f9fafb; */
                        font-size: 8pt;
                    }

                    .header-content th {
                        text-align: center;
                        vertical-align: middle;
                        padding: 6px 4px;
                        border: 1px solid #000;
                        font-weight: bold;
                        background-color: #e5e7eb;
                        font-size: 8pt;
                    }

                    .data-row td {
                        border: 1px solid #d1d5db;
                        padding: 3px 3px;
                        text-align: center;
                        vertical-align: middle;
                        font-size: 8pt;
                    }

                    .data-row td.text-left {
                        text-align: left;
                    }

                    .nilai-cell {
                        min-width: 25px;
                    }

                    .mapel-header {
                        transform: rotate(180deg);
                        writing-mode: vertical-lr;
                        text-orientation: mixed;
                        white-space: nowrap;
                        padding: 4px 2px !important;
                        font-size: 7pt;
                    }

                    tfoot strong {
                        font-weight: 700 !important;
                    }
                </style>

                <table class="leger-table">
                    <thead>
                        {{-- BARIS 1: JUDUL UTAMA --}}
                        <tr>
                            <th colspan="{{ 12 + count($mataPelajaranList) }}" class="leger-title">
                                LEGER NILAI HASIL BELAJAR PESERTA DIDIK<br>
                                <span style="font-size: 12pt; font-weight: bold;">
                                    {{ strtoupper($dataSekolah->nama_sekolah ?? 'N/A') }}
                                </span>
                            </th>
                        </tr>

                        {{-- BARIS 2-4: INFO SEKOLAH & KELAS --}}
                        <tr class="info-row info-small">
                            <td colspan="3" style="width: 15%; font-size: 12px;">TAHUN AJARAN</td>
                            <td colspan="{{ 9 + count($mataPelajaranList) }}" style="width: 85%; font-size: 12px;">
                                : {{ $semesterAktif->tahunAjaran->nama ?? 'N/A' }}
                            </td>
                        </tr>
                        <tr class="info-row info-small">
                            <td colspan="3" style="font-size: 12px;">SEMESTER</td>
                            <td colspan="{{ 9 + count($mataPelajaranList) }}" style="font-size: 12px;">
                                : {{ strtoupper($semesterAktif->semester->nama ?? 'N/A') }}
                            </td>
                        </tr>
                        <tr class="info-row info-small">
                            <td colspan="3" style="font-size: 12px;">KELAS</td>
                            <td colspan="{{ 9 + count($mataPelajaranList) }}" style="font-size: 12px;">
                                : {{ $rombel->nama ?? 'N/A' }}
                            </td>
                        </tr>

                        {{-- BARIS HEADER KOLOM --}}
                        <tr class="header-content">
                            {{-- Kolom identitas siswa --}}
                            <th rowspan="2" style="width: 3%;">NO</th>
                            <th rowspan="2" style="width: 5%;">NIS</th>
                            <th rowspan="2" style="width: 20%;">NAMA SISWA</th>
                            <th rowspan="2" style="width: 7%;">NISN</th>
                            <th rowspan="2" style="width: 3%;">JK</th>

                            {{-- Kolom mata pelajaran --}}
                            <th colspan="{{ count($mataPelajaranList) }}" style="border-bottom: none; width: 41%;">
                                MATA PELAJARAN
                            </th>

                            {{-- Kolom statistik --}}
                            <th rowspan="2" style="width: 3%;">KK</th>
                            <th rowspan="2" style="width: 3%;">JML</th>
                            <th rowspan="2" style="width: 3%;">RATA</th>
                            <th rowspan="2" style="width: 3%;">PRKT</th>

                            {{-- Kolom kehadiran --}}
                            <th colspan="3" style="width: 9%;">KEHADIRAN</th>
                        </tr>

                        {{-- Sub-header untuk mata pelajaran dan kehadiran --}}
                        <tr class="header-content">
                            @foreach($mataPelajaranList as $mapel)
                            <th class="mapel-header" title="{{ $mapel->nama }}">
                                {{ $mapel->kode ?? substr($mapel->nama, 0, 4) }}
                            </th>
                            @endforeach

                            <th style="width: 3%;">S</th>
                            <th style="width: 3%;">I</th>
                            <th style="width: 3%;">A</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($studentsList as $student)
                        <tr class="data-row">
                            <td>{{ $student['no'] }}</td>
                            <td>{{ $student['nis'] }}</td>
                            <td class="text-left" style="padding-left: 4px;">{{ $student['nama'] }}</td>
                            <td>{{ $student['nisn'] }}</td>
                            <td>{{ $student['jenis_kelamin'] }}</td>

                            {{-- Nilai per mata pelajaran --}}
                            @foreach($mataPelajaranList as $mapel)
                            <td class="nilai-cell">
                                {{ $student['nilai_per_mapel'][$mapel->id] ?? '-' }}
                            </td>
                            @endforeach

                            {{-- Statistik --}}
                            <td>{{ $student['kokurikuler'] }}</td>
                            <td>{{ $student['jumlah_nilai'] }}</td>
                            <td>{{ number_format($student['rata_rata'], 1, ',', '.') }}</td>
                            <td><strong>{{ $student['peringkat'] }}</strong></td>

                            {{-- Kehadiran --}}
                            <td>{{ $student['sakit'] }}</td>
                            <td>{{ $student['izin'] }}</td>
                            <td>{{ $student['tanpa_keterangan'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ 11 + count($mataPelajaranList) }}" class="text-center py-8 text-gray-500">
                                Tidak ada data siswa
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                    {{-- Footer dengan tanda tangan --}}
                    <tfoot>
                        <tr>
                            <td colspan="{{ 12 + count($mataPelajaranList) }}" style="padding: 20px 30px; border: none;">
                                <div style="display: flex; justify-content: space-between; margin-top: 30px;">
                                    <div style="text-align: center; width: 45%;">
                                        <div style="margin-bottom: 1px;">
                                            Mengetahui
                                        </div>
                                        <div style="margin-bottom: 80px;">Kepala Sekolah,</div>
                                        <div style="display: inline-block; padding: 0 50px;">
                                            <strong>{{ $pengaturan->kepalaSekolah->name ?? 'N/A' }}</strong>
                                        </div>
                                        <div style="margin-top: 5px;">
                                            NIP {{ $pengaturan->kepalaSekolah->nip ?? '-' }}
                                        </div>
                                    </div>
                                    <div style="text-align: center; width: 45%;">
                                        <div style="margin-bottom: 1px;">
                                            {{ $dataSekolah->kota_kabupaten ?? 'Kota' }},
                                            {{ \Carbon\Carbon::parse($pengaturan->tanggal_rapor ?? now())->isoFormat('D MMMM Y') }}
                                        </div>
                                        <div style="margin-bottom: 80px;">Wali Kelas,</div>
                                        <div style="display: inline-block; padding: 0 50px;">
                                            <strong>{{ $rombel->waliKelas->name ?? 'N/A' }}</strong>
                                        </div>
                                        <div style="margin-top: 5px;">
                                            NIP {{ $rombel->waliKelas->nip ?? '-' }}
                                        </div>
                                    </div>


                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Keterangan --}}
    <div class="mt-4 bg-blue-50 border-l-4 border-blue-500 p-4 no-print">
        <div class="flex">
            <div class="ml-3">
                <p class="text-blue-500" style="font-size: 12px;">
                    <strong>Keterangan:</strong><br>
                    • <strong>KK</strong> = Kokurikuler (predikat dari kegiatan kokurikuler)<br>
                    • <strong>JML</strong> = Jumlah total nilai semua mata pelajaran<br>
                    • <strong>RATA</strong> = Rata-rata nilai<br>
                    • <strong>PRKT</strong> = Peringkat (ranking berdasarkan jumlah nilai)<br>
                    • <strong>S</strong> = Sakit, <strong>I</strong> = Izin, <strong>A</strong> = Tanpa Keterangan
                </p>
            </div>
        </div>
    </div>
</div>