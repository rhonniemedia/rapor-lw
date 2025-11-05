<div>
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="page-header mb-0 border-bottom">
                                <div class="d-flex align-items-center">
                                    <h5 class="text-dark"><i class="mdi mdi-filter me-2"></i> Filter Data Ekstrakurikuler</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3 row">
                                <div class="col-sm-3">
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

                                <div class="col-sm-3">
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

                                <div class="col-sm-3">
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

                                <div class="col-sm-3">
                                    <label class="form-label">Ekstrakurikuler</label>
                                    <select wire:model.live="ekstrakurikulerId" class="form-select"
                                        @if(!$rombelId) disabled @endif>
                                        <option value="">-- Pilih Ekstrakurikuler --</option>
                                        @foreach($ekstrakurikulerList as $ekskul)
                                        <option value="{{ $ekskul->id }}">
                                            {{ $ekskul->nama }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Info Rombel & Ekstrakurikuler --}}
                    @if($rombel && $selectedRombelPengajarId && $selectedEkstrakurikuler)
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

                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                        style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-trophy text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3 d-flex flex-column justify-content-center">
                                        <small class="text-muted lh-2">Ekstrakurikuler</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">
                                            {{ $selectedEkstrakurikuler->nama }}
                                        </p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                        style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-account-star text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3 d-flex flex-column justify-content-center">
                                        <small class="text-muted lh-2">Pembina</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">
                                            {{ $selectedEkstrakurikuler->pembina->name ?? 'Belum Ada' }}
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

    @if($rombelId && $semesterId && $ekstrakurikulerId)
    <div class="row mt-1">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3 align-items-center">
                        <div class="col-lg-6">
                            <h5 class="text-dark"><i class="mdi mdi-trophy me-2"></i> Entri Data Ekstrakurikuler Pelajar</h5>
                        </div>
                        <div class="col-lg-6 d-flex justify-content-end">
                            <div class="input-group" style="width: 250px;"> <input type="text"
                                    wire:model.live.debounce.300ms="searchPelajar"
                                    class="form-control"
                                    placeholder="Cari nama, atau nomor induk...">
                            </div>

                            {{-- TOMBOL GENERATE DESKRIPSI (menggantikan tombol Create) --}}
                            <button
                                type="button"
                                class="btn btn-outline-primary btn-sm ms-2"
                                wire:click="openGenerateModal"
                                wire:key="btn-generate-deskripsi"
                                title="Generate Deskripsi Ekstrakurikuler"
                                style="padding: 0.25rem 0.5rem; width: 2.25rem; height: calc(2.25rem + 2px); display: flex; align-items: center; justify-content: center;">
                                <i class="mdi mdi-auto-fix"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Tabel Ekstrakurikuler (Struktur dikembalikan ke versi awal) --}}
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
                                        <small>Teks Capaian</small>
                                    </th>
                                    <th width="5%">
                                        <p class="mb-0">Aksi</p>
                                        <small>Delete</small>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pelajarData as $index => $pelajar)
                                <tr wire:key="ekskul-row-{{ $pelajar->pelajar_id }}">
                                    <td class="text-center align-middle">{{ $pelajarData->firstItem() + $index }}</td>
                                    <td class="align-middle">
                                        <p class="mb-0">{{ $pelajar->nama_lengkap }}</p>
                                        <small class="text-muted">{{ $pelajar->nomor_induk }} | {{ $pelajar->nisn }}</small>
                                    </td>
                                    <td>
                                        <select wire:model.defer="ekstrakurikulerInput.{{ $pelajar->pelajar_id }}.nilai"
                                            class="form-select form-select-sm @error('ekstrakurikulerInput.'.$pelajar->pelajar_id.'.nilai') is-invalid @enderror">
                                            <option value="">-- Pilih Nilai --</option>
                                            @foreach($nilaiOptions as $key => $label)
                                            <option value="{{ $key }}">{{ $key }} - {{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('ekstrakurikulerInput.'.$pelajar->pelajar_id.'.nilai')
                                        <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    </td>
                                    <td>
                                        @if($pelajar->ekstrakurikuler_existing)
                                        <span class="badge bg-success mb-1">
                                            {{ $pelajar->ekstrakurikuler_existing->nilai ?? '-' }}
                                        </span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word; max-width: 300px;">
                                        @if($pelajar->ekstrakurikuler_existing && $pelajar->ekstrakurikuler_existing->deskripsi)
                                        <span class="text-muted fs-7">
                                            {{ Str::limit($pelajar->ekstrakurikuler_existing->deskripsi, 100) }}
                                        </span>
                                        @endif

                                        @error('ekstrakurikulerInput.'.$pelajar->pelajar_id.'.deskripsi')
                                        <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    </td>
                                    <td class="align-middle">
                                        @if($pelajar->ekstrakurikuler_existing)
                                        <button type="button"
                                            wire:key="delete-ekskul-btn-{{ $pelajar->pelajar_id }}"
                                            id="delete-ekskul-btn-{{ $pelajar->pelajar_id }}"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDeleteEkstrakurikuler('{{ $pelajar->pelajar_id }}')"
                                            title="Hapus Data Ekstrakurikuler">
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
                            Pilih nilai dan masukkan deskripsi untuk setiap pelajar
                        </div>

                        <div>
                            {{-- Tombol Reset --}}
                            <button
                                type="button"
                                class="btn btn-labeled btn-outline-secondary me-2"
                                wire:click="confirmResetEkstrakurikuler"
                                wire:loading.attr="disabled"
                                wire:target="confirmResetEkstrakurikuler">
                                <span class="btn-label">
                                    <i class="mdi mdi-delete-sweep-outline"></i>
                                </span>
                                Reset
                            </button>

                            {{-- Tombol Simpan --}}
                            <button
                                type="button"
                                class="btn btn-labeled btn-primary"
                                wire:click="saveEkstrakurikuler"
                                wire:loading.attr="disabled"
                                wire:target="saveEkstrakurikuler">
                                <span class="btn-label">
                                    <i class="mdi mdi-loading mdi-spin d-none"
                                        wire:loading.class.remove="d-none"
                                        wire:target="saveEkstrakurikuler">
                                    </i>
                                    <i class="mdi mdi-content-save"
                                        wire:loading.class="d-none"
                                        wire:target="saveEkstrakurikuler">
                                    </i>
                                </span>
                                <span class="text-normal" wire:loading.class="d-none" wire:target="saveEkstrakurikuler">
                                    Simpan
                                </span>
                                <span class="text-loading d-none" wire:loading.class.remove="d-none" wire:target="saveEkstrakurikuler">
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
                <strong>Silakan pilih Tahun Ajaran, Semester, Rombel/Kelas, dan Ekstrakurikuler untuk mulai menginput data.</strong>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL GENERATE DESKRIPSI EKSTRAKURIKULER --}}
    <div
        class="modal fade"
        id="generateEkskulModal"
        tabindex="-1"
        aria-labelledby="generateEkskulModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generateEkskulModalLabel">
                        <i class="mdi mdi-auto-fix me-2"></i>
                        Generate Deskripsi Ekstrakurikuler
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        wire:click="closeGenerateModal"
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
                            <li>Pelajar dengan **Nilai (Predikat)** tersimpan: <strong><span id="modal-count-ekskul-nilai">0</span> orang</strong></li>
                            <li>Deskripsi yang belum terisi: <strong><span id="modal-count-kosong">0</span> orang</strong></li>
                            <li>Template tersedia (Spesifik/Umum): <strong><span id="modal-count-template">0</span> template</strong></li>
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
                                id="modeEkskulEmpty">
                            <label class="form-check-label" for="modeEkskulEmpty">
                                <strong>Generate Deskripsi Kosong</strong>
                                <br>
                                <small class="text-muted">
                                    Hanya mengisi deskripsi yang masih kosong/NULL (<span id="modal-count-kosong-2">0</span> pelajar)
                                </small>
                            </label>
                        </div>
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="radio"
                                wire:model="generateMode"
                                value="all"
                                id="modeEkskulAll">
                            <label class="form-check-label" for="modeEkskulAll">
                                <strong>Regenerate Semua Deskripsi</strong>
                                <br>
                                <small class="text-muted">
                                    Menimpa semua deskripsi yang ada **dengan nilai yang sudah diinput** (<span id="modal-count-ekskul-nilai-2">0</span> pelajar)
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
                        <strong>Perhatian!</strong> Mode ini akan menimpa semua deskripsi yang sudah ada sebelumnya.
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        wire:click="closeGenerateModal"
                        wire:loading.attr="disabled"
                        wire:target="generateDeskripsi">
                        Batal
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        wire:click="generateDeskripsi"
                        wire:loading.attr="disabled"
                        wire:target="generateDeskripsi">
                        <i class="mdi mdi-check me-1"
                            wire:loading.class="mdi-loading mdi-spin"
                            wire:loading.class.remove="mdi-check"
                            wire:target="generateDeskripsi">
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
        const generateModalEkskulEl = document.getElementById('generateEkskulModal');
        let generateModalEkskul = null;

        if (typeof bootstrap !== 'undefined' && generateModalEkskulEl) {
            generateModalEkskul = new bootstrap.Modal(generateModalEkskulEl, {
                backdrop: 'static',
                keyboard: true
            });

            // Event listener saat modal ditutup
            generateModalEkskulEl.addEventListener('hidden.bs.modal', function() {
                // Panggil method Livewire untuk reset state terkait generate
                Livewire.dispatch('closeGenerateModal');
            });
        }

        // --- Livewire Event Listeners ---

        // ✅ Listener untuk membuka modal
        window.addEventListener('show-generate-ekskul-modal', event => {
            const data = event.detail.params ?? event.detail[0] ?? event.detail;

            // Update nilai di modal (disesuaikan dengan property Ekstrakurikuler)
            document.getElementById('modal-count-ekskul-nilai').textContent = data.countPelajarWithNilai || 0;
            document.getElementById('modal-count-ekskul-nilai-2').textContent = data.countPelajarWithNilai || 0;
            document.getElementById('modal-count-kosong').textContent = data.countDeskripsiKosong || 0;
            document.getElementById('modal-count-kosong-2').textContent = data.countDeskripsiKosong || 0;
            document.getElementById('modal-count-template').textContent = data.countTemplateAvailable || 0;

            if (generateModalEkskul) {
                generateModalEkskul.show();
            }
        });

        // ✅ Listener untuk menutup modal
        window.addEventListener('hide-generate-ekskul-modal', event => {
            if (generateModalEkskul) {
                generateModalEkskul.hide();
            }
        });

        // ✅ Function untuk handle delete confirmation ekstrakurikuler
        window.confirmDeleteEkstrakurikuler = function(pelajarId) {
            Swal.fire({
                icon: 'warning',
                title: 'Hapus Data Ekstrakurikuler?',
                text: 'Anda yakin ingin menghapus data ekstrakurikuler pelajar ini?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then(result => {
                if (result.isConfirmed) {
                    // Tampilkan loading pada tombol spesifik
                    const btn = document.getElementById(`delete-ekskul-btn-${pelajarId}`);
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i>';
                    }

                    // Dispatch ke backend
                    Livewire.dispatch('deleteEkstrakurikuler', [pelajarId]);
                }
            });
        };

        // --- SweetAlert Handlers (Diadopsi) ---

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