<div>
    {{-- FILTER SECTION --}}
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="page-header mb-0 border-bottom">
                                <div class="d-flex align-items-center">
                                    <h5 class="text-dark"><i class="mdi mdi-filter me-2"></i> Filter Data Leger Nilai</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="mb-3 row">
                                <div class="col-sm-4">
                                    <label class="form-label">Tahun Ajaran</label>
                                    <select class="form-select" wire:model.live="tahunAjaranId">
                                        <option value="">-- Pilih Tahun Ajaran --</option>
                                        @foreach($tahunAjaranList as $ta)
                                        <option value="{{ $ta->id }}">{{ $ta->nama }} ({{ ucfirst($ta->status) }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-4">
                                    <label class="form-label">Semester</label>
                                    <select class="form-select" wire:model.live="semesterId" @if(!$tahunAjaranId) disabled @endif>
                                        <option value="">-- Pilih Semester --</option>
                                        @foreach($semesterList as $sem)
                                        <option value="{{ $sem->id }}">{{ $sem->semester->nama }} ({{ ucfirst($sem->status) }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-4">
                                    <label class="form-label">Rombongan Belajar</label>
                                    <select class="form-select" wire:model.live="rombelId" @if(!$semesterId) disabled @endif>
                                        <option value="">-- Pilih Rombel --</option>
                                        @foreach($rombelList as $r)
                                        <option value="{{ $r->id }}">{{ $r->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($rombel)
                    <div class="alert alert-success py-3 mt-3" role="alert">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                        style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-book text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted">Kurikulum</small>
                                        <p class="fw-bold mb-0 text-dark">{{ $rombel->tahunAjaranKurikulum->kurikulum->nama ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                        style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-calendar-clock text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted">Tahun Ajaran</small>
                                        <p class="fw-bold mb-0 text-dark">{{ $rombel->tahunAjaranKurikulum->tahunAjaran->nama ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                        style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-account-group text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted">Rombel</small>
                                        <p class="fw-bold mb-0 text-dark">{{ $rombel->nama }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                        style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-shield-star text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted">Jurusan</small>
                                        <p class="fw-bold mb-0 text-dark">{{ $rombel->jurusan->nama ?? 'Umum' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                        style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-account-tie text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted">Wali Kelas</small>
                                        <p class="fw-bold mb-0 text-dark">{{ $rombel->waliKelas->name ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                        style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-account-multiple text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted">Total Siswa</small>
                                        <p class="fw-bold mb-0 text-dark">{{ count($studentsList) }} Siswa</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- LEGER CONTENT SECTION --}}
    @if($rombelId && !empty($studentsList))
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    {{-- HEADER & BUTTONS --}}
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="page-header pb-3 mb-4 border-bottom">
                                <div class="d-flex justify-content-between align-items-center w-100">
                                    <div class="d-flex align-items-center">
                                        <span class="bg-gradient-primary p-2 rounded-3 shadow-sm me-3 d-inline-flex align-items-center justify-content-center">
                                            <i class="mdi mdi-file-document-box mdi-24px text-white"></i>
                                        </span>
                                        <div>
                                            <h4 class="mb-1 text-dark fw-bold">Leger Kelas</h4>
                                            <small class="text-muted">
                                                {{ $rombel->tahunAjaranKurikulum->tahunAjaran->nama ?? '' }}
                                            </small>
                                        </div>
                                    </div>

                                    @php
                                    // Siapkan nama file di sini agar HTML di bawahnya bersih
                                    $namaRombel = $rombel->nama ?? 'Kelas';
                                    $fileName = "Leger_{$namaRombel}.pdf";
                                    @endphp

                                    <div class="d-flex align-items-center gap-2 ms-auto">
                                        @if($pdfUrl)
                                        {{-- TOMBOL DOWNLOAD --}}
                                        <button
                                            type="button"
                                            onclick="forceDownload('{{ $pdfUrl }}', '{{ $fileName }}')"
                                            class="btn btn-labeled btn-danger text-decoration-none d-inline-flex align-items-center">
                                            <span class="btn-label me-2">
                                                <i class="mdi mdi-file-pdf-box"></i>
                                            </span>
                                            Download PDF
                                        </button>

                                        {{-- TOMBOL CETAK --}}
                                        <button
                                            type="button"
                                            class="btn btn-labeled btn-primary"
                                            onclick="window.open('{{ $pdfUrl }}', '_blank').print()">
                                            <span class="btn-label me-2">
                                                <i class="mdi mdi-printer"></i>
                                            </span>
                                            Cetak
                                        </button>
                                        @else
                                        <button class="btn btn-secondary" disabled>Loading PDF...</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TABEL LEGER PREVIEW --}}
                    <div id="tabel-leger">
                        <div class="bg-white overflow-hidden">
                            <div class="overflow-x-auto table-responsive">
                                <style>
                                    .leger-table {
                                        width: 100%;
                                        border-collapse: collapse;
                                        font-family: Arial, sans-serif;
                                        font-size: 9pt;
                                        min-width: 1200px;
                                    }

                                    .header-content th {
                                        text-align: center;
                                        padding: 6px 4px;
                                        border: 1px solid #000;
                                        font-weight: bold;
                                        background-color: #e5e7eb;
                                        font-size: 8pt;
                                        vertical-align: middle;
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

                                    .data-row td {
                                        border: 1px solid #d1d5db;
                                        padding: 3px 3px;
                                        text-align: center;
                                        font-size: 8pt;
                                    }

                                    .data-row td.text-left {
                                        text-align: left;
                                        padding-left: 5px;
                                    }

                                    .mapel-header-vertical {
                                        writing-mode: vertical-lr;
                                        transform: rotate(180deg);
                                        padding: 10px 2px;
                                        height: 120px;
                                        white-space: nowrap;
                                        font-size: 7pt;
                                    }

                                    .nilai-col {
                                        min-width: 35px;
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
                                        <tr class="header-content">
                                            <th rowspan="2" width="30">NO</th>
                                            <th rowspan="2" width="60">NIS</th>
                                            <th rowspan="2" style="min-width: 200px;">NAMA SISWA</th>
                                            <th rowspan="2" width="80">NISN</th>
                                            <th rowspan="2" width="30">JK</th>

                                            <th colspan="{{ count($mataPelajaranList) }}">MATA PELAJARAN</th>

                                            <th rowspan="2" width="30">KK</th>
                                            <th rowspan="2" width="40">JML</th>
                                            <th rowspan="2" width="40">RATA</th>
                                            <th rowspan="2" width="30">PRKT</th>

                                            <th colspan="3">KEHADIRAN</th>
                                        </tr>

                                        <tr class="header-content">
                                            @foreach ($mataPelajaranList as $m)
                                            <th class="nilai-col">
                                                <div class="mapel-header-vertical">{{ $m['kode'] }}</div>
                                            </th>
                                            @endforeach

                                            <th width="30">S</th>
                                            <th width="30">I</th>
                                            <th width="30">A</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($studentsList as $siswa)
                                        <tr class="data-row">
                                            <td>{{ $siswa['no'] }}</td>
                                            <td>{{ $siswa['nis'] }}</td>
                                            <td class="text-left">{{ $siswa['nama'] }}</td>
                                            <td>{{ $siswa['nisn'] }}</td>
                                            <td>{{ $siswa['jenis_kelamin'] }}</td>

                                            @foreach ($mataPelajaranList as $mapel)
                                            <td>
                                                @if(isset($siswa['nilai_per_mapel'][$mapel['id']]) && $siswa['nilai_per_mapel'][$mapel['id']] > 0)
                                                {{ $siswa['nilai_per_mapel'][$mapel['id']] }}
                                                @else
                                                -
                                                @endif
                                            </td>
                                            @endforeach

                                            <td>{{ $siswa['kokurikuler'] }}</td>
                                            <td>{{ $siswa['jumlah_nilai'] }}</td>
                                            <td>{{ number_format($siswa['rata_rata'], 1, ',', '.') }}</td>
                                            <td><strong>{{ $siswa['peringkat'] }}</strong></td>

                                            <td>{{ $siswa['sakit'] }}</td>
                                            <td>{{ $siswa['izin'] }}</td>
                                            <td>{{ $siswa['tanpa_keterangan'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Footer Info --}}
                    <div class="mt-4 bg-blue-50 border-l-4 border-blue-500 p-4">
                        <p style="font-size: 12px;">
                            <strong>Keterangan:</strong><br>
                            • KK = Kokurikuler (P5) | JML = Jumlah Nilai | RATA = Rata-rata | PRKT = Peringkat<br>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @elseif($rombelId && empty($studentsList))
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning text-center">
                Data siswa atau nilai belum tersedia untuk rombel ini.
            </div>
        </div>
    </div>
    @endif
</div>

{{-- SCRIPT FORCE DOWNLOAD --}}
<script>
    function forceDownload(url, filename) {
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>