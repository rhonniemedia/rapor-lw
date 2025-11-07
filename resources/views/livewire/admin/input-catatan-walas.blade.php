<div>
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="page-header mb-0 border-bottom">
                                <div class="d-flex align-items-center">
                                    <h5 class="text-dark"><i class="mdi mdi-filter me-2"></i> Filter Data Catatan</h5>
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
                            <h5 class="text-dark"><i class="mdi mdi-account-multiple me-2"></i> Entri Data Catatan Wali Kelas</h5>
                        </div>
                        <div class="col-lg-6 d-flex justify-content-end">
                            <div class="input-group w-50">
                                <input type="text"
                                    wire:model.live.debounce.300ms="searchPelajar"
                                    class="form-control"
                                    placeholder="Cari nama, atau nomor induk...">
                                @if($searchPelajar)
                                <div class="input-group-append">
                                    <button type="button"
                                        class="btn btn-secondary"
                                        wire:click="$set('searchPelajar', '')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Tabel Catatan --}}
                    <div class="table-responsive" wire:loading.class.delay.longest="opacity-50">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center" style="width: 5%;">#</th>
                                    <th style="width: 20%;">Nama Pelajar/NIS/NISN</th>
                                    <th class="text-center" style="width: 30%;">Catatan Baru/Update</th>
                                    <th style="width: 35%;" class="text-center">Catatan Terakhir</th>
                                    <th style="width: 10%;" class="text-center">Aksi</th> {{-- ✅ NEW: Kolom Aksi --}}
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pelajarData as $index => $pelajar)
                                <tr>
                                    <td class="text-center align-top">
                                        <p class="mb-0">{{ $pelajarData->firstItem() + $index }}</p>
                                    </td>
                                    <td class="align-top">
                                        <p class="mb-0"><span class="font-weight-medium">{{ $pelajar->nama_lengkap }}</span></p>
                                        <small class="text-muted">{{ $pelajar->nomor_induk }} | {{ $pelajar->nisn }}</small>
                                    </td>
                                    <td class=" align-top">
                                        <textarea
                                            wire:model.defer="catatanInput.{{ $pelajar->pelajar_id }}.catatan"
                                            class="form-control form-control-sm @error('catatanInput.'.$pelajar->pelajar_id.'.catatan') is-invalid @enderror"
                                            rows="3"
                                            placeholder="Tulis catatan untuk siswa."
                                            maxlength="1000"></textarea>
                                        <small class="text-muted">Maksimal 1000 karakter</small>
                                        @error('catatanInput.'.$pelajar->pelajar_id.'.catatan')
                                        <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    </td>
                                    <td class="align-top text-start" style="white-space: normal !important; vertical-align: top !important;">
                                        @if($pelajar->catatan_existing)
                                        <p class="mb-2">
                                            <span class="text-primary fs-7">{{ $pelajar->catatan_existing->catatan }}</span>
                                        </p>
                                        <small class="text-muted">
                                            <i class="mdi mdi-clock-outline"></i>
                                            {{ \Carbon\Carbon::parse($pelajar->catatan_existing->tanggal_input)->format('d M Y H:i') }}
                                        </small>
                                        @else
                                        <span class="text-muted small">Belum ada catatan</span>
                                        @endif
                                    </td>
                                    {{-- ✅ NEW: Tombol Aksi --}}
                                    <td class="text-center align-top">
                                        @if($pelajar->catatan_existing)
                                        <button type="button"
                                            wire:key="delete-catatan-btn-{{ $pelajar->pelajar_id }}"
                                            id="delete-catatan-btn-{{ $pelajar->pelajar_id }}"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDeleteCatatan('{{ $pelajar->pelajar_id }}')"
                                            title="Hapus Catatan Permanen">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                        @else
                                        <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center align-top text-muted py-4"> {{-- ✅ Kolom diubah dari 4 menjadi 5 --}}
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
                    <div class="d-flex justify-content-end mt-3">
                        {{-- Tombol Reset --}}
                        <button
                            type="button"
                            class="btn btn-labeled btn-outline-secondary me-2"
                            wire:click="confirmResetCatatan"
                            wire:loading.attr="disabled"
                            wire:target="confirmResetCatatan">
                            <span class="btn-label">
                                <i class="mdi mdi-delete-sweep-outline"></i>
                            </span>
                            Reset
                        </button>

                        {{-- Tombol Simpan --}}
                        <button
                            type="button"
                            class="btn btn-labeled btn-primary"
                            wire:click="saveCatatan" {{-- ✅ Perubahan: Panggil langsung saveCatatan --}}
                            wire:loading.attr="disabled"
                            wire:target="saveCatatan"> {{-- ✅ Perubahan: Target ke saveCatatan --}}
                            <span class="btn-label">
                                {{-- Icon loading tampil hanya saat saveCatatan aktif --}}
                                <i class="mdi mdi-loading mdi-spin d-none"
                                    wire:loading.class.remove="d-none"
                                    wire:target="saveCatatan">
                                </i>

                                {{-- Icon simpan hilang saat loading --}}
                                <i class="mdi mdi-content-save"
                                    wire:loading.class="d-none"
                                    wire:target="saveCatatan">
                                </i>
                            </span>

                            {{-- Teks tombol normal --}}
                            <span class="text-normal" wire:loading.class="d-none" wire:target="saveCatatan">
                                Simpan
                            </span>

                            {{-- Teks saat loading --}}
                            <span class="text-loading d-none" wire:loading.class.remove="d-none" wire:target="saveCatatan">
                                Menyimpan...
                            </span>
                        </button>
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
                <strong>Silakan pilih Tahun Ajaran, Semester, dan Rombel/Kelas untuk mulai menginput data catatan wali kelas.</strong>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- ✅ NEW: Skrip untuk SweetAlert dan deleteCatatan --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Function untuk handle delete confirmation Catatan Walas
        window.confirmDeleteCatatan = function(pelajarId) {
            Swal.fire({
                icon: 'warning',
                title: 'Hapus Catatan Wali Kelas?',
                text: 'Anda yakin ingin menghapus catatan ini secara permanen?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then(result => {
                if (result.isConfirmed) {
                    // Tampilkan loading pada tombol spesifik
                    const btn = document.getElementById(`delete-catatan-btn-${pelajarId}`);
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Hapus';
                    }

                    // Dispatch ke backend
                    Livewire.dispatch('deleteCatatan', [pelajarId]);
                }
            });
        };

        // Handler untuk response dari backend (Dipertahankan dari file input-nilai-akhir)
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