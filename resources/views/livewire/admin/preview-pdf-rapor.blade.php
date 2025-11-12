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

        table {
            border-collapse: collapse;
            margin-bottom: 10px;
            width: 100%;
        }

        td {
            padding: 4px;
            vertical-align: top;
        }

        th {
            padding: 4px;
            vertical-align: middle;
            text-align: center;
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

        .justify {
            text-align: justify;
        }

        .identitas {
            line-height: 0.9rem;
        }

        hr {
            height: 1.5px;
            background-color: black;
            border: none;
            /* margin-bottom: 15px; */
        }

        .title-header {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-top: 5px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .signature-table {
            /* Mengatur jarak baris (line-height) dan ukuran font untuk tabel tanda tangan */
            line-height: 0.7rem;
        }

        .signature-space td {
            /* Targetkan semua sel TD di dalam baris signature-space */
            height: 2cm;
            min-height: 2cm;
            /* Tambahkan min-height untuk memastikan ketinggian minimum */
            padding: 0;
            /* Hapus padding jika ada, untuk kontrol tinggi yang lebih baik */
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
                <td> {{ $sekolah ?? '-' }}</td>
                <td><b>Semester</b></td>
                <td>:</td>
                <td> {{ $semester_urutan ?? '-' }} ({{ $semester_nama ?? '-' }})</td>
            </tr>
            <tr>
                <td><b>Alamat</b></td>
                <td>:</td>
                <td> {{ $alamat_sekolah ?? '-' }}</td>
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
                    <table class="bordered">
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
                                <th colspan="2" class="center bordered" style="width: 40%;">
                                    Ketidakhadiran
                                </th>
                                <td style="width: 1%;"></td>
                                <th class="center bordered" style="width: 59%;">
                                    Catatan Wali Kelas
                                </th>
                            </tr>
                            <tr>
                                <td class="bordered">Sakit</td>
                                <td class="bordered">
                                    {{ $ketidakhadiran['sakit'] ?? 0 }} Hari
                                </td>
                                <td></td>
                                <td class="bordered" rowspan="3">
                                    {{ $catatan_wali ?? 'Tidak ada catatan.' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="bordered">Izin</td>
                                <td class="bordered">
                                    {{ $ketidakhadiran['izin'] ?? 0 }} Hari
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="bordered">Tanpa Keterangan</td>
                                <td class="bordered">
                                    {{ $ketidakhadiran['tanpa_keterangan'] ?? 0 }} Hari
                                </td>
                                <td></td>
                            </tr>
                        </table>
                    </div>

                    <!-- 6. TANGGAPAN ORANG TUA -->
                    <div class="tanggapan">
                        <table class="bordered" style="margin-top: 10px;">
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
                                <tr>
                                    <td style="width: 30%;"></td>
                                    <td style="width: 35%;"></td>
                                    <td style="width: 35%;">
                                        Rejang Lebong,
                                        @if(isset($tanggal_rapor))
                                        {{ \Carbon\Carbon::parse($tanggal_rapor)->translatedFormat('d F Y') }}
                                        @else
                                        {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Orang Tua/Wali Murid</td>
                                    <td>Kepala Sekolah</td>
                                    <td>Wali Kelas</td>
                                </tr>
                                <tr class="signature-space">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>(__________________)
                                    </td>
                                    <td>{{ $kepala_sekolah['nama'] ?? '-' }}</td>
                                    <td>{{ $wali_kelas['nama'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>NIP {{ $kepala_sekolah['nip'] ?? '-' }}</td>
                                    <td>NIP {{ $wali_kelas['nip'] ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>