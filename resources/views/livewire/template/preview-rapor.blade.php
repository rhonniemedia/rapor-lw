<div class="container py-4">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
        }

        h4 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        th,
        td {
            padding: 6px;
            vertical-align: top;
        }

        .table-bordered,
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #000;
        }

        .text-center {
            text-align: center;
        }

        .mb-4 {
            margin-bottom: 20px;
        }
    </style>

    <h4>LAPORAN HASIL BELAJAR (RAPOR)</h4>

    {{-- ====== Group 1: Identitas Murid ====== --}}
    <div class="mb-4">
        <table>
            <tr>
                <th width="25%">Nama Peserta Didik</th>
                <td>{{ $murid['nama'] }}</td>
            </tr>
            <tr>
                <th>NISN</th>
                <td>{{ $murid['nisn'] }}</td>
            </tr>
            <tr>
                <th>Kelas</th>
                <td>{{ $murid['kelas'] }}</td>
            </tr>
            <tr>
                <th>Semester</th>
                <td>{{ $murid['semester'] }}</td>
            </tr>
            <tr>
                <th>Tahun Pelajaran</th>
                <td>{{ $murid['tahun'] }}</td>
            </tr>
        </table>
    </div>

    {{-- ====== Group 2: Nilai, Kehadiran, Ekstrakurikuler, Catatan ====== --}}
    <div class="mb-4">
        <table class="table-bordered">
            <thead>
                <tr class="text-center">
                    <th width="5%">No</th>
                    <th width="45%">Mata Pelajaran</th>
                    <th width="20%">Nilai</th>
                    <th width="20%">Predikat</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($murid['nilai'] as $i => $n)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $n['mapel'] }}</td>
                    <td class="text-center">{{ $n['nilai'] }}</td>
                    <td class="text-center">{{ $n['predikat'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="table-bordered">
            <tr>
                <th>Kokurikuler</th>
                <td>{{ $murid['kokurikuler'] }}</td>
            </tr>
        </table>

        <table class="table-bordered">
            <thead>
                <tr class="text-center">
                    <th width="5%">No</th>
                    <th width="45%">Ekstrakurikuler</th>
                    <th width="50%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($murid['ekskul'] as $i => $e)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $e['nama'] }}</td>
                    <td>{{ $e['keterangan'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table>
            <tr>
                <th width="40%">Kehadiran</th>
                <td>
                    Sakit: {{ $murid['kehadiran']['sakit'] }} hari,
                    Izin: {{ $murid['kehadiran']['izin'] }} hari,
                    Tanpa Keterangan: {{ $murid['kehadiran']['alpa'] }} hari
                </td>
            </tr>
            <tr>
                <th>Catatan Wali Kelas</th>
                <td>{{ $murid['catatan'] }}</td>
            </tr>
            <tr>
                <th>Tanggapan Orang Tua</th>
                <td>{{ $murid['tanggapan_ortu'] }}</td>
            </tr>
        </table>
    </div>

    {{-- ====== Group 3: Tanda Tangan ====== --}}
    <div class="mb-4 text-center">
        <table>
            <tr>
                <td class="text-center">Orang Tua/Wali</td>
                <td class="text-center">Wali Kelas</td>
            </tr>
            <tr>
                <td colspan="2" style="height: 60px;"></td>
            </tr>
            <tr>
                <td class="text-center">(...................................)</td>
                <td class="text-center">({{ $murid['wali_kelas'] }})</td>
            </tr>
        </table>
    </div>

</div>