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

                    <!-- LiveWire -->



                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs mb-3" id="academicTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="kurikulum-tab" data-bs-toggle="tab" data-bs-target="#kurikulum" type="button" role="tab">
                                Kurikulum
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="tahun-tab" data-bs-toggle="tab" data-bs-target="#tahun" type="button" role="tab">
                                Tahun Ajaran
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="semester-tab" data-bs-toggle="tab" data-bs-target="#semester" type="button" role="tab">
                                Semester
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="mapping-tab" data-bs-toggle="tab" data-bs-target="#mapping" type="button" role="tab">
                                Mapping Tahun Ajaran
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Contents -->
                    <div class="tab-content" id="academicTabContent">
                        <!-- Kurikulum -->
                        <div class="tab-pane fade show active" id="kurikulum" role="tabpanel">
                            <h6 class="fw-bold mb-3">📘 Daftar Kurikulum</h6>
                            <table class="table table-bordered table-striped">
                                <thead class="table-primary">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Kurikulum</th>
                                        <th>Tahun Mulai</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Kurikulum Merdeka</td>
                                        <td>2022</td>
                                        <td><span class="badge bg-success">Aktif</span></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Kurikulum 2013</td>
                                        <td>2013</td>
                                        <td><span class="badge bg-secondary">Nonaktif</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Tahun Ajaran -->
                        <div class="tab-pane fade" id="tahun" role="tabpanel">
                            <h6 class="fw-bold mb-3">📆 Tahun Ajaran</h6>
                            <table class="table table-bordered table-striped">
                                <thead class="table-primary">
                                    <tr>
                                        <th>No</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>2024 / 2025</td>
                                        <td><span class="badge bg-success">Aktif</span></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>2023 / 2024</td>
                                        <td><span class="badge bg-secondary">Nonaktif</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Semester -->
                        <div class="tab-pane fade" id="semester" role="tabpanel">
                            <h6 class="fw-bold mb-3">🏫 Semester</h6>
                            <table class="table table-bordered table-striped">
                                <thead class="table-primary">
                                    <tr>
                                        <th>No</th>
                                        <th>Semester</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Ganjil</td>
                                        <td>2024 / 2025</td>
                                        <td><span class="badge bg-success">Aktif</span></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Genap</td>
                                        <td>2023 / 2024</td>
                                        <td><span class="badge bg-secondary">Nonaktif</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mapping Tahun Ajaran -->
                        <div class="tab-pane fade" id="mapping" role="tabpanel">
                            <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-3">
                                <h6 class="fw-bold mb-0">
                                    🔗 Mapping Tahun Ajaran
                                </h6>

                                <button type="button" class="btn btn-info btn-sm d-flex align-items-center">
                                    <i class="mdi mdi-note-plus me-1"></i> Tambah
                                </button>
                            </div>
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Semester</th>
                                        <th>Kurikulum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>2024 / 2025</td>
                                        <td>Ganjil</td>
                                        <td>Kurikulum Merdeka</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>2023 / 2024</td>
                                        <td>Genap</td>
                                        <td>Kurikulum 2013</td>
                                    </tr>
                                </tbody>
                            </table>
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