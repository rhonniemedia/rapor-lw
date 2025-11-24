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

                    <div class="page-header pb-3 mb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper position-relative">
                                <span class="bg-gradient-primary p-2 rounded-3 shadow-sm me-3 d-inline-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-calendar-clock mdi-24px text-white"></i>
                                </span>
                            </div>

                            <div>
                                <h4 class="mb-1 text-dark fw-bold">Mata Pelajaran</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <small class="text-muted">Kelola data mata pelajaran dan relasi</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs mb-3">

                        <li class="nav-item">
                            <a class="nav-link active d-flex align-items-center gap-2"
                                data-bs-toggle="tab" href="#tab-mapel">
                                <i class="mdi mdi-book-open-variant"></i> Mata Pelajaran
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2"
                                data-bs-toggle="tab" href="#tab-mapel-group">
                                <i class="mdi mdi-ungroup"></i> Kelompok Mata Pelajaran
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2"
                                data-bs-toggle="tab" href="#tab-kurikulum">
                                <i class="mdi mdi-arrow-decision"></i> Relasi Kurikulum
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2"
                                data-bs-toggle="tab" href="#tab-jurusan">
                                <i class="mdi mdi-account-circle-outline"></i> Relasi Jurusan
                            </a>
                        </li>

                    </ul>

                    <div class="tab-content">

                        <!-- =============== TAB MAPEL =============== -->
                        <div class="tab-pane fade show active" id="tab-mapel">
                            <livewire:admin.data-mata-pelajaran />
                        </div>

                        <!-- =============== TAB MAPEL GROUP =============== -->
                        <div class="tab-pane fade" id="tab-mapel-group">
                            <livewire:admin.data-kelompok-mapel />
                        </div>

                        <!-- =============== TAB KURIKULUM =============== -->
                        <div class="tab-pane fade" id="tab-kurikulum">
                            <livewire:admin.data-maping-kurikulum-mapel />
                        </div>

                        <!-- =============== TAB JURUSAN =============== -->
                        <div class="tab-pane fade" id="tab-jurusan">
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