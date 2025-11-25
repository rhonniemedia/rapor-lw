<div>
    {{-- Header Section --}}
    <div class="row mb-3 align-items-center">
        <div class="col-lg-6">
            <div class="d-flex align-items-center">
                <div class="icon-wrapper position-relative">
                    <span class="bg-gradient-primary p-2 rounded-3 shadow-sm me-3 d-inline-flex align-items-center justify-content-center">
                        <i class="mdi mdi-school mdi-24px text-white"></i>
                    </span>
                </div>
                <div>
                    <h4 class="mb-1 text-dark fw-bold">Rombongan Belajar yang Diampu</h4>
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-muted">Daftar kelas dan mata pelajaran yang Anda ampu</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 d-flex justify-content-end">
            <div class="input-group w-50">
                <input type="search"
                    wire:model.live.debounce.300ms="searchRombel"
                    class="form-control"
                    placeholder="Cari rombel, mata pelajaran, atau wali kelas...">
                @if($searchRombel)
                <div class="input-group-append">
                    <button type="button"
                        class="btn btn-info"
                        wire:click="$set('searchRombel', '')">
                        <i class="mdi mdi-close"></i>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Info Card --}}
    @if($rombels->total() > 0)
    <div class="alert alert-info d-flex align-items-center justify-content-between" role="alert">
        <div>
            <i class="mdi mdi-information me-2"></i>
            <strong>Informasi:</strong> Anda mengampu <strong>{{ $rombels->total() }}</strong> rombongan belajar.
            Klik icon detail untuk mulai input nilai.
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Tabel Rombongan Belajar --}}
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th style="width: 30%;">
                        <p class="mb-0">Rombongan Belajar</p>
                        <small>Rombel | Mata Pelajaran</small>
                    </th>
                    <th style="width: 30%;">
                        <p class="mb-0">Wali Kelas</p>
                        <small>Nama | Telepon</small>
                    </th>
                    <th style="width: 30%;">
                        <p class="mb-0">Peserta Didik</p>
                        <small>Sudah Dinilai / Total</small>
                    </th>
                    <th style="width: 10%;">
                        <p class="mb-0">Aksi</p>
                        <small>Detail Rombel</small>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rombels as $rombel)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('assets/images/icons/school.png') }}"
                                alt="image"
                                width="40"
                                height="40"
                                class="me-3" />
                            <div class="table-user-name">
                                <p class="mb-0 font-weight-medium">{{ $rombel->nama }}</p>
                                <small class="text-muted">{{ $rombel->mata_pelajaran_nama }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <p class="mb-0 font-weight-medium">{{ $rombel->walikelas_name }}</p>
                        <small class="text-muted">Telp. {{ $rombel->walikelas_telephone }}</small>
                    </td>
                    <td>
                        <div class="d-flex gap-2 align-items-center">
                            <span class="badge badge-success d-flex align-items-center gap-1">
                                <i class="mdi mdi-check-circle"></i>
                                <strong>{{ $rombel->selesai_dinilai }}</strong>
                            </span>
                            <span class="text-muted">/</span>
                            <span class="badge badge-inverse-dark d-flex align-items-center gap-1">
                                <i class="mdi mdi-account-multiple"></i>
                                <strong>{{ $rombel->total_pelajar }}</strong>
                            </span>
                        </div>
                        {{-- Progress Bar --}}
                        @php
                        $percentage = $rombel->total_pelajar > 0
                        ? round(($rombel->selesai_dinilai / $rombel->total_pelajar) * 100)
                        : 0;
                        @endphp
                        <div class="progress mt-2" style="height: 6px;">
                            <div class="progress-bar bg-success"
                                role="progressbar"
                                style="width: <?php echo $percentage; ?>%"
                                aria-valuenow="{{ $percentage }}"
                                aria-valuemin="0"
                                aria-valuemax="100">
                            </div>
                        </div>
                        <small class="text-muted">{{ $percentage }}% selesai</small>
                    </td>
                    <td>
                        <form action="{{ route('guru.class.detail', ['rombelId' => $rombel->id, 'mataPelajaranId' => $rombel->mata_pelajaran_id]) }}" method="get" style="display:inline">
                            <button type="submit"
                                class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                                title="Lihat Detail & Input Nilai">
                                <i class="mdi mdi-eye-outline"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        <i class="mdi mdi-information-outline" style="font-size: 48px; opacity: 0.3;"></i>
                        <p class="mb-0 mt-2">Belum ada data rombongan belajar yang diampu.</p>
                        <small>Hubungi admin untuk penugasan kelas.</small>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($rombels->hasPages())
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div>
            <small class="text-muted">
                Menampilkan {{ $rombels->firstItem() ?? 0 }} - {{ $rombels->lastItem() ?? 0 }}
                dari {{ $rombels->total() }} rombel
            </small>
        </div>
        <div>
            {{ $rombels->links() }}
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
    // Event listener untuk SweetAlert
    document.addEventListener('livewire:init', () => {
        Livewire.on('swal:success', (event) => {
            Swal.fire({
                icon: 'success',
                title: event.title || 'Berhasil!',
                text: event.text || '',
                timer: 3000,
                showConfirmButton: false
            });
        });

        Livewire.on('swal:error', (event) => {
            Swal.fire({
                icon: 'error',
                title: event.title || 'Gagal!',
                text: event.text || '',
                confirmButtonText: 'OK'
            });
        });

        Livewire.on('swal:info', (event) => {
            Swal.fire({
                icon: 'info',
                title: event.title || 'Info',
                text: event.text || '',
                confirmButtonText: 'OK'
            });
        });
    });
</script>
@endpush