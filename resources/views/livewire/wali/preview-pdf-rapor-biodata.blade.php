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
            margin-top: 15mm !important;
            height: 100%;
        }

        .cover {
            width: 100%;
            min-height: 100vh;
            text-align: center;
            padding-top: 25vh;
            /* sesuaikan untuk centering */
            padding-bottom: 25vh;
            box-sizing: border-box;
        }

        .cover-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            width: 100%;
        }

        /* Bagian judul label */
        .cover .name-section p,
        .cover .nis-section p {
            font-weight: bold;
            margin-bottom: 4px;
            font-size: 14px;
        }

        /* Kotak umum */
        .cover .nama-murid,
        .cover .nis-murid {
            width: 380px;
            height: 40px;
            border: 2px solid #333;
            border-radius: 4px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto;
            font-size: 17px;
            font-weight: bold;
            background-color: #fdfdfd;
            color: #000;
            line-height: 32px;
        }

        /* Supaya jarak antarbagian seragam */
        .cover .name-section,
        .cover .nis-section {
            margin-bottom: 12px;
        }

        .cover .name-section p,
        .cover .nis-section p {
            font-size: 16px;
        }

        .cover .header-section {
            font-size: 24px;
        }

        .cover .footer-section {
            margin-top: 50px;
            font-size: 18px;
        }

        .cover .header-section p,
        .cover .footer-section p {
            margin: 0;
            line-height: 1.3;
            font-weight: bold;

        }

        .cover .logo-pemda img {
            width: 120px;
            margin-bottom: 50px;
        }


        .cover .logo-sekolah img {
            width: 140px;
            margin: 50px 0;
        }


        .cover .logo-pemda img,
        .cover .logo-sekolah img {
            /* atur ukuran logo */
            height: auto;
            /* biar proporsional */
            object-fit: contain;
        }

        .cover .logo-pemda,
        .cover .logo-sekolah {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .sekolah {
            text-align: center;
            padding: 10px;
        }

        .sekolah .header-section {
            margin-bottom: 50px;
        }

        .sekolah .header-section p {
            margin: 0;
            line-height: 1.5;
            font-size: 18px;
            font-weight: bold;
        }

        .sekolah .body-section {
            margin: 0;
            line-height: 1.5;
            font-size: 16px;
        }

        .identitas {
            text-align: center;
        }

        .identitas .header-section {
            margin-bottom: 20px;
        }

        .identitas .header-section p {
            margin: 0;
            line-height: 1.5;
            font-size: 18px;
            font-weight: bold;
        }

        .identitas .body-section {
            margin: 0;
            line-height: 1.2;
            font-size: 15px;
        }

        .identitas .photo {
            height: 113px;
            border: 1px solid;
            text-align: center;
            vertical-align: middle;
            font-size: 12px;
        }

        .identitas .signature {
            margin: 20px 0px;
            line-height: 1.2rem;
        }

        /* PERBAIKAN CSS */

        .masuk,
        .keluar {
            /* Gaya untuk container utama */
            text-align: center;
            padding: 10px;
        }

        /* Terapkan gaya ini ke .header-section di dalam .masuk DAN .header-section di dalam .keluar */
        .masuk .header-section,
        .keluar .header-section {
            margin-bottom: 20px;
        }

        /* Terapkan gaya ini ke p di dalam .header-section di dalam .masuk DAN .keluar */
        .masuk .header-section p,
        .keluar .header-section p {
            margin: 0;
            line-height: 1.5;
            font-size: 18px;
            font-weight: bold;
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

        /* Hilangkan border bawah untuk semua baris kecuali header dan baris terakhir */
        .bordered tr:not(:first-child):not(:last-child) td:not(.keep-border) {
            border-bottom: none !important;
        }

        /* Khusus untuk td yang di-rowspan */
        .bordered td.keep-border {
            border: 1px solid black !important;
        }

        .no-border td,
        .no-border th {
            border: none;
        }

        .center {
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }
    </style>

    {{-- ==============================
          1. TABEL COVER
    =============================== --}}
    <div class="cover">
        <div class="cover-content">

            <div class="logo-pemda">
                <img src="{{ public_path('assets/images/logo-bengkulu.png') }}" alt="Logo Pemda Bengkulu">
            </div>

            <div class="header-section">
                <p>SEKOLAH MENENGAH KEJURUAN</p>
                <p>(SMK)</p>
            </div>

            <div class="logo-sekolah">
                <img src="{{ public_path('assets/images/logo-sekolah.png') }}" alt="Logo Sekolah">
            </div>

            <div class="name-section">
                <p>Nama Peserta Didik</p>
                <div class="nama-murid">{{ strtoupper($nama ?? 'N/A') }}</div>
            </div>

            <div class="nis-section">
                <p>NISN / NIS</p>
                <div class="nis-murid">{{ $nisn ?? 'N/A' }} / {{ $nis ?? 'N/A' }}</div>
            </div>

            <div class="footer-section">
                <p>KEMENTERIAN PENDIDIKAN DAN KEBUDAYAAN</p>
                <p>REPUBLIK INDONESIA</p>
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    {{-- ==============================
          2. TABEL IDENTITAS SEKOLAH
    =============================== --}}
    <div class="sekolah">
        <div class="header-section">
            <p>RAPOR</p>
            <p>SEKOLAH MENENGAH KEJURUAN (SMK)</p>
        </div>

        <div>
            <table class="body-section" style="padding: 2rem;">
                <tr>
                    <td>Nama Sekolah</td>
                    <td>:</td>
                    <td><strong>{{ strtoupper($sekolah['nama_sekolah'] ?? '-') }}</strong></td>
                </tr>
                <tr>
                    <td>NPSN</td>
                    <td>:</td>
                    <td>{{ $sekolah['npsn'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>NSS</td>
                    <td>:</td>
                    <td>{{ $sekolah['nss'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Alamat Sekolah</td>
                    <td>:</td>
                    <td>{{ $sekolah['alamat'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Kode Pos</td>
                    <td>:</td>
                    <td>{{ $sekolah['kode_pos'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Telepon</td>
                    <td>:</td>
                    <td>{{ $sekolah['telepon'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Kelurahan / Desa</td>
                    <td>:</td>
                    <td>{{ $sekolah['kelurahan'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Kecamatan</td>
                    <td>:</td>
                    <td>{{ $sekolah['kecamatan'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Kota/Kabupaten</td>
                    <td>:</td>
                    <td>{{ $sekolah['kota_kabupaten'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Provinsi</td>
                    <td>:</td>
                    <td>{{ $sekolah['provinsi'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Website</td>
                    <td>:</td>
                    <td>{{ $sekolah['website'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>E-mail</td>
                    <td>:</td>
                    <td>{{ $sekolah['email'] ?? '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="page-break"></div>

    {{-- ==============================
          3. TABEL IDENTITAS PELAJAR
    =============================== --}}
    <div class="identitas">
        <div class="header-section">
            <p>IDENTITAS PESERTA DIDIK</p>
        </div>

        <div class="body-section">
            <table class="section-identitas">
                <tr>
                    <td style="width: 3%;">1.</td>
                    <td colspan="2">Nama Lengkap Peserta Didik</td>
                    <td style="width: 3%;">:</td>
                    <td><strong>{{ strtoupper($nama ?? 'N/A') }}</strong></td>
                </tr>
                <tr>
                    <td>2.</td>
                    <td colspan="2">Nomor Induk/NISN</td>
                    <td>:</td>
                    <td>{{ $nis ?? 'N/A' }} / {{ $nisn ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>3.</td>
                    <td colspan="2">Tempat, Tanggal Lahir</td>
                    <td>:</td>
                    <td>{{ $tempat_lahir ?? 'N/A' }}, {{ \Carbon\Carbon::parse($tanggal_lahir ?? now())->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td>4.</td>
                    <td colspan="2">Jenis Kelamin</td>
                    <td>:</td>
                    <td>{{ ($jenis_kelamin ?? 'L') == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                </tr>
                <tr>
                    <td>5.</td>
                    <td colspan="2">Agama</td>
                    <td>:</td>
                    <td>{{ ucfirst($agama ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td>6.</td>
                    <td colspan="2">Status dalam Keluarga</td>
                    <td>:</td>
                    <td>{{ ucwords(str_replace('-', ' ', $status_dalam_keluarga ?? 'N/A')) }}</td>
                </tr>
                <tr>
                    <td>7.</td>
                    <td colspan="2">Anak ke</td>
                    <td>:</td>
                    <td>{{ $anak_ke ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>8.</td>
                    <td colspan="2">Alamat Peserta Didik</td>
                    <td>:</td>
                    <td>{{ $alamat ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>9.</td>
                    <td colspan="2">Nomor Telepon Rumah</td>
                    <td>:</td>
                    <td>{{ $telepon ?? '-' }}</td>
                </tr>
                <tr>
                    <td>10.</td>
                    <td colspan="2">Sekolah Asal</td>
                    <td>:</td>
                    <td>{{ $sekolah_asal ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td rowspan="3">11.</td>
                    <td colspan="2">Diterima di SMA ini</td>
                    <td>:</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="2">Di kelas</td>
                    <td>:</td>
                    <td>{{ $diterima_di_kelas ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td colspan="2">Pada tanggal</td>
                    <td>:</td>
                    <td>{{ $pada_tanggal ? \Carbon\Carbon::parse($pada_tanggal)->translatedFormat('d F Y') : 'N/A' }}</td>
                </tr>
                <tr>
                    <td rowspan="3">12.</td>
                    <td colspan="4">Nama Orang Tua</td>
                </tr>
                <tr>
                    <td style="width: 3%;">a.</td>
                    <td style="width: 30%;">Ayah</td>
                    <td>:</td>
                    <td style="width: 64%;">{{ $ayah['nama'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>b.</td>
                    <td>Ibu</td>
                    <td>:</td>
                    <td>{{ $ibu['nama'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td rowspan="2">13.</td>
                    <td colspan="2">Alamat Orang Tua</td>
                    <td>:</td>
                    <td>{{ $ayah['alamat'] ?? ($ibu['alamat'] ?? $alamat ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td colspan="2">Nomor Telepon</td>
                    <td>:</td>
                    <td>{{ $ayah['telepon'] ?? ($ibu['telepon'] ?? '-') }}</td>
                </tr>
                <tr>
                    <td rowspan="3">14.</td>
                    <td colspan="4">Pekerjaan Orang Tua</td>
                </tr>
                <tr>
                    <td>a.</td>
                    <td>Ayah</td>
                    <td>:</td>
                    <td>{{ ucwords(str_replace(['-', '_'], ' ', $ayah['pekerjaan'] ?? 'N/A')) }}</td>
                </tr>
                <tr>
                    <td>b.</td>
                    <td>Ibu</td>
                    <td>:</td>
                    <td>{{ ucwords(str_replace(['-', '_'], ' ', $ibu['pekerjaan'] ?? 'N/A')) }}</td>
                </tr>
                <tr>
                    <td rowspan="5">15.</td>
                    <td colspan="4">Wali Peserta Didik</td>
                </tr>
                <tr>
                    <td>a.</td>
                    <td>Nama Wali</td>
                    <td>:</td>
                    <td>{{ $wali['nama'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>b.</td>
                    <td>Nomor Telepon</td>
                    <td>:</td>
                    <td>{{ $wali['telepon'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>c.</td>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ $wali['alamat'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td>d.</td>
                    <td>Pekerjaan</td>
                    <td>:</td>
                    <td>{{ $wali['pekerjaan'] ?? '-' }}</td>
                </tr>
            </table>

            <table>
                <tr style="height: 100%;">
                    <td style="width: 55%; vertical-align: middle; text-align: center;">
                        <table style="margin: 0 auto; margin-right: 2cm; border-collapse: collapse; width: 103px;">
                            <tr>
                                <td class="photo">
                                    Pas Foto<br>
                                    3 x 4
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 45%;">
                        <div class="signature">
                            <div>{{ $sekolah['kota_kabupaten'] ?? '-' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                            <div>Kepala Sekolah,</div>
                            <br><br><br><br>
                            <div><strong>{{ $kepala_sekolah['nama'] ?? 'N/A' }}</strong></div>
                            <div>NIP {{ $kepala_sekolah['nip'] ?? 'N/A' }}</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

    </div>

    <div class="page-break"></div>

    {{-- ==============================
          4. TABEL PELAJAR KELUAR
    =============================== --}}

    <div class="keluar">

        <div class="header-section">
            <p>KETERANGAN PINDAH SEKOLAH</p>
        </div>

        <div class="body-section">
            <table>
                <tr>
                    <td>Nama Peserta Didik</td>
                    <td>:</td>
                    <td>{{ strtoupper($nama ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td>Nomor Induk</td>
                    <td>:</td>
                    <td>{{ $nis ?? 'N/A' }}</td>

                </tr>
            </table>
        </div>

        <table class="bordered">
            <tr>
                <th colspan="4">
                    KELUAR
                </th>
            </tr>
            <tr>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 15%;">Kelas yang ditinggalkan</th>
                <th style="width: 35%;">Sebab-sebab Keluar atau Atas Permintaan (Tertulis)</th>
                <th style="width: 40%;">Tanda Tangan Kepala Sekolah, Stempel Sekolah, dan Tanda Tangan Orang Tua/Wali</th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>
                    <div style="margin-top: 10px; margin-bottom: 10px;">
                        .............................,...............
                        <br>
                        Kepala Sekolah,
                        <br><br><br><br>
                        <span class="underline">.............................................</span>
                        <br>
                        NIP.
                        <br><br>
                        Orang Tua/Wali,
                        <br><br><br><br>
                        <span class="underline">.............................................</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>
                    <div style="margin-top: 10px; margin-bottom: 10px;">
                        .............................,...............
                        <br>
                        Kepala Sekolah,
                        <br><br><br><br>
                        <span class="underline">.............................................</span>
                        <br>
                        NIP.
                        <br><br>
                        Orang Tua/Wali,
                        <br><br><br><br>
                        <span class="underline">.............................................</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>
                    <div style="margin-top: 10px; margin-bottom: 10px;">
                        .............................,...............
                        <br>
                        Kepala Sekolah,
                        <br><br><br><br>
                        <span class="underline">.............................................</span>
                        <br>
                        NIP.
                        <br><br>
                        Orang Tua/Wali,
                        <br><br><br><br>
                        <span class="underline">.............................................</span>
                    </div>
                </td>
            </tr>

        </table>

    </div>

    <div class="page-break"></div>

    {{-- ==============================
          4. TABEL PELAJAR MASUK
    =============================== --}}

    <div class="masuk">

        <div class="header-section">
            <p>KETERANGAN PINDAH SEKOLAH</p>
        </div>

        <div class="body-section">
            <table>
                <tr>
                    <td>Nama Peserta Didik</td>
                    <td>:</td>
                    <td>{{ strtoupper($nama ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td>Nomor Induk</td>
                    <td>:</td>
                    <td>{{ $nis ?? 'N/A' }}</td>

                </tr>
            </table>
        </div>

        <table class="bordered">
            <tr>
                <th>NO</th>
                <th colspan="3">MASUK</th>
            </tr>
            <tr>
                <td style="width: 5%; padding: 0;">
                    <table style="border: none; width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px; text-align: center;">1.</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px; text-align: center;">2.</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px; text-align: center;">3.</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px; text-align: center;">4.</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 6px; text-align: center;">5.</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 25%; padding: 0; border-left: 1px solid black;">
                    <table style="border: none; width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">Nama Siswa</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">Nomor Induk</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">Nama Sekolah</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">Masuk di Sekolah ini:</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">a. Tanggal</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">b. Di kelas</td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 6px;">Tahun Ajaran</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 35%; padding: 0; border-left: 1px solid black;">
                    <table style="border: none; width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">_______________________________</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">_______________________________</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">_______________________________</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">_______________________________</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">_______________________________</td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 6px;">_______________________________</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 35%; padding: 10px; vertical-align: top; border-left: 1px solid black;">
                    .............................................
                    <br><br>
                    Kepala Sekolah,
                    <br><br><br><br><br>
                    .............................................
                    <br>
                    NIP.
                </td>
            </tr>
            <tr>
                <td style="width: 5%; padding: 0;">
                    <table style="border: none; width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px; text-align: center;">1.</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px; text-align: center;">2.</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px; text-align: center;">3.</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px; text-align: center;">4.</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 6px; text-align: center;">5.</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 25%; padding: 0; border-left: 1px solid black;">
                    <table style="border: none; width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">Nama Siswa</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">Nomor Induk</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">Nama Sekolah</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">Masuk di Sekolah ini:</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">a. Tanggal</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">b. Di kelas</td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 6px;">Tahun Ajaran</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 35%; padding: 0; border-left: 1px solid black;">
                    <table style="border: none; width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">_______________________________</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">_______________________________</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">_______________________________</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">_______________________________</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">_______________________________</td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 6px;">_______________________________</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 35%; padding: 10px; vertical-align: top; border-left: 1px solid black;">
                    .............................................
                    <br><br>
                    Kepala Sekolah,
                    <br><br><br><br><br>
                    .............................................
                    <br>
                    NIP.
                </td>
            </tr>
            <tr>
                <td style="width: 5%; padding: 0;">
                    <table style="border: none; width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px; text-align: center;">1.</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px; text-align: center;">2.</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px; text-align: center;">3.</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px; text-align: center;">4.</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 6px; text-align: center;">5.</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 25%; padding: 0; border-left: 1px solid black;">
                    <table style="border: none; width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">Nama Siswa</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">Nomor Induk</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">Nama Sekolah</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">Masuk di Sekolah ini:</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">a. Tanggal</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">b. Di kelas</td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 6px;">Tahun Ajaran</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 35%; padding: 0; border-left: 1px solid black;">
                    <table style="border: none; width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">_______________________________</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">_______________________________</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">_______________________________</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">_______________________________</td>
                        </tr>
                        <tr>
                            <td style="border: none; border-bottom: 0px solid black; padding: 6px;">_______________________________</td>
                        </tr>
                        <tr>
                            <td style="border: none; padding: 6px;">_______________________________</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 35%; padding: 10px; vertical-align: top; border-left: 1px solid black;">
                    .............................................
                    <br><br>
                    Kepala Sekolah,
                    <br><br><br><br><br>
                    .............................................
                    <br>
                    NIP.
                </td>
            </tr>
        </table>

    </div>
</div>