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
                                {{ $semesterAktif->tahunAjaran->nama ?? 'N/A' }} ~ {{ $semesterAktif->semester->nama ?? 'Belum Ada' }}
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
                            <i class="mdi mdi-teach text-white fs-5"></i>
                        </div>
                        <div class="ms-3 d-flex flex-column justify-content-center">
                            <small class="text-muted lh-2">Guru Mata Pelajaran</small>
                            <p class="fw-bold mb-0 text-primary lh-sm">
                                @if($selectedRombelPengajarId && $guruName)
                                {{ $guruName }}
                                @else
                                <span class="text-muted">Belum Dipilih</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-danger rounded-3"
                            style="width: 36px; height: 36px;">
                            <i class="mdi mdi-book-open-variant text-white fs-5"></i>
                        </div>
                        <div class="ms-3 d-flex flex-column justify-content-center">
                            <small class="text-muted lh-2">Mata Pelajaran</small>
                            <p class="fw-bold mb-0 text-danger lh-sm">
                                @if($selectedRombelPengajarId)
                                @php
                                $selectedMapel = $mataPelajaranList->firstWhere('id', $selectedRombelPengajarId);
                                @endphp
                                {{ $selectedMapel->mataPelajaran->nama ?? 'N/A' }}
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
        <strong>Perhatian!</strong> Tidak ada kelas binaan atau semester aktif.
    </div>
    @endif

    {{-- Filter Mata Pelajaran --}}
    @if($rombel && $semesterAktif)
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="page-header mb-0 border-bottom mb-3">
                <div class="d-flex align-items-center">
                    <h5 class="text-dark"><i class="mdi mdi-filter"></i> Filter dan Generate Data</h5>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <select wire:model.live="selectedRombelPengajarId" class="form-select">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($mataPelajaranList as $rp)
                        <option value="{{ $rp->id }}">
                            {{ $rp->mataPelajaran->nama }}
                            @if($rp->mataPelajaran->kelompok)
                            - {{ $rp->mataPelajaran->kelompok->nama }}
                            @endif
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 d-flex align-items-start gap-2">
                    <input type="text"
                        wire:model.live.debounce.300ms="searchPelajar"
                        class="form-control"
                        placeholder="Cari nama atau nomor induk..."
                        @if(!$selectedRombelPengajarId) disabled @endif>

                    {{-- TOMBOL GENERATE CAPAIAN DITAMBAHKAN --}}
                    <button
                        type="button"
                        class="btn btn-outline-primary"
                        wire:click="openGenerateModal"
                        wire:key="btn-generate-capaian"
                        title="Generate Capaian Kompetensi"
                        @if(!$selectedRombelPengajarId) disabled @endif
                        style="width: 2.5rem; display: flex; align-items: center; justify-content: center;">
                        <i class="mdi mdi-auto-fix"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Tabel Input Nilai --}}
    @if($selectedRombelPengajarId)
    <h5 class="text-dark mb-3">
        <i class="mdi mdi-account-multiple me-2"></i> Entri Data Nilai Akhir Pelajar
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
                    <th width="10%">
                        <p class="mb-0">Form</p>
                        <small>Entri Nilai</small>
                    </th>
                    <th width="10%">
                        <p class="mb-0">Nilai</p>
                        <small>Tersimpan</small>
                    </th>
                    <th width="40%">
                        <p class="mb-0">Capaian Kompetensi</p>
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
                <tr wire:key="pelajar-{{ $pelajar->pelajar_id }}">
                    <td class="text-center">
                        {{ $pelajarData->firstItem() + $index }}
                    </td>
                    <td>
                        <p class="mb-0">{{ $pelajar->nama_lengkap }}</p>
                        <small>{{ $pelajar->nomor_induk ?? '-' }} | {{ $pelajar->nisn ?? '-' }}</small>
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
                        @php
                        $capaian = $pelajar->capaian_kompetensi;
                        @endphp
                        @if (strlen($capaian) > 100)
                        <p class="mb-0">
                            <a href="javascript:void(0)"
                                onclick="showFullCapaian('{{ addslashes($pelajar->nama_lengkap) }}', `{{ addslashes($capaian) }}`)"
                                class="fs-7 text-muted text-decoration-none"
                                title="Klik untuk lihat selengkapnya">
                                {{ Str::limit($capaian, 100) }}
                            </a>
                        </p>
                        @else
                        <span class="fs-7 text-muted">{{ $capaian }}</span>
                        @endif
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
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
            wire:click="confirmResetNilai"
            wire:loading.attr="disabled"
            wire:target="resetNilai, confirmResetNilai">
            <span class="btn-label">
                <i class="mdi mdi-delete-sweep-outline"></i>
            </span>
            Reset
        </button>

        {{-- WIRE:CLICK DIUBAH DARI confirmSaveNilai MENJADI saveNilai --}}
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
                Simpan
            </span>
            <span class="d-none" wire:loading.class.remove="d-none" wire:target="saveNilai">
                Menyimpan...
            </span>
        </button>
    </div>
    @elseif($rombel && $semesterAktif)
    <div class="alert alert-warning text-center" role="alert">
        <i class="mdi mdi-information-outline me-2"></i>
        <strong>Silakan pilih Mata Pelajaran untuk mulai mengentri nilai.</strong>
    </div>
    @endif

    {{-- Loading Overlay - Hanya untuk saveNilai dan actions penting --}}
    <div wire:loading.flex
        wire:target="saveNilai,selectedRombelPengajarId,searchPelajar"
        class="position-fixed top-0 start-0 w-100 h-100 align-items-center justify-content-center"
        style="background-color: rgba(0,0,0,0.3); z-index: 9999; display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    {{-- MODAL GENERATE CAPAIAN --}}
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
                        class="btn btn-labeled btn-outline-secondary"
                        data-bs-dismiss="modal">
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
        // INISIALISASI MODAL GENERATE
        const generateModalEl = document.getElementById('generateModal');
        let generateModal = null;

        if (typeof bootstrap !== 'undefined' && generateModalEl) {
            generateModal = new bootstrap.Modal(generateModalEl, {
                backdrop: 'static',
                keyboard: true
            });

            // Reset mode ke 'empty' saat modal ditutup
            generateModalEl.addEventListener('hidden.bs.modal', function() {
                // Menggunakan Livewire.dispatch('$set', ...) untuk Livewire 3
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
                    // Tampilkan loading pada tombol spesifik
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

        // SweetAlert Handlers
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

        // Handler untuk konfirmasi umum (Simpan/Reset) - Tetap dipertahankan untuk RESET dan DELETE
        window.addEventListener('swal:confirm', event => {
            let detail = event.detail.params ?? event.detail[0] ?? event.detail;

            Swal.fire({
                title: detail.title ?? 'Konfirmasi',
                text: detail.text ?? 'Anda yakin?',
                icon: detail.icon ?? 'warning',
                showCancelButton: true,
                confirmButtonColor: detail.confirmButtonColor ?? '#3085d6',
                cancelButtonColor: detail.cancelButtonColor ?? '#d33',
                confirmButtonText: detail.confirmButtonText ?? 'Ya',
                cancelButtonText: detail.cancelButtonText ?? 'Batal',
            }).then(result => {
                if (result.isConfirmed) {
                    if (detail.nextEvent) {
                        Livewire.dispatch(detail.nextEvent);
                    }
                    if (detail.nextAction) {
                        Livewire.dispatch(detail.nextAction, [detail.id]);
                    }
                }
            });
        });

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