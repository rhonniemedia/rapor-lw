<div>
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="page-header mb-0 border-bottom">
                                <div class="d-flex align-items-center">
                                    <h5 class="text-dark"><i class="mdi mdi-filter me-2"></i> Filter Data Kehadiran</h5>
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

                    {{-- Info Rombel - Diperbarui mengikuti style input-nilai-akhir --}}
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

                                <!-- Jurusan -->
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

                            <!-- Kolom 3 -->
                            <div class="col-md-4">
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
                            <h5 class="text-dark"><i class="mdi mdi-account-multiple me-2"></i> Entri Data Kehadiran Pelajar</h5>
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

                    {{-- Tabel Kehadiran --}}
                    <div class="table-responsive" wire:loading.class.delay.longest="opacity-50">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="10%" class="text-center">Nomor Induk</th>
                                    <th width="10%" class="text-center">NISN</th>
                                    <th width="35%">Nama Lengkap</th>
                                    <th width="10%" class="text-center">Sakit (S)</th>
                                    <th width="10%" class="text-center">Izin (I)</th>
                                    <th width="10%" class="text-center">Tanpa Ket. (A)</th>
                                    <th width="10%" class="text-center">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pelajarData as $index => $pelajar)
                                <tr>
                                    <td class="text-center">{{ $pelajarData->firstItem() + $index }}</td>
                                    <td class="text-center">{{ $pelajar->nomor_induk }}</td>
                                    <td class="text-center">{{ $pelajar->nisn }}</td>
                                    <td>
                                        <strong>{{ $pelajar->nama_lengkap }}</strong>
                                    </td>
                                    {{-- Input Sakit --}}
                                    <td>
                                        <input type="number"
                                            wire:model.blur="kehadiranInput.{{ $pelajar->pelajar_id }}.sakit"
                                            class="form-control form-control-sm text-center @error('kehadiranInput.'.$pelajar->pelajar_id.'.sakit') is-invalid @enderror"
                                            min="0"
                                            max="999"
                                            placeholder="0">
                                        @error('kehadiranInput.'.$pelajar->pelajar_id.'.sakit')
                                        <small class="text-danger">{{ str_replace('kehadiranInput.'.$pelajar->pelajar_id.'.sakit', 'Jumlah sakit', $message) }}</small>
                                        @enderror
                                    </td>
                                    {{-- Input Izin --}}
                                    <td>
                                        <input type="number"
                                            wire:model.blur="kehadiranInput.{{ $pelajar->pelajar_id }}.izin"
                                            class="form-control form-control-sm text-center @error('kehadiranInput.'.$pelajar->pelajar_id.'.izin') is-invalid @enderror"
                                            min="0"
                                            max="999"
                                            placeholder="0">
                                        @error('kehadiranInput.'.$pelajar->pelajar_id.'.izin')
                                        <small class="text-danger">{{ str_replace('kehadiranInput.'.$pelajar->pelajar_id.'.izin', 'Jumlah izin', $message) }}</small>
                                        @enderror
                                    </td>
                                    {{-- Input Tanpa Keterangan (Alpa) --}}
                                    <td>
                                        <input type="number"
                                            wire:model.blur="kehadiranInput.{{ $pelajar->pelajar_id }}.tanpa_keterangan"
                                            class="form-control form-control-sm text-center @error('kehadiranInput.'.$pelajar->pelajar_id.'.tanpa_keterangan') is-invalid @enderror"
                                            min="0"
                                            max="999"
                                            placeholder="0">
                                        @error('kehadiranInput.'.$pelajar->pelajar_id.'.tanpa_keterangan')
                                        <small class="text-danger">{{ str_replace('kehadiranInput.'.$pelajar->pelajar_id.'.tanpa_keterangan', 'Jumlah tanpa keterangan', $message) }}</small>
                                        @enderror
                                    </td>
                                    {{-- Total --}}
                                    <td class="text-center">
                                        <span class="badge badge-{{ $pelajar->total_ketidakhadiran > 0 ? 'danger' : 'success' }}">
                                            {{ $pelajar->total_ketidakhadiran }} hari
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle me-2"></i> Data pelajar tidak ditemukan atau rombel belum memiliki pelajar.
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

                        {{-- Tombol Reset --}}
                        <button
                            type="button"
                            class="btn btn-labeled btn-outline-secondary me-2"
                            wire:click="confirmResetKehadiran"
                            wire:loading.attr="disabled"
                            wire:target="confirmResetKehadiran">
                            <span class="btn-label">
                                <i class="mdi mdi-delete-sweep-outline"></i>
                            </span>
                            Reset
                        </button>

                        {{-- Tombol Simpan --}}
                        <button
                            type="button"
                            class="btn btn-labeled btn-primary"
                            wire:click="confirmSaveKehadiran"
                            wire:loading.attr="disabled"
                            wire:target="confirmSaveKehadiran">
                            <span class="btn-label">
                                {{-- Icon loading tampil hanya saat confirmSaveKehadiran aktif --}}
                                <i class="mdi mdi-loading mdi-spin d-none"
                                    wire:loading.class.remove="d-none"
                                    wire:target="confirmSaveKehadiran">
                                </i>

                                {{-- Icon simpan hilang saat loading --}}
                                <i class="mdi mdi-content-save"
                                    wire:loading.class="d-none"
                                    wire:target="confirmSaveKehadiran">
                                </i>
                            </span>

                            {{-- Teks tombol normal --}}
                            <span class="text-normal" wire:loading.class="d-none" wire:target="confirmSaveKehadiran">
                                Simpan
                            </span>

                            {{-- Teks saat loading --}}
                            <span class="text-loading d-none" wire:loading.class.remove="d-none" wire:target="confirmSaveKehadiran">
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
                <strong>Silakan pilih Tahun Ajaran, Semester, dan Rombel/Kelas untuk mulai menginput data kehadiran.</strong>
            </div>
        </div>
    </div>
    @endif
</div>