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

    <!-- Deskripsi Capaian -->
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <!--datepicker-->
            <div class="card">
                <div class="card-body">
                    <div class="page-header pb-3 mb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper position-relative">
                                <span class="bg-gradient-primary p-2 rounded-3 shadow-sm me-3 d-inline-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-account-star mdi-24px text-white"></i>
                                </span>
                            </div>

                            <div>
                                <h4 class="mb-1 text-dark fw-bold">Capaian</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <small class="text-muted">Kelola data Deskripsi Capaian</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Info Tahun Ajaran --}}
                    <div class="alert alert-success" role="alert">
                        @livewire('admin.info-tahun-ajaran')
                    </div>

                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link active d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab-nilai">
                                <i class="mdi mdi-calculator-variant"></i> Nilai
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab-kokurikuler">
                                <i class="mdi mdi-book-open-page-variant"></i> Kokurikuler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab-ekskul">
                                <i class="mdi mdi-trophy"></i> Ekstrakurikuler
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">

                        <!-- ================== TAB NILAI ================== -->
                        <div class="tab-pane fade show active" id="tab-nilai">
                            @livewire('admin.deskripsi-nilai')
                        </div>

                        <!-- ================== TAB KOKURIKULER ================== -->
                        <div class="tab-pane fade" id="tab-kokurikuler">
                            @livewire('admin.deskripsi-kokurikuler')
                        </div>

                        <!-- ================== TAB EKSTRAKURIKULER ================== -->
                        <div class="tab-pane fade" id="tab-ekskul">
                            @livewire('admin.deskripsi-ekstrakurikuler')
                        </div>

                    </div>

                </div>
            </div>
            <!--datepicker ends-->
        </div>
    </div>
</div>

<!-- content-wrapper ends -->
<!-- main-panel ends -->

@endsection