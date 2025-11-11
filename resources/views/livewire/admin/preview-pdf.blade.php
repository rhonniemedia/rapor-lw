<div>
    <!-- ========================= -->
    <!-- BAGIAN FILTER DATA -->
    <!-- ========================= -->
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="page-header mb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <h5 class="text-dark mb-2"><i class="mdi mdi-filter me-2"></i> Filter Data</h5>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tahun Ajaran</label>
                            <select class="form-select" wire:model="tahunAjaranId">
                                <option value="1">2024/2025 (Aktif)</option>
                                <option value="2">2023/2024</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Semester</label>
                            <select class="form-select" wire:model="semesterId">
                                <option value="1">Semester Ganjil</option>
                                <option value="2">Semester Genap</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Rombongan Belajar</label>
                            <select class="form-select" wire:model="rombonganBelajarId">
                                <option value="1">X RPL 1</option>
                                <option value="2">X RPL 2</option>
                                <option value="3">X TKJ 1</option>
                            </select>
                        </div>
                    </div>

                    <!-- Info Rombel -->
                    <div class="alert alert-success py-3 mt-4" role="alert">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-success rounded-3 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                        <i class="mdi mdi-book text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Kurikulum</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">Kurikulum Merdeka</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-success rounded-3 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                        <i class="mdi mdi-calendar-clock text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Tahun Ajaran & Semester</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">2024/2025 ~ Semester Ganjil</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-success rounded-3 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                        <i class="mdi mdi-account-group text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Rombel</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">X RPL 1</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-success rounded-3 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                        <i class="mdi mdi-shield-star text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Jurusan</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">Rekayasa Perangkat Lunak</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-success rounded-3 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                        <i class="mdi mdi-account-tie text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Wali Kelas</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">Siti Nurhaliza, S.Pd</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-success rounded-3 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                        <i class="mdi mdi-account-multiple text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Total Siswa</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">{{ $totalStudents }} Siswa</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================= -->
    <!-- BAGIAN PREVIEW LAPORAN -->
    <!-- ========================= -->
    @if($currentStudent)
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <!-- Header Preview -->
                    <div class="row align-items-center mb-3">
                        <div class="col-lg-5">
                            <h5 class="text-dark mb-0">
                                <i class="mdi mdi-file-document-outline me-2"></i>
                                Preview Laporan Hasil Belajar
                            </h5>
                            <small class="text-muted">
                                Siswa {{ $currentIndex + 1 }} dari {{ $totalStudents }} -
                                <strong>{{ $currentStudent['nama'] }}</strong>
                            </small>
                        </div>
                        <div class="col-lg-7 d-flex justify-content-end align-items-center flex-wrap gap-2">
                            <!-- Dropdown Pilih Siswa -->
                            <div class="input-group me-2" style="width: 300px; height: 38px;">
                                <label class="input-group-text bg-light border rounded-start-3 h-100">
                                    <i class="mdi mdi-account"></i>
                                </label>
                                <select class="form-select border rounded-end-3 h-100" wire:change="selectStudent($event.target.value)">
                                    @foreach($students as $index => $student)
                                    <option value="{{ $index }}" {{ $currentIndex == $index ? 'selected' : '' }}>
                                        {{ $student['nama'] }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tombol Navigasi -->
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

                    <!-- Preview PDF -->
                    @if($pdfUrl)
                    <!-- Biodata -->
                    <div class="ratio ratio-16x9 border rounded shadow-sm mb-3" style="height: 600px;">
                        <iframe
                            src="{{ $pdfUrl }}"
                            title="Preview Laporan Hasil Belajar - {{ $currentStudent['nama'] }}"
                            frameborder="0"
                            key="{{ $currentStudent['id'] }}">
                        </iframe>
                    </div>

                    <!-- Rapor -->
                    <!-- <div class="ratio ratio-16x9 border rounded shadow-sm mb-3" style="height: 600px;">
                        <iframe
                            src="{{ $pdfUrl }}"
                            title="Preview Laporan Hasil Belajar - {{ $currentStudent['nama'] }}"
                            frameborder="0"
                            key="{{ $currentStudent['id'] }}">
                        </iframe>
                    </div> -->
                    @else
                    <div class="alert alert-warning">
                        <i class="mdi mdi-alert me-2"></i>
                        PDF tidak dapat dimuat. Pastikan data lengkap dan coba lagi.
                    </div>
                    @endif

                    <!-- Info Siswa -->
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <div class="card border bg-light">
                                <div class="card-body py-2 px-3">
                                    <small class="text-muted">NIS / NISN</small>
                                    <p class="mb-0 fw-bold">{{ $currentStudent['nis'] }} / {{ $currentStudent['nisn'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border bg-light">
                                <div class="card-body py-2 px-3">
                                    <small class="text-muted">Kelas</small>
                                    <p class="mb-0 fw-bold">{{ $currentStudent['kelas'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border bg-light">
                                <div class="card-body py-2 px-3">
                                    <small class="text-muted">Fase</small>
                                    <p class="mb-0 fw-bold">{{ $currentStudent['fase'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border bg-light">
                                <div class="card-body py-2 px-3">
                                    <small class="text-muted">Mata Pelajaran</small>
                                    <p class="mb-0 fw-bold">{{ count($currentStudent['nilai']) }} Mapel</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-xl-12">
            <div class="alert alert-warning">
                <i class="mdi mdi-information me-2"></i>
                <strong>Tidak ada data siswa.</strong> Silakan pilih filter yang sesuai.
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Listen for student changed event (Livewire 3)
    document.addEventListener('livewire:init', () => {
        Livewire.on('student-changed', (event) => {
            const direction = event.direction === 'next' ? 'Berikutnya' : 'Sebelumnya';

            // Optional: Toast notification if available
            if (typeof toastr !== 'undefined') {
                toastr.success(`Navigasi ke siswa ${direction}: ${event.student}`);
            }

            // Scroll smooth to top
            const contentWrapper = document.querySelector('.content-wrapper');
            if (contentWrapper) {
                contentWrapper.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Keyboard navigation (Arrow keys)
    document.addEventListener('keydown', function(e) {
        // Only if not typing in input/textarea
        if (!e.target.matches('input, textarea, select')) {
            // Left arrow = Previous
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                @this.call('previousStudent');
            }
            // Right arrow = Next
            if (e.key === 'ArrowRight') {
                e.preventDefault();
                @this.call('nextStudent');
            }
        }
    });
</script>
@endpush