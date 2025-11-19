<!-- content-wrapper -->
<div>
    <!-- Header Dashboard Guru -->
    <div class="page-header flex-wrap">
        <h3 class="mb-0">
            Selamat datang <strong>{{ $namaGuru }}!</strong>
            <span class="pl-0 h6 pl-sm-2 text-muted d-inline-block">
                Guru {{ $mataPelajaranUtama }}
            </span>
        </h3>
        <div class="d-flex">
            <div class="btn-btn">
                <button type="button" class="btn btn-sm btn-success">
                    Tahun Ajaran {{ $tahunAjaran }} ~ Semester {{ $semesterNama }}
                </button>
            </div>
        </div>
    </div>

    <!-- Progress Input Nilai -->
    <div class="row">
        <!-- Kolom Kiri -->
        <div class="col-xl-3 col-lg-12 stretch-card grid-margin">
            <div class="row h-100">
                <div class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 mb-3">
                    <div class="card bg-primary h-100">
                        <div class="card-body px-3 py-4 d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="color-card">
                                    <p class="mb-0 color-card-head">Kelas yang Diampu</p>
                                    <h2 class="text-white">{{ $jumlahKelas }}</h2>
                                </div>
                                <i class="card-icon-indicator mdi mdi-google-classroom bg-inverse-icon-primary"></i>
                            </div>
                            <h6 class="text-white mb-0">{{ $daftarKelasText }}</h6>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 mb-3">
                    <div class="card bg-success h-100">
                        <div class="card-body px-3 py-4 d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="color-card">
                                    <p class="mb-0 color-card-head">Progress Input</p>
                                    <h2 class="text-white">{{ $progressInputNilai }}%</h2>
                                </div>
                                <i class="card-icon-indicator mdi mdi-progress-check bg-inverse-icon-success"></i>
                            </div>
                            <h6 class="text-white mb-0">{{ $kelasBelumLengkap }} kelas belum lengkap</h6>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 mb-3">
                    <div class="card bg-warning h-100">
                        <div class="card-body px-3 py-4 d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="color-card">
                                    <p class="mb-0 color-card-head">Total Pelajar</p>
                                    <h2 class="text-white">{{ $totalPelajar }}</h2>
                                </div>
                                <i class="card-icon-indicator mdi mdi-account-multiple bg-inverse-icon-warning"></i>
                            </div>
                            <h6 class="text-white mb-0">Semua kelas yang diampu</h6>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12 col-md-6 stretch-card">
                    <div class="card bg-danger h-100">
                        <div class="card-body px-3 py-4 d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="color-card">
                                    <p class="mb-0 color-card-head">Belum Diinput</p>
                                    <h2 class="text-white">{{ $nilaiBelumDiinput }}</h2>
                                </div>
                                <i class="card-icon-indicator mdi mdi-alert-circle bg-inverse-icon-danger"></i>
                            </div>
                            <h6 class="text-white mb-0">Nilai yang masih kosong</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="col-xl-9 stretch-card grid-margin">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="row mb-4">
                        <div class="col-sm-12">
                            <h5>Progress Input Nilai {{ $mataPelajaranUtama }}</h5>
                            <p class="text-muted mb-2">
                                Status kelengkapan nilai untuk semester {{ strtolower($semesterNama) }} {{ $tahunAjaran }}
                            </p>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 mr-3" style="height: 12px">
                                    <div class="progress-bar bg-success" role="progressbar"
                                        style="width: {{ $progressInputNilai }}%"
                                        aria-valuenow="{{ $progressInputNilai }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                                <span class="font-weight-bold text-success">{{ $progressInputNilai }}%</span>
                            </div>
                        </div>
                    </div>

                    <div class="row flex-grow-1">
                        <!-- Chart Distribusi Nilai -->
                        <div class="col-md-12">
                            <div class="d-flex flex-column h-100">
                                <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                                    <div style="position: relative; height: 280px; width: 280px">
                                        <canvas id="chart_distribusi_nilai"
                                            data-nilai-a="{{ $distribusiNilai['A']['jumlah'] ?? 0 }}"
                                            data-nilai-b="{{ $distribusiNilai['B']['jumlah'] ?? 0 }}"
                                            data-nilai-c="{{ $distribusiNilai['C']['jumlah'] ?? 0 }}"
                                            data-nilai-d="{{ $distribusiNilai['D']['jumlah'] ?? 0 }}">
                                        </canvas>
                                    </div>
                                </div>
                                <div class="mt-3 text-center">

                                    <!-- Judul -->
                                    <h6 class="text-black mb-3">Distribusi Nilai {{ $mataPelajaranUtama }}</h6>

                                    <!-- Wrapper 1 baris -->
                                    <div class="d-flex justify-content-center">

                                        <!-- A -->
                                        <div class="mx-4">
                                            <div class="d-flex align-items-center justify-content-center mb-1">
                                                <span class="legend-dot" style="background-color: #4CAF50"></span>
                                                <span class="text-muted ml-2">A (90-100)</span>
                                                <span class="font-weight-bold" style="margin-left: 6px;">{{ $distribusiNilai['A']['persentase'] }}%</span>
                                            </div>
                                        </div>

                                        <!-- B -->
                                        <div class="mx-4">
                                            <div class="d-flex align-items-center justify-content-center mb-1">
                                                <span class="legend-dot" style="background-color: #2196F3"></span>
                                                <span class="text-muted ml-2">B (80-89)</span>
                                                <span class="font-weight-bold" style="margin-left: 6px;">{{ $distribusiNilai['B']['persentase'] }}%</span>
                                            </div>
                                        </div>

                                        <!-- C -->
                                        <div class="mx-4">
                                            <div class="d-flex align-items-center justify-content-center mb-1">
                                                <span class="legend-dot" style="background-color: #FFC107"></span>
                                                <span class="text-muted ml-2">C (70-79)</span>
                                                <span class="font-weight-bold" style="margin-left: 6px;">{{ $distribusiNilai['C']['persentase'] }}%</span>
                                            </div>
                                        </div>

                                        <!-- D -->
                                        <div class="mx-4">
                                            <div class="d-flex align-items-center justify-content-center mb-1">
                                                <span class="legend-dot" style="background-color: #F44336"></span>
                                                <span class="text-muted ml-2">D (0-69)</span>
                                                <span class="font-weight-bold" style="margin-left: 6px;">{{ $distribusiNilai['D']['persentase'] }}%</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Input Nilai per Kelas -->
        <div class="col-xl-5 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title text-black mb-0">Status Input per Kelas</h4>
                    </div>
                    <p class="text-muted">Progres kelengkapan nilai tiap kelas</p>

                    <div class="mt-4">
                        @forelse($progressPerKelas as $kelas)
                        <!-- Kelas Card -->
                        <a href="#" class="text-decoration-none mb-3">
                            <div class="card {{ $kelas['badge'] == 'danger' ? 'urgent-card' : ($kelas['badge'] == 'warning' ? 'warning-card' : 'success-card') }} p-3 progress-card-hover">
                                <div class="d-flex align-items-center h-100">
                                    <i class="mdi mdi-google-classroom mdi-24px text-{{ $kelas['badge'] }} mr-3"></i>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1 text-dark">{{ $kelas['nama'] }}</h5>
                                        <p class="mb-0 text-muted">
                                            @if($kelas['pelajar_belum_dinilai'] > 0)
                                            <span class="text-{{ $kelas['badge'] }}">{{ $kelas['pelajar_belum_dinilai'] }} pelajar</span> belum diinput
                                            @else
                                            <span class="text-success">Semua pelajar sudah dinilai</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="badge badge-{{ $kelas['badge'] }}">{{ $kelas['status'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="text-center text-muted py-4">
                            <i class="mdi mdi-information-outline" style="font-size: 2rem;"></i>
                            <p class="mt-2">Belum ada data kelas</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Pelajar Belum Dinilai -->
        <div class="col-xl-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title text-black mb-0">Pelajar Belum Dinilai</h4>
                        <span class="badge badge-warning">{{ $nilaiBelumDiinput }} Pelajar</span>
                    </div>
                    <p class="text-muted">Pelajar yang belum memiliki nilai {{ $mataPelajaranUtama }}</p>

                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 55%;">Pelajar</th>
                                    <th style="width: 30%;">Rombel</th>
                                    <th style="width: 15%;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pelajarBelumDinilai as $pelajar)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($pelajar['foto'])
                                            <img src="{{ asset('storage/' . $pelajar['foto']) }}" alt="profile" class="rounded-circle me-3" style="width: 35px; height: 35px; object-fit: cover;">
                                            @else
                                            <img src="{{ asset('assets/images/icons/' . (strtolower($pelajar['jenis_kelamin']) == 'l' ? 'male' : 'female') . '.png') }}" alt="profile" class="rounded-circle me-3" style="width: 35px; height: 35px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <p class="mb-0 font-weight-medium">{{ $pelajar['nama'] }}</p>
                                                <small class="text-muted">NIS: {{ $pelajar['nis'] }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $pelajar['kelas'] }}</td>
                                    <td>
                                        <div class="badge badge-inverse-danger">Belum Dinilai</div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="mdi mdi-check-circle-outline" style="font-size: 2rem;"></i>
                                        <p class="mt-2">Semua pelajar sudah dinilai</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Ambil element canvas
            const canvas = document.getElementById('chart_distribusi_nilai');

            // Baca data dari data attributes
            const nilaiA = parseInt(canvas.dataset.nilaiA) || 0;
            const nilaiB = parseInt(canvas.dataset.nilaiB) || 0;
            const nilaiC = parseInt(canvas.dataset.nilaiC) || 0;
            const nilaiD = parseInt(canvas.dataset.nilaiD) || 0;

            // Chart Distribusi Nilai
            const ctx = canvas.getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['A (90-100)', 'B (80-89)', 'C (70-79)', 'D (0-69)'],
                    datasets: [{
                        data: [nilaiA, nilaiB, nilaiC, nilaiD],
                        backgroundColor: ['#4CAF50', '#2196F3', '#FFC107', '#F44336'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
</div>