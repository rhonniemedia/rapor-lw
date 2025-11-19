<div>
    <div class="page-header flex-wrap">
        <h3 class="mb-0">
            Dashboard E-Rapor
            <span class="pl-0 h6 pl-sm-2 text-muted d-inline-block">Sistem Manajemen Nilai Sekolah</span>
        </h3>
        <div class="d-flex">
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success">
                    Tahun Ajaran Aktif
                </button>
            </div>
        </div>
    </div>

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
                            <h6 class="text-white">Siswa Aktif</h6>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 pb-sm-3">
                    <div class="card bg-warning">
                        <div class="card-body px-3 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="color-card">
                                    <p class="mb-0 color-card-head">Total Guru</p>
                                    <h2 class="text-white">{{ $totalGuru }}</h2>
                                </div>
                                <i class="card-icon-indicator mdi mdi-teach bg-inverse-icon-warning"></i>
                            </div>
                            <h6 class="text-white">Guru Pengajar</h6>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 pb-sm-3 pb-lg-0 pb-xl-3">
                    <div class="card bg-success">
                        <div class="card-body px-3 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="color-card">
                                    <p class="mb-0 color-card-head">Rombel Aktif</p>
                                    <h2 class="text-white">{{ $totalRombel }}</h2>
                                </div>
                                <i class="card-icon-indicator mdi mdi-google-classroom bg-inverse-icon-success"></i>
                            </div>
                            <h6 class="text-white font-weight-normal" style="font-size: 0.8rem;">{{ Str::limit($totalRombelByJurusan, 30) }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-md-6 stretch-card pb-sm-3 pb-lg-0">
                    <div class="card bg-info">
                        <div class="card-body px-3 py-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="color-card">
                                    <p class="mb-0 color-card-head">Mata Pelajaran</p>
                                    <h2 class="text-white">{{ $totalMataPelajaran }}</h2>
                                </div>
                                <i class="card-icon-indicator mdi mdi-book-open-variant bg-inverse-icon-info"></i>
                            </div>
                            <h6 class="text-white">Mapel Aktif</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                    </div>

                    <div class="row mb-4">
                        @foreach([10 => ['class' => 'danger', 'icon' => 'x', 'label' => 'Kelas X'], 
                                  11 => ['class' => 'warning', 'icon' => 'y', 'label' => 'Kelas XI'], 
                                  12 => ['class' => 'success', 'icon' => 'z', 'label' => 'Kelas XII']] as $key => $meta)
                            <div class="col-md-4">
                                <div class="card p-3 bg-light mapel-progress-card {{ $meta['class'] }}">
                                    <div class="d-flex align-items-center">
                                        <i class="mdi mdi-alpha-{{ $meta['icon'] }} mdi-36px text-{{ $meta['class'] }} mr-3"></i>
                                        <div>
                                            <p class="mb-0 text-muted font-weight-bold">{{ $meta['label'] }}</p>
                                            {{-- Mengambil rata-rata progress total jenjang secara kasar untuk display --}}
                                            @php
                                                $pData = $progressPerJenjang[$key] ?? ['nilai'=>0, 'kokurikuler'=>0, 'kehadiran'=>0, 'ekstrakurikuler'=>0];
                                                $avg = round(($pData['nilai'] + $pData['kokurikuler'] + $pData['kehadiran'] + $pData['ekstrakurikuler']) / 4);
                                            @endphp
                                            <h4 class="mb-0 text-{{ $meta['class'] }}">{{ $avg }}% Selesai</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <h6 class="mt-4 mb-3 text-black">Proporsi Kelengkapan Rapor per Jenjang</h6>
                    <div class="row">
                        @foreach([10 => 'chart_kelas_x', 11 => 'chart_kelas_xi', 12 => 'chart_kelas_xii'] as $jenjangId => $chartId)
                        <div class="col-md-4 grid-margin stretch-card">
                            <div class="card p-3 mapel-progress-card">
                                <h6 class="card-title text-black mb-3 text-center">
                                    Kelas {{ $jenjangId == 10 ? 'X' : ($jenjangId == 11 ? 'XI' : 'XII') }}
                                </h6>
                                <div style="height: 250px; position: relative">
                                    <canvas id="{{ $chartId }}"></canvas>
                                </div>
                                <div class="chart-legend mt-3">
                                    <div class="legend-item"><span class="legend-color" style="background-color: #a0b9ff"></span>Nilai: <span class="legend-value font-weight-bold">0%</span></div>
                                    <div class="legend-item"><span class="legend-color" style="background-color: #92ddcc"></span>Kokurikuler: <span class="legend-value font-weight-bold">0%</span></div>
                                    <div class="legend-item"><span class="legend-color" style="background-color: #ffc0cb"></span>Kehadiran: <span class="legend-value font-weight-bold">0%</span></div>
                                    <div class="legend-item"><span class="legend-color" style="background-color: #dda0dd"></span>Ekskul: <span class="legend-value font-weight-bold">0%</span></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                                            <div class="progress-bar bg-{{ $rombel['progress'] < 50 ? 'danger' : ($rombel['progress'] < 80 ? 'warning' : 'success') }}"
                                                 role="progressbar"
                                                 style="width: {{ $rombel['progress'] }}%"
                                                 aria-valuenow="{{ $rombel['progress'] }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100"></div>
                                        </div>
                                        <small>{{ $rombel['progress'] }}%</small>
                                    </td>
                                    <td>
                                        <div class="badge badge-inverse-{{ $rombel['progress'] < 50 ? 'danger' : ($rombel['progress'] < 80 ? 'warning' : 'success') }}">
                                            {{ $rombel['progress'] < 50 ? 'Perlu Perhatian' : ($rombel['progress'] < 80 ? 'Sedang' : 'Baik') }}
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Data rombel belum tersedia.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-title font-weight-medium">Distribusi Nilai Rata-rata</div>
                    <p class="text-muted">Semester Aktif</p>
                    
                    @forelse($distribusiNilai as $nilai)
                    <div class="d-flex flex-wrap border-bottom py-2 {{ $loop->first ? 'border-top' : '' }} justify-content-between">
                        <div class="pt-2">
                            <h5 class="mb-0">{{ $nilai['range'] }} ({{ $nilai['grade'] }})</h5>
                            <p class="mb-0 text-muted">{{ $nilai['label'] }}</p>
                            <h5 class="mb-0">{{ $nilai['percentage'] }}%</h5>
                        </div>
                        <div class="pt-2">
                            <div class="badge badge-inverse-{{ $nilai['badge_class'] }} mt-3">
                                {{ $nilai['count'] }} siswa
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">Belum ada data nilai masuk.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-black">Aktivitas Terbaru</h4>
                    <p class="text-muted">Input nilai & data terakhir</p>
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
                            <li class="text-center text-muted">Belum ada aktivitas tercatat.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-black">Progress Guru Tertinggi</h4>
                    <p class="text-muted">Semester Aktif</p>
                    
                    @forelse($progressPerGuru as $guru)
                    <div class="row py-2 border-bottom">
                        <div class="col-sm-7">
                            <div class="row">
                                <div class="col-4 col-sm-4">
                                    {{-- Gambar statis karena tidak ada data avatar di komponen --}}
                                    <img class="customer-img" src="{{ asset('assets/images/faces/face'.rand(1,8).'.jpg') }}" alt="" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($guru['nama']) }}'" />
                                </div>
                                <div class="col-8 col-sm-8 p-sm-0">
                                    <h6 class="mb-0 text-truncate">{{ $guru['nama'] }}</h6>
                                    <p class="text-muted font-12">{{ Str::limit($guru['mapel'], 20) }}</p>
                                    <div class="progress" style="height: 6px">
                                        <div class="progress-bar bg-{{ $guru['progress'] >= 90 ? 'success' : ($guru['progress'] >= 70 ? 'info' : 'warning') }}"
                                             role="progressbar"
                                             style="width: {{ $guru['progress'] }}%"
                                             aria-valuenow="{{ $guru['progress'] }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100"></div>
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
                    <div class="text-center py-4">Belum ada data guru.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-black">Countdown Rapor</h4>
                    <p class="text-muted pb-2">Deadline: {{ $deadline ? \Carbon\Carbon::parse($deadline)->format('d M Y') : 'Belum ditentukan' }}</p>
                    
                    <div class="d-flex justify-content-around text-center py-3 bg-light rounded">
                        <div>
                            <h3 class="font-weight-bold text-primary" id="days">0</h3>
                            <small>Hari</small>
                        </div>
                        <div>
                            <h3 class="font-weight-bold text-primary" id="hours">0</h3>
                            <small>Jam</small>
                        </div>
                        <div>
                            <h3 class="font-weight-bold text-primary" id="minutes">0</h3>
                            <small>Menit</small>
                        </div>
                        <div>
                            <h3 class="font-weight-bold text-primary" id="seconds">0</h3>
                            <small>Detik</small>
                        </div>
                    </div>

                    <div class="mt-4">
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
    </div>

    <style>
        .progress-ring { position: relative; width: 80px; height: 80px; }
        .progress-ring-circle { transform: rotate(-90deg); transform-origin: 50% 50%; }
        .progress-ring-value { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: bold; }
        .mapel-progress-card { transition: all 0.3s ease; }
        .mapel-progress-card:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1); }
        .mapel-progress-card.warning { border-left: 4px solid #ffee00; }
        .mapel-progress-card.danger { border-left: 4px solid #f44336; }
        .mapel-progress-card.success { border-left: 4px solid #4caf50; }
        .chart-legend { display: flex; flex-wrap: wrap; justify-content: center; margin-top: 15px; }
        .legend-item { display: flex; align-items: center; margin: 5px 10px; font-size: 0.85em; }
        .legend-color { width: 12px; height: 12px; border-radius: 50%; margin-right: 8px; display: inline-block; }
    </style>

    <script>
        // Dynamic Countdown Timer
        function updateCountdown() {
            // Ambil deadline dari PHP
            const deadlineStr = "{{ $deadline ? $deadline : now()->addDays(7)->toDateTimeString() }}";
            const deadline = new Date(deadlineStr).getTime();
            const now = new Date().getTime();
            const timeLeft = deadline - now;

            if (timeLeft > 0) {
                const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                document.getElementById("days").textContent = days;
                document.getElementById("hours").textContent = hours;
                document.getElementById("minutes").textContent = minutes;
                document.getElementById("seconds").textContent = seconds;
            } else {
                document.getElementById("days").textContent = "0";
                document.getElementById("hours").textContent = "0";
                document.getElementById("minutes").textContent = "0";
                document.getElementById("seconds").textContent = "0";
            }
        }
        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof Chart === "undefined") {
                console.error("Chart.js is not loaded.");
                return;
            }

            const categoryColors = ["#A0B9FF", "#92DDCC", "#FFC0CB", "#DDA0DD"];
            
            // Menerima data array dari Livewire PHP
            const phpData = @json($progressPerJenjang);

            function createJenjangDonutChart(elementId, jenjangKey) {
                const ctx = document.getElementById(elementId);
                if (!ctx) return;

                // Ambil data spesifik jenjang (10, 11, atau 12)
                const dataJenjang = phpData[jenjangKey] || { nilai: 0, kokurikuler: 0, kehadiran: 0, ekstrakurikuler: 0 };

                const labels = ["Nilai", "Kokurikuler", "Kehadiran", "Ekstrakurikuler"];
                const values = [
                    dataJenjang.nilai, 
                    dataJenjang.kokurikuler, 
                    dataJenjang.kehadiran, 
                    dataJenjang.ekstrakurikuler
                ];

                new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
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
                            legend: { display: false },
                            tooltip: {
                                enabled: true,
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ": " + context.parsed + "%";
                                    },
                                },
                            },
                        },
                    },
                });

                // Update Legend HTML
                const legendContainer = ctx.closest(".card").querySelector(".chart-legend");
                if (legendContainer) {
                    values.forEach((val, index) => {
                        const valueElement = legendContainer.querySelector(`.legend-item:nth-child(${index + 1}) .legend-value`);
                        if (valueElement) valueElement.textContent = val + "%";
                    });
                }
            }

            // Render Chart
            setTimeout(() => {
                createJenjangDonutChart("chart_kelas_x", 10);
                createJenjangDonutChart("chart_kelas_xi", 11);
                createJenjangDonutChart("chart_kelas_xii", 12);
            }, 200);
        });
    </script>
</div>