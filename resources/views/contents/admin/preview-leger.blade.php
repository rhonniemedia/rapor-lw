@extends('layouts.admin.main')

@section('container')

<!-- partial -->

<div class="content-wrapper pb-0">
    <div class="page-header flex-wrap">
        <h3 class="mb-0"> {{ $title }}
            <span class="pl-0 h6 pl-sm-2 text-muted d-inline-block">
                Pratinjau Laporan Hasil Belajar
            </span>
        </h3>

    </div>

    <!-- Ekstrakurikuler -->

    <!-- LiveWire -->
    <livewire:admin.preview-leger />

</div>

<!-- content-wrapper ends -->
<!-- main-panel ends -->

@endsection