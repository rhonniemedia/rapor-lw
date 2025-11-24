@extends('layouts.admin.main')

@section('container')

<!-- partial -->

<div class="content-wrapper pb-0">
    <div class="page-header flex-wrap">
        <h3 class="mb-0"> Konsentrasi Keahlian
            <span class="pl-0 h6 pl-sm-2 text-muted d-inline-block">
                Sekolah
            </span>
        </h3>
        <div class="d-flex">
            <button type="button" class="btn btn-labeled btn-info" onclick="Livewire.dispatch('createJurusan')">
                <span class="btn-label"><i class="mdi mdi-note-plus"></i></span>Tambah
            </button>
        </div>
    </div>

    <!-- Jurusan -->
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <!--datepicker-->
            <div class="card">
                <div class="card-body">
                    <div class="page-header pb-3 mb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper position-relative">
                                <span class="bg-gradient-primary p-2 rounded-3 shadow-sm me-3 d-inline-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-account-tie mdi-24px text-white"></i>
                                </span>
                            </div>

                            <div>
                                <h4 class="mb-1 text-dark fw-bold">Konsentrasi Keahlian</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <small class="text-muted">Manajemen data konsentrasi keahlian (Jurusan)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- LiveWire -->
                    <livewire:admin.data-jurusan />

                </div>
            </div>
            <!--datepicker ends-->
        </div>
    </div>
</div>

<!-- content-wrapper ends -->
<!-- main-panel ends -->

@endsection