<div>
    <table class="table table-hover mb-0">
        <thead class="bg-light">
            <tr>
                <th style="width: 50%;">
                    <p class="mb-0">Master Data</p>
                    <small>Jenis | Dibuat | Jumlah</small>
                </th>
                <th style="width: 50%;">
                    <p class="mb-0">Informasi</p>
                    <small>Status | Tanggal</small>
                </th>
            </tr>
        </thead>
        <tbody>

            @php
            $rainbowColors = [
            'bg-danger',
            'bg-warning',
            'bg-success',
            'bg-info',
            'bg-primary',
            'bg-secondary',
            'bg-dark'
            ];
            @endphp

            @forelse ($masterData as $data)

            @php
            $color = $rainbowColors[($loop->iteration - 1) % count($rainbowColors)];
            @endphp

            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="p-2 mr-3 rounded {{ $color }} d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="mdi {{ $data['icon'] }} mdi-24px text-white"></i>
                        </div>

                        <div class="ml-3">
                            <p class="mb-0 font-weight-medium">{{ $data['label'] }}</p>
                            <p class="mb-0">
                                <span class="badge badge-inverse-warning align-items-center gap-1">
                                    <i class="mdi mdi-plus"></i><strong>Lokal</strong>
                                </span>
                                <i class="mdi mdi-calendar-range"></i>
                                <span class="text-muted"><small> {{ $data['latest_created_at'] }}</small></span>
                                <i class="mdi mdi-equal-box"></i>
                                <span class="text-muted"><small>{{ $data['count'] }} Record</small></span>
                            </p>
                            <p class="mb-0">
                                <small>
                                    Terakhir diperbarui {{ $data['latest_updated_at'] }}
                                </small>
                            </p>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="d-flex gap-1 align-items-center">
                        @if ($data['has_data'])
                        <span class="badge badge-inverse-success d-flex align-items-center gap-1">
                            <i class="mdi mdi-check"></i><strong>{{ $data['status'] }}</strong>
                        </span>
                        <span class="badge badge-inverse-primary d-flex align-items-center gap-1">
                            <i class="mdi mdi-update"></i><strong>{{ $data['status_date'] }}</strong>
                        </span>
                        @else
                        <span class="badge badge-inverse-danger d-flex align-items-center gap-1">
                            <i class="mdi mdi-alert-circle-outline"></i><strong>Belum Ada Data</strong>
                        </span>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="2" class="text-center text-muted">Tidak ada data master yang terdaftar.</td>
            </tr>
            @endforelse

        </tbody>
    </table>

    <!-- Modal Sinkron -->
    <div wire:ignore.self class="modal fade" id="syncronData" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="sinkronDataLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <form wire:submit.prevent="syncDataToDatabase">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Sinkronisasi Data</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        @if(session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-alert-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endif

                        <div class="alert alert-info d-flex align-items-center mb-4">
                            <i class="mdi mdi-information me-2 fs-4"></i>
                            <span>Perbandingan data lokal dengan data server</span>
                        </div>

                        <div class="row g-4">
                            <!-- DATA LOKAL -->
                            <div class="col-lg-6">
                                <h5 class="mb-3 text-primary"><i class="mdi mdi-database me-2"></i> Data Lokal</h5>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-danger bg-opacity-10 p-2 rounded me-3">
                                                        <i class="mdi mdi-calendar-range fs-4 text-white"></i>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fw-medium">Data Tahun Ajaran</p>
                                                        <small class="text-muted">Tahun Ajaran</small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <h4 class="mb-0 text-danger fw-bold">
                                                        {{ number_format(\App\Models\TahunAjaran::count()) }}
                                                    </h4>
                                                    <small class="text-muted">records</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-warning bg-opacity-10 p-2 rounded me-3">
                                                        <i class="mdi mdi-school fs-4 text-white"></i>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fw-medium">Data Jurusan</p>
                                                        <small class="text-muted">Konsentrasi Keahlian</small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <h4 class="mb-0 text-warning fw-bold">
                                                        {{ number_format(\App\Models\Jurusan::count()) }}
                                                    </h4>
                                                    <small class="text-muted">records</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                                                        <i class="mdi mdi-account-group fs-4 text-white"></i>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fw-medium">Data Rombel</p>
                                                        <small class="text-muted">Rombongan Belajar</small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <h4 class="mb-0 text-success fw-bold">
                                                        {{ number_format(\App\Models\Rombel::count()) }}
                                                    </h4>
                                                    <small class="text-muted">records</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-info bg-opacity-10 p-2 rounded me-3">
                                                        <i class="mdi mdi-table-large fs-4 text-white"></i>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fw-medium">Detail Rombel</p>
                                                        <small class="text-muted">Data Anggota Rombel</small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <h4 class="mb-0 text-info fw-bold">
                                                        {{ number_format(\App\Models\RombelPelajar::count()) }}
                                                    </h4>
                                                    <small class="text-muted">records</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                                        <i class="mdi mdi-account-details fs-4 text-white"></i>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fw-medium">Peserta Didik</p>
                                                        <small class="text-muted">Data Siswa</small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <h4 class="mb-0 text-primary fw-bold">
                                                        {{ number_format(\App\Models\Pelajar::count()) }}
                                                    </h4>
                                                    <small class="text-muted">records</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-dark bg-opacity-10 p-2 rounded me-3">
                                                        <i class="mdi mdi-human-greeting fs-4 text-white"></i>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fw-medium">Data Guru</p>
                                                        <small class="text-muted">Pendidik & Tenaga Kependidikan</small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <h4 class="mb-0 text-dark fw-bold">
                                                        {{ number_format(\App\Models\User::count()) }}
                                                    </h4>
                                                    <small class="text-muted">records</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3 mt-3 bg-primary bg-opacity-10 rounded border border-primary text-center">
                                    <p class="mb-0 text-primary fw-bold">Total Lokal</p>
                                    <h4 class="mb-0 text-primary fw-bolder">
                                        {{ number_format(\App\Models\Rombel::count() + \App\Models\Pelajar::count() + \App\Models\User::count()) }}
                                    </h4>
                                </div>
                            </div>

                            <!-- DATA SERVER -->
                            <div class="col-lg-6">
                                <h5 class="mb-3 text-success"><i class="mdi mdi-server me-2"></i> Data Server (API)</h5>
                                <div class="row g-3">

                                    <!-- Data Tahun Ajaran -->
                                    <div class="col-md-12">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-danger bg-opacity-10 p-2 rounded me-3">
                                                        <i class="mdi mdi-calendar-range fs-4 text-white"></i>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fw-medium">Data Tahun Ajaran</p>
                                                        <small class="text-muted">API: /pintar/api/tahun-ajaran</small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    @if($isLoadingTahunAjaran)
                                                    <div class="spinner-border spinner-border-sm text-danger" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    @else
                                                    <h4 class="mb-0 text-danger fw-bold">
                                                        {{ is_array($apiTahunAjaran) ? number_format(count($apiTahunAjaran)) : 0 }}
                                                    </h4>
                                                    <small class="text-muted">records</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Data Jurusan -->
                                    <div class="col-md-12">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-warning bg-opacity-10 p-2 rounded me-3">
                                                        <i class="mdi mdi-school fs-4 text-white"></i>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fw-medium">Data Jurusan</p>
                                                        <small class="text-muted">API: /pintar/api/jurusan</small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    @if($isLoadingJurusan)
                                                    <div class="spinner-border spinner-border-sm text-warning" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    @else
                                                    <h4 class="mb-0 text-warning fw-bold">
                                                        {{ is_array($apiDataJurusan) ? number_format(count($apiDataJurusan)) : 0 }}
                                                    </h4>
                                                    <small class="text-muted">records</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Data Rombel -->
                                    <div class="col-md-12">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                                                        <i class="mdi mdi-account-group fs-4 text-white"></i>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fw-medium">Data Rombel</p>
                                                        <small class="text-muted">API: /pintar/api/rombel</small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    @if($isLoadingRombel)
                                                    <div class="spinner-border spinner-border-sm text-success" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    @else
                                                    <h4 class="mb-0 text-success fw-bold">
                                                        {{ is_array($apiDataRombel) ? number_format(count($apiDataRombel)) : 0 }}
                                                    </h4>
                                                    <small class="text-muted">records</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Detail Rombel -->
                                    <div class="col-md-12">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-info bg-opacity-10 p-2 rounded me-3">
                                                        <i class="mdi mdi-table-large fs-4 text-white"></i>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fw-medium">Detail Rombel</p>
                                                        <small class="text-muted">API: /pintar/api/rombel-data</small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    @if($isLoadingRombelDetail)
                                                    <div class="spinner-border spinner-border-sm text-info" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    @else
                                                    <h4 class="mb-0 text-info fw-bold">
                                                        {{ is_array($apiDataRombelDetail) ? number_format(count($apiDataRombelDetail)) : 0 }}
                                                    </h4>
                                                    <small class="text-muted">records</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Peserta Didik -->
                                    <div class="col-md-12">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                                        <i class="mdi mdi-account-details fs-4 text-white"></i>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fw-medium">Peserta Didik</p>
                                                        <small class="text-muted">API: /pintar/api/data-peserta-didik</small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    @if($isLoadingPesertaDidik)
                                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    @else
                                                    <h4 class="mb-0 text-primary fw-bold">
                                                        {{ is_array($apiDataPesertaDidik) ? number_format(count($apiDataPesertaDidik)) : 0 }}
                                                    </h4>
                                                    <small class="text-muted">records</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Data Guru -->
                                    <div class="col-md-12">
                                        <div class="border rounded p-3 bg-light">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-dark bg-opacity-10 p-2 rounded me-3">
                                                        <i class="mdi mdi-human-greeting fs-4 text-white"></i>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fw-medium">Data Guru</p>
                                                        <small class="text-muted">API: /simka/api/data-guru</small>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    @if($isLoadingGuru)
                                                    <div class="spinner-border spinner-border-sm text-dark" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    @else
                                                    <h4 class="mb-0 text-dark fw-bold">
                                                        {{ is_array($apiDataGuru) ? number_format(count($apiDataGuru)) : 0 }}
                                                    </h4>
                                                    <small class="text-muted">records</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Server -->
                                <div class="p-3 mt-3 bg-success bg-opacity-10 rounded border border-success text-center">
                                    <p class="mb-0 text-success fw-bold">Total Server</p>
                                    @if($isLoadingRombel || $isLoadingRombelDetail || $isLoadingPesertaDidik || $isLoadingGuru)
                                    <div class="spinner-border spinner-border-sm text-success mt-2" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    @else
                                    <h4 class="mb-0 text-success fw-bolder">{{ number_format($totalServerData) }}</h4>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Status Koneksi -->
                        @if(!$isLoadingRombel && !$isLoadingRombelDetail && !$isLoadingPesertaDidik && !$isLoadingGuru)
                        <div class="mt-4 p-3 rounded border border-success bg-success bg-opacity-10">
                            <div class="d-flex align-items-center">
                                <i class="mdi mdi-check-circle fs-3 text-success me-3"></i>
                                <div>
                                    <p class="mb-0 fw-bold text-success">Koneksi ke Server Berhasil</p>
                                    <small class="text-muted">Data siap untuk disinkronkan</small>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-labeled btn-secondary" data-bs-dismiss="modal">
                            <span class="btn-label"><i class="fa fa-remove"></i></span>Batal
                        </button>
                        <button type="submit" class="btn btn-labeled btn-warning"
                            @if($isLoadingRombel || $isLoadingRombelDetail || $isLoadingPesertaDidik || $isLoadingGuru) disabled @endif>
                            <span class="btn-label"><i class="mdi mdi-sync"></i></span>
                            @if($isLoadingRombel || $isLoadingRombelDetail || $isLoadingPesertaDidik || $isLoadingGuru)
                            Memuat Data...
                            @else
                            Tarik Data
                            @endif
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('showSyncModal', () => {
            const modalElement = document.getElementById('syncronData');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();

            const component = window.Livewire.find('{{ $this->getId() }}');

            setTimeout(() => {
                component.call('fetchTahunAjaran').then(() => {
                    setTimeout(() => {
                        component.call('fetchJurusan').then(() => {
                            setTimeout(() => {
                                component.call('fetchRombel').then(() => {
                                    setTimeout(() => {
                                        component.call('fetchRombelDetail').then(() => {
                                            setTimeout(() => {
                                                component.call('fetchPesertaDidik').then(() => {
                                                    setTimeout(() => {
                                                        component.call('fetchGuru');
                                                    }, 500);
                                                });
                                            }, 500);
                                        });
                                    }, 500);
                                });
                            }, 500);
                        });
                    }, 500);
                });
            }, 100);
        });

        // Event untuk close modal
        Livewire.on('closeSyncModal', () => {
            const modalElement = document.getElementById('syncronData');
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        });
    });
</script>
@endpush