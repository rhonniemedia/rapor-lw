<div>
    {{-- Page Header --}}
    <div class="page-header pb-3 mb-4 border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="icon-wrapper position-relative">
                    <span class="bg-gradient-primary p-2 rounded-3 shadow-sm me-3 d-inline-flex align-items-center justify-content-center">
                        <i class="mdi mdi-school mdi-24px text-white"></i>
                    </span>
                </div>
                <div>
                    <h4 class="mb-1 text-dark fw-bold">Kelas Binaan</h4>
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-muted">Daftar Pelajar yang Diampu</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Rombel Card --}}
    <div class="card border-success shadow-sm mb-4">
        <div class="card-body">
            <div class="row">

                <!-- Kolom 1 -->
                <div class="col-md-4">
                    <!-- Kurikulum -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                            style="width: 36px; height: 36px;">
                            <i class="mdi mdi-book text-white fs-5"></i>
                        </div>
                        <div class="ms-3 d-flex flex-column justify-content-center">
                            <small class="text-muted lh-2">Kurikulum</small>
                            <p class="font-weight-medium mb-0 text-dark lh-sm">
                                {{ $rombel->tahunAjaranKurikulum->kurikulum->nama ?? 'Global' }}
                            </p>
                        </div>
                    </div>

                    <!-- Tahun Ajaran -->
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                            style="width: 36px; height: 36px;">
                            <i class="mdi mdi-calendar-clock text-white fs-5"></i>
                        </div>
                        <div class="ms-3 d-flex flex-column justify-content-center">
                            <small class="text-muted lh-2">Tahun Ajaran</small>
                            <p class="font-weight-medium mb-0 text-dark lh-sm">
                                {{ $rombel->tahunAjaranKurikulum->tahunAjaran->nama ?? 'N/A' }} ~
                                @php
                                $semesterAktif = \App\Models\TahunAjaranSemester::where('status', 'aktif')
                                ->with('semester')
                                ->first();
                                @endphp
                                {{ $semesterAktif->semester->nama ?? 'Belum Ada' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Kolom 2 -->
                <div class="col-md-4">
                    <!-- Rombel -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                            style="width: 36px; height: 36px;">
                            <i class="mdi mdi-google-classroom text-white fs-5"></i>
                        </div>
                        <div class="ms-3 d-flex flex-column justify-content-center">
                            <small class="text-muted lh-2">Kelas & Jurusan</small>
                            <p class="font-weight-medium mb-0 text-dark lh-sm">
                                {{ $rombel->tingkat ?? '-' }} {{ $rombel->jurusan->alias ?? 'Umum' }} {{ $rombel->nomor ?? '' }}
                            </p>
                        </div>
                    </div>

                    <!-- Wali Kelas -->
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                            style="width: 36px; height: 36px;">
                            <i class="mdi mdi-account-tie text-white fs-5"></i>
                        </div>
                        <div class="ms-3 d-flex flex-column justify-content-center">
                            <small class="text-muted lh-2">Wali Kelas</small>
                            <p class="font-weight-medium mb-0 text-dark lh-sm">
                                {{ $rombel->waliKelas->name ?? 'Belum Ditentukan' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Kolom 3 -->
                <div class="col-md-4">
                    <!-- Total Siswa -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                            style="width: 36px; height: 36px;">
                            <i class="mdi mdi-account-group text-white fs-5"></i>
                        </div>
                        <div class="ms-3 d-flex flex-column justify-content-center">
                            <small class="text-muted lh-2">Total Peserta</small>
                            <p class="font-weight-medium mb-0 text-dark lh-sm">
                                {{ $stats['total_siswa'] }} Pelajar Aktif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}

    {{-- Search & Pagination Controls --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted">Tampilkan</span>
            <select class="form-select form-select-sm" wire:model.live="perPage" style="width: auto;">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span class="text-muted">data</span>
        </div>

        <div class="input-group" style="width: 300px;">
            <input
                type="text"
                class="form-control"
                placeholder="Cari nama atau nomor induk..."
                wire:model.live.debounce.500ms="search">
        </div>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 30%;">
                        <p class="mb-0">Peserta Didik</p>
                        <small>Nama | Jenis Kelamin</small>
                    </th>
                    <th style="width: 30%;">
                        <p class="mb-0">Kelahiran</p>
                        <small>Tempat | Tanggal Lahir</small>
                    </th>
                    <th style="width: 30%;">
                        <p class="mb-0">Nomor Induk</p>
                        <small>Sekolah | Siswa Nasional</small>
                    </th>
                    <th style="width: 10%;">
                        <p class="mb-0">Aksi</p>
                        <small>Detail | Delete</small>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pelajars as $index => $item)
                <tr>
                    {{-- Info Siswa --}}
                    <td>
                        <div class="d-flex align-items-center">
                            <img
                                src="{{ $item->pelajar->icon ?? asset('assets/images/default-avatar.png') }}"
                                alt="avatar"
                                class="rounded-circle me-3"
                                style="width: 40px; height: 40px; object-fit: cover;">
                            <div>
                                <p class="mb-0 font-weight-medium">{{ $item->pelajar->nama_lengkap ?? '-' }}</p>
                                <small class="text-muted">
                                    <i class="mdi mdi-{{ $item->pelajar->jenis_kelamin === 'L' ? 'gender-male' : 'gender-female' }}"></i>
                                    {{ $item->pelajar->jenis_kelamin_label ?? 'N/A' }}
                                </small>
                            </div>
                        </div>
                    </td>

                    {{-- Tempat, Tanggal Lahir --}}
                    <td>
                        <p class="mb-0">{{ $item->pelajar->tempat_lahir ?? '-' }}</p>
                        <small class="text-muted">{{ $item->pelajar->tanggal_lahir_formatted ?? '-' }}</small>
                    </td>

                    {{-- Nomor Induk --}}
                    <td>
                        <div class="d-flex gap-1">
                            <span class="badge badge-inverse-dark d-flex align-items-center">
                                <strong>{{ $item->pelajar->nomor_induk ?? 'N/A' }}</strong>
                            </span>
                            <span class="badge badge-inverse-primary d-flex align-items-center">
                                <strong>{{ $item->pelajar->nisn ?? 'N/A' }}</strong>
                            </span>
                        </div>
                    </td>

                    {{-- Aksi --}}
                    <td>
                        <button type="button"
                            class="btn btn-sm btn-outline-info"
                            wire:click="openDetailModal('{{ $item->pelajar->id }}')"
                            title="Detail Data">
                            <i class="mdi mdi-eye"></i>
                        </button>
                        <button type="button"
                            class="btn btn-sm btn-outline-warning"
                            wire:click="openEditModal('{{ $item->pelajar->id }}')"
                            title="Edit Data">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <div class="text-muted">
                            <i class="mdi mdi-information-outline mdi-24px"></i>
                            <p class="mb-0 mt-2">
                                @if(!empty($search))
                                Tidak ada data siswa yang sesuai dengan pencarian "{{ $search }}"
                                @else
                                Belum ada siswa yang terdaftar di kelas ini
                                @endif
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $pelajars->links() }}
    </div>

    {{-- Modal Detail Modifikasi --}}
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header align-items-start">
                    <div class="d-flex flex-column align-items-start">
                        <h4 class="mb-0">Detail Pelajar</h4>
                        <p class="text-muted mb-0"><small>Informasi lengkap data Pelajar</small></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <ul class="nav nav-tabs nav-fill mb-3 gap-2" id="detailTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="tab-button nav-link active" id="pelajar-tab" data-bs-toggle="tab" data-bs-target="#tab-pelajar" type="button" role="tab" aria-controls="tab-pelajar" aria-selected="true">
                                <i class="mdi mdi-school me-2"></i> Data Pelajar
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="tab-button nav-link" id="orangtua-tab" data-bs-toggle="tab" data-bs-target="#tab-orangtua" type="button" role="tab" aria-controls="tab-orangtua" aria-selected="false">
                                <i class="mdi mdi-account me-2"></i> Data Keluarga
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content" id="detailTabContent">

                        <div class="tab-pane fade show active" id="tab-pelajar" role="tabpanel" aria-labelledby="pelajar-tab">

                            <div class="card shadow-sm personal-details-card mb-4">
                                <div class="card-header-modifikasi">
                                    <h5 class="card-header-title">
                                        <i class="mdi mdi-account-card-details-outline me-2"></i> Identitas Pelajar
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="personal-details-list">
                                        <div class="personal-detail-item">
                                            <span class="detail-label">Nama Lengkap</span>
                                            <span class="detail-value">{{ $selectedStudent?->nama_lengkap ?? '-' }}</span>
                                        </div>
                                        <div class="personal-detail-item">
                                            <span class="detail-label">Nomor Induk (NIS)</span>
                                            <span class="detail-value">{{ $selectedStudent?->nomor_induk ?? '-' }}</span>
                                        </div>
                                        <div class="personal-detail-item">
                                            <span class="detail-label">NISN</span>
                                            <span class="detail-value">{{ $selectedStudent?->nisn ?? '-' }}</span>
                                        </div>
                                        <div class="personal-detail-item">
                                            <span class="detail-label">Jenis Kelamin</span>
                                            <span class="detail-value">
                                                @if($selectedStudent?->jenis_kelamin == 'L')
                                                Laki-laki
                                                @elseif($selectedStudent?->jenis_kelamin == 'P')
                                                Perempuan
                                                @else
                                                -
                                                @endif
                                            </span>
                                        </div>
                                        <div class="personal-detail-item">
                                            <span class="detail-label">Tempat & Tanggal Lahir</span>
                                            <span class="detail-value">
                                                {{ $selectedStudent?->tempat_lahir ?? '-' }},
                                                {{ $selectedStudent?->tanggal_lahir_formatted ?? '-' }}
                                            </span>
                                        </div>
                                        <div class="personal-detail-item">
                                            <span class="detail-label">Agama</span>
                                            <span class="detail-value">
                                                {{-- ✅ CARA 1: Menggunakan helper enum_label --}}
                                                {{ enum_label('agama', $selectedStudent?->agama) }}
                                            </span>
                                        </div>
                                        <div class="personal-detail-item">
                                            <span class="detail-label">Status Keluarga</span>
                                            <span class="detail-value">
                                                @php
                                                $status = $selectedStudent?->status_dalam_keluarga ?? '-';
                                                // Mengganti underscore/hyphen dengan spasi, lalu mengkapitalkan setiap kata
                                                $formattedStatus = ($status !== '-')
                                                ? ucwords(str_replace(['_', '-'], ' ', $status))
                                                : '-';
                                                @endphp

                                                {{ $formattedStatus }}
                                                (Anak ke {{ $selectedStudent?->anak_ke ?? '-' }})
                                            </span>
                                        </div>
                                        <div class="personal-detail-item">
                                            <span class="detail-label">Telepon Pelajar</span>
                                            <span class="detail-value">{{ $selectedStudent?->telepon ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm personal-details-card mb-4">
                                <div class="card-header-modifikasi">
                                    <h5 class="card-header-title">
                                        <i class="mdi mdi-map-marker-outline me-2"></i> Alamat Domisili
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="personal-details-list">
                                        <div class="personal-detail-item">
                                            <span class="detail-label">Alamat Lengkap</span>
                                            <span class="detail-value">{{ $selectedStudent?->alamat ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card shadow-sm personal-details-card">
                                <div class="card-header-modifikasi">
                                    <h5 class="card-header-title">
                                        <i class="mdi mdi-school-outline me-2"></i> Informasi Sekolah
                                    </h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="personal-details-list">
                                        <div class="personal-detail-item">
                                            <span class="detail-label">Sekolah Asal</span>
                                            <span class="detail-value">{{ $selectedStudent?->sekolah_asal ?? '-' }}</span>
                                        </div>
                                        <div class="personal-detail-item">
                                            <span class="detail-label">Diterima di Kelas</span>
                                            <span class="detail-value">{{ $selectedStudent?->diterima_di_kelas ?? '-' }}</span>
                                        </div>
                                        <div class="personal-detail-item">
                                            <span class="detail-label">Pada Tanggal</span>
                                            <span class="detail-value">
                                                {{ $selectedStudent?->pada_tanggal_formatted ?? '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="tab-pane fade" id="tab-orangtua" role="tabpanel" aria-labelledby="orangtua-tab">
                            <div class="row g-4">

                                @php
                                $ayah = $selectedStudent?->orangTuaWalis->where('hubungan', 'ayah')->first();
                                $ibu = $selectedStudent?->orangTuaWalis->where('hubungan', 'ibu')->first();
                                $wali = $selectedStudent?->orangTuaWalis->where('hubungan', 'wali')->first();
                                @endphp

                                <div class="col-12">
                                    <div class="card shadow-sm personal-details-card h-100">
                                        <div class="card-header-modifikasi">
                                            <h5 class="card-header-title">
                                                <i class="mdi mdi-account-tie me-2"></i> Ayah
                                            </h5>
                                        </div>
                                        <div class="card-body p-4">
                                            @if($ayah)
                                            <div class="personal-details-list">
                                                <div class="personal-detail-item">
                                                    <span class="detail-label">Nama</span>
                                                    <span class="detail-value">{{ $ayah->nama ?? '-' }}</span>
                                                </div>
                                                <div class="personal-detail-item">
                                                    <span class="detail-label">Pekerjaan</span>
                                                    <span class="detail-value">
                                                        {{-- ✅ CARA 2: Inline config --}}
                                                        {{ config("enums.pekerjaan.{$ayah->pekerjaan}", '-') }}
                                                    </span>
                                                </div>
                                                <div class="personal-detail-item">
                                                    <span class="detail-label">Telepon</span>
                                                    <span class="detail-value">{{ $ayah->telepon ?? '-' }}</span>
                                                </div>
                                                <div class="personal-detail-item">
                                                    <span class="detail-label">Alamat</span>
                                                    <span class="detail-value">{{ $ayah->alamat ?? '-' }}</span>
                                                </div>
                                            </div>
                                            @else
                                            <p class="text-muted">Data ayah belum tersedia</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="card shadow-sm personal-details-card h-100">
                                        <div class="card-header-modifikasi">
                                            <h5 class="card-header-title">
                                                <i class="mdi mdi-account-heart me-2"></i> Ibu
                                            </h5>
                                        </div>
                                        <div class="card-body p-4">
                                            @if($ibu)
                                            <div class="personal-details-list">
                                                <div class="personal-detail-item">
                                                    <span class="detail-label">Nama</span>
                                                    <span class="detail-value">{{ $ibu->nama ?? '-' }}</span>
                                                </div>
                                                <div class="personal-detail-item">
                                                    <span class="detail-label">Pekerjaan</span>
                                                    <span class="detail-value">
                                                        {{-- ✅ CARA 2: Inline config --}}
                                                        {{ config("enums.pekerjaan.{$ibu->pekerjaan}", '-') }}
                                                    </span>
                                                </div>
                                                <div class="personal-detail-item">
                                                    <span class="detail-label">Telepon</span>
                                                    <span class="detail-value">{{ $ibu->telepon ?? '-' }}</span>
                                                </div>
                                                <div class="personal-detail-item">
                                                    <span class="detail-label">Alamat</span>
                                                    <span class="detail-value">{{ $ibu->alamat ?? '-' }}</span>
                                                </div>
                                            </div>
                                            @else
                                            <p class="text-muted">Data ibu belum tersedia</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="card shadow-sm personal-details-card h-100">
                                        <div class="card-header-modifikasi">
                                            <h5 class="card-header-title">
                                                <i class="mdi mdi-account-group-outline me-2"></i> Wali
                                            </h5>
                                        </div>
                                        <div class="card-body p-4">
                                            @if($wali)
                                            <div class="personal-details-list">
                                                <div class="personal-detail-item">
                                                    <span class="detail-label">Nama</span>
                                                    <span class="detail-value">{{ $wali->nama ?? '-' }}</span>
                                                </div>
                                                <div class="personal-detail-item">
                                                    <span class="detail-label">Pekerjaan</span>
                                                    <span class="detail-value">
                                                        {{-- ✅ CARA 2: Inline config --}}
                                                        {{ config("enums.pekerjaan.{$wali->pekerjaan}", '-') }}
                                                    </span>
                                                </div>
                                                <div class="personal-detail-item">
                                                    <span class="detail-label">Telepon</span>
                                                    <span class="detail-value">{{ $wali->telepon ?? '-' }}</span>
                                                </div>
                                                <div class="personal-detail-item">
                                                    <span class="detail-label">Alamat</span>
                                                    <span class="detail-value">{{ $wali->alamat ?? '-' }}</span>
                                                </div>
                                            </div>
                                            @else
                                            <p class="text-muted">Data wali belum tersedia</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-labeled btn-outline-secondary me-2"
                        data-bs-dismiss="modal">
                        <span class="btn-label">
                            <i class="mdi mdi-close-outline"></i>
                        </span>
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal Edit (Menggunakan kelas Bootstrap standar) --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content"> {{-- Dihapus: border-0 shadow --}}

                {{-- MODAL HEADER (Menggunakan kelas standar Bootstrap) --}}
                <div class="modal-header bg-warning"> {{-- Dihapus: text-dark rounded-top (text-dark diasumsikan default) --}}
                    <h5 class="modal-title" id="editModalLabel">
                        {{-- Ikon MDI masih dipertahankan karena tidak termasuk kustomisasi layout Bootstrap --}}
                        <i class="mdi mdi-pencil-circle me-2"></i>Edit Data Pelajar
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form wire:submit.prevent="saveStudent" id="editStudentForm">
                        @if($selectedStudent)

                        {{-- Tab Navigation (Menggunakan kelas nav-tabs dan nav-link standar) --}}

                        <ul class="nav nav-tabs nav-fill mb-3 gap-2" id="detailTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="tab-button nav-link active" id="edit-pelajar-tab" data-bs-toggle="tab" data-bs-target="#edit-pelajar" type="button" role="tab" aria-controls="tab-pelajar" aria-selected="true">
                                    <i class="mdi mdi-school me-2"></i> Data Pelajar
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="tab-button nav-link" id="orangtua-tab" data-bs-toggle="tab" data-bs-target="#edit-orangtua" type="button" role="tab" aria-controls="tab-orangtua" aria-selected="false">
                                    <i class="mdi mdi-account me-2"></i> Data Keluarga
                                </button>
                            </li>
                        </ul>


                        {{-- Tab Content --}}
                        <div class="tab-content" id="editTabContent"> {{-- Dihapus: p-4 --}}

                            {{-- Tab Data Pelajar --}}
                            <div class="tab-pane fade show active" id="edit-pelajar" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        {{-- Dihapus: fw-medium --}}
                                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" wire:model="studentData.nama_lengkap"
                                            class="form-control @error('studentData.nama_lengkap') is-invalid @enderror"
                                            placeholder="Masukkan nama lengkap">
                                        @error('studentData.nama_lengkap')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nomor Induk</label>
                                        <input type="text" wire:model="studentData.nomor_induk"
                                            class="form-control @error('studentData.nomor_induk') is-invalid @enderror"
                                            placeholder="Masukkan nomor induk">
                                        @error('studentData.nomor_induk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">NISN</label>
                                        <input type="text" wire:model="studentData.nisn"
                                            class="form-control @error('studentData.nisn') is-invalid @enderror"
                                            maxlength="10" placeholder="Masukkan NISN">
                                        @error('studentData.nisn')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Jenis Kelamin</label>
                                        <select wire:model="studentData.jenis_kelamin"
                                            class="form-select @error('studentData.jenis_kelamin') is-invalid @enderror">
                                            <option value="">-- Pilih Jenis Kelamin --</option>
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                        @error('studentData.jenis_kelamin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tempat Lahir</label>
                                        <input type="text" wire:model="studentData.tempat_lahir"
                                            class="form-control @error('studentData.tempat_lahir') is-invalid @enderror"
                                            placeholder="Masukkan tempat lahir">
                                        @error('studentData.tempat_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Lahir</label>

                                        {{-- ✅ Input type="date" butuh format Y-m-d --}}
                                        <input type="date"
                                            wire:model="studentData.tanggal_lahir"
                                            class="form-control @error('studentData.tanggal_lahir') is-invalid @enderror">

                                        @error('studentData.tanggal_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Agama</label>
                                        <select wire:model="studentData.agama"
                                            class="form-select @error('studentData.agama') is-invalid @enderror">
                                            {{-- ✅ CARA 1: Menggunakan helper enum_options --}}
                                            @foreach(enum_options('agama', true) as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('studentData.agama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Status Dalam Keluarga</label>
                                        <input
                                            type="text"
                                            {{-- Hapus wire:model karena input ini menjadi readonly dan tidak boleh di-edit --}}
                                            value="{{ ucwords(str_replace('-', ' ', $studentData['status_dalam_keluarga'] ?? '')) }}"
                                            class="form-control"
                                            readonly {{-- Menambahkan atribut readonly --}}>
                                        {{-- Karena ini readonly, error validation Livewire biasanya tidak diperlukan --}}
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Anak Ke</label>
                                        <input type="number" wire:model="studentData.anak_ke"
                                            class="form-control @error('studentData.anak_ke') is-invalid @enderror"
                                            min="1" placeholder="Contoh: 1">
                                        @error('studentData.anak_ke')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Telepon</label>
                                        <input type="text" wire:model="studentData.telepon"
                                            class="form-control @error('studentData.telepon') is-invalid @enderror"
                                            placeholder="Masukkan nomor telepon">
                                        @error('studentData.telepon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Alamat</label>
                                        <textarea wire:model="studentData.alamat"
                                            class="form-control @error('studentData.alamat') is-invalid @enderror"
                                            rows="3" placeholder="Masukkan alamat lengkap"></textarea>
                                        @error('studentData.alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Sekolah Asal</label>
                                        <input type="text" wire:model="studentData.sekolah_asal"
                                            class="form-control @error('studentData.sekolah_asal') is-invalid @enderror"
                                            placeholder="Masukkan sekolah asal">
                                        @error('studentData.sekolah_asal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Diterima di Kelas</label>
                                        <input type="text" wire:model="studentData.diterima_di_kelas"
                                            class="form-control @error('studentData.diterima_di_kelas') is-invalid @enderror"
                                            placeholder="Contoh: X IPA 1">
                                        @error('studentData.diterima_di_kelas')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Pada Tanggal</label>
                                        <input type="date" wire:model="studentData.pada_tanggal"
                                            class="form-control @error('studentData.pada_tanggal') is-invalid @enderror">
                                        @error('studentData.pada_tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Tab Data Orang Tua --}}
                            <div class="tab-pane fade" id="edit-orangtua" role="tabpanel">
                                {{-- Data Ayah --}}
                                <div class="card mb-4"> {{-- Dihapus: border-0 shadow-sm --}}
                                    <div class="card-header bg-light"> {{-- Dihapus: d-flex align-items-center --}}
                                        {{-- Ikon dan Judul diletakkan dalam satu h6 untuk kesederhanaan --}}
                                        <h6 class="mb-0"><i class="mdi mdi-account-tie me-2 text-primary"></i>Data Ayah</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Nama</label>
                                                <input type="text" wire:model="ayahData.nama"
                                                    class="form-control @error('ayahData.nama') is-invalid @enderror"
                                                    placeholder="Masukkan nama ayah">
                                                @error('ayahData.nama')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Status</label>
                                                <select wire:model="ayahData.status"
                                                    class="form-select @error('ayahData.status') is-invalid @enderror">
                                                    <option value="masih-hidup">Masih Hidup</option>
                                                    <option value="sudah-meninggal">Sudah Meninggal</option>
                                                </select>
                                                @error('ayahData.status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Pekerjaan</label>
                                                <select wire:model="ayahData.pekerjaan"
                                                    class="form-select @error('ayahData.pekerjaan') is-invalid @enderror">
                                                    {{-- ✅ CARA 2: Menggunakan Livewire property --}}
                                                    <option value="">-- Pilih Pekerjaan --</option>
                                                    @foreach($pekerjaanOptions as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                @error('ayahData.pekerjaan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Telepon</label>
                                                <input type="text" wire:model="ayahData.telepon"
                                                    class="form-control @error('ayahData.telepon') is-invalid @enderror"
                                                    placeholder="Masukkan nomor telepon ayah">
                                                @error('ayahData.telepon')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Alamat</label>
                                                <textarea wire:model="ayahData.alamat"
                                                    class="form-control @error('ayahData.alamat') is-invalid @enderror"
                                                    rows="2" placeholder="Masukkan alamat ayah"></textarea>
                                                @error('ayahData.alamat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Data Ibu --}}
                                <div class="card mb-4"> {{-- Dihapus: border-0 shadow-sm --}}
                                    <div class="card-header bg-light"> {{-- Dihapus: d-flex align-items-center --}}
                                        <h6 class="mb-0"><i class="mdi mdi-account-tie-woman me-2 text-pink"></i>Data Ibu</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Nama</label>
                                                <input type="text" wire:model="ibuData.nama"
                                                    class="form-control @error('ibuData.nama') is-invalid @enderror"
                                                    placeholder="Masukkan nama ibu">
                                                @error('ibuData.nama')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Status</label>
                                                <select wire:model="ibuData.status"
                                                    class="form-select @error('ibuData.status') is-invalid @enderror">
                                                    <option value="masih-hidup">Masih Hidup</option>
                                                    <option value="sudah-meninggal">Sudah Meninggal</option>
                                                </select>
                                                @error('ibuData.status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Pekerjaan</label>
                                                <select wire:model="ibuData.pekerjaan"
                                                    class="form-select @error('ibuData.pekerjaan') is-invalid @enderror">
                                                    {{-- ✅ CARA 3: Inline config --}}
                                                    <option value="">-- Pilih Pekerjaan --</option>
                                                    @foreach(config('enums.pekerjaan') as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                @error('ibuData.pekerjaan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Telepon</label>
                                                <input type="text" wire:model="ibuData.telepon"
                                                    class="form-control @error('ibuData.telepon') is-invalid @enderror"
                                                    placeholder="Masukkan nomor telepon ibu">
                                                @error('ibuData.telepon')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Alamat</label>
                                                <textarea wire:model="ibuData.alamat"
                                                    class="form-control @error('ibuData.alamat') is-invalid @enderror"
                                                    rows="2" placeholder="Masukkan alamat ibu"></textarea>
                                                @error('ibuData.alamat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Data Wali --}}
                                <div class="card"> {{-- Dihapus: border-0 shadow-sm --}}
                                    <div class="card-header bg-light"> {{-- Dihapus: d-flex align-items-center --}}
                                        <h6 class="mb-0"><i class="mdi mdi-account-supervisor me-2 text-warning"></i>Data Wali</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Nama</label>
                                                <input type="text" wire:model="waliData.nama"
                                                    class="form-control @error('waliData.nama') is-invalid @enderror"
                                                    placeholder="Masukkan nama wali">
                                                @error('waliData.nama')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Status</label>
                                                <select wire:model="waliData.status"
                                                    class="form-select @error('waliData.status') is-invalid @enderror">
                                                    <option value="masih-hidup">Masih Hidup</option>
                                                    <option value="sudah-meninggal">Sudah Meninggal</option>
                                                </select>
                                                @error('waliData.status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Pekerjaan</label>
                                                <select wire:model="waliData.pekerjaan"
                                                    class="form-select @error('waliData.pekerjaan') is-invalid @enderror">
                                                    {{-- ✅ CARA 3: Inline config --}}
                                                    <option value="">-- Pilih Pekerjaan --</option>
                                                    @foreach(config('enums.pekerjaan') as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                @error('waliData.pekerjaan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Telepon</label>
                                                <input type="text" wire:model="waliData.telepon"
                                                    class="form-control @error('waliData.telepon') is-invalid @enderror"
                                                    placeholder="Masukkan nomor telepon wali">
                                                @error('waliData.telepon')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Alamat</label>
                                                <textarea wire:model="waliData.alamat"
                                                    class="form-control @error('waliData.alamat') is-invalid @enderror"
                                                    rows="2" placeholder="Masukkan alamat wali"></textarea>
                                                @error('waliData.alamat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>

                {{-- MODAL FOOTER --}}
                <div class="modal-footer">
                    <!-- Tombol Batal: Tetap sederhana -->
                    <button
                        type="button"
                        class="btn btn-labeled btn-outline-secondary"
                        data-bs-dismiss="modal">
                        <span class="btn-label">
                            <i class="mdi mdi-close"></i>
                        </span>
                        Batal
                    </button>

                    <!-- Tombol Simpan Perubahan: Diubah agar memiliki indikator loading -->
                    <button type="submit"
                        form="editStudentForm"
                        class="btn btn-labeled btn-primary"
                        wire:loading.attr="disabled"
                        wire:target="editStudentForm">

                        <span class="btn-label">
                            <!-- Ikon Loading (MDI Spin) - Muncul saat Livewire sedang memproses aksi -->
                            <i class="mdi mdi-loading mdi-spin d-none"
                                wire:loading.class.remove="d-none"
                                wire:target="editStudentForm">
                            </i>
                            <!-- Ikon Default (Simpan) - Tersembunyi saat Livewire sedang memproses aksi -->
                            <i class="mdi mdi-content-save"
                                wire:loading.class="d-none"
                                wire:target="editStudentForm">
                            </i>
                        </span>

                        <!-- Teks Default - Tersembunyi saat Livewire sedang memproses aksi -->
                        <span wire:loading.class="d-none" wire:target="editStudentForm">
                            Simpan
                        </span>

                        <!-- Teks Loading ("Menyimpan...") - Muncul saat Livewire sedang memproses aksi -->
                        <span class="d-none" wire:loading.class.remove="d-none" wire:target="editStudentForm">
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>


    @push('styles')
    <style>
        /* ... (CSS Card Header dan Detail lainnya tetap sama) ... */

        .personal-details-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .card-header-modifikasi {
            background-color: #f7fafc;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .card-header-title {
            font-size: 1.15rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0;
        }

        .card-header-title i.mdi {
            font-size: 1.25rem;
            vertical-align: middle;
            color: #0d6efd;
        }

        .personal-details-title,
        .parent-title {
            display: none;
        }

        .personal-details-list {
            display: flex;
            flex-direction: column;
        }

        .personal-detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            padding: .75rem 0;
            gap: 16px;
        }

        .personal-detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-size: 0.95rem;
            font-weight: 500;
            color: #718096;
            min-width: 40%;
        }

        .detail-value {
            font-size: 0.95rem;
            font-weight: medium;
            color: #2d3748;
            text-align: left;
            flex: 1;
            line-height: 1.5rem;
        }

        /* --- Perbaikan Styling Tabs --- */
        /* Menghilangkan gaya default nav-tabs */
        .nav-tabs {
            border-bottom: none !important;
            /* Hapus garis bawah default */
        }

        .nav-item {
            margin-bottom: 0 !important;
            /* Hapus margin bawah default */
        }

        /* Sesuaikan gaya tab-button Anda agar berfungsi sebagai nav-link */
        .tab-button {
            /* Menggunakan !important untuk menimpa nav-link default Bootstrap */
            border: 1px solid #e2e8f0 !important;
            border-radius: 2px !important;
            padding: .5rem 1rem !important;
            margin-right: .5rem !important;
            background: #f7fafc !important;
            font-weight: 500 !important;
            transition: .15s !important;
            color: #4a5568 !important;
            /* Warna teks default */
            border-bottom: 1px solid #e2e8f0 !important;
            /* Pastikan border bawah ada saat tidak aktif */
        }

        .tab-button.active {
            /* Gaya aktif yang Anda inginkan */
            background: #9b07a8ff !important;
            color: white !important;
            border-color: #9b07a8ff !important;
        }

        .tab-button:hover:not(.active) {
            /* Gaya hover saat tidak aktif */
            background: #ebedf0 !important;
            border-color: #e2e8f0 !important;
        }

        /* Akhir Perbaikan Styling Tabs */

        /* Modal cleanup */
        .modal-content {
            border-radius: 12px;
            border: none;
        }
    </style>
    @endpush

    @push('scripts')
    {{-- ✅ SWEETALERT2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Handle modal detail
        window.addEventListener('show-detail-modal', event => {
            var detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
            detailModal.show();
        });

        // Handle modal edit
        window.addEventListener('show-edit-modal', event => {
            var editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        });

        // Close modal edit
        window.addEventListener('close-edit-modal', event => {
            var editModal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
            if (editModal) {
                editModal.hide();
            }
        });

        // ✅ SWEETALERT2 HANDLER (TANPA AUTO CLOSE)
        window.addEventListener('show-alert', event => {
            const data = event.detail[0];
            const icons = {
                success: 'success',
                error: 'error',
                warning: 'warning',
                info: 'info'
            };

            const titles = {
                success: 'Berhasil!',
                error: 'Gagal!',
                warning: 'Peringatan!',
                info: 'Informasi'
            };

            Swal.fire({
                icon: icons[data.type] || 'info',
                title: titles[data.type] || 'Notifikasi',
                text: data.message,
                confirmButtonText: 'OK',
                confirmButtonColor: '#0d6efd'
            });
        });

        // Reset tab ke tab pertama saat modal dibuka
        document.getElementById('detailModal').addEventListener('show.bs.modal', function() {
            var firstTab = new bootstrap.Tab(document.getElementById('pelajar-tab'));
            firstTab.show();
        });

        document.getElementById('editModal').addEventListener('show.bs.modal', function() {
            var firstTab = new bootstrap.Tab(document.getElementById('edit-pelajar-tab'));
            firstTab.show();
        });
    </script>
    @endpush
</div>