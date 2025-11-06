<div>
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="page-header mb-0 border-bottom">
                                <div class="d-flex align-items-center">
                                    <h5 class="text-dark"><i class="mdi mdi-filter me-2"></i> Filter Data</h5>
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
                                    <label class="form-label">Mata Pelajaran</label>
                                    <select wire:model.live="selectedRombelPengajarId" class="form-select"
                                        @if(!$rombelId) disabled @endif>
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        @foreach($mataPelajaranList as $rp)
                                        <option value="{{ $rp->id }}">
                                            {{ $rp->mataPelajaran->nama }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

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
                                        <i class="mdi mdi-book-open-page-variant text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3 d-flex flex-column justify-content-center">
                                        <small class="text-muted lh-2">Mata Pelajaran</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">
                                            @php
                                            $selectedMapel = $mataPelajaranList->firstWhere('id', $selectedRombelPengajarId);
                                            @endphp
                                            {{ $selectedMapel->mataPelajaran->nama ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                        style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-teach text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3 d-flex flex-column justify-content-center">
                                        <small class="text-muted lh-2">Guru Pengampu</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">{{ $guruName ?? 'N/A' }}</p>
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

    @if($selectedRombelPengajarId)
    <div class="row mt-1">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3 align-items-center">
                        <div class="col-lg-6">
                            <h5 class="text-dark"><i class="mdi mdi-account-multiple me-2"></i> Entri Data Nilai Akhir Pelajar</h5>
                        </div>
                        <div class="col-lg-6 d-flex justify-content-end">
                            <div class="input-group" style="width: 250px;">
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="searchPelajar"
                                    class="form-control"
                                    placeholder="Cari nama, atau nomor induk...">
                            </div>

                            {{-- TOMBOL GENERATE CAPAIAN DITAMBAHKAN --}}
                            <button
                                type="button"
                                class="btn btn-outline-primary btn-sm ms-2"
                                wire:click="openGenerateModal"
                                wire:key="btn-generate-capaian"
                                title="Generate Capaian Kompetensi"
                                style="padding: 0.25rem 0.5rem; width: 2.25rem; height: calc(2.25rem + 2px); display: flex; align-items: center; justify-content: center;">
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
                                        <small>Entri Nilai</small>
                                    </th>
                                    <th width="10%">
                                        <p class="mb-0">Nilai</p>
                                        <small>Tersimpan</small>
                                    </th>
                                    <th width="35%">
                                        <p class="mb-0">Capaian</p>
                                        <small>Telah atau Belum Tercapai</small>
                                    </th>
                                    <th width="5%">
                                        <p class="mb-0">Aksi</p>
                                        <small>Delete</small>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pelajarData as $index => $pelajar)
                                <tr wire:key="pelajar-row-{{ $pelajar->pelajar_id }}"> {{-- KUNCI ISOLASI: wire:key pada baris --}}
                                    <td class="text-center">
                                        {{ $pelajarData->firstItem() + $index }}
                                    </td>
                                    <td>
                                        <p class="mb-0">{{ $pelajar->nama_lengkap }}</p>
                                        <small>{{ $pelajar->nomor_induk ?? '-' }} | {{ $pelajar->nisn ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <input
                                            type="number"
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
                                    <td>
                                        @if($pelajar->nilai_sekarang)
                                        <span class="badge bg-info">
                                            {{ number_format($pelajar->nilai_sekarang, 2) }}
                                        </span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word; max-width: 300px;">
                                        @if($pelajar->capaian_kompetensi)
                                        {{-- Str::limit perlu diimpor di AppServiceProvider jika tidak default --}}
                                        <span class="fs-7 text-muted">{{ Str::limit($pelajar->capaian_kompetensi, 100) }}</span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pelajar->nilai_sekarang)
                                        <button
                                            type="button"
                                            wire:key="delete-btn-{{ $pelajar->pelajar_id }}"
                                            id="delete-btn-{{ $pelajar->pelajar_id }}"
                                            class="btn btn-sm btn-outline-danger"
                                            x-data
                                            @click="confirmDeleteNilai('{{ $pelajar->pelajar_id }}')"
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
                                        Tidak ada data pelajar
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($pelajarData->hasPages())
                    <div class="mt-3">
                        {{ $pelajarData->links() }}
                    </div>
                    @endif

                    <div class="d-flex justify-content-end mt-3">
                        <button
                            type="button"
                            class="btn btn-labeled btn-outline-secondary me-2"
                            wire:click="confirmResetNilai"
                            wire:loading.attr="disabled"
                            wire:target="resetNilai">
                            <span class="btn-label">
                                <i class="mdi mdi-delete-sweep-outline"></i>
                            </span>
                            Reset
                        </button>

                        <button
                            type="button"
                            class="btn btn-labeled btn-primary"
                            wire:click="confirmSaveNilai" {{-- Menggunakan confirmSaveNilai --}}
                            wire:loading.attr="disabled"
                            wire:target="confirmSaveNilai, saveNilai">
                            <span class="btn-label">
                                <i
                                    class="mdi mdi-loading mdi-spin d-none"
                                    wire:loading.class.remove="d-none"
                                    wire:target="confirmSaveNilai, saveNilai">
                                </i>
                                <i
                                    class="mdi mdi-content-save"
                                    wire:loading.class="d-none"
                                    wire:target="confirmSaveNilai, saveNilai">
                                </i>
                            </span>
                            <span class="text-normal" wire:loading.class="d-none" wire:target="confirmSaveNilai, saveNilai">
                                Simpan
                            </span>
                            <span class="text-loading d-none" wire:loading.class.remove="d-none" wire:target="confirmSaveNilai, saveNilai">
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="alert alert-danger text-center" role="alert">
                <i class="mdi mdi-information-outline me-2"></i>
                <strong>Silakan pilih Tahun Ajaran, Semester, Rombel, dan Mata Pelajaran untuk mulai mengentri nilai.</strong>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL GENERATE CAPAIAN DITAMBAHKAN --}}
    <div
        class="modal fade"
        id="generateModal"
        tabindex="-1"
        aria-labelledby="generateModalLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="generateModalLabel">
                        <i class="mdi mdi-auto-fix me-2"></i>
                        Generate Capaian Kompetensi
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal" {{-- Ditutup via Bootstrap JS --}}
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
                            <li>Pelajar dengan nilai tersimpan: <strong><span id="modal-count-nilai">0</span> orang</strong></li>
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
                                    Menimpa semua capaian, termasuk yang sudah ada (<span id="modal-count-nilai-2">0</span> pelajar)
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
                        data-bs-dismiss="modal">
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

{{-- SCRIPT SWEETALERT DAN MODAL DITAMBAHKAN --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const generateModalEl = document.getElementById('generateModal');
        let generateModal = null;

        if (typeof bootstrap !== 'undefined' && generateModalEl) {
            generateModal = new bootstrap.Modal(generateModalEl, {
                backdrop: 'static',
                keyboard: true
            });

            // Reset mode ke 'empty' saat modal ditutup
            generateModalEl.addEventListener('hidden.bs.modal', function() {
                Livewire.dispatch('$set', ['generateMode', 'empty']);
            });
        }

        // ✅ Listener untuk membuka modal
        window.addEventListener('show-generate-modal', event => {
            const data = event.detail[0] || event.detail;

            // Update nilai di modal
            document.getElementById('modal-count-nilai').textContent = data.countPelajarWithNilai || 0;
            document.getElementById('modal-count-nilai-2').textContent = data.countPelajarWithNilai || 0;
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

        // ✅ Function delete confirmation
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
                    const btn = document.getElementById(`delete-btn-${pelajarId}`);
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i>';
                    }
                    Livewire.dispatch('deleteNilai', [pelajarId]);
                }
            });
        };

        // ✅ Handler SweetAlert Success
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

        // ✅ Handler SweetAlert Error
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

        // ✅ Handler SweetAlert Info
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

        // ✅ Handler SweetAlert Warning
        window.addEventListener('swal:warning', event => {
            let detail = event.detail.params ?? event.detail[0] ?? event.detail;

            if (typeof detail === 'string') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    html: detail.replace(/\n/g, '<br>'),
                    confirmButtonText: 'Tutup',
                    width: '600px',
                });
            } else if (typeof detail === 'object' && detail !== null) {
                Swal.fire({
                    icon: 'warning',
                    title: detail.title ?? 'Peringatan',
                    html: (detail.text ?? '').replace(/\n/g, '<br>'),
                    confirmButtonText: 'Tutup',
                    width: '600px',
                });
            }
        });
    });
</script>
@endpush