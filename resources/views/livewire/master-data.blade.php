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

            {{-- Looping untuk menampilkan setiap Master Data --}}

            @php
            // Definisi warna pelangi untuk BACKGROUND
            $rainbowColors = [
            'bg-danger', // Merah
            'bg-warning', // Oranye/Kuning
            'bg-success', // Hijau
            'bg-info', // Biru muda
            'bg-primary', // Biru tua
            'bg-secondary', // Abu-abu
            'bg-dark' // Hitam
            ];
            @endphp

            @forelse ($masterData as $data)

            @php
            // Pilih warna berdasarkan urutan baris
            $color = $rainbowColors[($loop->iteration - 1) % count($rainbowColors)];
            @endphp

            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        {{-- Ganti dengan ikon atau gambar yang sesuai, saya gunakan placeholder mdi class --}}
                        <div class="p-2 mr-3 rounded {{ $color }} d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="mdi {{ $data['icon'] }} mdi-24px text-white"></i>
                        </div>

                        <div class="ml-3">
                            <p class="mb-0 font-weight-medium">{{ $data['label'] }}</p>
                            <p class="mb-0">
                                {{-- Jenis (Lokal) --}}
                                <span class="badge badge-inverse-warning align-items-center gap-1">
                                    <i class="mdi mdi-plus"></i><strong>Lokal</strong>
                                </span>
                                {{-- Dibuat (Tanggal Terbaru) --}}
                                <i class="mdi mdi-calendar-range"></i>
                                <span class="text-muted"><small> {{ $data['latest_created_at'] }}</small></span>
                                {{-- Jumlah Record --}}
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
                        {{-- Status (Mock) --}}
                        <span class="badge badge-inverse-success d-flex align-items-center gap-1">
                            <i class="mdi mdi-check"></i><strong>{{ $data['status'] }}</strong>
                        </span>
                        {{-- Tanggal Status/Sinkronisasi (Mock) --}}
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
        <div class="modal-dialog modal-dialog-scrollable">
            <form action="{{ url('main-data/synchronize') }}" class="needs-validation" novalidate method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Sinkronisasi Data</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning d-flex align-items-center mb-4">
                            <i class="mdi mdi-information me-2 fs-4"></i>
                            <span>Data hampir tersinkronisasi sepenuhnya</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                    <i class="mdi mdi-database fs-4 text-white"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted">Jumlah Data Lokal</p>
                                    <h4 class="mb-0 text-primary">jumlahDataLokal</h4>
                                </div>
                            </div>
                            <span class="badge badge-inverse-primary">+12 dari bulan lalu</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                                    <i class="mdi mdi-server fs-4 text-white"></i>
                                </div>
                                <div>
                                    <p class="mb-0 text-muted">Jumlah Data Server</p>
                                    <h4 class="mb-0 text-success">jumlahData</h4>
                                </div>
                            </div>
                            <span class="badge badge-inverse-success">Tersinkronisasi</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-labeled btn-secondary" data-bs-dismiss="modal">
                            <span class="btn-label"><i class="fa fa-remove"></i></span>Batal</button>
                        <button type="submit" class="btn btn-labeled btn-warning">
                            <span class="btn-label"><i class="mdi mdi-sync"></i></span>Tarik Data</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ✅ Listen to Livewire event
    document.addEventListener('livewire:init', () => {
        Livewire.on('showSyncModal', () => {
            const modalElement = document.getElementById('syncronData');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        });
    });
</script>
@endpush