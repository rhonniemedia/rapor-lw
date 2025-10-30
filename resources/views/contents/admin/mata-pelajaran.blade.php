@extends('layouts.admin.main')

@section('container')

<!-- partial -->

<div class="content-wrapper pb-0">
    <div class="page-header flex-wrap">
        <h3 class="mb-0"> {{ $title }}
            <span class="pl-0 h6 pl-sm-2 text-muted d-inline-block">
                Akademik
            </span>
        </h3>
    </div>

    <!-- Ekstrakurikuler -->
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <!--datepicker-->
            <div class="card">
                <div class="card-body">

                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs mb-3" id="academicTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="mapel-tab" data-bs-toggle="tab" data-bs-target="#mapel" type="button" role="tab">
                                Mata Pelajaran
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="mapelGroup-tab" data-bs-toggle="tab" data-bs-target="#mapelGroup" type="button" role="tab">
                                Kelompok Mata Pelajaran
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="kurikulum-tab" data-bs-toggle="tab" data-bs-target="#kurikulum" type="button" role="tab">
                                Relasi Kurikulum
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="jurusan-tab" data-bs-toggle="tab" data-bs-target="#jurusan" type="button" role="tab">
                                Relasi Jurusan
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Contents -->
                    <div class="tab-content" id="academicTabContent">
                        <!-- Mata Pelajaran -->
                        <div class="tab-pane fade show active" id="mapel" role="tabpanel">

                            <!-- Livewire Mata Pelajaran -->
                            <livewire:admin.data-mata-pelajaran />

                        </div>

                        <!-- Kelompok Mata Pelajaran -->
                        <div class="tab-pane fade" id="mapelGroup" role="tabpanel">

                            <!-- Livewire Kelompok Mata Pelajaran -->
                            <livewire:admin.data-kelompok-mapel />

                        </div>

                        <!-- Kurikulum -->
                        <div class="tab-pane fade" id="kurikulum" role="tabpanel">

                            <!-- Livewire Kurikulum -->
                            <livewire:admin.data-maping-kurikulum-mapel />

                        </div>

                        <!-- Jurusan -->
                        <div class="tab-pane fade" id="jurusan" role="tabpanel">

                            <!-- Livewire Jurusan -->
                            <livewire:admin.data-maping-jurusan-mapel />


                        </div>
                    </div>

                    <!-- LiveWire -->

                </div>
            </div>
            <!--datepicker ends-->
        </div>
    </div>
</div>

<!-- content-wrapper ends -->
<!-- main-panel ends -->

@endsection