<div>
    {{-- Page Header --}}
    <div class="page-header pb-3 mb-4 border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="icon-wrapper position-relative">
                    <span class="bg-gradient-primary p-2 rounded-3 shadow-sm me-3 d-inline-flex align-items-center justify-content-center">
                        <i class="mdi mdi-trophy mdi-24px text-white"></i>
                    </span>
                </div>
                <div>
                    <h4 class="mb-1 text-dark fw-bold">Entri Ekstrakurikuler Pelajar</h4>
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-muted">Kelola data nilai ekstrakurikuler pelajar</small>
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

                <div class="col-md-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                            style="width: 36px; height: 36px;">
                            <i class="mdi mdi-account-supervisor text-white fs-5"></i>
                        </div>
                        <div class="ms-3 d-flex flex-column justify-content-center">
                            <small class="text-muted lh-2">Pembina Ekstrakurikuler</small>
                            <p class="fw-bold mb-0 text-primary lh-sm">
                                @if($selectedEkstrakurikulerId && $pembinaName)
                                {{ $pembinaName }}
                                @else
                                <span class="text-muted">Belum Dipilih</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-warning rounded-3"
                            style="width: 36px; height: 36px;">
                            <i class="mdi mdi-trophy text-white fs-5"></i>
                        </div>
                        <div class="ms-3 d-flex flex-column justify-content-center">
                            <small class="text-muted lh-2">Ekstrakurikuler</small>
                            <p class="fw-bold mb-0 text-warning lh-sm">
                                @if($selectedEkstrakurikulerId)
                                @php
                                $selectedEkskul = $ekstrakurikulerList->firstWhere('id', $selectedEkstrakurikulerId);
                                @endphp
                                {{ $selectedEkskul->nama ?? 'N/A' }}
                                @else
                                <span class="text-muted">Belum Dipilih</span>
                                @endif
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

    {{-- Filter Tahun Ajaran, Semester & Ekstrakurikuler --}}
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
                <div class="col-md-3">
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
                <div class="col-md-3">
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

                {{-- Ekstrakurikuler --}}
                <div class="col-md-3">
                    <label class="form-label">Ekstrakurikuler</label>
                    <select wire:model.live="selectedEkstrakurikulerId" class="form-select"
                        @if(!$semesterId) disabled @endif>
                        <option value="">-- Pilih Ekstrakurikuler --</option>
                        @foreach($ekstrakurikulerList as $ekskul)
                        <option value="{{ $ekskul->id }}">
                            {{ $ekskul->nama }}
                            @if($ekskul->pembina)
                            - {{ $ekskul->pembina->name }}
                            @endif
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Pencarian + Generate --}}
                <div class="col-md-3">
                    <label class="form-label">Cari & Generate</label>
                    <div class="d-flex gap-2">
                        <input type="search"
                            wire:model.live.debounce.300ms="searchPelajar"
                            class="form-control"
                            placeholder="Cari..."
                            @if(!$selectedEkstrakurikulerId) disabled @endif>

                        <button
                            type="button"
                            class="btn btn-outline-primary"
                            wire:click="openGenerateModal"
                            wire:key="btn-generate-deskripsi"
                            title="Generate"
                            @if(!$selectedEkstrakurikulerId) disabled @endif
                            style="width: 2.5rem; display: flex; align-items: center; justify-content: center;">
                            <i class="mdi mdi-auto-fix"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @endif

    {{-- Tabel Input Ekstrakurikuler --}}
    @if($selectedEkstrakurikulerId && $semesterId)
    <h5 class="text-dark mb-3">
        <i class="mdi mdi-account-multiple me-2"></i> Entri Data Ekstrakurikuler Pelajar
    </h5>

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
                    <th width="12%">
                        <p class="mb-0">Form</p>
                        <small>Pilih Nilai</small>
                    </th>
                    <th width="8%">
                        <p class="mb-0">Nilai</p>
                        <small>Tersimpan</small>
                    </th>
                    <th width="40%">
                        <p class="mb-0">Deskripsi Tersimpan</p>
                        <small>Deskripsi Ekstrakurikuler</small>
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
                            wire:key="nilai-input-{{ $pelajar->pelajar_id }}"
                            wire:model.defer="ekskulInput.{{ $pelajar->pelajar_id }}.nilai"
                            class="form-select form-select-sm">
                            <option value="">Pilih Nilai</option>
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
                        <div class="mb-1">
                            @if($pelajar->ekskul_existing->nilai)
                            @php
                            $nilaiKey = $pelajar->ekskul_existing->nilai;
                            @endphp
                            <span class="badge 
                                @if($nilaiKey == 'A') bg-success
                                @elseif($nilaiKey == 'B') bg-primary
                                @elseif($nilaiKey == 'C') bg-warning
                                @else bg-secondary
                                @endif">
                                {{ $nilaiKey }}
                            </span>
                            @endif
                        </div>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word; max-width: 300px;">
                        @if ($pelajar->ekskul_existing && $pelajar->ekskul_existing->deskripsi)
                        @php
                        $deskripsi = $pelajar->ekskul_existing->deskripsi;
                        @endphp
                        @if (strlen($deskripsi) > 90)
                        <p class="mb-0">
                            <a href="javascript:void(0)"
                                onclick="showFullDeskripsi('{{ addslashes($pelajar->nama_lengkap) }}', `{{ addslashes($deskripsi) }}`)"
                                class="text-muted fs-7 text-decoration-none"
                                title="Klik untuk lihat selengkapnya">
                                {{ Str::limit($deskripsi, 90) }}...
                            </a>
                        </p>
                        @else
                        <span class="text-muted fs-7">{{ $deskripsi }}</span>
                        @endif
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($pelajar->ekskul_existing)
                        <button type="button"
                            wire:key="delete-btn-{{ $pelajar->pelajar_id }}"
                            id="delete-btn-{{ $pelajar->pelajar_id }}"
                            class="btn btn-sm btn-outline-danger"
                            onclick="confirmDeleteEkskul('{{ $pelajar->pelajar_id }}')"
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
            wire:click="$dispatch('swal:confirm', { title: 'Reset Input?', text: 'Input akan dikembalikan ke data tersimpan di database.', nextEvent: 'confirmResetEkskul' })"
            wire:loading.attr="disabled"
            wire:target="resetEkskul">
            <span class="btn-label">
                <i class="mdi mdi-delete-sweep-outline"></i>
            </span>
            Reset
        </button>

        <button type="button"
            class="btn btn-labeled btn-primary"
            wire:click="saveEkskul"
            wire:loading.attr="disabled"
            wire:target="saveEkskul">
            <span class="btn-label">
                <i class="mdi mdi-loading mdi-spin d-none"
                    wire:loading.class.remove="d-none"
                    wire:target="saveEkskul">
                </i>
                <i class="mdi mdi-content-save"
                    wire:loading.class="d-none"
                    wire:target="saveEkskul">
                </i>
            </span>
            <span wire:loading.class="d-none" wire:target="saveEkskul">
                Simpan
            </span>
            <span class="d-none" wire:loading.class.remove="d-none" wire:target="saveEkskul">
                Menyimpan...
            </span>
        </button>
    </div>
    @elseif($rombel && $semesterId)
    <div class="alert alert-warning text-center" role="alert">
        <i class="mdi mdi-information-outline me-2"></i>
        <strong>Silakan pilih Ekstrakurikuler untuk mulai mengentri data.</strong>
    </div>
    @endif

    {{-- Loading Overlay --}}
    <div wire:loading.flex
        wire:target="saveEkskul,selectedEkstrakurikulerId,semesterId,tahunAjaranId,searchPelajar"
        class="position-fixed top-0 start-0 w-100 h-100 align-items-center justify-content-center"
        style="background-color: rgba(0,0,0,0.3); z-index: 9999; display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

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
                    <button
                        type="button"
                        class="btn btn-labeled btn-outline-secondary"
                        wire:click="closeGenerateModal"
                        wire:loading.attr="disabled"
                        wire:target="generateDeskripsi">
                        <span class="btn-label">
                            <i class="mdi mdi-close"></i>
                        </span>
                        Batal
                    </button>

                    <button
                        type="button"
                        class="btn btn-labeled btn-primary"
                        wire:click="generateDeskripsi"
                        wire:loading.attr="disabled"
                        wire:target="generateDeskripsi">
                        <span class="btn-label">
                            <i class="mdi mdi-loading mdi-spin d-none"
                                wire:loading.class.remove="d-none"
                                wire:target="generateDeskripsi">
                            </i>
                            <i class="mdi mdi-check"
                                wire:loading.class="d-none"
                                wire:target="generateDeskripsi">
                            </i>
                        </span>
                        <span wire:loading.class="d-none" wire:target="generateDeskripsi">
                            Generate
                        </span>
                        <span class="d-none" wire:loading.class.remove="d-none" wire:target="generateDeskripsi">
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
                    const btn = document.getElementById(`delete-btn-${pelajarId}`);
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i>';
                    }

                    Livewire.dispatch('deleteEkskul', [pelajarId]);
                }
            });
        };

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