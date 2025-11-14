<div>
    <!-- Konten Dashboard Wali Kelas -->
    <div class="content-wrapper pb-0">
        <div class="page-header flex-wrap">
            <h3 class="mb-0">
                Selamat datang <strong>{{ Auth::user()->name }}!</strong>
                @if($rombel)
                <span class="pl-0 h6 pl-sm-2 text-muted d-inline-block">
                    Wali Kelas {{ $rombel->nama }}
                </span>
                @endif
            </h3>
            <div class="d-flex">
                <div class="btn-btn">
                    @if($tahunAjaranSemester)
                    <button type="button" class="btn btn-sm btn-success">
                        {{ $tahunAjaranSemester->tahunAjaran->nama }} ~ {{ $tahunAjaranSemester->semester->nama }}
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistik Utama Kelas -->
        <div class="row">
            <div class="col-xl-3 col-lg-12 stretch-card grid-margin">
                <div class="row h-100">
                    <!-- Jumlah Siswa -->
                    <div class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 mb-3">
                        <div class="card bg-primary h-100">
                            <div class="card-body px-3 py-4 d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="color-card">
                                        <p class="mb-0 color-card-head">Jumlah Siswa</p>
                                        <h2 class="text-white">{{ $totalSiswa }}</h2>
                                    </div>
                                    <i class="card-icon-indicator mdi mdi-account-multiple bg-inverse-icon-primary"></i>
                                </div>
                                <h6 class="text-white mb-0">
                                    {{ $siswaLakiLaki }} Laki-laki, {{ $siswaPerempuan }} Perempuan
                                </h6>
                            </div>
                        </div>
                    </div>

                    <!-- Rata-rata Nilai -->
                    <div class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 mb-3">
                        <div class="card bg-info h-100">
                            <div class="card-body px-3 py-4 d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="color-card">
                                        <p class="mb-0 color-card-head">Rata-rata Nilai</p>
                                        <h2 class="text-white">{{ $rataRataNilai }}</h2>
                                    </div>
                                    <i class="card-icon-indicator mdi mdi-chart-line bg-inverse-icon-info"></i>
                                </div>
                                <h6 class="text-white mb-0">Nilai Tertinggi: {{ $nilaiTertinggi }}</h6>
                            </div>
                        </div>
                    </div>

                    <!-- Kehadiran -->
                    <div class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 mb-3">
                        <div class="card bg-success h-100">
                            <div class="card-body px-3 py-4 d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="color-card">
                                        <p class="mb-0 color-card-head">Kehadiran</p>
                                        <h2 class="text-white">{{ $persentaseKehadiran }}%</h2>
                                    </div>
                                    <i class="card-icon-indicator mdi mdi-calendar-check bg-inverse-icon-success"></i>
                                </div>
                                <h6 class="text-white mb-0">
                                    {{ $siswaKehadiranRendah }} siswa dengan kehadiran &lt;80%
                                </h6>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Input -->
                    <div class="col-xl-12 col-md-6 stretch-card">
                        <div class="card bg-warning h-100">
                            <div class="card-body px-3 py-4 d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="color-card">
                                        <p class="mb-0 color-card-head">Progress Input</p>
                                        <h2 class="text-white">{{ $progressInput }}%</h2>
                                    </div>
                                    <i class="card-icon-indicator mdi mdi-progress-check bg-inverse-icon-warning"></i>
                                </div>
                                <h6 class="text-white mb-0">{{ $this->mapelBelumLengkap }} mapel belum lengkap</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Kelengkapan Data Kelas -->
            <div class="col-xl-9 stretch-card grid-margin">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <!-- Header -->
                        <div class="row mb-4">
                            <div class="col-sm-12">
                                <h5>Progress Kelengkapan Data {{ $rombel ? $rombel->nama : 'Kelas' }}</h5>
                                <p class="text-muted mb-2">
                                    Status kelengkapan data untuk
                                    @if($tahunAjaranSemester)
                                    {{ strtolower($tahunAjaranSemester->semester->nama) }} {{ $tahunAjaranSemester->tahunAjaran->nama }}
                                    @endif
                                </p>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 mr-3" style="height: 12px">
                                        <div class="progress-bar bg-success" role="progressbar"
                                            style="width: {{ $progressInput }}%"
                                            aria-valuenow="{{ $progressInput }}"
                                            aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                    <span class="font-weight-bold text-success">{{ $progressInput }}%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Konten Utama -->
                        <div class="row flex-grow-1">
                            <!-- Kartu Progress -->
                            <div class="col-md-5">
                                <div class="d-flex flex-column h-100 justify-content-between">
                                    <!-- Kartu 1: Nilai Akademik -->
                                    <div class="card bg-light-secondary p-3 mb-2 progress-card-hover" style="min-height: 100px">
                                        <div class="d-flex align-items-center h-100">
                                            <i class="mdi mdi-book-open-page-variant mdi-24px text-secondary mr-3"></i>
                                            <div class="flex-grow-1">
                                                <h5 class="mb-1">{{ $progressNilai }}%</h5>
                                                <p class="mb-0 text-muted">Nilai Akademik</p>
                                            </div>
                                            <small class="text-secondary">{{ $this->mapelBelumLengkap }} mapel belum lengkap</small>
                                        </div>
                                    </div>

                                    <!-- Kartu 2: Kokurikuler -->
                                    <div class="card bg-light-secondary p-3 mb-2 progress-card-hover" style="min-height: 100px">
                                        <div class="d-flex align-items-center h-100">
                                            <i class="mdi mdi-account-group mdi-24px text-secondary mr-3"></i>
                                            <div class="flex-grow-1">
                                                <h5 class="mb-1">{{ $progressKokurikuler }}%</h5>
                                                <p class="mb-0 text-muted">Kokurikuler</p>
                                            </div>
                                            <small class="text-secondary">
                                                {{ $totalSiswa - (int)(($progressKokurikuler / 100) * $totalSiswa) }} siswa belum lengkap
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Kartu 3: Kehadiran -->
                                    <div class="card bg-light-secondary p-3 mb-2 progress-card-hover" style="min-height: 100px">
                                        <div class="d-flex align-items-center h-100">
                                            <i class="mdi mdi-calendar-check mdi-24px text-secondary mr-3"></i>
                                            <div class="flex-grow-1">
                                                <h5 class="mb-1">{{ $progressKehadiran }}%</h5>
                                                <p class="mb-0 text-muted">Kehadiran</p>
                                            </div>
                                            <small class="text-success">
                                                {{ (int)(($progressKehadiran / 100) * $totalSiswa) }}/{{ $totalSiswa }} siswa
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Kartu 4: Ekstrakurikuler -->
                                    <div class="card bg-light-secondary p-3 mb-2 progress-card-hover" style="min-height: 100px">
                                        <div class="d-flex align-items-center h-100">
                                            <i class="mdi mdi-soccer mdi-24px text-secondary mr-3"></i>
                                            <div class="flex-grow-1">
                                                <h5 class="mb-1">{{ $progressEkstrakurikuler }}%</h5>
                                                <p class="mb-0 text-muted">Ekstrakurikuler</p>
                                            </div>
                                            <small class="text-secondary">
                                                {{ $totalSiswa - (int)(($progressEkstrakurikuler / 100) * $totalSiswa) }} siswa belum terdaftar
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Kartu 5: Catatan Sikap -->
                                    <div class="card bg-light-secondary p-3 progress-card-hover" style="min-height: 100px">
                                        <div class="d-flex align-items-center h-100">
                                            <i class="mdi mdi-clipboard-text mdi-24px text-secondary mr-3"></i>
                                            <div class="flex-grow-1">
                                                <h5 class="mb-1">{{ $progressCatatan }}%</h5>
                                                <p class="mb-0 text-muted">Catatan Sikap</p>
                                            </div>
                                            <small class="text-secondary">
                                                {{ (int)(($progressCatatan / 100) * $totalSiswa) }}/{{ $totalSiswa }} siswa
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Chart dan Legend -->
                            <div class="col-md-7">
                                <div class="d-flex flex-column h-100">
                                    <div class="flex-grow-1 d-flex align-items-center justify-content-center">
                                        <div style="position: relative; height: 280px; width: 280px">
                                            <canvas id="chart_kelas_xi_ips2"></canvas>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <div class="d-flex align-items-center">
                                                <span class="legend-dot" style="background-color: #A0B9FF"></span>
                                                <span class="text-muted ml-2">Nilai Akademik</span>
                                            </div>
                                            <span class="font-weight-bold">{{ $progressNilai }}%</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <div class="d-flex align-items-center">
                                                <span class="legend-dot" style="background-color: #92DDCC"></span>
                                                <span class="text-muted ml-2">Kokurikuler</span>
                                            </div>
                                            <span class="font-weight-bold">{{ $progressKokurikuler }}%</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <div class="d-flex align-items-center">
                                                <span class="legend-dot" style="background-color: #FFC0CB"></span>
                                                <span class="text-muted ml-2">Kehadiran</span>
                                            </div>
                                            <span class="font-weight-bold">{{ $progressKehadiran }}%</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <div class="d-flex align-items-center">
                                                <span class="legend-dot" style="background-color: #DDA0DD"></span>
                                                <span class="text-muted ml-2">Ekstrakurikuler</span>
                                            </div>
                                            <span class="font-weight-bold">{{ $progressEkstrakurikuler }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions dan Siswa Perlu Perhatian -->
        <div class="row">
            <div class="col-xl-4 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-black">Aksi Cepat</h4>
                        <p class="text-muted">
                            Tugas yang perlu segera diselesaikan
                        </p>
                        <div class="list-wrapper">
                            <div class="d-flex flex-column">

                                <!-- Input Nilai -->
                                <a href="{{ route('walikelas.entri.nilai')}}" class="text-decoration-none mb-2">
                                    <div class="card bg-light-primary p-3 progress-card-hover" style="min-height: 95px">
                                        <div class="d-flex align-items-center h-100">
                                            <i class="mdi mdi-pencil-box-outline mdi-24px text-primary mr-3"></i>

                                            <div class="flex-grow-1">
                                                <h5 class="mb-1 text-dark">Input Nilai</h5>
                                                <p class="mb-0 text-muted">
                                                    {{ $this->mapelBelumLengkap }} mata pelajaran belum lengkap
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                                <!-- Rekap Kehadiran -->
                                <a href="{{ route('walikelas.entri.kehadiran')}}" class="text-decoration-none mb-2">
                                    <div class="card bg-light-warning p-3 progress-card-hover" style="min-height: 95px">
                                        <div class="d-flex align-items-center h-100">
                                            <i class="mdi mdi-calendar-check mdi-24px text-warning mr-3"></i>

                                            <div class="flex-grow-1">
                                                <h5 class="mb-1 text-dark">Rekap Kehadiran</h5>
                                                <p class="mb-0 text-muted">
                                                    {{ $siswaKehadiranRendah }} pelajar perlu perhatian khusus
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                                <!-- Catatan Sikap -->
                                <a href="{{ route('walikelas.entri.catatan')}}" class="text-decoration-none mb-2">
                                    <div class="card bg-light-primary p-3 progress-card-hover" style="min-height: 95px">
                                        <div class="d-flex align-items-center h-100">
                                            <i class="mdi mdi-file-document-outline mdi-24px text-primary mr-3"></i>

                                            <div class="flex-grow-1">
                                                <h5 class="mb-1 text-dark">Catatan Sikap</h5>
                                                <p class="mb-0 text-muted">
                                                    Isi catatan untuk {{ $totalSiswa - (int)(($progressCatatan / 100) * $totalSiswa) }} pelajar
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                                <!-- Kokurikuler -->
                                <a href="{{ route('walikelas.entri.kokurikuler')}}" class="text-decoration-none">
                                    <div class="card bg-light-warning p-3 progress-card-hover" style="min-height: 95px">
                                        <div class="d-flex align-items-center h-100">
                                            <i class="mdi mdi-clipboard-account mdi-24px text-warning mr-3"></i>

                                            <div class="flex-grow-1">
                                                <h5 class="mb-1 text-dark">Kokurikuler</h5>
                                                <p class="mb-0 text-muted">
                                                    {{ $totalSiswa - (int)(($progressKokurikuler / 100) * $totalSiswa) }} pelajar belum diinput
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                            </div>


                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-black">Pelajar Perlu Perhatian</h4>
                        <p class="text-muted">
                            Pelajar dengan nilai di bawah KKM atau kehadiran rendah
                        </p>
                        <div class="table-responsive mb-3">
                            <table class="table">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Pelajar</th>
                                        <th>Status</th>
                                        <th>Nilai Rata-rata</th>
                                        <th>Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswaPerluPerhatian as $siswa)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img
                                                    src="{{ asset('assets/images/icons/male.png') }}"
                                                    alt="profile"
                                                    class="rounded-circle me-3"
                                                    style="width: 40px; height: 40px; object-fit: cover;">

                                                <div>
                                                    <p class="mb-0 font-weight-medium">{{ $siswa['nama'] }}</p>

                                                    <small class="text-muted">
                                                        <i class="mdi mdi-comment-alert-outline"></i>
                                                        {{ implode(', ', $siswa['alasan']) }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="badge {{ $siswa['badge_class'] }}">
                                                @if(str_contains(implode('', $siswa['alasan']), 'Nilai'))
                                                Nilai Rendah
                                                @elseif(str_contains(implode('', $siswa['alasan']), 'Kehadiran'))
                                                Kehadiran Rendah
                                                @else
                                                Perlu Perhatian
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $siswa['nilai_rata_rata'] }}</td>
                                        <td>{{ $siswa['persentase_kehadiran'] }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="mdi mdi-emoticon-happy mdi-48px text-success"></i>
                                            <p class="text-muted mt-2 mb-0">Semua pelajar dalam kondisi baik!</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- <a class="text-black mt-3 d-block font-weight-medium h6" href="#">
                            Lihat semua siswa <i class="mdi mdi-chevron-right"></i>
                        </a> -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribusi Nilai -->
        <div class="row">
            <!-- Distribusi Nilai Kelas -->
            <div class="col-xl-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-black">Distribusi Nilai Kelas</h4>
                        <p class="text-muted">Sebaran nilai pelajar berdasarkan predikat</p>

                        <div class="d-flex flex-wrap border-bottom py-2 justify-content-between">
                            <div class="pt-2">
                                <h5 class="mb-0">90-100 (A)</h5>
                                <p class="mb-0 text-muted">Sangat Baik</p>
                                <h5 class="mb-0">{{ $distribusiNilai['A']['persentase'] }}%</h5>
                            </div>
                            <div class="pt-2">
                                <div class="badge badge-inverse-success mt-3">
                                    {{ $distribusiNilai['A']['jumlah'] }} siswa
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap border-bottom py-2 justify-content-between">
                            <div class="pt-2">
                                <h5 class="mb-0">80-89 (B)</h5>
                                <p class="mb-0 text-muted">Baik</p>
                                <h5 class="mb-0">{{ $distribusiNilai['B']['persentase'] }}%</h5>
                            </div>
                            <div class="pt-2">
                                <div class="badge badge-inverse-primary mt-3">
                                    {{ $distribusiNilai['B']['jumlah'] }} siswa
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap border-bottom py-2 justify-content-between">
                            <div class="pt-2">
                                <h5 class="mb-0">70-79 (C)</h5>
                                <p class="mb-0 text-muted">Cukup</p>
                                <h5 class="mb-0">{{ $distribusiNilai['C']['persentase'] }}%</h5>
                            </div>
                            <div class="pt-2">
                                <div class="badge badge-inverse-warning mt-3">
                                    {{ $distribusiNilai['C']['jumlah'] }} siswa
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap border-bottom py-2 justify-content-between mb-3">
                            <div class="pt-2">
                                <h5 class="mb-0">0-69 (D)</h5>
                                <p class="mb-0 text-muted">Perlu Perbaikan</p>
                                <h5 class="mb-0">{{ $distribusiNilai['D']['persentase'] }}%</h5>
                            </div>
                            <div class="pt-2">
                                <div class="badge badge-inverse-danger mt-3">
                                    {{ $distribusiNilai['D']['jumlah'] }} siswa
                                </div>
                            </div>
                        </div>

                        <!-- <a class="text-black mt-3 d-block font-weight-medium h6" href="#">
                            Lihat detail per mata pelajaran
                            <i class="mdi mdi-chevron-right"></i>
                        </a> -->
                    </div>
                </div>
            </div>

            <div class="col-xl-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-black">Aktivitas Terbaru</h4>
                        <p class="text-muted">
                            Update terakhir untuk {{ $rombel ? $rombel->nama : 'kelas' }}
                        </p>
                        <div class="list-wrapper">
                            <ul class="d-flex flex-column-reverse todo-list todo-list-custom">
                                @forelse($aktivitasTerbaru as $aktivitas)
                                <li>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <span class="text-primary">{{ $aktivitas['user'] }}</span>
                                            {{ $aktivitas['action'] }}
                                        </label>
                                        <span class="list-time">{{ $aktivitas['formatted_time'] }}</span>
                                    </div>
                                </li>
                                @empty
                                <li>
                                    <div class="text-center py-3">
                                        <p class="text-muted">Belum ada aktivitas</p>
                                    </div>
                                </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Chart untuk Progress Kelas
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof Chart === "undefined") {
                console.error("Chart.js is not loaded.");
                return;
            }

            const categoryColors = [
                "#A0B9FF", // Nilai
                "#92DDCC", // Kokurikuler
                "#FFC0CB", // Kehadiran
                "#DDA0DD", // Ekstrakurikuler
            ];

            const progressDataKelas = {
                labels: @js($chartData['labels'] ?? []),
                data: @js($chartData['data'] ?? []),
            };

            function createKelasDonutChart(elementId, kelasData) {
                const ctx = document.getElementById(elementId);
                if (!ctx) return;

                new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        labels: kelasData.labels,
                        datasets: [{
                            data: kelasData.data,
                            backgroundColor: categoryColors,
                            borderColor: "#ffffff",
                            borderWidth: 2,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: "60%",
                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                enabled: true,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || "";
                                        if (label) {
                                            label += ": ";
                                        }
                                        if (context.parsed !== null) {
                                            label += context.parsed + "%";
                                        }
                                        return label;
                                    },
                                },
                            },
                        },
                    },
                });
            }

            setTimeout(() => {
                createKelasDonutChart("chart_kelas_xi_ips2", progressDataKelas);
            }, 200);
        });
    </script>
    @endpush

    <style>
        .legend-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .progress-card-hover {
            transition: all 0.3s ease;
        }

        .progress-card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .bg-light-secondary {
            background-color: #f8f9fa !important;
        }

        /* Student Avatar Style */
        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Quick Action Card Styles */
        .quick-action-card {
            transition: all 0.3s ease;
            text-align: left;
            border: none;
        }

        .quick-action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .quick-action-card h6 {
            color: white;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .quick-action-card .text-white-50 {
            font-size: 0.875rem;
        }
    </style>
</div>