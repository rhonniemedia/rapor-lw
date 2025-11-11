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

            /* Tambahkan ini untuk memastikan tidak ada padding atas bawaan */
            padding-top: 0;

            /* Atur ulang margin atas secara eksplisit */
            margin-top: 1cm !important;
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

        .title-header {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
    </style>

    {{-- ==============================
          1. TABEL IDENTITAS MURID
    =============================== --}}
    <p class="title-header"><strong>Laporan Hasil Belajar (Rapor)</strong></p>

    <table>
        <tbody>
            <tr>
                <td style="width: 15%;"><b>Nama Murid</b></td>
                <td style="width: 45%;">: {{ $nama ?? '-' }}</td>
                <td style="width: 15%;"><b>Kelas</b></td>
                <td style="width: 25%;">: {{ $kelas ?? '-' }}</td>
            </tr>
            <tr>
                <td><b>NIS/NISN</b></td>
                <td>: {{ $nis ?? '-' }} / {{ $nisn ?? '-' }}</td>
                <td><b>Fase</b></td>
                <td>: {{ $fase ?? '-' }}</td>
            </tr>
            <tr>
                <td><b>Sekolah</b></td>
                <td>: {{ $sekolah ?? '-' }}</td>
                <td><b>Semester</b></td>
                <td>: {{ $semester ?? '-' }}</td>
            </tr>
            <tr>
                <td><b>Alamat</b></td>
                <td>: {{ $alamat ?? '-' }}</td>
                <td><b>Tahun Ajaran</b></td>
                <td>: {{ $tahun_ajaran ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ==============================
          2. TABEL NILAI & CATATAN
    =============================== --}}
    <table class="bordered">
        <tbody>
            <tr class="center" style="vertical-align: middle;">
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">Mata Pelajaran</th>
                <th style="width: 8%;">Nilai Akhir</th>
                <th style="width: 57%;">Capaian Kompetensi</th>
            </tr>

            {{-- Kelompok A --}}
            @if(isset($nilai_kelompok_a) && count($nilai_kelompok_a) > 0)
            <tr>
                <td colspan="4"><strong>A. Kelompok Mata Pelajaran</strong></td>
            </tr>
            @foreach($nilai_kelompok_a as $nilai)
            <tr>
                <td class="center">{{ $nilai['no'] }}</td>
                <td>{{ $nilai['mapel'] }}</td>
                <td class="center">{{ $nilai['nilai'] }}</td>
                <td>{{ $nilai['capaian'] }}</td>
            </tr>
            @endforeach
            @endif

            {{-- Kelompok B --}}
            @if(isset($nilai_kelompok_b) && count($nilai_kelompok_b) > 0)
            <tr>
                <td colspan="4"><strong>B. Mata Pelajaran Kejuruan</strong></td>
            </tr>
            @foreach($nilai_kelompok_b as $nilai)
            <tr>
                <td class="center">{{ $nilai['no'] }}</td>
                <td>{{ $nilai['mapel'] }}</td>
                <td class="center">{{ $nilai['nilai'] }}</td>
                <td>{{ $nilai['capaian'] }}</td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>

    {{-- Kokurikuler --}}
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

    {{-- Ekstrakurikuler --}}
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

    {{-- ==============================
          3. KETIDAKHADIRAN & CATATAN WALI KELAS
    =============================== --}}
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <!-- Header Ketidakhadiran -->
            <th colspan="2" class="center" style="width: 49%; border: 1px solid #000; padding: 5px; font-weight: bold;">
                Ketidakhadiran
            </th>
            <th style="width: 2%; border: none;">&nbsp;</th>
            <!-- Header Catatan -->
            <th class="center" style="width: 49%; border: 1px solid #000; padding: 5px; font-weight: bold;">
                Catatan Wali Kelas
            </th>
        </tr>
        <tr>
            <!-- Baris 1 Ketidakhadiran -->
            <td style="width: 24%; border: 1px solid #000; padding: 5px;">Sakit</td>
            <td style="width: 24%; border: 1px solid #000; padding: 5px;">
                {{ $ketidakhadiran['sakit'] ?? 0 }} Hari
            </td>
            <td style="border: none;">&nbsp;</td>
            <!-- Catatan dengan rowspan -->
            <td rowspan="3" style="border: 1px solid #000; padding: 5px; vertical-align: top;">
                {{ $catatan_wali ?? 'Tidak ada catatan.' }}
            </td>
        </tr>
        <tr>
            <!-- Baris 2 Ketidakhadiran -->
            <td style="border: 1px solid #000; padding: 5px;">Izin</td>
            <td style="border: 1px solid #000; padding: 5px;">
                {{ $ketidakhadiran['izin'] ?? 0 }} Hari
            </td>
            <td style="border: none;">&nbsp;</td>
        </tr>
        <tr>
            <!-- Baris 3 Ketidakhadiran -->
            <td style="border: 1px solid #000; padding: 5px;">Tanpa Keterangan</td>
            <td style="border: 1px solid #000; padding: 5px;">
                {{ $ketidakhadiran['tanpa_keterangan'] ?? 0 }} Hari
            </td>
            <td style="border: none;">&nbsp;</td>
        </tr>
    </table>

    {{-- ==============================
          4. TANGGAPAN ORANG TUA
    =============================== --}}
    <table class="bordered" style="margin-top: 10px;">
        <tbody>
            <tr>
                <th class="center"><strong>Tanggapan Orang Tua/Wali Murid</strong></th>
            </tr>
            <tr>
                <td>{{ $tanggapan_ortu ?? 'Tidak ada tanggapan.' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ==============================
          5. TANDA TANGAN
    =============================== --}}
    <table class="no-border" style="margin-top: 20px;">
        <tbody>
            <tr>
                <td style="width: 33%;"></td>
                <td style="width: 33%;"></td>
                <td style="width: 33%;">Rejang Lebong, {{ date('d F Y') }}</td>
            </tr>
            <tr>
                <td>Orang Tua/Wali Murid</td>
                <td>Kepala Sekolah</td>
                <td>Wali Kelas</td>
            </tr>
            <tr style="height: 60px;">
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>({{ $orang_tua ?? '-' }})</td>
                <td>({{ $kepala_sekolah['nama'] ?? '-' }})</td>
                <td>({{ $wali_kelas['nama'] ?? '-' }})</td>
            </tr>
            <tr>
                <td></td>
                <td>NIP. {{ $kepala_sekolah['nip'] ?? '-' }}</td>
                <td>NIP. {{ $wali_kelas['nip'] ?? '-' }}</td>
            </tr>
        </tbody>
    </table>
</div>