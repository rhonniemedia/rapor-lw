@extends('layouts.admin.main')

@section('container')

<!-- partial -->

<div class="content-wrapper pb-0">
    <div class="page-header flex-wrap">
        <h3 class="mb-0"> {{ $title }}
            <span class="pl-0 h6 pl-sm-2 text-muted d-inline-block">
                Detil
            </span>
        </h3>
        <div class="d-flex">
            <form action="{{ route('admin.rombel.list') }}" method="get" style="display:inline">
                <button type="submit" class="btn btn-labeled btn-success">
                    <span class="btn-label"><i class="mdi mdi-arrow-left-bold"></i></span>Kembali
                </button>
            </form>
        </div>
    </div>

    <!-- Ekstrakurikuler -->
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <!--datepicker-->
            <div class="card">
                <div class="card-body">

                    <!-- LiveWire -->
                    <livewire:admin.data-rombel-pelajar :rombel-id="$rombelId" />

                </div>
            </div>
            <!--datepicker ends-->
        </div>
    </div>
</div>

<!-- content-wrapper ends -->
<!-- main-panel ends -->

@endsection