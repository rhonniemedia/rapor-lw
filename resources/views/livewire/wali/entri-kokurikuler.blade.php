<div>
    {{-- Page Header --}}
    <div class="page-header pb-3 mb-4 border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="icon-wrapper position-relative">
                    <span class="bg-gradient-primary p-2 rounded-3 shadow-sm me-3 d-inline-flex align-items-center justify-content-center">
                        <i class="mdi mdi-leaf mdi-24px text-white"></i>
                    </span>
                </div>
                <div>
                    <h4 class="mb-1 text-dark fw-bold">Entri Data Kokurikuler</h4>
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-muted">Kelola data Kokurikuler Pelajar</small>
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
                <div class="col-md-4">
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

                <div class="col-md-4">
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

                <div class="col-md-4">
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
                    <h5 class="text-dark"><i class="mdi mdi-filter"></i> Filter dan Generate Data</h5>
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

                {{-- Pencarian + Generate --}}
                <div class="col-md-4">
                    <label class="form-label">Cari & Generate</label>
                    <div class="d-flex gap-2">
                        <input type="search"
                            wire:model.live.debounce.300ms="searchPelajar"
                            class="form-control"
                            placeholder="Cari..."
                            @if(!$semesterId) disabled @endif>

                        <button
                            type="button"
                            class="btn btn-outline-primary"
                            wire:click="openGenerateModal"
                            wire:key="btn-generate-capaian"
                            title="Generate"
                            @if(!$semesterId) disabled @endif
                            style="width: 2.5rem; display: flex; align-items: center; justify-content: center;">
                            <i class="mdi mdi-auto-fix"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endif

    {{-- Tabel Input Kokurikuler --}}
    @if($rombel && $semesterId)
    <div class="row mb-3 align-items-center">
        <div class="col-lg-6">
            <h5 class="text-dark"><i class="mdi mdi-account-multiple me-2"></i> Entri Data Kokurikuler</h5>
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
                    <th width="35%">
                        <p class="mb-0">Nama Lengkap</p>
                        <small>Nomor Induk Sekolah & Nasional</small>
                    </th>
                    <th width="10%">
                        <p class="mb-0">Form</p>
                        <small>Pilih Predikat</small>
                    </th>
                    <th width="10%">
                        <p class="mb-0">Predikat</p>
                        <small>Tersimpan</small>
                    </th>
                    <th width="35%">
                        <p class="mb-0">Data Tersimpan</p>
                        <small>Predikat & Capaian Tersimpan</small>
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
                        <select
                            wire:key="predikat-input-{{ $pelajar->pelajar_id }}"
                            wire:model.defer="kokurikulerInput.{{ $pelajar->pelajar_id }}.predikat"
                            class="form-select form-select-sm">
                            <option value="">Pilih Predikat</option>
                            @foreach($predikatOptions as $key => $label)
                            <option value="{{ $key }}">{{ $key }} - {{ $label }}</option>
                            @endforeach
                        </select>
                        @error('kokurikulerInput.' . $pelajar->pelajar_id . '.predikat')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </td>
                    <td>
                        @if($pelajar->kokurikuler_existing)
                        <div class="mb-1">
                            @if($pelajar->kokurikuler_existing->predikat)
                            @php $predikatKey = $pelajar->kokurikuler_existing->predikat; @endphp
                            <span class="badge
                                @if($predikatKey == 'A') bg-success
                                @elseif($predikatKey == 'B') bg-primary
                                @elseif($predikatKey == 'C') bg-warning
                                @else bg-secondary
                                @endif">
                                {{ $predikatKey }}
                            </span>
                            @endif
                        </div>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word; max-width: 300px;">
                        @if($pelajar->kokurikuler_existing && $pelajar->kokurikuler_existing->capaian)
                        @php
                        $capaian = $pelajar->kokurikuler_existing->capaian;
                        $teksPendek = Str::limit($capaian, 90);
                        @endphp
                        <p class="mb-0">
                            <a href="javascript:void(0)"
                                class="text-muted fs-7 text-decoration-none"
                                onclick="showFullCapaian('{{ addslashes($pelajar->nama_lengkap) }}', `{{ addslashes($capaian) }}`)"
                                title="Klik untuk melihat capaian lengkap">
                                {{ $teksPendek }}
                            </a>
                        </p>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($pelajar->kokurikuler_existing)
                        <button type="button"
                            wire:key="delete-btn-{{ $pelajar->pelajar_id }}"
                            id="delete-btn-{{ $pelajar->pelajar_id }}"
                            class="btn btn-sm btn-outline-danger"
                            onclick="confirmDeleteKokurikuler('{{ $pelajar->pelajar_id }}')"
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
                    <td colspan="6" class="text-center text-muted py-4">
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
            wire:click="confirmResetKokurikuler"
            wire:loading.attr="disabled"
            wire:target="resetKokurikuler, confirmResetKokurikuler">
            <span class="btn-label">
                <i class="mdi mdi-delete-sweep-outline"></i>
            </span>
            Reset
        </button>

        <button type="button"
            class="btn btn-labeled btn-primary"
            wire:click="saveKokurikuler"
            wire:loading.attr="disabled"
            wire:target="saveKokurikuler">
            <span class="btn-label">
                <i class="mdi mdi-loading mdi-spin d-none"
                    wire:loading.class.remove="d-none"
                    wire:target="saveKokurikuler">
                </i>
                <i class="mdi mdi-content-save"
                    wire:loading.class="d-none"
                    wire:target="saveKokurikuler">
                </i>
            </span>
            <span wire:loading.class="d-none" wire:target="saveKokurikuler">
                Simpan
            </span>
            <span class="d-none" wire:loading.class.remove="d-none" wire:target="saveKokurikuler">
                Menyimpan...
            </span>
        </button>
    </div>
    @elseif($rombel && !$semesterId)
    <div class="alert alert-warning text-center" role="alert">
        <i class="mdi mdi-information-outline me-2"></i>
        <strong>Silakan pilih Tahun Ajaran dan Semester untuk mulai mengentri data kokurikuler.</strong>
    </div>
    @endif

    {{-- Loading Overlay --}}
    <div wire:loading.flex
        wire:target="saveKokurikuler,searchPelajar,tahunAjaranId,semesterId"
        class="position-fixed top-0 start-0 w-100 h-100 align-items-center justify-content-center"
        style="background-color: rgba(0,0,0,0.3); z-index: 9999; display: none;">
        <div class="spinner-border text-success" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    {{-- MODAL GENERATE CAPAIAN KOKURIKULER --}}
    <div
        class="modal fade"
        id="generateKokurikulerModal"
        tabindex="-1"
        aria-labelledby="generateKokurikulerModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generateKokurikulerModalLabel">
                        <i class="mdi mdi-auto-fix me-2"></i>
                        Generate Capaian Kokurikuler
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <h6 class="alert-heading mb-2">
                            <i class="mdi mdi-information-outline me-2"></i>
                            Informasi Data
                        </h6>
                        <ul class="mb-0 ps-3">
                            <li>Pelajar dengan predikat tersimpan: <strong><span id="modal-count-kokurikuler">0</span> orang</strong></li>
                            <li>Capaian yang belum terisi: <strong><span id="modal-count-kosong">0</span> orang</strong></li>
                            <li>Template tersedia: <strong><span id="modal-count-template">0</span> template</strong></li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Mode Generate:</label>
                        <div class="form-check mb-2">
                            <input
                                class="form-check-input"
                                type="radio"
                                wire:model="generateMode"
                                value="empty"
                                id="modeEmpty">
                            <label class="form-check-label" for="modeEmpty">
                                <strong>Generate Capaian Kosong</strong>
                                <br>
                                <small class="text-muted">
                                    Hanya mengisi capaian yang masih kosong/NULL (<span id="modal-count-kosong-2">0</span> pelajar)
                                </small>
                            </label>
                        </div>
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="radio"
                                wire:model="generateMode"
                                value="all"
                                id="modeAll">
                            <label class="form-check-label" for="modeAll">
                                <strong>Regenerate Semua Capaian</strong>
                                <br>
                                <small class="text-muted">
                                    Menimpa semua capaian yang ada dengan predikat yang sudah diinput (<span id="modal-count-kokurikuler-2">0</span> pelajar)
                                </small>
                            </label>
                        </div>
                        @error('generateMode')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    @if($generateMode === 'all')
                    <div class="alert alert-warning mb-0">
                        <i class="mdi mdi-alert me-2"></i>
                        <strong>Perhatian!</strong> Mode ini akan menimpa semua capaian yang sudah ada sebelumnya.
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-labeled btn-outline-secondary"
                        wire:click="closeGenerateModal"
                        wire:loading.attr="disabled"
                        wire:target="generateCapaian">
                        <span class="btn-label">
                            <i class="mdi mdi-close"></i>
                        </span>
                        Batal
                    </button>

                    <button
                        type="button"
                        class="btn btn-labeled btn-primary"
                        wire:click="generateCapaian"
                        wire:loading.attr="disabled"
                        wire:target="generateCapaian">
                        <span class="btn-label">
                            <i class="mdi mdi-loading mdi-spin d-none"
                                wire:loading.class.remove="d-none"
                                wire:target="generateCapaian">
                            </i>
                            <i class="mdi mdi-check"
                                wire:loading.class="d-none"
                                wire:target="generateCapaian">
                            </i>
                        </span>
                        <span wire:loading.class="d-none" wire:target="generateCapaian">
                            Generate
                        </span>
                        <span class="d-none" wire:loading.class.remove="d-none" wire:target="generateCapaian">
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi Modal Generate
        const generateModalEl = document.getElementById('generateKokurikulerModal');
        let generateModal = null;

        if (typeof bootstrap !== 'undefined' && generateModalEl) {
            generateModal = new bootstrap.Modal(generateModalEl, {
                backdrop: 'static',
                keyboard: true
            });

            // Reset mode ke 'empty' saat modal ditutup
            generateModalEl.addEventListener('hidden.bs.modal', function() {
                Livewire.dispatch('closeGenerateModal');
            });
        }

        // Listener untuk membuka modal
        window.addEventListener('show-generate-modal', event => {
            const data = event.detail.params ?? event.detail[0] ?? event.detail;

            document.getElementById('modal-count-kokurikuler').textContent = data.countPelajarWithKokurikuler || 0;
            document.getElementById('modal-count-kokurikuler-2').textContent = data.countPelajarWithKokurikuler || 0;
            document.getElementById('modal-count-kosong').textContent = data.countCapaianKosong || 0;
            document.getElementById('modal-count-kosong-2').textContent = data.countCapaianKosong || 0;
            document.getElementById('modal-count-template').textContent = data.countTemplateAvailable || 0;

            if (generateModal) {
                generateModal.show();
            }
        });

        // Listener untuk menutup modal
        window.addEventListener('hide-generate-modal', event => {
            if (generateModal) {
                generateModal.hide();
            }
        });

        // Function untuk menampilkan capaian lengkap
        window.showFullCapaian = function(namaPelajar, capaian) {
            Swal.fire({
                title: 'Capaian Kokurikuler - ' + namaPelajar,
                html: '<div style="text-align: left; white-space: pre-wrap;">' + capaian + '</div>',
                width: '600px',
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#3085d6',
            });
        };

        // Function untuk handle delete confirmation kokurikuler
        window.confirmDeleteKokurikuler = function(pelajarId) {
            Swal.fire({
                icon: 'warning',
                title: 'Hapus Data Kokurikuler?',
                text: 'Anda yakin ingin menghapus data kokurikuler pelajar ini?',
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
                    Livewire.dispatch('deleteKokurikuler', [pelajarId]);
                }
            });
        };

        // SweetAlert Handlers
        window.addEventListener('swal:success', event => {
            let detail = event.detail.params ?? event.detail[0] ?? event.detail;
            const params = typeof detail === 'string' ? {
                text: detail
            } : detail;
            Swal.fire({
                icon: 'success',
                title: params.title ?? 'Berhasil!',
                text: params.text ?? '',
                showConfirmButton: true
            });
        });

        window.addEventListener('swal:error', event => {
            let detail = event.detail.params ?? event.detail[0] ?? event.detail;
            const params = typeof detail === 'string' ? {
                text: detail
            } : detail;
            Swal.fire({
                icon: 'error',
                title: params.title ?? 'Error!',
                text: params.text ?? '',
                confirmButtonText: 'Tutup'
            });
        });

        window.addEventListener('swal:info', event => {
            let detail = event.detail.params ?? event.detail[0] ?? event.detail;
            const params = typeof detail === 'string' ? {
                text: detail
            } : detail;
            Swal.fire({
                icon: 'info',
                title: params.title ?? 'Info',
                text: params.text ?? '',
                timer: 2000,
                showConfirmButton: false
            });
        });

        window.addEventListener('swal:warning', event => {
            let detail = event.detail.params ?? event.detail[0] ?? event.detail;
            const params = typeof detail === 'string' ? {
                text: detail
            } : detail;
            Swal.fire({
                icon: 'warning',
                title: params.title ?? 'Perhatian!',
                html: (params.text ?? '').replace(/\n/g, '<br>'),
                confirmButtonText: 'OK',
                width: '600px'
            });
        });

        window.addEventListener('swal:confirm', event => {
            let detail = event.detail.params ?? event.detail[0] ?? event.detail;
            const params = typeof detail === 'string' ? {
                text: detail
            } : detail;
            Swal.fire({
                icon: 'question',
                title: params.title ?? 'Konfirmasi',
                text: params.text ?? '',
                showCancelButton: true,
                confirmButtonText: params.confirmButtonText ?? 'Ya',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then(result => {
                if (result.isConfirmed && params.nextEvent) {
                    Livewire.dispatch(params.nextEvent);
                }
            });
        });
    });
</script>
@endpush