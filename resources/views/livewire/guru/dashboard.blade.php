<div>
    @push('styles')
    <style>
        .legend-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
    </style>
    @endpush

    <div class="content-wrapper pb-0">
        {{-- Header --}}
        <div class="page-header flex-wrap">
            <h3 class="mb-0">
                Selamat datang <strong>{{ $guru->name ?? 'Guru' }}!</strong>
                <span class="pl-0 h6 pl-sm-2 text-muted d-inline-block">
                    {{ $guru->mata_pelajaran_utama ?? 'Guru' }}
                </span>
            </h3>
            <div class="d-flex">
                @if($tahunAjaranSemester)
                <button type="button" class="btn btn-sm btn-success">
                    {{ $tahunAjaranSemester->tahunAjaran->nama ?? '' }} ~
                    {{ $tahunAjaranSemester->semester->nama ?? '' }}
                </button>
                @endif
            </div>
        </div>

        @if(!$tahunAjaranSemester)
        <div class="alert alert-warning">
            <i class="mdi mdi-alert"></i> Belum ada tahun ajaran semester yang aktif. Silakan hubungi admin.
        </div>
        @else
        {{-- Statistik Cards --}}
        <div class="row">
            <div class="col-xl-3 col-lg-12 stretch-card grid-margin">
                <div class="row h-100">
                    {{-- Kelas yang Diampu --}}
                    <div class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 mb-3">
                        <div class="card bg-primary h-100">
                            <div class="card-body px-3 py-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="color-card">
                                        <p class="mb-0 color-card-head">Kelas yang Diampu</p>
                                        <h2 class="text-white">{{ $jumlahKelas }}</h2>
                                    </div>
                                    <i class="card-icon-indicator mdi mdi-google-classroom bg-inverse-icon-primary"></i>
                                </div>
                                <h6 class="text-white mb-0">{{ $totalMapel }} Mata Pelajaran</h6>
                            </div>
                        </div>
                    </div>

                    {{-- Total Siswa --}}
                    <div class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 mb-3">
                        <div class="card bg-info h-100">
                            <div class="card-body px-3 py-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="color-card">
                                        <p class="mb-0 color-card-head">Total Siswa</p>
                                        <h2 class="text-white">{{ $totalSiswa }}</h2>
                                    </div>
                                    <i class="card-icon-indicator mdi mdi-account-multiple bg-inverse-icon-info"></i>
                                </div>
                                <h6 class="text-white mb-0">Semua kelas yang diampu</h6>
                            </div>
                        </div>
                    </div>

                    {{-- Progress Input Nilai --}}
                    <div class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 mb-3">
                        <div class="card bg-success h-100">
                            <div class="card-body px-3 py-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="color-card">
                                        <p class="mb-0 color-card-head">Progress Input Nilai</p>
                                        <h2 class="text-white">{{ $progressInputNilai }}%</h2>
                                    </div>
                                    <i class="card-icon-indicator mdi mdi-chart-line bg-inverse-icon-success"></i>
                                </div>
                                <h6 class="text-white mb-0">Input nilai siswa</h6>
                            </div>
                        </div>
                    </div>

                    {{-- Rata-rata Kelas --}}
                    <div class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 mb-3">
                        <div class="card bg-warning h-100">
                            <div class="card-body px-3 py-4">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="color-card">
                                        <p class="mb-0 color-card-head">Rata-rata Kelas</p>
                                        <h2 class="text-white">{{ $rataRataKelas }}</h2>
                                    </div>
                                    <i class="card-icon-indicator mdi mdi-star bg-inverse-icon-warning"></i>
                                </div>
                                <h6 class="text-white mb-0">Nilai rata-rata keseluruhan</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chart dan Progress --}}
            <div class="col-xl-9 stretch-card grid-margin">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            {{-- Distribusi Nilai --}}
                            <div class="col-xl-4 border-right">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="card-title text-black mb-0">Distribusi Nilai</h4>
                                    <button wire:click="refreshData" class="btn btn-sm btn-outline-primary" wire:loading.attr="disabled">
                                        <i class="mdi mdi-refresh" wire:loading.class="mdi-spin"></i>
                                    </button>
                                </div>
                                <p class="text-muted mb-4">Berdasarkan semua kelas</p>
                                <canvas id="chart_distribusi_nilai" height="200" wire:ignore></canvas>
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span><span class="legend-dot" style="background-color: #4CAF50;"></span> A (90-100)</span>
                                        <strong>{{ $distribusiNilai['A']['jumlah'] }} ({{ $distribusiNilai['A']['persentase'] }}%)</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span><span class="legend-dot" style="background-color: #2196F3;"></span> B (80-89)</span>
                                        <strong>{{ $distribusiNilai['B']['jumlah'] }} ({{ $distribusiNilai['B']['persentase'] }}%)</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span><span class="legend-dot" style="background-color: #FFC107;"></span> C (70-79)</span>
                                        <strong>{{ $distribusiNilai['C']['jumlah'] }} ({{ $distribusiNilai['C']['persentase'] }}%)</strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span><span class="legend-dot" style="background-color: #F44336;"></span> D (0-69)</span>
                                        <strong>{{ $distribusiNilai['D']['jumlah'] }} ({{ $distribusiNilai['D']['persentase'] }}%)</strong>
                                    </div>
                                </div>
                            </div>

                            {{-- Progress per Kelas --}}
                            <div class="col-xl-8">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="card-title text-black mb-0">Progress Input Nilai per Kelas</h4>
                                    <a href="#" class="btn btn-sm btn-primary">Input Nilai</a>
                                </div>
                                <p class="text-muted mb-4">Status input nilai untuk setiap kelas</p>

                                @forelse($progressPerKelas as $kelas)
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center">
                                            <h5 class="mb-0 font-weight-medium text-dark">{{ $kelas['nama'] }}</h5>
                                            <p class="mb-0 ml-2 text-muted">{{ $kelas['nilai_selesai'] }}/{{ $kelas['total_siswa'] }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="mb-0 text-dark font-weight-medium">{{ $kelas['progress_persen'] }}%</p>
                                            <small class="text-muted">Rata-rata: {{ $kelas['rata_rata'] }}</small>
                                        </div>
                                    </div>
                                    <div class="progress progress-md">
                                        <div class="progress-bar bg-{{ $kelas['warna'] }}"
                                            style="width: {{ $kelas['progress_persen'] }}%"
                                            role="progressbar"></div>
                                    </div>
                                </div>
                                @if(!$loop->last)
                                <hr class="my-3">
                                @endif
                                @empty
                                <div class="alert alert-info">
                                    <i class="mdi mdi-information"></i> Belum ada data progress input nilai
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Siswa Perlu Perhatian --}}
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="card-title text-black mb-0">Siswa Perlu Perhatian</h4>
                                <p class="text-muted mb-0">Siswa dengan nilai di bawah KKM (70)</p>
                            </div>
                            <a href="#" class="btn btn-sm btn-outline-primary">
                                Lihat Semua
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Nilai</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswaPerluPerhatian as $siswa)
                                    <tr>
                                        <td>{{ $siswa['siswa_nis'] }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $siswa['siswa_foto'] ? asset('storage/' . $siswa['siswa_foto']) : asset('assets/images/faces/face1.jpg') }}"
                                                    alt="{{ $siswa['siswa_nama'] }}"
                                                    class="rounded-circle mr-2"
                                                    style="width: 32px; height: 32px;">
                                                <div>
                                                    <h6 class="mb-0">{{ $siswa['siswa_nama'] }}</h6>
                                                    <small class="text-muted">Nilai rendah</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $siswa['kelas_nama'] }}</td>
                                        <td>{{ $siswa['mata_pelajaran'] }}</td>
                                        <td><strong class="text-danger">{{ $siswa['nilai_akhir'] }}</strong></td>
                                        <td>
                                            <div class="badge badge-inverse-danger">{{ $siswa['kategori'] }}</div>
                                        </td>
                                        <td>
                                            <button wire:click="openCatatanModal({{ $siswa['siswa_id'] }})"
                                                class="btn btn-sm btn-outline-primary">
                                                Input Catatan
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="alert alert-success mb-0">
                                                <i class="mdi mdi-check-circle"></i> Tidak ada siswa yang perlu perhatian khusus
                                            </div>
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

        {{-- Aktivitas dan Jadwal --}}
        <div class="row">
            {{-- Aktivitas Terbaru --}}
            <div class="col-xl-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-black">Aktivitas Terbaru</h4>
                        <p class="text-muted">Input nilai dan aktivitas mengajar terakhir</p>
                        <div class="list-wrapper">
                            <ul class="d-flex flex-column-reverse todo-list todo-list-custom">
                                @forelse($aktivitasTerbaru as $aktivitas)
                                <li>
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            {{ $aktivitas['deskripsi'] }}
                                        </label>
                                        <span class="list-time">{{ $aktivitas['waktu'] }}</span>
                                    </div>
                                </li>
                                @empty
                                <li>
                                    <div class="text-muted text-center py-3">
                                        Belum ada aktivitas
                                    </div>
                                </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Jadwal Mengajar Besok --}}
            <div class="col-xl-6 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-black">Jadwal Mengajar Besok</h4>
                        <p class="text-muted">{{ \Carbon\Carbon::tomorrow()->isoFormat('dddd, D MMMM YYYY') }}</p>
                        <div class="list-wrapper">
                            <div class="d-flex flex-column">
                                @forelse($jadwalBesok as $jadwal)
                                <div class="card bg-light-{{ $jadwal['warna'] ?? 'primary' }} p-3 mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $jadwal['kelas_nama'] }}</h6>
                                            <p class="mb-0 text-muted">{{ $jadwal['jam_mulai'] }} - {{ $jadwal['jam_selesai'] }}</p>
                                        </div>
                                        <span class="badge badge-{{ $jadwal['warna'] ?? 'primary' }}">
                                            {{ $jadwal['materi'] }}
                                        </span>
                                    </div>
                                </div>
                                @empty
                                <div class="alert alert-info mb-0">
                                    <i class="mdi mdi-information"></i> Tidak ada jadwal mengajar besok
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:navigated', function() {
            initChart();
        });

        document.addEventListener('DOMContentLoaded', function() {
            initChart();
        });

        Livewire.on('data-refreshed', () => {
            setTimeout(() => initChart(), 100);
        });

        let chart = null;

        function initChart() {
            const ctx = document.getElementById('chart_distribusi_nilai');
            if (ctx) {
                if (chart) {
                    chart.destroy();
                }

                chart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['A (90-100)', 'B (80-89)', 'C (70-79)', 'D (0-69)'],
                        datasets: [{
                            data: [{
                                    {
                                        $distribusiNilai['A']['jumlah']
                                    }
                                },
                                {
                                    {
                                        $distribusiNilai['B']['jumlah']
                                    }
                                },
                                {
                                    {
                                        $distribusiNilai['C']['jumlah']
                                    }
                                },
                                {
                                    {
                                        $distribusiNilai['D']['jumlah']
                                    }
                                }
                            ],
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
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        let value = context.parsed || 0;
                                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        let percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return label + ': ' + value + ' siswa (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
    </script>
    @endpush
</div>