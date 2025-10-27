<div>
    <!-- Filter Data -->
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
                                <!-- Filter Tahun Ajaran -->
                                <div class="col-sm-3">
                                    <label class="form-label">Tahun Ajaran</label>
                                    <select wire:model.live="tahunAjaranId" class="form-select">
                                        <option value="">-- Pilih Tahun Ajaran --</option>
                                        @foreach($tahunAjaranList as $ta)
                                        <option value="{{ $ta->id }}">
                                            {{ $ta->tahun_mulai }}/{{ $ta->tahun_selesai }}
                                            @if($ta->status === 'aktif') (Aktif) @endif
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Filter Semester -->
                                <div class="col-sm-3">
                                    <label class="form-label">Semester</label>
                                    <select wire:model.live="semesterId" class="form-select"
                                        @if(!$tahunAjaranId) disabled @endif>
                                        <option value="">-- Pilih Semester --</option>
                                        @foreach($semesterList as $semester)
                                        <option value="{{ $semester->id }}">
                                            Semester {{ $semester->semester }}
                                            @if($semester->status === 'aktif') (Aktif) @endif
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Filter Rombel -->
                                <div class="col-sm-3">
                                    <label class="form-label">Rombongan Belajar</label>
                                    <select wire:model.live="rombelId" class="form-select"
                                        @if(!$semesterId) disabled @endif>
                                        <option value="">-- Pilih Rombel --</option>
                                        @foreach($rombelList as $rb)
                                        <option value="{{ $rb->id }}">
                                            {{ $rb->nama_rombel }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Filter Mata Pelajaran -->
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

                    <!-- Info Rombel - Tampilkan jika semua filter sudah dipilih -->
                    @if($rombel && $selectedRombelPengajarId)
                    <div class="alert alert-success py-2 mt-3" role="alert">
                        <div class="row align-items-center">
                            <div class="col-sm-3 py-2">
                                <small class="text-muted d-block">Kurikulum</small>
                                <p class="mb-0 font-weight-bold">
                                    {{ $rombel->tahunAjaranKurikulum->kurikulum->nama ?? 'N/A' }}
                                </p>
                            </div>
                            <div class="col-sm-3 py-2">
                                <small class="text-muted d-block">Tahun Ajaran & Semester</small>
                                <p class="mb-0 font-weight-bold">
                                    @php
                                    $selectedSemester = $semesterList->firstWhere('id', $semesterId);
                                    $selectedTahunAjaran = $tahunAjaranList->firstWhere('id', $tahunAjaranId);
                                    @endphp
                                    {{ $selectedTahunAjaran->tahun_mulai ?? '' }}/{{ $selectedTahunAjaran->tahun_selesai ?? '' }}
                                    ~ {{ $selectedSemester->semester == 1 ? 'Ganjil' : 'Genap' }}
                                </p>
                            </div>
                            <div class="col-sm-3 py-2">
                                <small class="text-muted d-block">Wali Kelas</small>
                                <p class="mb-0 font-weight-bold">
                                    {{ $rombel->waliKelas->name ?? 'Belum Ada' }} ({{ $rombel->nama_rombel }})
                                </p>
                            </div>
                            <div class="col-sm-3 py-2">
                                <small class="text-muted d-block">Mata Pelajaran</small>
                                <p class="mb-0 font-weight-bold">
                                    @php
                                    $selectedMapel = $mataPelajaranList->firstWhere('id', $selectedRombelPengajarId);
                                    @endphp
                                    {{ $selectedMapel->mataPelajaran->nama ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                        @if($guruName)
                        <div class="row mt-2">
                            <div class="col-12">
                                <small class="text-muted d-block">Guru Pengampu</small>
                                <p class="mb-0 font-weight-bold">{{ $guruName }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Input Nilai - Hanya tampil jika semua filter sudah dipilih -->
    @if($selectedRombelPengajarId)
    <div class="row mt-4">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <!-- Header & Search -->
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <h5 class="text-dark"><i class="mdi mdi-account-multiple me-2"></i> Daftar Pelajar</h5>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-group">
                                <input type="text"
                                    wire:model.live.debounce.300ms="searchPelajar"
                                    class="form-control"
                                    placeholder="Cari nama, NISN, atau NIS...">
                                <span class="input-group-text">
                                    <i class="mdi mdi-magnify"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mb-3">
                        <button wire:click="confirmSaveNilai"
                            class="btn btn-primary btn-sm">
                            <i class="mdi mdi-content-save me-1"></i> Simpan Nilai
                        </button>
                        <button wire:click="confirmResetNilai"
                            class="btn btn-warning btn-sm">
                            <i class="mdi mdi-refresh me-1"></i> Reset Input
                        </button>
                    </div>

                    <!-- Tabel Nilai -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">NIS</th>
                                    <th width="15%">NISN</th>
                                    <th width="35%">Nama Lengkap</th>
                                    <th width="15%">Nilai Tersimpan</th>
                                    <th width="15%">Input Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pelajarData as $index => $pelajar)
                                <tr>
                                    <td class="text-center">
                                        {{ $pelajarData->firstItem() + $index }}
                                    </td>
                                    <td>{{ $pelajar->nomor_induk ?? '-' }}</td>
                                    <td>{{ $pelajar->nisn ?? '-' }}</td>
                                    <td>{{ $pelajar->nama_lengkap }}</td>
                                    <td class="text-center">
                                        @if($pelajar->nilai_sekarang)
                                        <span class="badge bg-info">
                                            {{ number_format($pelajar->nilai_sekarang, 2) }}
                                        </span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <input type="number"
                                            wire:model.live.debounce.500ms="nilaiInput.{{ $pelajar->pelajar_id }}"
                                            class="form-control form-control-sm"
                                            min="0"
                                            max="100"
                                            step="0.01"
                                            placeholder="0-100">
                                        @error('nilaiInput.' . $pelajar->pelajar_id)
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
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

                    <!-- Pagination -->
                    @if($pelajarData->hasPages())
                    <div class="mt-3">
                        {{ $pelajarData->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Pesan jika belum memilih filter -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info text-center" role="alert">
                <i class="mdi mdi-information-outline me-2"></i>
                <strong>Silakan pilih Tahun Ajaran, Semester, Rombel, dan Mata Pelajaran untuk mulai input nilai.</strong>
            </div>
        </div>
    </div>
    @endif

    <!-- Loading Indicator -->
    <div wire:loading class="position-fixed top-50 start-50 translate-middle" style="z-index: 9999;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Sweet Alert Handlers
    window.addEventListener('swal:confirm', event => {
        Swal.fire({
            title: event.detail.title,
            text: event.detail.text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Lanjutkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.dispatch(event.detail.nextEvent);
            }
        });
    });

    window.addEventListener('swal:success', event => {
        Swal.fire({
            title: event.detail.title,
            text: event.detail.text,
            icon: 'success',
            confirmButtonText: 'OK'
        });
    });

    window.addEventListener('swal:error', event => {
        Swal.fire({
            title: event.detail.title,
            text: event.detail.text,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });

    window.addEventListener('swal:info', event => {
        Swal.fire({
            title: event.detail.title,
            text: event.detail.text,
            icon: 'info',
            confirmButtonText: 'OK'
        });
    });
</script>
@endpush