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

    <!-- Akademik -->
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <!--datepicker-->
            <div class="card">
                <div class="card-body">

                    <!-- LiveWire -->



                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs mb-3" id="academicTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="tahun-tab" data-bs-toggle="tab" data-bs-target="#tahun" type="button" role="tab">
                                Tahun Ajaran
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="kurikulum-tab" data-bs-toggle="tab" data-bs-target="#kurikulum" type="button" role="tab">
                                Kurikulum
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="semester-tab" data-bs-toggle="tab" data-bs-target="#semester" type="button" role="tab">
                                Semester
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Contents -->
                    <div class="tab-content" id="academicTabContent">
                        <!-- Tahun Ajaran -->
                        <div class="tab-pane fade show active" id="tahun" role="tabpanel">

                            <!-- Livewire Tahun Ajaran -->
                            <livewire:admin.data-tahun-ajaran />

                        </div>

                        <!-- Kurikulum -->
                        <div class="tab-pane fade" id="kurikulum" role="tabpanel">

                            <!-- Livewire Kurikulum -->
                            <livewire:admin.data-kurikulum />

                            <!-- Livewire Kurikulum -->
                            <livewire:admin.data-maping-kurikulum />

                        </div>

                        <!-- Semester -->
                        <div class="tab-pane fade" id="semester" role="tabpanel">

                            <!-- Livewire Semester -->
                            <livewire:admin.data-semester />

                            <!-- Livewire Semester -->
                            <livewire:admin.data-maping-semester />



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