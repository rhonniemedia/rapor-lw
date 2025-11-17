<div>
    {{-- Header dengan tombol download --}}
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Leger Kelas {{ $rombel->nama ?? 'N/A' }}</h1>
            <p class="text-gray-600">
                Tahun Ajaran: {{ $semesterAktif->tahunAjaran->nama ?? 'N/A' }} -
                Semester {{ $semesterAktif->semester->nama ?? 'N/A' }}
            </p>
            <p class="text-gray-600">
                Total Siswa: {{ $totalStudents }} | Total Mata Pelajaran: {{ $totalMataPelajaran }}
            </p>
        </div>

        <div class="flex gap-3">
            {{-- Tombol Download PDF --}}
            @if($pdfUrl)
            <a href="{{ $pdfUrl }}"
                target="_blank"
                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-md transition duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Download PDF
            </a>
            @endif

            {{-- Tombol Print --}}
            <button onclick="window.print()"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition duration-300">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </button>
        </div>
    </div>

    {{-- Tabel Leger --}}
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <style>
                @media print {
                    .no-print {
                        display: none !important;
                    }

                    body {
                        print-color-adjust: exact;
                        -webkit-print-color-adjust: exact;
                    }

                    .leger-table {
                        font-size: 8pt !important;
                    }

                    @page {
                        size: landscape;
                        margin: 0.5cm;
                    }
                }

                .leger-table {
                    width: 100%;
                    border-collapse: collapse;
                    font-family: Arial, sans-serif;
                    font-size: 9pt;
                }

                .leger-title {
                    text-align: center;
                    font-weight: bold;
                    padding: 12px 0;
                    font-size: 14pt;
                    background-color: #f3f4f6;
                }

                .info-row td {
                    padding: 4px 8px;
                    background-color: #f9fafb;
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
                    padding: 4px 6px;
                    text-align: center;
                    vertical-align: middle;
                    font-size: 8pt;
                }

                .data-row td.text-left {
                    text-align: left;
                }

                .nilai-cell {
                    min-width: 35px;
                }

                .mapel-header {
                    writing-mode: vertical-rl;
                    text-orientation: mixed;
                    white-space: nowrap;
                    min-width: 25px;
                    max-width: 25px;
                    padding: 4px 2px !important;
                    font-size: 7pt;
                }
            </style>

            <table class="leger-table">
                <thead>
                    {{-- BARIS 1: JUDUL UTAMA --}}
                    <tr>
                        <th colspan="{{ 11 + count($mataPelajaranList) }}" class="leger-title">
                            LEGER NILAI HASIL BELAJAR PESERTA DIDIK<br>
                            <span style="font-size: 11pt; font-weight: normal;">
                                {{ $dataSekolah->nama_sekolah ?? 'N/A' }}
                            </span>
                        </th>
                    </tr>

                    {{-- BARIS 2-5: INFO SEKOLAH & KELAS --}}
                    <tr class="info-row">
                        <td colspan="3" style="width: 15%;">Tahun Ajaran</td>
                        <td colspan="{{ 8 + count($mataPelajaranList) }}" style="width: 85%;">
                            : {{ $semesterAktif->tahunAjaran->nama ?? 'N/A' }}
                        </td>
                    </tr>
                    <tr class="info-row">
                        <td colspan="3">Semester</td>
                        <td colspan="{{ 8 + count($mataPelajaranList) }}">
                            : {{ $semesterAktif->semester->nama ?? 'N/A' }}
                        </td>
                    </tr>
                    <tr class="info-row">
                        <td colspan="3">Kelas</td>
                        <td colspan="{{ 7 + count($mataPelajaranList) }}">
                            : {{ $rombel->nama ?? 'N/A' }}
                        </td>
                        <td style="text-align: right;">KKM: {{ $pengaturan->kkm ?? 75 }}</td>
                    </tr>

                    {{-- BARIS HEADER KOLOM --}}
                    <tr class="header-content">
                        {{-- Kolom identitas siswa --}}
                        <th rowspan="2" style="width: 3%;">NO</th>
                        <th rowspan="2" style="width: 7%;">NIS</th>
                        <th rowspan="2" style="width: 12%;">NAMA SISWA</th>
                        <th rowspan="2" style="width: 7%;">NISN</th>
                        <th rowspan="2" style="width: 3%;">JK</th>

                        {{-- Kolom mata pelajaran --}}
                        <th colspan="{{ count($mataPelajaranList) }}" style="border-bottom: none;">
                            MATA PELAJARAN
                        </th>

                        {{-- Kolom statistik --}}
                        <th rowspan="2" style="width: 3%;">KK</th>
                        <th rowspan="2" style="width: 4%;">JML</th>
                        <th rowspan="2" style="width: 4%;">RATA</th>
                        <th rowspan="2" style="width: 3%;">PRKT</th>

                        {{-- Kolom kehadiran --}}
                        <th colspan="3" style="width: 8%;">KEHADIRAN</th>
                    </tr>

                    {{-- Sub-header untuk mata pelajaran dan kehadiran --}}
                    <tr class="header-content">
                        @foreach($mataPelajaranList as $mapel)
                        <th class="mapel-header" title="{{ $mapel->nama }}">
                            {{ $mapel->kode ?? substr($mapel->nama, 0, 4) }}
                        </th>
                        @endforeach

                        <th style="width: 2.5%;">S</th>
                        <th style="width: 2.5%;">I</th>
                        <th style="width: 3%;">A</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($studentsList as $student)
                    <tr class="data-row">
                        <td>{{ $student['no'] }}</td>
                        <td>{{ $student['nis'] }}</td>
                        <td class="text-left" style="padding-left: 8px;">{{ $student['nama'] }}</td>
                        <td>{{ $student['nisn'] }}</td>
                        <td>{{ $student['jenis_kelamin'] }}</td>

                        {{-- Nilai per mata pelajaran --}}
                        @foreach($mataPelajaranList as $mapel)
                        <td class="nilai-cell">
                            {{ $student['nilai_per_mapel'][$mapel->id] ?? '-' }}
                        </td>
                        @endforeach

                        {{-- Statistik --}}
                        <td>{{ $student['ketuntasan'] }}</td>
                        <td>{{ $student['jumlah_nilai'] }}</td>
                        <td>{{ $student['rata_rata'] }}</td>
                        <td><strong>{{ $student['predikat'] }}</strong></td>

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
                        <td colspan="{{ 11 + count($mataPelajaranList) }}" style="padding: 20px 30px; border: none;">
                            <div style="display: flex; justify-content: space-between; margin-top: 30px;">
                                <div style="text-align: center; width: 45%;">
                                    <div style="margin-bottom: 80px;">Wali Kelas</div>
                                    <div style="border-bottom: 1px solid #000; display: inline-block; padding: 0 50px;">
                                        <strong>{{ $rombel->waliKelas->name ?? 'N/A' }}</strong>
                                    </div>
                                    <div style="margin-top: 5px;">
                                        NIP. {{ $rombel->waliKelas->nip ?? '-' }}
                                    </div>
                                </div>

                                <div style="text-align: center; width: 45%;">
                                    <div style="margin-bottom: 10px;">
                                        {{ $dataSekolah->kota_kabupaten ?? 'Kota' }},
                                        {{ \Carbon\Carbon::parse($pengaturan->tanggal_rapor ?? now())->isoFormat('D MMMM Y') }}
                                    </div>
                                    <div style="margin-bottom: 60px;">Kepala Sekolah</div>
                                    <div style="border-bottom: 1px solid #000; display: inline-block; padding: 0 50px;">
                                        <strong>{{ $pengaturan->kepalaSekolah->name ?? 'N/A' }}</strong>
                                    </div>
                                    <div style="margin-top: 5px;">
                                        NIP. {{ $pengaturan->kepalaSekolah->nip ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Keterangan --}}
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 no-print">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Keterangan:</strong><br>
                    • <strong>KK</strong> = Ketuntasan Kompetensi (jumlah mata pelajaran yang tuntas)<br>
                    • <strong>JML</strong> = Jumlah total nilai semua mata pelajaran<br>
                    • <strong>RATA</strong> = Rata-rata nilai<br>
                    • <strong>PRKT</strong> = Predikat (A = 90-100, B = 80-89, C = 70-79, D = 60-69, E = <60)<br>
                        • <strong>S</strong> = Sakit, <strong>I</strong> = Izin, <strong>A</strong> = Tanpa Keterangan
                </p>
            </div>
        </div>
    </div>
</div>