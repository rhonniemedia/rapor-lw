<div>
    {{-- Filter Data Ekstrakurikuler --}}
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    {{-- Header Filter --}}
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="page-header mb-0 border-bottom">
                                <div class="d-flex align-items-center">
                                    <h5 class="text-dark"><i class="mdi mdi-filter me-2"></i> Filter Data Ekstrakurikuler</h5>
                                </div>
                            </div>
                        </div>

                        {{-- Form Filter --}}
                        <div class="col-lg-12">
                            <div class="mb-3 row">
                                {{-- Filter Tahun Ajaran --}}
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

                                {{-- Filter Semester --}}
                                <div class="col-sm-3">
                                    <label class="form-label">Semester</label>
                                    <select wire:model.live="semesterMurniId" class="form-select" @if(!$tahunAjaranId) disabled @endif>
                                        <option value="">-- Pilih Semester --</option>
                                        @foreach($semesterMurniList as $semester)
                                        <option value="{{ $semester->id }}">
                                            {{ $semester->nama }}
                                            @if($semesterAktif && $semesterAktif->semester_id === $semester->id) (Aktif) @endif
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Filter Rombongan Belajar --}}
                                <div class="col-sm-3">
                                    <label class="form-label">Rombongan Belajar</label>
                                    <select wire:model.live="selectedRombelId" class="form-select"
                                        @if(!$selectedTahunAjaranSemesterId) disabled @endif>
                                        <option value="">-- Pilih Rombel --</option>
                                        @foreach($rombelList as $rombelItem)
                                        <option value="{{ $rombelItem->id }}">
                                            {{ $rombelItem->nama }}
                                        </option>
                                        @endforeach
                                    </select>



                                </div>

                                {{-- Filter Ekstrakurikuler --}}
                                <div class="col-sm-3">
                                    <label class="form-label">Ekstrakurikuler</label>
                                    <select wire:model.live="selectedEkstrakurikulerId" class="form-select" @if(!$selectedRombelId) disabled @endif>
                                        <option value="">-- Pilih Ekstrakurikuler --</option>
                                        @foreach($ekstrakurikulerList as $ekskul)
                                        <option value="{{ $ekskul->id }}">
                                            {{ $ekskul->nama }} @if($ekskul->pembina) - {{ $ekskul->pembina->name }} @endif
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Info Rombel & Ekstrakurikuler --}}
                    @if($rombel && $semesterAktif && $selectedEkstrakurikuler)
                    <div class="alert alert-success py-3 mt-3" role="alert">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 bg-success rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-book text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Kurikulum</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">{{ $rombel->tahunAjaranKurikulum->kurikulum->nama ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 bg-success rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-calendar-clock text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Tahun Ajaran & Semester</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">
                                            {{ $semesterAktif->tahunAjaran->nama ?? 'N/A' }} ~ {{ $semesterAktif->semester->nama ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 bg-success rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-account-group text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Rombel</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">
                                            {{ $rombel->tingkat }} {{ $rombel->jurusan->alias ?? '' }} {{ $rombel->nama }}
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 bg-success rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-account-tie text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Wali Kelas</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">{{ $rombel->waliKelas->name ?? 'Belum Ditentukan' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 bg-warning rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-trophy text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Ekstrakurikuler</small>
                                        <p class="fw-bold mb-0 text-warning lh-sm">{{ $selectedEkstrakurikuler->nama ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 bg-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-account-star text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Pembina</small>
                                        <p class="fw-bold mb-0 text-primary lh-sm">{{ $pembinaName ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif


                    {{-- Tabel Entri Ekstrakurikuler --}}
                    @if($selectedRombelId && $selectedEkstrakurikulerId && $selectedTahunAjaranSemesterId)
                    <div class="mt-4">
                        <div class="row mb-3 align-items-center">
                            <div class="col-lg-6">
                                <h5 class="text-dark">
                                    <i class="mdi mdi-account-multiple me-2"></i>
                                    Entri Data Ekstrakurikuler <span class="badge bg-info ms-2">{{ $totalSiswa }} Siswa</span>
                                </h5>
                            </div>

                            <div class="col-lg-6 d-flex justify-content-end align-items-center">
                                <div class="input-group w-50">
                                    <input type="text"
                                        wire:model.live.debounce.300ms="searchPelajar"
                                        class="form-control"
                                        placeholder="Cari nama, atau nomor induk...">
                                    @if($searchPelajar)
                                    <button type="button" class="btn btn-secondary" wire:click="$set('searchPelajar', '')">
                                        <i class="mdi mdi-close"></i>
                                    </button>
                                    @endif
                                </div>

                                <button type="button"
                                    class="btn btn-outline-primary btn-sm ms-2 d-flex align-items-center justify-content-center"
                                    title="Buat Deskripsi Otomatis"
                                    wire:click="openGenerateModal"
                                    style="padding: 0.25rem 0.5rem; width: 2.25rem; height: calc(2.25rem + 2px);">
                                    <i class="mdi mdi-auto-fix"></i>
                                </button>
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
                                    @forelse($pelajarData as $index => $pelajar)
                                    <tr wire:key="ekskul-pelajar-{{ $pelajar->pelajar_id }}">
                                        <td class="text-center">{{ $pelajarData->firstItem() + $index }}</td>
                                        <td>
                                            <p class="mb-0">{{ $pelajar->nama_lengkap }}</p><small class="text-muted">{{ $pelajar->nomor_induk ?? '-' }} | {{ $pelajar->nisn ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <select wire:model.defer="ekskulInput.{{ $pelajar->pelajar_id }}.nilai" class="form-select form-select-sm">
                                                <option value="">-- Pilih Nilai --</option>
                                                @foreach($predikatOptions as $key => $label)
                                                <option value="{{ $key }}">{{ $key }} - {{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('ekskulInput.' . $pelajar->pelajar_id . '.nilai')
                                            <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </td>
                                        <td>
                                            @if($pelajar->ekskul_existing)
                                            @php
                                            $nilaiKey = $pelajar->ekskul_existing->nilai;
                                            @endphp
                                            <span class="badge 
                                                @if($nilaiKey == 'A') bg-success
                                                @elseif($nilaiKey == 'B') bg-warning text-dark
                                                @elseif($nilaiKey == 'C') bg-primary
                                                @else bg-secondary
                                                @endif">
                                                {{ $nilaiKey }}
                                            </span>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word; max-width: 300px;">
                                            @if($pelajar->ekskul_existing && $pelajar->ekskul_existing->deskripsi)
                                            @php
                                            $deskripsi = $pelajar->ekskul_existing->deskripsi;
                                            $teksPendek = Str::limit($deskripsi, 90);
                                            @endphp
                                            <a href="javascript:void(0)"
                                                class="text-muted text-decoration-none"
                                                onclick="showFullDeskripsi('{{ addslashes($pelajar->nama_lengkap) }}', `{{ addslashes($deskripsi) }}`)"
                                                title="Klik untuk melihat deskripsi lengkap">
                                                {{ $teksPendek }}
                                            </a>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                            @error('ekskulInput.' . $pelajar->pelajar_id . '.deskripsi')
                                            <small class="text-danger d-block">{{ $message }}</small>
                                            @enderror
                                        </td>
                                        <td class="">
                                            @if($pelajar->ekskul_existing)
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm"
                                                onclick="confirmDeleteEkskul('{{ $pelajar->pelajar_id }}')">
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
                                            Tidak ada data pelajar untuk Rombel ini atau data Ekstrakurikuler belum dipilih.
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

                        <div class="d-flex justify-content-end mt-3">
                            <button type="button"
                                class="btn btn-outline-secondary me-2"
                                wire:click="$dispatch('swal:confirm', { title: 'Reset Input?', text: 'Input akan dikembalikan ke data tersimpan di database.', nextEvent: 'confirmResetEkskul' })"
                                wire:loading.attr="disabled"
                                wire:target="resetEkskul">
                                <i class="mdi mdi-delete-sweep-outline me-1"></i> Reset Input
                            </button>

                            <button type="button"
                                class="btn btn-primary"
                                wire:click="saveEkskul"
                                wire:loading.attr="disabled"
                                wire:target="saveEkskul">
                                <i class="mdi mdi-content-save me-1"
                                    wire:loading.class="mdi-loading mdi-spin"
                                    wire:loading.class.remove="mdi-content-save"
                                    wire:target="saveEkskul">
                                </i>
                                Simpan
                            </button>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-warning text-center mt-4" role="alert">
                        <i class="mdi mdi-information-outline me-2"></i>
                        <strong>Silakan pilih Tahun Ajaran, Semester, Rombel, dan Ekstrakurikuler untuk mulai mengentri data.</strong>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal dan Scripts di sini (Tambahkan di bawah jika Anda punya file scripts.js atau stack) --}}

    {{-- MODAL GENERATE DESKRIPSI EKSTRAKURIKULER --}}
    <div class="modal fade"
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
                    <button type="button"
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
                            <li>Pelajar dengan nilai tersimpan: <strong><span id="modal-count-ekskul">0</span> orang</strong></li>
                            <li>Deskripsi yang belum terisi: <strong><span id="modal-count-kosong">0</span> orang</strong></li>
                            <li>Template tersedia: <strong><span id="modal-count-template">0</span> template</strong></li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Mode Generate:</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input"
                                type="radio"
                                wire:model="generateMode"
                                value="empty"
                                id="modeEmpty">
                            <label class="form-check-label" for="modeEmpty">
                                <strong>Generate Deskripsi Kosong</strong>
                                <br>
                                <small class="text-muted">
                                    Hanya mengisi deskripsi yang masih kosong/NULL (<span id="modal-count-kosong-2">0</span> pelajar)
                                </small>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input"
                                type="radio"
                                wire:model="generateMode"
                                value="all"
                                id="modeAll">
                            <label class="form-check-label" for="modeAll">
                                <strong>Regenerate Semua Deskripsi</strong>
                                <br>
                                <small class="text-muted">
                                    Menimpa semua deskripsi yang ada (<span id="modal-count-ekskul-2">0</span> pelajar)
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
                    <button type="button"
                        class="btn btn-secondary"
                        wire:click="closeGenerateModal"
                        wire:loading.attr="disabled"
                        wire:target="generateDeskripsi">
                        Batal
                    </button>
                    <button type="button"
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
        // Inisialisasi Modal Generate
        const generateModalEl = document.getElementById('generateEkskulModal');
        let generateModal = null;

        if (typeof bootstrap !== 'undefined' && generateModalEl) {
            generateModal = new bootstrap.Modal(generateModalEl, {
                backdrop: 'static',
                keyboard: true
            });

            generateModalEl.addEventListener('hidden.bs.modal', function() {
                Livewire.dispatch('closeGenerateModal');
            });
        }

        // Event Listeners
        window.addEventListener('show-generate-modal', event => {
            const data = event.detail.params ?? event.detail[0] ?? event.detail;

            document.getElementById('modal-count-ekskul').textContent = data.countPelajarWithEkskul || 0;
            document.getElementById('modal-count-ekskul-2').textContent = data.countPelajarWithEkskul || 0;
            document.getElementById('modal-count-kosong').textContent = data.countDeskripsiKosong || 0;
            document.getElementById('modal-count-kosong-2').textContent = data.countDeskripsiKosong || 0;
            document.getElementById('modal-count-template').textContent = data.countTemplateAvailable || 0;

            if (generateModal) {
                generateModal.show();
            }
        });

        window.addEventListener('hide-generate-modal', event => {
            if (generateModal) {
                generateModal.hide();
            }
        });

        window.showFullDeskripsi = function(namaPelajar, deskripsi) {
            Swal.fire({
                title: 'Deskripsi Ekstrakurikuler - ' + namaPelajar,
                html: '<div style="text-align: left; white-space: pre-wrap;">' + deskripsi + '</div>',
                width: '600px',
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#3085d6',
            });
        };

        window.confirmDeleteEkskul = function(pelajarId) {
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
                    Livewire.dispatch('deleteEkskul', [pelajarId]);
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