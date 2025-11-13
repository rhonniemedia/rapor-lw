<div>
    <style>
        /* 1. Atur margin halaman bawaan dompdf ke 0 */
        @page {
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 15mm;
            padding-top: 0;
        }

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

        /* Aturan Generik TD (Ini akan memengaruhi semua TD kecuali ditimpa) */
        td {
            padding: 4px;
            vertical-align: top;
            line-height: 1rem;
        }

        /* Aturan untuk Tabel IDENTITAS */
        table.identitas td {
            padding: 4px;
            vertical-align: top;
            line-height: 0.9rem;
        }

        /* Aturan untuk Tabel NILAI */
        table.bordered td,
        table.bordered th {
            padding: 4px;
            border: 1px solid black;
        }

        table.bordered.nilai td {
            line-height: 1rem;
        }

        /* Aturan untuk Tabel KEHADIRAN */
        .bordered {
            border: 1px solid black;
            padding: 4px 0;
        }

        .no-border td,
        .no-border th {
            border: none;
        }

        /* Kunci TD di tabel .kehadiran (karena memiliki aturan padding 0 4px) */
        .section-kehadiran table.kehadiran td {
            padding: 0 4px;
            vertical-align: middle;
        }

        /* Tanda Tangan: Kontainer Utama */
        .signature-table {
            width: 100%;
        }

        /* Tanda Tangan: Sel Khusus */
        .signature-table .signature td {
            padding-left: 3.5rem;
            line-height: 0.8rem;
        }

        /* Tanda Tangan: Spasi Khusus untuk TTD */
        .signature-table .signature-space td {
            height: 2cm;
            min-height: 2cm;
            padding: 0 !important;
        }

        /* Jika Anda ingin tanda tangan Kepala Sekolah berada di tengah-kanan dokumen: */
        .kepala-sekolah td[colspan="2"] {
            text-align: left;
            padding-left: 40%;
        }

        .page-break {
            page-break-after: always;
        }
    </style>

    {{-- ==============================
          1. TABEL IDENTITAS MURID
    =============================== --}}
    <table class="identitas">
        <thead>

            <!-- 1. TABLE IDENTITAS -->
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
        <tbody>
            <tr>
                <td colspan="6">

                    <p class="title-header"><strong>LAPORAN HASIL BELAJAR</strong></p>

                    <!-- 2. TABEL NILAI & CATATAN -->
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
                            {{-- LOOP DYNAMIC BERDASARKAN KELOMPOK --}}
                            @if(isset($nilai_grouped) && count($nilai_grouped) > 0)
                            {{-- KOREKSI: Iterasi melalui $dataKelompok --}}
                            @foreach($nilai_grouped as $kelompokNama => $dataKelompok)

                            @php
                            // DEFINISIKAN variabel $kelompokKode dari sub-array 'kode'
                            $kelompokKode = $dataKelompok['kode'] ?? 'Z';
                            // DEFINISIKAN $nilaiList (daftar mapel) dari sub-array 'items'
                            $nilaiList = $dataKelompok['items'] ?? [];

                            // INISIASI COUNTER LOKAL UNTUK RESET PENOMORAN DI AWAL KELOMPOK BARU
                            $mapelCounter = 1;
                            @endphp

                            <tr>
                                <td colspan="4" style="padding-left: 12px;"><strong>{{ $kelompokKode }}. {{ $kelompokNama }}</strong></td>
                            </tr>

                            {{-- LOOP daftar mata pelajaran di dalam kelompok --}}
                            @foreach($nilaiList as $nilai)
                            <tr>
                                {{-- PENGGUNAAN COUNTER LOKAL $mapelCounter, BUKAN $nilai['no'] --}}
                                <td class="center">{{ $mapelCounter++ }}</td>
                                <td>{{ $nilai['mapel'] ?? '-' }}</td>
                                <td class="center">{{ $nilai['nilai'] ?? '-' }}</td>
                                <td class="justify">{{ $nilai['capaian'] ?? '-' }}</td>
                            </tr>
                            @endforeach

                            @endforeach
                            @else
                            <tr>
                                <td colspan="4" class="center" style="padding: 15px;">
                                    <em>Belum ada data nilai.</em>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>

                    <div class="page-break"></div>

                    <!-- 3. TABEL KOKURIKULER -->
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

                    <!-- 4. EKSTRAKURIKULER -->
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

                    <!-- 5. KETIDAKHADIRAN & CATATAN WALI KELAS -->
                    <div class="section-kehadiran">
                        <table class="kehadiran">
                            <tr>
                                <th colspan="2" class="center bordered" style="width: 39%;">
                                    Ketidakhadiran
                                </th>
                                <td style="width: 2%;"></td>
                                <th class="center bordered" style="width: 59%;">
                                    Catatan Wali Kelas
                                </th>
                            </tr>
                            <tr>
                                <td class="bordered kehadiran">Sakit</td>
                                <td class="bordered kehadiran">
                                    {{ $ketidakhadiran['sakit'] ?? 0 }} Hari
                                </td>
                                <td></td>

                                <!-- Catatan Wali Kelas -->
                                <td class="bordered catatan" rowspan="3">
                                    {{ $catatan_wali ?? 'Tidak ada catatan.' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="bordered kehadiran">Izin</td>
                                <td class="bordered kehadiran">
                                    {{ $ketidakhadiran['izin'] ?? 0 }} Hari
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="bordered kehadiran">Tanpa Keterangan</td>
                                <td class="bordered kehadiran">
                                    {{ $ketidakhadiran['tanpa_keterangan'] ?? 0 }} Hari
                                </td>
                                <td></td>
                            </tr>
                        </table>
                    </div>

                    <!-- 6. TANGGAPAN ORANG TUA -->
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

                    <!-- 7. TANDA TANGAN -->
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
</div>