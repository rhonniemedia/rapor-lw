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
                                <!-- Filter Tahun Ajaran -->
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

                                <!-- Filter Semester -->
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

                                <!-- Filter Rombel -->
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
                                            {{ $rombel->tahunAjaranKurikulum->kurikulum->nama ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Tahun Ajaran & Semester -->
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

                            <!-- Kolom 2 -->
                            <div class="col-md-4">
                                <!-- Rombel -->
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

                                <!-- Wali Kelas -->
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

                            <!-- Kolom 3 -->
                            <div class="col-md-4">
                                <!-- Total Ekstrakurikuler Aktif -->
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                        style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-trophy text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3 d-flex flex-column justify-content-center">
                                        <small class="text-muted lh-2">Ekstrakurikuler Tersedia</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">
                                            {{ count($ekstrakurikulerList) }} Pilihan
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
                            <h5 class="text-dark"><i class="mdi mdi-trophy me-2"></i> Input Data Ekstrakurikuler Pelajar</h5>
                        </div>
                        <div class="col-lg-6 d-flex justify-content-end">
                            <div class="input-group w-50">
                                <input type="text"
                                    wire:model.live.debounce.300ms="searchPelajar"
                                    class="form-control"
                                    placeholder="Cari nama, NIS, atau NISN...">
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

                    {{-- Tabel Ekstrakurikuler --}}
                    <div class="table-responsive" wire:loading.class.delay.longest="opacity-50">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center" style="width: 5%;">#</th>
                                    <th style="width: 20%;">Nama Pelajar/NIS/NISN</th>
                                    <th class="text-center" style="width: 20%;">Ekstrakurikuler</th>
                                    <th class="text-center" style="width: 10%;">Nilai</th>
                                    <th class="text-center" style="width: 25%;">Keterangan</th>
                                    <th style="width: 20%;" class="text-center">Data Tersimpan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pelajarData as $index => $pelajar)
                                <tr>
                                    <td class="text-center align-middle">{{ $pelajarData->firstItem() + $index }}</td>
                                    <td class="align-middle">
                                        <strong>{{ $pelajar->nama_lengkap }}</strong><br>
                                        <small class="text-muted">{{ $pelajar->nomor_induk }} | {{ $pelajar->nisn }}</small>
                                    </td>
                                    <td>
                                        <select
                                            wire:model.defer="ekstrakurikulerInput.{{ $pelajar->pelajar_id }}.ekstrakurikuler_id"
                                            class="form-select form-select-sm @error('ekstrakurikulerInput.'.$pelajar->pelajar_id.'.ekstrakurikuler_id') is-invalid @enderror">
                                            <option value="">-- Pilih Ekstrakurikuler --</option>
                                            @foreach($ekstrakurikulerList as $ekskul)
                                            <option value="{{ $ekskul->id }}">{{ $ekskul->nama }}</option>
                                            @endforeach
                                        </select>
                                        @error('ekstrakurikulerInput.'.$pelajar->pelajar_id.'.ekstrakurikuler_id')
                                        <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="number"
                                            wire:model.defer="ekstrakurikulerInput.{{ $pelajar->pelajar_id }}.nilai"
                                            class="form-control form-control-sm text-center @error('ekstrakurikulerInput.'.$pelajar->pelajar_id.'.nilai') is-invalid @enderror"
                                            min="0"
                                            max="100"
                                            placeholder="0-100">
                                        @error('ekstrakurikulerInput.'.$pelajar->pelajar_id.'.nilai')
                                        <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="text"
                                            wire:model.defer="ekstrakurikulerInput.{{ $pelajar->pelajar_id }}.keterangan"
                                            class="form-control form-control-sm @error('ekstrakurikulerInput.'.$pelajar->pelajar_id.'.keterangan') is-invalid @enderror"
                                            placeholder="Misal: Baik, Cukup, dll"
                                            maxlength="500">
                                        @error('ekstrakurikulerInput.'.$pelajar->pelajar_id.'.keterangan')
                                        <small class="text-danger d-block">{{ $message }}</small>
                                        @enderror
                                    </td>
                                    <td class="align-middle">
                                        @if($pelajar->ekstrakurikuler_existing)
                                        <div class="small">
                                            <span class="badge badge-info mb-1">
                                                {{ $pelajar->ekstrakurikuler_existing->ekstrakurikuler->nama ?? '-' }}
                                            </span>
                                            <p class="mb-1">
                                                <strong>Nilai:</strong> {{ $pelajar->ekstrakurikuler_existing->nilai ?? '-' }}
                                            </p>
                                            @if($pelajar->ekstrakurikuler_existing->keterangan)
                                            <p class="mb-0">
                                                <strong>Ket:</strong> {{ $pelajar->ekstrakurikuler_existing->keterangan }}
                                            </p>
                                            @endif
                                        </div>
                                        @else
                                        <span class="text-muted small">Belum ada data</span>
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
                            Pilih ekstrakurikuler, masukkan nilai (0-100), dan keterangan untuk setiap pelajar
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
                                wire:click="confirmSaveEkstrakurikuler"
                                wire:loading.attr="disabled"
                                wire:target="confirmSaveEkstrakurikuler">
                                <span class="btn-label">
                                    {{-- Icon loading tampil hanya saat confirmSaveEkstrakurikuler aktif --}}
                                    <i class="mdi mdi-loading mdi-spin d-none"
                                        wire:loading.class.remove="d-none"
                                        wire:target="confirmSaveEkstrakurikuler">
                                    </i>

                                    {{-- Icon simpan hilang saat loading --}}
                                    <i class="mdi mdi-content-save"
                                        wire:loading.class="d-none"
                                        wire:target="confirmSaveEkstrakurikuler">
                                    </i>
                                </span>

                                {{-- Teks tombol normal --}}
                                <span class="text-normal" wire:loading.class="d-none" wire:target="confirmSaveEkstrakurikuler">
                                    Simpan Ekstrakurikuler
                                </span>

                                {{-- Teks saat loading --}}
                                <span class="text-loading d-none" wire:loading.class.remove="d-none" wire:target="confirmSaveEkstrakurikuler">
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
            <div class="alert alert-danger text-center mt-3" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Silakan pilih Tahun Ajaran, Semester, dan Rombel/Kelas untuk mulai menginput data ekstrakurikuler.</strong>
            </div>
        </div>
    </div>
    @endif
</div>