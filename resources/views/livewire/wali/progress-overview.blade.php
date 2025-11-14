@props([
'className',
'academicProgress',
'academicIncomplete',
'kokurikulerProgress',
'kehadiranProgress',
'kehadiranAttention',
'ekskulProgress',
'ekskulIncomplete'
])

@php
// Menghitung Rata-rata Progress Keseluruhan (Overall Progress)
$overallProgress = round(($academicProgress + $kokurikulerProgress + $kehadiranProgress + $ekskulProgress) / 4);
@endphp

<div class="card h-100">
    <div class="card-body d-flex flex-column">

        {{-- Header Progress Bar --}}
        <div class="row mb-4">
            <div class="col-sm-12">
                <h5>Progress Kelengkapan Data Kelas {{ $className }}</h5>
                <p class="text-muted mb-2">
                    Status kelengkapan data untuk semester ganjil 2024/2025
                </p>
                <div class="d-flex align-items-center">
                    <div class="progress flex-grow-1 mr-3" style="height: 12px">
                        <div
                            class="progress-bar bg-success"
                            role="progressbar"
                            style="width: {{ $overallProgress }}%"
                            aria-valuenow="{{ $overallProgress }}"
                            aria-valuemin="0"
                            aria-valuemax="100">
                        </div>
                    </div>
                    <span class="font-weight-bold text-success">{{ $overallProgress }}%</span>
                </div>
            </div>
        </div>

        {{-- Konten Utama (Kartu dan Chart) --}}
        <div class="row flex-grow-1">
            {{-- Kolom Kiri: Kartu Progress --}}
            <div class="col-md-5">
                <div class="d-flex flex-column h-100 justify-content-between">

                    {{-- Kartu 1: Nilai Akademik --}}
                    <div class="card bg-light-secondary p-3 mb-2 progress-card-hover" style="min-height: 100px">
                        <div class="d-flex align-items-center h-100">
                            <i class="mdi mdi-book-open-page-variant mdi-24px text-secondary mr-3"></i>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ $academicProgress }}%</h5>
                                <p class="mb-0 text-muted">Nilai Akademik</p>
                            </div>
                            <small class="text-secondary">{{ $academicIncomplete }} mapel belum lengkap</small>
                        </div>
                    </div>

                    {{-- Kartu 2: Kokurikuler --}}
                    <div class="card bg-light-secondary p-3 mb-2 progress-card-hover" style="min-height: 100px">
                        <div class="d-flex align-items-center h-100">
                            <i class="mdi mdi-account-group mdi-24px text-secondary mr-3"></i>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ $kokurikulerProgress }}%</h5>
                                <p class="mb-0 text-muted">Kokurikuler</p>
                            </div>
                            <small class="text-secondary">@if($kokurikulerProgress == 100) Selesai @else Hampir selesai @endif</small>
                        </div>
                    </div>

                    {{-- Kartu 3: Kehadiran --}}
                    <div class="card bg-light-secondary p-3 mb-2 progress-card-hover" style="min-height: 100px">
                        <div class="d-flex align-items-center h-100">
                            <i class="mdi mdi-calendar-check mdi-24px text-secondary mr-3"></i>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ $kehadiranProgress }}%</h5>
                                <p class="mb-0 text-muted">Kehadiran</p>
                            </div>
                            <small class="text-secondary">{{ $kehadiranAttention }} siswa perlu perhatian</small>
                        </div>
                    </div>

                    {{-- Kartu 4: Ekstrakurikuler --}}
                    <div class="card bg-light-secondary p-3 progress-card-hover" style="min-height: 100px">
                        <div class="d-flex align-items-center h-100">
                            <i class="mdi mdi-soccer mdi-24px text-secondary mr-3"></i>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ $ekskulProgress }}%</h5>
                                <p class="mb-0 text-muted">Ekstrakurikuler</p>
                            </div>
                            <small class="text-secondary">{{ $ekskulIncomplete }} siswa belum lengkap</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Chart --}}
            <div class="col-md-7">
                <div class="d-flex flex-column h-100">
                    <div class="d-flex align-items-center justify-content-center flex-grow-1">
                        <div style="height: 300px; position: relative; width: 100%;">
                            {{-- Target untuk Chart JS dari Komponen Livewire --}}
                            <canvas id="chart_kelas_xi_ips2"></canvas>
                        </div>
                    </div>
                    <div class="chart-legend mt-3">
                        <div class="legend-item">
                            <span class="legend-color" style="background-color: #a0b9ff"></span>
                            Nilai: <span class="legend-value font-weight-bold">{{ $academicProgress }}%</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background-color: #92ddcc"></span>
                            Kokurikuler: <span class="legend-value font-weight-bold">{{ $kokurikulerProgress }}%</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background-color: #ffc0cb"></span>
                            Kehadiran: <span class="legend-value font-weight-bold">{{ $kehadiranProgress }}%</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-color" style="background-color: #dda0dd"></span>
                            Ekstrakurikuler: <span class="legend-value font-weight-bold">{{ $ekskulProgress }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>