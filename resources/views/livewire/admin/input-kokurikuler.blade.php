<div>
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="page-header mb-0 border-bottom">
                                <div class="d-flex align-items-center">
                                    <h5 class="text-dark"><i class="mdi mdi-filter me-2"></i> Filter Data Kokurikuler</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3 row">
                                <div class="col-sm-4">
                                    <label class="form-label">Tahun Ajaran</label>
                                    <select wire:model.live="tahunAjaranId" class="form-select">
                                        <option value="">-- Pilih Tahun Ajaran --</option>
                                        @foreach($tahunAjaranList as $ta)
                                        <option value="{{ $ta->id }}">
                                            {{ $ta->nama }}
                                            @if($ta->status === 'aktif') (Aktif) @endif
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-4">
                                    <label class="form-label">Semester</label>
                                    <select wire:model.live="semesterId" class="form-select"
                                        @if(!$tahunAjaranId) disabled @endif>
                                        <option value="">-- Pilih Semester --</option>
                                        @foreach($semesterList as $smt)
                                        <option value="{{ $smt->id }}">
                                            Semester {{ $smt->semester->nama }}
                                            @if($smt->status === 'aktif') (Aktif) @endif
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-4">
                                    <label class="form-label">Rombongan Belajar</label>
                                    <select wire:model.live="rombelId" class="form-select"
                                        @if(!$semesterId) disabled @endif>
                                        <option value="">-- Pilih Rombel --</option>
                                        @foreach($rombelList as $rb)
                                        <option value="{{ $rb->id }}">
                                            {{ $rb->nama }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Info Rombel --}}
                    @if($rombel && $selectedRombelPengajarId)
                    <div class="alert alert-success py-3 mt-3" role="alert">
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
                                            {{ $rombel->tahunAjaranKurikulum->kurikulum->nama ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                        style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-calendar-clock text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3 d-flex flex-column justify-content-center">
                                        <small class="text-muted lh-2">Tahun Ajaran & Semester</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">
                                            @php
                                            $selectedSemester = $semesterList->firstWhere('id', $semesterId);
                                            $selectedTahunAjaran = $tahunAjaranList->firstWhere('id', $tahunAjaranId);
                                            @endphp
                                            {{ $selectedTahunAjaran->nama ?? '' }}
                                            ~ {{ $selectedSemester->semester->nama ?? '' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                        style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-account-group text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3 d-flex flex-column justify-content-center">
                                        <small class="text-muted lh-2">Rombel</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">{{ $rombel->nama ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                        style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-shield-star text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3 d-flex flex-column justify-content-center">
                                        <small class="text-muted lh-2">Jurusan</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">
                                            {{ $rombel->jurusan->nama ?? 'Belum Ada' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                        style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-account-tie text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3 d-flex flex-column justify-content-center">
                                        <small class="text-muted lh-2">Wali Kelas</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">
                                            {{ $rombel->waliKelas->name ?? 'Belum Ada' }}
                                        </p>
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

    @if($rombelId && $semesterId)
    <div class="row mt-1">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3 align-items-center">
                        <div class="col-lg-6">
                            <h5 class="text-dark"><i class="mdi mdi-school me-2"></i> Entri Data Kokurikuler</h5>
                        </div>
                        <div class="col-lg-6 d-flex justify-content-end">
                            <div class="input-group" style="width: 250px;">
                                <input type="text"
                                    wire:model.live.debounce.300ms="searchPelajar"
                                    class="form-control"
                                    placeholder="Cari nama, atau nomor induk...">
                            </div>

                            {{-- Tombol Generate Capaian --}}
                            <button
                                type="button"
                                class="btn btn-outline-primary btn-sm ms-2"
                                wire:click="openGenerateModal"
                                wire:key="btn-generate-capaian"
                                title="Generate Capaian Kokurikuler"
                                style="padding: 0.25rem 0.5rem; width: 2.25rem; height: calc(2.25rem + 2px); display: flex; align-items: center; justify-content: center;">
                                <i class="mdi mdi-auto-fix"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Tabel Kokurikuler (Diadopsi dari input-nilai-akhir.blade.php) --}}
                    <div class="table-responsive" wire:loading.class.delay.longest="opacity-50">
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
                                        <small>Entri Predikat</small>
                                    </th>
                                    <th width="10%">
                                        <p class="mb-0">Predikat</p>
                                        <small>Tersimpan</small>
                                    </th>
                                    <th width="35%">
                                        <p class="mb-0">Capaian</p>
                                        <small>Deskripsi Capaian Tersimpan</small>
                                    </th>
                                    <th width="5%">
                                        <p class="mb-0">Aksi</p>
                                        <small>Delete</small>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pelajarData as $index => $pelajar)
                                <tr wire:key="kokurikuler-row-{{ $pelajar->pelajar_id }}">
                                    <td class="text-center align-middle">{{ $pelajarData->firstItem() + $index }}</td>
                                    <td class="align-middle">
                                        <p class="mb-0">{{ $pelajar->nama_lengkap }}</p>
                                        <small class="text-muted">{{ $pelajar->nomor_induk }} | {{ $pelajar->nisn }}</small>
                                    </td>
                                    <td>
                                        <select
                                            wire:model.defer="kokurikulerInput.{{ $pelajar->pelajar_id }}.predikat"
                                            class="form-select form-select-sm @error('kokurikulerInput.'.$pelajar->pelajar_id.'.predikat') is-invalid @enderror">
                                            <option value="">-- Pilih Predikat --</option>
                                            @foreach($predikatOptions as $key => $label)
                                            <option value="{{ $key }}">{{ $key }} - {{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('kokurikulerInput.'.$pelajar->pelajar_id.'.predikat')
                                        <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    </td>
                                    <td class="align-middle">
                                        @if($pelajar->kokurikuler_existing)
                                        <div class="small">
                                            {{-- Menampilkan label predikat yang di-lookup dari predikatOptions --}}
                                            @php
                                            $predikatKey = $pelajar->kokurikuler_existing->predikat ?? '-';
                                            $predikatLabel = $predikatOptions[$predikatKey] ?? $predikatKey;
                                            @endphp
                                            <span class="badge bg-success mb-1">
                                                {{ $predikatKey }} - {{ $predikatLabel }}
                                            </span>
                                        </div>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word; max-width: 300px;">
                                        <div class="text-muted fs-7">
                                            @if($pelajar->kokurikuler_existing && $pelajar->kokurikuler_existing->capaian)
                                            Data Tersimpan: {{ Str::limit($pelajar->kokurikuler_existing->capaian, 80) }}
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                        @error('kokurikulerInput.'.$pelajar->pelajar_id.'.capaian')
                                        <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($pelajar->kokurikuler_existing)
                                        <button type="button"
                                            wire:key="delete-kokurikuler-btn-{{ $pelajar->pelajar_id }}"
                                            id="delete-kokurikuler-btn-{{ $pelajar->pelajar_id }}"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDeleteKokurikuler('{{ $pelajar->pelajar_id }}')"
                                            title="Hapus Data Kokurikuler">
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
                                        @if($searchPelajar)
                                        Tidak ada pelajar yang ditemukan dengan kata kunci "{{ $searchPelajar }}"
                                        @else
                                        Data pelajar tidak ditemukan atau rombel belum memiliki pelajar.
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

                    @if($pelajarData->count() > 0)
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            <i class="mdi mdi-information-outline"></i>
                            Pilih predikat dan masukkan capaian kokurikuler untuk setiap pelajar
                        </div>

                        <div>
                            {{-- Tombol Reset --}}
                            <button
                                type="button"
                                class="btn btn-labeled btn-outline-secondary me-2"
                                wire:click="confirmResetKokurikuler"
                                wire:loading.attr="disabled"
                                wire:target="confirmResetKokurikuler">
                                <span class="btn-label">
                                    <i class="mdi mdi-delete-sweep-outline"></i>
                                </span>
                                Reset
                            </button>

                            {{-- Tombol Simpan --}}
                            <button
                                type="button"
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
                                <span class="text-normal" wire:loading.class="d-none" wire:target="saveKokurikuler">
                                    Simpan
                                </span>
                                <span class="text-loading d-none" wire:loading.class.remove="d-none" wire:target="saveKokurikuler">
                                    Menyimpan...
                                </span>
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="alert alert-danger text-center" role="alert">
                <i class="mdi mdi-information-outline me-2"></i>
                <strong>Silakan pilih Tahun Ajaran, Semester, dan Rombel/Kelas untuk mulai menginput data kokurikuler.</strong>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL GENERATE CAPAIAN KOKURIKULER (Tanpa Subdimensi) --}}
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
                        wire:click="closeGenerateModal" {{-- Panggil method Livewire --}}
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
                            <li>Pelajar dengan **Predikat** tersimpan: <strong><span id="modal-count-kokurikuler">0</span> orang</strong></li>
                            <li>Capaian yang belum terisi: <strong><span id="modal-count-kosong">0</span> orang</strong></li>
                            <li>Template tersedia: <strong><span id="modal-count-template">0</span> template</strong></li>
                        </ul>
                    </div>

                    {{-- Pilihan Mode Generate --}}
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
                                    Menimpa semua capaian yang ada **dengan predikat yang sudah diinput** (<span id="modal-count-kokurikuler-2">0</span> pelajar)
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
                        class="btn btn-secondary"
                        wire:click="closeGenerateModal"
                        wire:loading.attr="disabled"
                        wire:target="generateCapaian">
                        Batal
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        wire:click="generateCapaian"
                        wire:loading.attr="disabled"
                        wire:target="generateCapaian">
                        <i class="mdi mdi-check me-1"
                            wire:loading.class="mdi-loading mdi-spin"
                            wire:loading.class.remove="mdi-check"
                            wire:target="generateCapaian">
                        </i>
                        Generate
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Inisialisasi Modal Generate ---
        const generateModalEl = document.getElementById('generateKokurikulerModal');
        let generateModal = null;

        if (typeof bootstrap !== 'undefined' && generateModalEl) {
            generateModal = new bootstrap.Modal(generateModalEl, {
                backdrop: 'static',
                keyboard: true
            });

            // Reset mode ke 'empty' saat modal ditutup
            generateModalEl.addEventListener('hidden.bs.modal', function() {
                // Panggil method Livewire untuk reset state terkait generate
                Livewire.dispatch('closeGenerateModal');
            });
        }

        // --- Livewire Event Listeners ---

        // ✅ Listener untuk membuka modal
        window.addEventListener('show-generate-modal', event => {
            const data = event.detail.params ?? event.detail[0] ?? event.detail;

            // Update nilai di modal (disesuaikan dengan property Kokurikuler)
            document.getElementById('modal-count-kokurikuler').textContent = data.countPelajarWithKokurikuler || 0;
            document.getElementById('modal-count-kokurikuler-2').textContent = data.countPelajarWithKokurikuler || 0;
            document.getElementById('modal-count-kosong').textContent = data.countCapaianKosong || 0;
            document.getElementById('modal-count-kosong-2').textContent = data.countCapaianKosong || 0;
            document.getElementById('modal-count-template').textContent = data.countTemplateAvailable || 0;

            if (generateModal) {
                generateModal.show();
            }
        });

        // ✅ Listener untuk menutup modal
        window.addEventListener('hide-generate-modal', event => {
            if (generateModal) {
                generateModal.hide();
            }
        });

        // ✅ Function untuk handle delete confirmation kokurikuler
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
                    // Tampilkan loading pada tombol spesifik
                    const btn = document.getElementById(`delete-kokurikuler-btn-${pelajarId}`);
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i>';
                    }

                    // Dispatch ke backend
                    Livewire.dispatch('deleteKokurikuler', [pelajarId]);
                }
            });
        };

        // --- SweetAlert Handlers ---

        window.addEventListener('swal:success', event => {
            let detail = event.detail.params ?? event.detail[0] ?? event.detail;
            const params = typeof detail === 'string' ? {
                text: detail
            } : detail;

            Swal.fire({
                icon: 'success',
                title: params.title ?? 'Berhasil!',
                text: params.text ?? '',
                showConfirmButton: true,
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
                // Menggunakan html dan replace \n dengan <br> untuk multiline
                html: (params.text ?? '').replace(/\n/g, '<br>'),
                confirmButtonText: 'OK',
                width: '600px',
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