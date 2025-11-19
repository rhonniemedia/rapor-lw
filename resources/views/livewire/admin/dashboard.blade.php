<div>
    <div class="page-header flex-wrap">
        <h3 class="mb-0">
            Dashboard E-Rapor
            <span class="pl-0 h6 pl-sm-2 text-muted d-inline-block">Sistem Manajemen Nilai Sekolah</span>
        </h3>
        <div class="d-flex">
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success">
                    Tahun Ajaran 2024/2025
                </button>
                <button type="button" class="btn btn-sm btn-success dropdown-toggle dropdown-toggle-split"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    style="width: 32px; display: flex; align-items: center; justify-content: center; padding: 0;">
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#">2023/2024</a>
                    <a class="dropdown-item" href="#">2022/2023</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="row">
        <div class="col-xl-3 col-lg-12 stretch-card grid-margin">
            <div class="row">
                <div class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 pb-sm-3">
                    <div class="card bg-danger">
                        <div class="card-body px-3 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="color-card">
                                    <p class="mb-0 color-card-head">Total Siswa</p>
                                    <h2 class="text-white">{{ number_format($totalPelajar) }}</h2>
                                </div>
                                <i class="card-icon-indicator mdi mdi-account-multiple bg-inverse-icon-danger"></i>
                            </div>
                            <h6 class="text-white">Semester Aktif</h6>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 pb-sm-3">
                    <div class="card bg-warning">
                        <div class="card-body px-3 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="color-card">
                                    <p class="mb-0 color-card-head">Total Guru</p>
                                    <h2 class="text-white">{{ number_format($totalGuru) }}</h2>
                                </div>
                                <i class="card-icon-indicator mdi mdi-teach bg-inverse-icon-warning"></i>
                            </div>
                            <h6 class="text-white">Status Aktif</h6>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 pb-sm-3 pb-lg-0 pb-xl-3">
                    <div class="card bg-success">
                        <div class="card-body px-3 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="color-card">
                                    <p class="mb-0 color-card-head">Rombel Aktif</p>
                                    <h2 class="text-white">{{ number_format($totalRombel) }}</h2>
                                </div>
                                <i class="card-icon-indicator mdi mdi-google-classroom bg-inverse-icon-success"></i>
                            </div>
                            <h6 class="text-white">{{ $totalRombelByJurusan }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-md-6 stretch-card pb-sm-3 pb-lg-0">
                    <div class="card bg-info">
                        <div class="card-body px-3 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="color-card">
                                    <p class="mb-0 color-card-head">Mata Pelajaran</p>
                                    <h2 class="text-white">{{ number_format($totalMataPelajaran) }}</h2>
                                </div>
                                <i class="card-icon-indicator mdi mdi-book-open-variant bg-inverse-icon-info"></i>
                            </div>
                            <h6 class="text-white">Kurikulum Aktif</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Input Nilai - REVISI -->
        <div class="col-xl-9 stretch-card grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-8">
                            <h5>Matriks Kelengkapan Data Final Rapor per Jenjang</h5>
                            <p class="text-muted">
                                Perbandingan proporsi kelengkapan Nilai, Kokurikuler, Kehadiran, dan Ekstrakurikuler.
                            </p>
                        </div>
                        <div class="col-sm-4 text-md-right">
                            <div class="dropdown dropleft d-block">
                                <button class="btn btn-sm btn-outline-secondary btn-icon" id="dropdownProgressAction"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                    style="width: 32px; height: 32px; line-height: 1">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownProgressAction">
                                    <a class="dropdown-item" href="#">Lihat Detail Rombel</a>
                                    <a class="dropdown-item text-danger" href="#">Kirim Peringatan Wali Kelas</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        @php
                        // Sort jenjang berdasarkan average progress
                        $sortedJenjang = collect($progressPerJenjang)->sortBy(function($data) {
                        return ($data['nilai'] + $data['kokurikuler'] + $data['kehadiran'] + $data['ekstrakurikuler']) / 4;
                        })->values();

                        $statusConfig = [
                        ['icon' => 'mdi-alpha-x', 'label' => 'Tingkat Paling Kritis', 'class' => 'danger'],
                        ['icon' => 'mdi-alpha-y', 'label' => 'Tingkat Sedang', 'class' => 'warning'],
                        ['icon' => 'mdi-alpha-z', 'label' => 'Tingkat Hampir Selesai', 'class' => 'success']
                        ];
                        @endphp

                        @foreach($sortedJenjang as $index => $data)
                        <div class="col-md-4">
                            <div class="card p-3 bg-light mapel-progress-card {{ $statusConfig[$index]['class'] }}">
                                <div class="d-flex align-items-center">
                                    <i class="mdi {{ $statusConfig[$index]['icon'] }} mdi-36px text-{{ $statusConfig[$index]['class'] }} mr-3"></i>
                                    <div>
                                        <p class="mb-0 text-muted font-weight-bold">{{ $statusConfig[$index]['label'] }}</p>
                                        <h4 class="mb-0 text-{{ $statusConfig[$index]['class'] }}">{{ $data['nama'] }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <h6 class="mt-4 mb-3 text-black">Proporsi Kelengkapan Rapor per Jenjang</h6>
                    <div class="row">
                        @foreach([10, 11, 12] as $tingkat)
                        @php
                        $data = $progressPerJenjang[$tingkat];
                        $kelasKey = strtolower(str_replace('Kelas ', '', $data['nama']));
                        @endphp
                        <div class="col-md-4 grid-margin stretch-card">
                            <div class="card p-3 mapel-progress-card">
                                <h6 class="card-title text-black mb-3 text-center">{{ $data['nama'] }}</h6>
                                <div style="height: 250px; position: relative">
                                    <canvas id="chart_kelas_{{ $kelasKey }}"></canvas>
                                </div>
                                <div class="chart-legend mt-3">
                                    <div class="legend-item">
                                        <span class="legend-color" style="background-color: #a0b9ff"></span>
                                        Nilai:
                                        <span class="legend-value font-weight-bold">{{ $data['nilai'] }}%</span>
                                    </div>
                                    <div class="legend-item">
                                        <span class="legend-color" style="background-color: #92ddcc"></span>
                                        Kokurikuler:
                                        <span class="legend-value font-weight-bold">{{ $data['kokurikuler'] }}%</span>
                                    </div>
                                    <div class="legend-item">
                                        <span class="legend-color" style="background-color: #ffc0cb"></span>
                                        Kehadiran:
                                        <span class="legend-value font-weight-bold">{{ $data['kehadiran'] }}%</span>
                                    </div>
                                    <div class="legend-item">
                                        <span class="legend-color" style="background-color: #dda0dd"></span>
                                        Ekstrakurikuler:
                                        <span class="legend-value font-weight-bold">{{ $data['ekstrakurikuler'] }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Rombel dan Nilai -->
    <div class="row">
        <div class="col-xl-8 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body px-0 overflow-auto">
                    <h4 class="card-title pl-4">Rombel dengan Progress Terendah</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-light">
                                <tr>
                                    <th>Rombel</th>
                                    <th>Wali Kelas</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rombelTerendah as $rombel)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="mdi mdi-google-classroom text-primary mr-3"></i>
                                            <div class="table-user-name ml-3">
                                                <p class="mb-0 font-weight-medium">{{ $rombel['nama'] }}</p>
                                                <small>{{ $rombel['jumlah_siswa'] }} siswa</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $rombel['wali_kelas'] }}</td>
                                    <td>
                                        <div class="progress" style="height: 6px">
                                            <div class="progress-bar bg-{{ $rombel['progress'] < 70 ? 'danger' : ($rombel['progress'] < 80 ? 'warning' : ($rombel['progress'] < 90 ? 'info' : 'success')) }}"
                                                role="progressbar" style="width: {{ $rombel['progress'] }}%"
                                                aria-valuenow="{{ $rombel['progress'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small>{{ $rombel['progress'] }}%</small>
                                    </td>
                                    <td>
                                        @if($rombel['progress'] < 70)
                                            <div class="badge badge-inverse-danger">Perlu Perhatian
                    </div>
                    @elseif($rombel['progress'] < 80)
                        <div class="badge badge-inverse-warning">Sedang
                </div>
                @elseif($rombel['progress'] < 90)
                    <div class="badge badge-inverse-info">Baik
            </div>
            @else
            <div class="badge badge-inverse-success">Sangat Baik</div>
            @endif
            </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted">Belum ada data rombel</td>
            </tr>
            @endforelse
            </tbody>
            </table>
        </div>
        <a class="text-black mt-3 d-block pl-4" href="#">
            <span class="font-weight-medium h6">Lihat semua rombel</span>
            <i class="mdi mdi-chevron-right"></i>
        </a>
    </div>
</div>
</div>
<div class="col-xl-4 col-sm-6 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <div class="card-title font-weight-medium">Distribusi Nilai Rata-rata</div>
            <p class="text-muted">Semester Aktif</p>
            @forelse($distribusiNilai as $dist)
            <div class="d-flex flex-wrap border-bottom py-2 {{ $loop->first ? 'border-top' : '' }} justify-content-between">
                <div class="pt-2">
                    <h5 class="mb-0">{{ $dist['range'] }} ({{ $dist['grade'] }})</h5>
                    <p class="mb-0 text-muted">{{ $dist['label'] }}</p>
                    <h5 class="mb-0">{{ $dist['percentage'] }}%</h5>
                </div>
                <div class="pt-2">
                    <div class="badge badge-inverse-{{ $dist['badge_class'] }} mt-3">
                        {{ $dist['count'] }} siswa
                    </div>
                </div>
            </div>
            @empty
            <p class="text-muted">Belum ada data distribusi nilai</p>
            @endforelse
            <a class="text-black mt-3 d-block font-weight-medium h6" href="#">
                Lihat detail <i class="mdi mdi-chevron-right"></i>
            </a>
        </div>
    </div>
</div>
</div>

<!-- Aktivitas Terbaru dan Kalender -->
<div class="row">
    <div class="col-xl-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-black">Aktivitas Terbaru</h4>
                <p class="text-muted">Update sistem terakhir</p>
                <div class="list-wrapper">
                    <ul class="d-flex flex-column-reverse todo-list todo-list-custom">
                        @forelse($aktivitasTerbaru as $aktivitas)
                        <li>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <span class="text-primary">{{ $aktivitas['user'] }}</span>
                                    {{ $aktivitas['action'] }}
                                </label>
                                <span class="list-time">{{ $aktivitas['time'] }}</span>
                            </div>
                        </li>
                        @empty
                        <li class="text-muted">Belum ada aktivitas</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-black">Guru dengan Input Terbanyak</h4>
                <p class="text-muted">Semester Aktif</p>
                @forelse($progressPerGuru as $guru)
                <div class="row {{ $loop->first ? 'pt-2 pb-1' : 'py-1' }}">
                    <div class="col-12 col-sm-7">
                        <div class="row">
                            <div class="col-4 col-md-4">
                                <img class="customer-img" src="{{ asset('assets/images/faces/face22.jpg') }}" alt="" />
                            </div>
                            <div class="col-8 col-md-8 p-sm-0">
                                <h6 class="mb-0">{{ $guru['nama'] }}</h6>
                                <p class="text-muted font-12">{{ $guru['mapel'] }}</p>
                                <div class="progress" style="height: 6px">
                                    <div class="progress-bar bg-{{ $guru['progress'] < 70 ? 'danger' : ($guru['progress'] < 90 ? 'warning' : 'success') }}"
                                        role="progressbar" style="width: {{ $guru['progress'] }}%"
                                        aria-valuenow="{{ $guru['progress'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5 pl-0 text-right">
                        <h4 class="mb-0">{{ $guru['progress'] }}%</h4>
                        <p class="text-muted">{{ $guru['jumlah_rombel'] }} rombel</p>
                    </div>
                </div>
                @empty
                <p class="text-muted">Belum ada data guru</p>
                @endforelse
                <a class="text-black mt-3 d-block font-weight-medium h6" href="#">
                    Lihat semua guru <i class="mdi mdi-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-black">Kalender Akademik</h4>
                <p class="text-muted pb-2">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
                <div id="inline-datepicker" class="datepicker table-responsive"></div>
                <div class="mt-3">
                    <h6 class="text-black">Jadwal Penting</h6>
                    <ul class="list-ticked">
                        @foreach($jadwalPenting as $jadwal)
                        <li>{{ $jadwal['tanggal'] }} - {{ $jadwal['kegiatan'] }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @push('style')
    <style>
        .progress-ring {
            position: relative;
            width: 80px;
            height: 80px;
        }

        .progress-ring-circle {
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }

        .progress-ring-value {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-weight: bold;
        }

        .mapel-progress-card {
            transition: all 0.3s ease;
        }

        .mapel-progress-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .mapel-progress-card.warning {
            border-left: 4px solid #ffee00;
        }

        .mapel-progress-card.danger {
            border-left: 4px solid #f44336;
        }

        .mapel-progress-card.success {
            border-left: 4px solid #4caf50;
        }

        .chart-legend {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 15px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin: 5px 10px;
            font-size: 0.85em;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
            display: inline-block;
        }
    </style>
    @endpush

    @push('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof Chart === "undefined") {
                console.error("Chart.js is not loaded. Ensure assets/vendors/chart.js/Chart.min.js is linked correctly.");
                return;
            }

            const categoryColors = [
                "#A0B9FF", // Nilai
                "#92DDCC", // Kokurikuler
                "#FFC0CB", // Kehadiran
                "#DDA0DD", // Ekstrakurikuler
            ];

            const progressDataByJenjang = {
                @foreach([10, 11, 12] as $tingkat)
                @php
                $data = $progressPerJenjang[$tingkat];
                $kelasKey = strtolower(str_replace('Kelas ', '', $data['nama']));
                @endphp 'kelas_{{ $kelasKey }}': {
                    labels: ["Nilai", "Kokurikuler", "Kehadiran", "Ekstrakurikuler"],
                    data: [{
                        {
                            $data['nilai']
                        }
                    }, {
                        {
                            $data['kokurikuler']
                        }
                    }, {
                        {
                            $data['kehadiran']
                        }
                    }, {
                        {
                            $data['ekstrakurikuler']
                        }
                    }]
                },
                @endforeach
            };

            function createJenjangDonutChart(elementId, jenjangData) {
                const ctx = document.getElementById(elementId);
                if (!ctx) return;

                new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        labels: jenjangData.labels,
                        datasets: [{
                            data: jenjangData.data,
                            backgroundColor: categoryColors,
                            borderColor: "#ffffff",
                            borderWidth: 2,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: "70%",
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

                // Update nilai di legenda HTML (yang ada di bawah)
                const legendContainer = ctx.closest(".card").querySelector(".chart-legend");
                if (legendContainer) {
                    jenjangData.labels.forEach((label, index) => {
                        const valueElement = legendContainer.querySelector(
                            `.legend-item:nth-child(${index + 1}) .legend-value`
                        );
                        if (valueElement) {
                            valueElement.textContent = jenjangData.data[index] + "%";
                        }
                    });
                }
            }

            // Inisialisasi charts setelah DOM dimuat dan Chart.js tersedia
            setTimeout(() => {
                createJenjangDonutChart("chart_kelas_x", progressDataByJenjang.kelas_x);
                createJenjangDonutChart("chart_kelas_xi", progressDataByJenjang.kelas_xi);
                createJenjangDonutChart("chart_kelas_xii", progressDataByJenjang.kelas_xii);
            }, 200);
        });

        // Initialize datepicker if jQuery exists
        if (typeof $ !== 'undefined') {
            $('#inline-datepicker').datepicker({
                todayHighlight: true,
                format: 'dd/mm/yyyy'
            });
        }
    </script>
    @endpush
</div>