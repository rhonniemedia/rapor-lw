<div>
    {{-- BARIS UTAMA (CARD INFO ROMBEL) --}}
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    {{-- Header halaman --}}
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="page-header pb-3 mb-4 border-bottom">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper position-relative">
                                            <span class="bg-gradient-primary p-2 rounded-3 shadow-sm me-3 d-inline-flex align-items-center justify-content-center">
                                                <i class="mdi mdi-file-chart mdi-24px text-white"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h4 class="mb-1 text-dark fw-bold">Preview Laporan Hasil Belajar</h4>
                                            <div class="d-flex align-items-center gap-2">
                                                <small class="text-muted">Pratinjau, cetak, dan unduh laporan hasil belajar</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Info Rombel Card (Cek pakai $this->) --}}
                    @if($this->rombel && $this->semesterAktif)
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
                                                {{-- AKSES MENGGUNAKAN $this-> --}}
                                                {{ $this->rombel->tahunAjaranKurikulum->kurikulum->nama ?? 'Global' }}
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
                                                {{-- AKSES MENGGUNAKAN $this-> --}}
                                                {{ $this->semesterAktif->tahunAjaran->nama ?? 'N/A' }} ~ {{ $this->semesterAktif->semester->nama ?? 'Belum Ada' }}
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
                                                {{-- AKSES MENGGUNAKAN $this-> --}}
                                                {{ $this->rombel->tingkat ?? '-' }} {{ $this->rombel->jurusan->alias ?? 'Umum' }} {{ $this->rombel->nomor ?? '' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                            style="width: 36px; height: 36px;">
                                            <i class="mdi mdi-school text-white fs-5"></i>
                                        </div>
                                        <div class="ms-3 d-flex flex-column justify-content-center">
                                            <small class="text-muted lh-2">Kompetensi Keahlian</small>
                                            <p class="fw-bold mb-0 text-dark lh-sm">
                                                {{-- AKSES MENGGUNAKAN $this-> --}}
                                                {{ $this->rombel->jurusan->nama ?? 'Umum' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                            style="width: 36px; height: 36px;">
                                            <i class="mdi mdi-account-tie text-white fs-5"></i>
                                        </div>
                                        <div class="ms-3 d-flex flex-column justify-content-center">
                                            <small class="text-muted lh-2">Wali Kelas</small>
                                            <p class="fw-bold mb-0 text-dark lh-sm">
                                                {{-- AKSES MENGGUNAKAN $this-> --}}
                                                {{ $this->rombel->waliKelas->name ?? 'Belum Ditentukan' }}
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

                    {{-- Cek pakai $this-> --}}
                    @if($this->rombel && $this->semesterAktif && isset($currentStudent))
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
                                    {{-- PERBAIKAN UTAMA: Gunakan $this->studentsList --}}
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

                    @if(isset($pdfUrl) && $pdfUrl)
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
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>