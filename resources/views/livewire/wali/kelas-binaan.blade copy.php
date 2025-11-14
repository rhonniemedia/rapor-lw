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
                        <a href="#"
                            class="btn btn-sm btn-outline-primary"
                            title="Lihat Detail">
                            <i class="mdi mdi-eye"></i>
                        </a>
                        <!-- route('homeroom.student.detail', $item->pelajar->id) -->
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

</div>

@push('styles')
<style>
    .border-left-primary {
        border-left: 4px solid #4e73df;
    }

    .border-left-success {
        border-left: 4px solid #1cc88a;
    }

    .border-left-info {
        border-left: 4px solid #36b9cc;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table> :not(caption)>*>* {
        padding: 1rem 0.75rem;
    }
</style>
@endpush