@extends('layouts.admin.main')

@section('container')

<!-- partial -->

<div class="content-wrapper pb-0">
    <div class="page-header flex-wrap">
        <h3 class="mb-0"> {{ $title }}
            <span class="pl-0 h6 pl-sm-2 text-muted d-inline-block">
                <!-- Sekolah -->
            </span>
        </h3>
    </div>

    <!-- Ekstrakurikuler -->
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <!--datepicker-->
            <div class="card">
                <div class="card-body">

                    <div class="row g-4">
                        <!-- LEFT -->
                        <div class="col-lg-12">
                            <div class="page-header mb-0 border-bottom">
                                <div class="d-flex align-items-center">
                                    <h5 class="text-dark"><i class="mdi mdi-database me-2"></i> Filter Data</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3 row">
                                <div class="col-sm-3">
                                    <select class="form-select" aria-label="Default select example">
                                        <option selected>-- Tahun Ajaran --</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <select class="form-select" aria-label="Default select example">
                                        <option selected>-- Semester --</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <select class="form-select" aria-label="Default select example">
                                        <option selected>-- Rombongan Belajar --</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <select class="form-select" aria-label="Default select example">
                                        <option selected>-- Mata Pelajaran --</option>
                                        <option value="1">One</option>
                                        <option value="2">Two</option>
                                        <option value="3">Three</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Rombel -->

                    <div class="alert alert-success py-2" role="alert">
                        <div class="row align-items-center">
                            <div class="col-sm-3 py-3">
                                <small class="text-muted d-block">Kurikulum</small>
                                <p class="mb-0 font-weight-bold">Kurikulum Merdeka (KM)</p>
                            </div>
                            <div class="col-sm-3 py-3">
                                <small class="text-muted d-block">Tahun Ajaran & Semester</small>
                                <p class="mb-0 font-weight-bold">2025/2026 ~ Ganjil</p>
                            </div>

                            <div class="col-sm-3 py-3">
                                <small class="text-muted d-block">Wali Kelas:</small>
                                <p class="mb-0 font-weight-bold">Fepriadi Irawan, S.Pd (X DPIB)</p>
                            </div>

                            <div class="col-sm-3 py-3">
                                <small class="text-muted d-block">Mata Pelajaran</small>
                                <p class="mb-0 font-weight-bold">Pendidikan Agama dan Budi Pekerti</p>
                            </div>
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