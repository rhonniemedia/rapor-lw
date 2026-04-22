<div>
    {{-- Page Header --}}
    <div class="page-header pb-3 mb-4 border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="icon-wrapper position-relative">
                    <span class="bg-gradient-primary p-2 rounded-3 shadow-sm me-3 d-inline-flex align-items-center justify-content-center">
                        <i class="mdi mdi-clipboard-text mdi-24px text-white"></i>
                    </span>
                </div>
                <div>
                    <h4 class="mb-1 text-dark fw-bold">Entri Catatan Wali Kelas</h4>
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-muted">Kelola data catatan Wali Kelas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Rombel Card --}}
    @if($rombel && $semesterId)
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

                    <!-- Tahun Ajaran & Semester -->
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                            style="width: 36px; height: 36px;">
                            <i class="mdi mdi-calendar-clock text-white fs-5"></i>
                        </div>
                        <div class="ms-3 d-flex flex-column justify-content-center">
                            <small class="text-muted lh-2">Tahun Ajaran & Semester</small>
                            <p class="fw-bold mb-0 text-dark lh-sm">
                                @if($selectedSemesterObj)
                                {{ $selectedSemesterObj->tahunAjaran->nama ?? 'N/A' }} ~
                                {{ $selectedSemesterObj->semester->nama ?? 'N/A' }}
                                @else
                                <span class="text-muted">Belum Dipilih</span>
                                @endif
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

                    <!-- Jurusan -->
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                            style="width: 36px; height: 36px;">
                            <i class="mdi mdi-school text-white fs-5"></i>
                        </div>
                        <div class="ms-3 d-flex flex-column justify-content-center">
                            <small class="text-muted lh-2">Kompetensi Keahlian</small>
                            <p class="fw-bold mb-0 text-dark lh-sm">
                                {{ $rombel->jurusan->nama ?? 'Umum' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Kolom 3 -->
                <div class="col-md-4">
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
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-danger" role="alert">
        <i class="mdi mdi-alert-circle me-2"></i>
        <strong>Perhatian!</strong>
        @if(!$rombel)
        Anda tidak memiliki kelas binaan yang aktif.
        @else
        Pilih tahun ajaran dan semester untuk melanjutkan.
        @endif
    </div>
    @endif

    {{-- Filter Tahun Ajaran & Semester --}}
    @if($rombel)
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="page-header mb-0 border-bottom mb-3">
                <div class="d-flex align-items-center">
                    <h5 class="text-dark"><i class="mdi mdi-filter"></i> Filter Data</h5>
                </div>
            </div>
            <div class="row g-3">

                {{-- Tahun Ajaran --}}
                <div class="col-md-4">
                    <label class="form-label">Tahun Ajaran</label>
                    <select wire:model.live="tahunAjaranId" class="form-select">
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        @foreach($tahunAjaranList as $ta)
                        <option value="{{ $ta->id }}">
                            {{ $ta->nama }}
                            @if($ta->status === 'aktif')
                            (Aktif)
                            @endif
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Semester --}}
                <div class="col-md-4">
                    <label class="form-label">Semester</label>
                    <select wire:model.live="semesterId" class="form-select"
                        @if(!$tahunAjaranId) disabled @endif>
                        <option value="">-- Pilih Semester --</option>
                        @foreach($semesterList as $sem)
                        <option value="{{ $sem->id }}">
                            {{ $sem->semester->nama ?? $sem->id }}
                            @if($sem->status === 'aktif')
                            (Aktif)
                            @endif
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Pencarian --}}
                <div class="col-md-4">
                    <label class="form-label">Pencarian</label>
                    <div class="d-flex">
                        <input type="search"
                            wire:model.live.debounce.300ms="searchPelajar"
                            class="form-control"
                            placeholder="Cari..."
                            @if(!$semesterId) disabled @endif>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endif

    {{-- Tabel Input Catatan --}}
    @if($rombel && $semesterId)
    <div class="row mb-3 align-items-center">
        <div class="col-lg-6">
            <h5 class="text-dark"><i class="mdi mdi-account-multiple me-2"></i> Entri Data Catatan Wali Kelas</h5>
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
                    <th width="30%">
                        <p class="mb-0">Nama Lengkap</p>
                        <small>Nomor Induk Sekolah & Nasional</small>
                    </th>
                    <th width="30%">
                        <p class="mb-0">Form</p>
                        <small>Entri Catatan Wali Kelas</small>
                    </th>
                    <th width="30%">
                        <p class="mb-0">Catatan</p>
                        <small>Catatan Tersimpan</small>
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
                        <p class="mb-0">{{ $pelajar->nama_lengkap }}</p>
                        <small>{{ $pelajar->nomor_induk ?? '-' }} | {{ $pelajar->nisn ?? '-' }}</small>
                    </td>
                    <td>
                        <textarea
                            wire:key="catatan-input-{{ $pelajar->pelajar_id }}"
                            wire:model.defer="catatanInput.{{ $pelajar->pelajar_id }}"
                            class="form-control form-control-sm"
                            rows="3"
                            placeholder="Masukkan catatan untuk pelajar..."
                            maxlength="5000"></textarea>
                        @error('catatanInput.' . $pelajar->pelajar_id)
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </td>
                    <td style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word; max-width: 300px;">
                        @if($pelajar->catatan_sekarang)
                        @php
                        $catatan = $pelajar->catatan_sekarang;
                        $teksPendek = Str::limit($catatan, 150);
                        @endphp
                        <p class="mb-0">
                            <a href="javascript:void(0)"
                                class="text-muted fs-7 text-decoration-none"
                                onclick="showFullCatatan('{{ addslashes($pelajar->nama_lengkap) }}', `{{ addslashes($catatan) }}`)"
                                title="Klik untuk melihat catatan lengkap">
                                {{ $teksPendek }}
                            </a>
                        </p>
                        @if($pelajar->tanggal_input)
                        <small class="text-muted">
                            <i class="mdi mdi-clock-outline me-1"></i>
                            {{ \Carbon\Carbon::parse($pelajar->tanggal_input)->format('d/m/Y') }}
                        </small>
                        @endif
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($pelajar->catatan_sekarang)
                        <button type="button"
                            wire:key="delete-btn-{{ $pelajar->pelajar_id }}"
                            id="delete-btn-{{ $pelajar->pelajar_id }}"
                            class="btn btn-sm btn-outline-danger"
                            onclick="confirmDeleteCatatan('{{ $pelajar->pelajar_id }}')"
                            title="Hapus Data">
                            <i class="mdi mdi-delete"></i>
                        </button>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="mdi mdi-information-outline me-2"></i>
                        Tidak ada data pelajar
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
    <div class="d-flex justify-content-end mt-3">
        <button type="button"
            class="btn btn-labeled btn-outline-secondary me-2"
            wire:click="resetCatatan"
            wire:loading.attr="disabled"
            wire:target="resetCatatan">
            <span class="btn-label">
                <i class="mdi mdi-delete-sweep-outline"></i>
            </span>
            Reset
        </button>

        <button type="button"
            class="btn btn-labeled btn-primary"
            wire:click="saveCatatan"
            wire:loading.attr="disabled"
            wire:target="saveCatatan">
            <span class="btn-label">
                <i class="mdi mdi-loading mdi-spin d-none"
                    wire:loading.class.remove="d-none"
                    wire:target="saveCatatan">
                </i>
                <i class="mdi mdi-content-save"
                    wire:loading.class="d-none"
                    wire:target="saveCatatan">
                </i>
            </span>
            <span wire:loading.class="d-none" wire:target="saveCatatan">
                Simpan
            </span>
            <span class="d-none" wire:loading.class.remove="d-none" wire:target="saveCatatan">
                Menyimpan...
            </span>
        </button>
    </div>
    @elseif($rombel && !$semesterId)
    <div class="alert alert-warning text-center" role="alert">
        <i class="mdi mdi-information-outline me-2"></i>
        <strong>Silakan pilih Tahun Ajaran dan Semester untuk mulai mengentri catatan pelajar.</strong>
    </div>
    @endif

    {{-- Loading Overlay --}}
    <div wire:loading.flex
        wire:target="saveCatatan,searchPelajar,tahunAjaranId,semesterId"
        class="position-fixed top-0 start-0 w-100 h-100 align-items-center justify-content-center"
        style="background-color: rgba(0,0,0,0.3); z-index: 9999; display: none;">
        <div class="spinner-border text-success" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Function untuk menampilkan catatan lengkap
        window.showFullCatatan = function(namaPelajar, catatan) {
            Swal.fire({
                title: 'Catatan untuk ' + namaPelajar,
                html: '<div style="text-align: left; white-space: pre-wrap;">' + catatan + '</div>',
                width: '600px',
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#3085d6',
            });
        };

        // Function untuk handle delete confirmation dengan loading state
        window.confirmDeleteCatatan = function(pelajarId) {
            Swal.fire({
                icon: 'warning',
                title: 'Hapus Catatan Pelajar?',
                text: 'Anda yakin ingin menghapus semua catatan untuk pelajar ini?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then(result => {
                if (result.isConfirmed) {
                    const btn = document.getElementById(`delete-btn-${pelajarId}`);
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i>';
                    }
                    Livewire.dispatch('deleteCatatan', [pelajarId]);
                }
            });
        };

        // SweetAlert Handlers
        window.addEventListener('swal:success', event => {
            let detail = event.detail.params ?? event.detail[0] ?? event.detail;
            if (typeof detail === 'string') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: detail,
                    showConfirmButton: true
                });
            } else if (typeof detail === 'object' && detail !== null) {
                Swal.fire({
                    icon: 'success',
                    title: detail.title ?? 'Berhasil!',
                    text: detail.text ?? '',
                    showConfirmButton: true
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