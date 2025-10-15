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
        <div class="d-flex">
            <button type="button" class="btn btn-labeled btn-info" onclick="Livewire.dispatch('openSyncModal')">
                <span class="btn-label"><i class="mdi mdi-note-plus"></i></span>Tambah
            </button>
        </div>
    </div>

    <!-- Master Data -->
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <!--datepicker-->
            <div class="card">
                <div class="card-body">

                    <!-- LiveWire -->
                    @livewire('master-data')

                </div>
            </div>
            <!--datepicker ends-->
        </div>
    </div>
</div>

<!-- content-wrapper ends -->
<!-- main-panel ends -->

@endsection