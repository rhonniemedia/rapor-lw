<div>
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="page-header mb-0 border-bottom">
                                <div class="d-flex align-items-center">
                                    <h5 class="text-dark"><i class="mdi mdi-filter me-2"></i> Filter Data Laporan Hasil Belajar</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3 row">
                                <div class="col-sm-4">
                                    <label class="form-label">Tahun Ajaran</label>
                                    <select wire:model.live="tahunAjaranId" class="form-select">
                                        <option value="">-- Pilih Tahun Ajaran --</option>
                                        {{-- PERBAIKAN: Gunakan $this-> --}}
                                        @foreach($this->tahunAjaranList as $ta)
                                        <option value="{{ $ta->id }}">
                                            {{ $ta->nama }}
                                            @if(($ta->status ?? '') === 'aktif') (Aktif) @endif
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-4">
                                    <label class="form-label">Semester</label>
                                    <select wire:model.live="semesterId" class="form-select"
                                        @if(!$tahunAjaranId) disabled @endif>
                                        <option value="">-- Pilih Semester --</option>
                                        {{-- PERBAIKAN: Gunakan $this-> --}}
                                        @foreach($this->semesterList as $smt)
                                        <option value="{{ $smt->id }}">
                                            Semester {{ $smt->semester->nama ?? '' }}
                                            @if(($smt->status ?? '') === 'aktif') (Aktif) @endif
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-4">
                                    <label class="form-label">Rombongan Belajar</label>
                                    <select wire:model.live="rombelId" class="form-select"
                                        @if(!$semesterId) disabled @endif>
                                        <option value="">-- Pilih Rombel --</option>
                                        {{-- PERBAIKAN: Gunakan $this-> --}}
                                        @foreach($this->rombelList as $rb)
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
                    {{-- PERBAIKAN: Gunakan $this->rombel --}}
                    @if($this->rombel && $rombelId)
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
                                            {{ $this->rombel->tahunAjaranKurikulum->kurikulum->nama ?? 'N/A' }}
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
                                            // Ambil dari computed list
                                            $selectedSemester = $this->semesterList->firstWhere('id', $semesterId);
                                            $selectedTahunAjaran = $this->tahunAjaranList->firstWhere('id', $tahunAjaranId);
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
                                        <p class="fw-bold mb-0 text-dark lh-sm">{{ $this->rombel->nama ?? 'N/A' }}</p>
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
                                            {{ $this->rombel->jurusan->nama ?? 'Belum Ada' }}
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
                                            {{ $this->rombel->waliKelas->name ?? 'Belum Ada' }}
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

    @if($rombelId && $semesterId && $currentStudent)
    <div class="row mt-1">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center mb-3">
                        <div class="col-lg-4">
                            <h5 class="text-dark mb-0">
                                <i class="mdi mdi-file-document-outline me-2"></i>
                                Preview Laporan Hasil Belajar
                            </h5>
                            <small class="text-muted">
                                Siswa {{ $currentIndex + 1 }} dari {{ $totalStudents }} -
                                <strong>{{ $currentStudent['nama'] }}</strong>
                            </small>
                        </div>
                        <div class="col-lg-8 d-flex justify-content-end align-items-center flex-wrap gap-1">
                            <div class="input-group me-2" style="width: 300px; height: 38px;">
                                <label class="input-group-text bg-light border rounded-start-3 h-100 d-flex align-items-center justify-content-center px-2" style="width: 40px;">
                                    <i class="mdi mdi-file-document"></i>
                                </label>
                                <select class="form-select border rounded-end-3 h-100" wire:model.live="selectedPage">
                                    <option value="cover">Halaman Biodata (Cover)</option>
                                    <option value="content">Halaman Nilai (Content)</option>
                                </select>
                            </div>

                            <div class="input-group me-2" style="width: 300px; height: 38px;">
                                <label class="input-group-text bg-light border rounded-start-3 h-100 d-flex align-items-center justify-content-center px-2" style="width: 40px;">
                                    <i class="mdi mdi-account"></i>
                                </label>
                                <select class="form-select border rounded-end-3 h-100" wire:change="selectStudent($event.target.value)">
                                    {{-- PERBAIKAN: Gunakan $this->studentsList --}}
                                    @foreach($this->studentsList as $index => $student)
                                    <option value="{{ $index }}" {{ $currentIndex == $index ? 'selected' : '' }}>
                                        {{ $student['nama'] }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <button
                                type="button"
                                class="btn btn-outline-light-muted btn-sm d-flex align-items-center justify-content-center me-1 border rounded-3"
                                style="height: 38px;"
                                title="Previous"
                                wire:click="previousStudent"
                                @if($currentIndex <=0) disabled @endif>
                                <i class="mdi mdi-arrow-left-bold-outline text-muted fs-5"></i>
                            </button>

                            <button
                                type="button"
                                class="btn btn-outline-light-muted btn-sm d-flex align-items-center justify-content-center border rounded-3"
                                style="height: 38px;"
                                title="Next"
                                wire:click="nextStudent"
                                @if($currentIndex>= $totalStudents - 1) disabled @endif>
                                <i class="mdi mdi-arrow-right-bold-outline text-muted fs-5"></i>
                            </button>
                        </div>
                    </div>

                    @if($pdfUrl)
                    <div class="ratio ratio-16x9 border rounded shadow-sm mb-3" style="height: 600px;">
                        <iframe
                            src="{{ $pdfUrl }}"
                            title="Preview {{ $selectedPage === 'cover' ? 'Biodata' : 'Nilai' }} - {{ $currentStudent['nama'] }}"
                            frameborder="0"
                            wire:key="pdf-{{ $currentStudent['id'] }}-{{ $selectedPage }}">
                        </iframe>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="mdi mdi-alert me-2"></i>
                        PDF tidak dapat dimuat. Pastikan data lengkap dan coba lagi.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>