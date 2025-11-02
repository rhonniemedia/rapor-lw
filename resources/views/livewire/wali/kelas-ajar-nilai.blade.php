<div>
    {{-- Page Header --}}
    <div class="page-header pb-3 mb-4 border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="icon-wrapper position-relative">
                    <span class="bg-gradient-primary p-2 rounded-3 shadow-sm me-3 d-inline-flex align-items-center justify-content-center">
                        <i class="mdi mdi-numeric mdi-24px text-white"></i>
                    </span>
                </div>
                <div>
                    <h4 class="mb-1 text-dark fw-bold">Entri Nilai Akhir Pelajar</h4>
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-muted">Kelola nilai per mata pelajaran</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Rombel Card --}}
    @if($rombel && $semesterAktif)
    <div class="card border-success shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-4">
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
                            <p class="fw-bold mb-0 text-dark lh-sm">
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
                            <small class="text-muted lh-2">Tahun Ajaran & Semester</small>
                            <p class="fw-bold mb-0 text-dark lh-sm">
                                {{ $semesterAktif->tahunAjaran->nama ?? 'N/A' }} ~ {{ $semesterAktif->semester->nama ?? 'Belum Ada' }}
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
                            <p class="fw-bold mb-0 text-dark lh-sm">
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
                            <p class="fw-bold mb-0 text-dark lh-sm">
                                {{ $rombel->waliKelas->name ?? 'Belum Ditentukan' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Kolom 3 -->
                <div class="col-md-4">
                    <!-- Guru Mata Pelajaran -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-primary rounded-3"
                            style="width: 36px; height: 36px;">
                            <i class="mdi mdi-teach text-white fs-5"></i>
                        </div>
                        <div class="ms-3 d-flex flex-column justify-content-center">
                            <small class="text-muted lh-2">Guru Mata Pelajaran</small>
                            <p class="fw-bold mb-0 text-primary lh-sm">
                                {{ $rombelPengajar->guru->name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>

                    <!-- Mata Pelajaran -->
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-danger rounded-3"
                            style="width: 36px; height: 36px;">
                            <i class="mdi mdi-book-open-variant text-white fs-5"></i>
                        </div>
                        <div class="ms-3 d-flex flex-column justify-content-center">
                            <small class="text-muted lh-2">Mata Pelajaran</small>
                            <p class="fw-bold mb-0 text-danger lh-sm">
                                {{ $mataPelajaran->nama ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Card --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="mdi mdi-account-multiple mdi-36px me-3"></i>
                        <div>
                            <h6 class="mb-0">Total Siswa</h6>
                            <h3 class="mb-0">{{ $totalSiswa }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="mdi mdi-check-circle mdi-36px me-3"></i>
                        <div>
                            <h6 class="mb-0">Sudah Dinilai</h6>
                            <h3 class="mb-0">{{ $cachedNilaiExist ? $cachedNilaiExist->count() : 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="mdi mdi-clock-alert mdi-36px me-3"></i>
                        <div>
                            <h6 class="mb-0">Belum Dinilai</h6>
                            <h3 class="mb-0">{{ $totalSiswa - ($cachedNilaiExist ? $cachedNilaiExist->count() : 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="mdi mdi-chart-line mdi-36px me-3"></i>
                        <div>
                            <h6 class="mb-0">Progress</h6>
                            <h3 class="mb-0">
                                @php
                                $progress = $totalSiswa > 0 ? round(($cachedNilaiExist ? $cachedNilaiExist->count() : 0) / $totalSiswa * 100) : 0;
                                @endphp
                                {{ $progress }}%
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-danger" role="alert">
        <i class="mdi mdi-alert-circle me-2"></i>
        <strong>Perhatian!</strong> Data tidak lengkap atau semester tidak aktif.
    </div>
    @endif

    {{-- Search and Table Section --}}
    @if($rombel && $semesterAktif && $rombelPengajar)
    <div class="row mb-3">
        <div class="col-lg-6">
            <h5 class="text-dark"><i class="mdi mdi-account-multiple me-2"></i> Entri Data Nilai Akhir Pelajar</h5>
        </div>
        <div class="col-lg-6 d-flex justify-content-end">
            <div class="input-group w-50">
                <input type="text"
                    wire:model.live.debounce.300ms="searchPelajar"
                    class="form-control"
                    placeholder="Cari nama, atau nomor induk...">
                @if($searchPelajar)
                <button type="button"
                    class="btn btn-secondary"
                    wire:click="$set('searchPelajar', '')">
                    <i class="mdi mdi-close"></i>
                </button>
                @endif
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th class="text-center" width="5%">
                        <p class="mb-0">No</p>
                        <small>Urut</small>
                    </th>
                    <th width="40%">
                        <p class="mb-0">Nama Lengkap</p>
                        <small>Nomor Induk Sekolah & Nasional</small>
                    </th>
                    <th width="10%">
                        <p class="mb-0">Form</p>
                        <small>Entri Nilai</small>
                    </th>
                    <th width="10%">
                        <p class="mb-0">Nilai</p>
                        <small>Tersimpan</small>
                    </th>
                    <th width="10%">
                        <p class="mb-0">Predikat</p>
                        <small>A, B, C, D</small>
                    </th>
                    <th width="20%">
                        <p class="mb-0">Capaian Kompetensi</p>
                        <small>Status Pencapaian</small>
                    </th>
                    <th width="5%">
                        <p class="mb-0">Aksi</p>
                        <small>Delete</small>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($pelajarData as $index => $pelajar)
                <tr wire:key="pelajar-{{ $pelajar->pelajar_id }}">
                    <td class="text-center">
                        {{ $pelajarData->firstItem() + $index }}
                    </td>
                    <td>
                        <p class="mb-0 fw-medium">{{ $pelajar->nama_lengkap }}</p>
                        <small class="text-muted">{{ $pelajar->nomor_induk ?? '-' }} | {{ $pelajar->nisn ?? '-' }}</small>
                    </td>
                    <td>
                        <input type="number"
                            wire:key="input-{{ $pelajar->pelajar_id }}"
                            wire:model.defer="nilaiInput.{{ $pelajar->pelajar_id }}"
                            class="form-control form-control-sm text-center"
                            min="0"
                            max="100"
                            step="0.01"
                            placeholder="0-100">
                        @error('nilaiInput.' . $pelajar->pelajar_id)
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </td>
                    <td class="text-center">
                        @if($pelajar->nilai_sekarang)
                        <span class="badge bg-info">
                            {{ number_format($pelajar->nilai_sekarang, 2) }}
                        </span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($pelajar->nilai_sekarang)
                        @php
                        $nilai = $pelajar->nilai_sekarang;
                        if ($nilai >= 90) {
                        $predikat = 'A';
                        $badgeClass = 'bg-success';
                        } elseif ($nilai >= 75) {
                        $predikat = 'B';
                        $badgeClass = 'bg-primary';
                        } elseif ($nilai >= 60) {
                        $predikat = 'C';
                        $badgeClass = 'bg-warning';
                        } else {
                        $predikat = 'D';
                        $badgeClass = 'bg-danger';
                        }
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $predikat }}</span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($pelajar->nilai_sekarang)
                        @if($pelajar->nilai_sekarang >= 75)
                        <span class="badge bg-success">
                            <i class="mdi mdi-check-circle me-1"></i>Tercapai
                        </span>
                        @else
                        <span class="badge bg-danger">
                            <i class="mdi mdi-close-circle me-1"></i>Belum Tercapai
                        </span>
                        @endif
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($pelajar->nilai_sekarang)
                        <button type="button"
                            wire:key="delete-btn-{{ $pelajar->pelajar_id }}"
                            id="delete-btn-{{ $pelajar->pelajar_id }}"
                            class="btn btn-sm btn-outline-danger"
                            onclick="confirmDeleteNilai('{{ $pelajar->pelajar_id }}')"
                            title="Hapus Nilai">
                            <i class="mdi mdi-delete"></i>
                        </button>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="mdi mdi-information-outline me-2"></i>
                        @if($searchPelajar)
                        Tidak ada data pelajar yang sesuai dengan pencarian "{{ $searchPelajar }}"
                        @else
                        Tidak ada data pelajar
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($pelajarData->hasPages())
    <div class="mt-3">
        {{ $pelajarData->links() }}
    </div>
    @endif

    {{-- Action Buttons --}}
    <div class="d-flex justify-content-end mt-3 gap-2">
        <button type="button"
            class="btn btn-labeled btn-outline-secondary"
            wire:click="resetNilai"
            wire:loading.attr="disabled"
            wire:target="resetNilai">
            <span class="btn-label">
                <i class="mdi mdi-refresh"></i>
            </span>
            Reset
        </button>

        <button type="button"
            class="btn btn-labeled btn-primary"
            wire:click="saveNilai"
            wire:loading.attr="disabled"
            wire:target="saveNilai">
            <span class="btn-label">
                <i class="mdi mdi-loading mdi-spin d-none"
                    wire:loading.class.remove="d-none"
                    wire:target="saveNilai">
                </i>
                <i class="mdi mdi-content-save"
                    wire:loading.class="d-none"
                    wire:target="saveNilai">
                </i>
            </span>
            <span wire:loading.class="d-none" wire:target="saveNilai">
                Simpan Nilai
            </span>
            <span class="d-none" wire:loading.class.remove="d-none" wire:target="saveNilai">
                Menyimpan...
            </span>
        </button>
    </div>
    @elseif($rombel && $semesterAktif)
    <div class="alert alert-warning text-center" role="alert">
        <i class="mdi mdi-information-outline me-2"></i>
        <strong>Data tidak lengkap untuk mengentri nilai.</strong>
    </div>
    @endif

    {{-- Loading Overlay --}}
    <div wire:loading.flex
        wire:target="saveNilai,searchPelajar"
        class="position-fixed top-0 start-0 w-100 h-100 align-items-center justify-content-center"
        style="background-color: rgba(0,0,0,0.3); z-index: 9999; display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ✅ Function untuk handle delete confirmation dengan loading state
        window.confirmDeleteNilai = function(pelajarId) {
            Swal.fire({
                icon: 'warning',
                title: 'Hapus Nilai Pelajar?',
                text: 'Anda yakin ingin menghapus nilai ini?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then(result => {
                if (result.isConfirmed) {
                    // ✅ Tampilkan loading pada tombol spesifik
                    const btn = document.getElementById(`delete-btn-${pelajarId}`);
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i>';
                    }

                    // Dispatch ke backend
                    Livewire.dispatch('deleteNilai', [pelajarId]);
                }
            });
        };

        // Handler untuk response dari backend
        window.addEventListener('swal:success', event => {
            let detail = event.detail.params ?? event.detail[0] ?? event.detail;

            if (typeof detail === 'string') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: detail,
                    showConfirmButton: true,
                });
            } else if (typeof detail === 'object' && detail !== null) {
                Swal.fire({
                    icon: 'success',
                    title: detail.title ?? 'Berhasil!',
                    text: detail.text ?? '',
                    showConfirmButton: true,
                });
            }
        });

        window.addEventListener('swal:error', event => {
            let detail = event.detail.params ?? event.detail[0] ?? event.detail;

            if (typeof detail === 'string') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: detail,
                    confirmButtonText: 'Tutup'
                });
            } else if (typeof detail === 'object' && detail !== null) {
                Swal.fire({
                    icon: 'error',
                    title: detail.title ?? 'Error!',
                    text: detail.text ?? '',
                    confirmButtonText: 'Tutup'
                });
            }
        });

        window.addEventListener('swal:info', event => {
            let detail = event.detail.params ?? event.detail[0] ?? event.detail;

            if (typeof detail === 'string') {
                Swal.fire({
                    icon: 'info',
                    title: 'Info',
                    text: detail,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else if (typeof detail === 'object' && detail !== null) {
                Swal.fire({
                    icon: 'info',
                    title: detail.title ?? 'Info',
                    text: detail.text ?? '',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    });
</script>
@endpush