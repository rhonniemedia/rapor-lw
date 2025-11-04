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
                <!-- <div class="card-body"> -->

                <!-- LiveWire -->
                <!-- livewire:admin.data-rombel -->

                <!-- </div> -->

                <div class="card-body">
                    <div class="page-header pb-3 mb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper position-relative">
                                <span class="bg-gradient-primary p-2 rounded-3 shadow-sm me-3 d-inline-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-account-group mdi-24px text-white"></i>
                                </span>
                            </div>

                            <div>
                                <h4 class="mb-1 text-dark fw-bold">Manajemen Capaian</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <small class="text-muted">Kelola data Deskripsi Capaian</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Info Rombel --}}

                    <div class="alert alert-success" role="alert">
                        <div class="row align-items-center">
                            <!-- Kolom 1 -->
                            <div class="col-md-4">
                                <div class="d-flex align-items-center my-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                        style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-book text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3 d-flex flex-column justify-content-center">
                                        <small class="text-muted d-block">Kurikulum:</small>
                                        <p class="mb-0 font-weight-bold">Kurikulum Merdeka (KM)</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Kolom 2 -->
                            <div class="col-md-4">
                                <div class="d-flex align-items-center my-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                        style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-book text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3 d-flex flex-column justify-content-center">
                                        <small class="text-muted d-block">Tahun Ajaran:</small>
                                        <p class="mb-0 font-weight-bold">2025/2026</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Kolom 3 -->
                            <div class="col-md-4">
                                <div class="d-flex align-items-center my-3">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                        style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-book text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3 d-flex flex-column justify-content-center">
                                        <small class="text-muted d-block">Semester</small>
                                        <p class="mb-0 font-weight-bold">
                                            Ganjil (1)
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
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

                            {{-- Input Search & Per Page --}}
                            <div class="tab-pane fade show active" id="tab-nilai">
                                @livewire('admin.deskripsi-nilai')
                            </div>
                        </div>

                        <!-- ================== TAB KOKURIKULER ================== -->
                        <div class="tab-pane fade" id="tab-kokurikuler">
                            {{-- Input Search & Per Page --}}
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span>Show</span>
                                    <select class="form-select form-select-sm h-100" wire:model.live="perPage">
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                    </select>
                                    <span>entries</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div>
                                        <input type="text" class="form-control"
                                            placeholder=""
                                            wire:model.live.debounce.500ms="search" style="width:250px;">
                                    </div>
                                    <button type="button" wire:click="createMapel" class="btn btn-outline-light-muted btn-sm d-flex align-items-center justify-content-center h-100" data-bs-toggle="modal" data-bs-target="#modalKokurikuler" style="padding: 0 0.75rem;">
                                        <i class="mdi mdi-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Subdimensi</th>
                                        <th>Predikat</th>
                                        <th>Deskripsi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Kepedulian Sosial</td>
                                        <td><span class="badge bg-success">A</span></td>
                                        <td>Menunjukkan kepedulian tinggi terhadap lingkungan dan sesama.</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-secondary"><i class="mdi mdi-pencil"></i></button>
                                            <button class="btn btn-sm btn-outline-danger"><i class="mdi mdi-delete"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- ================== TAB EKSTRAKURIKULER ================== -->
                        <div class="tab-pane fade" id="tab-ekskul">
                            {{-- Input Search & Per Page --}}
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span>Show</span>
                                    <select class="form-select form-select-sm h-100" wire:model.live="perPage">
                                        <option value="5">5</option>
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                    </select>
                                    <span>entries</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div>
                                        <input type="text" class="form-control"
                                            placeholder=""
                                            wire:model.live.debounce.500ms="search" style="width:250px;">
                                    </div>
                                    <button type="button" wire:click="createMapel" class="btn btn-outline-light-muted btn-sm d-flex align-items-center justify-content-center h-100" data-bs-toggle="modal" data-bs-target="#modalEkskul" style="padding: 0 0.75rem;">
                                        <i class="mdi mdi-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <table class="table table-bordered table-striped align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ekstrakurikuler</th>
                                        <th>Predikat</th>
                                        <th>Deskripsi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Pramuka</td>
                                        <td><span class="badge bg-success">A</span></td>
                                        <td>Menunjukkan komitmen tinggi dalam mengikuti kegiatan.</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-secondary"><i class="mdi mdi-pencil"></i></button>
                                            <button class="btn btn-sm btn-outline-danger"><i class="mdi mdi-delete"></i></button>
                                        </td>
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

<!-- ========== MODAL TEMPLATE KOKURIKULER ========== -->
<div class="modal fade" id="modalKokurikuler" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Template Kokurikuler</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Subdimensi</label>
                            <input type="text" class="form-control form-control-sm" placeholder="Misal: Kepedulian Sosial">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Predikat</label>
                            <select class="form-select form-select-sm">
                                <option>A</option>
                                <option>B</option>
                                <option>C</option>
                                <option>D</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control form-control-sm" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========== MODAL TEMPLATE EKSKUL ========== -->
<div class="modal fade" id="modalEkskul" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Template Ekstrakurikuler</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Nama Ekstrakurikuler</label>
                            <input type="text" class="form-control form-control-sm" placeholder="Misal: Pramuka">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Predikat</label>
                            <select class="form-select form-select-sm">
                                <option>A</option>
                                <option>B</option>
                                <option>C</option>
                                <option>D</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="usePlaceholder">
                                <label class="form-check-label" for="usePlaceholder">
                                    Gunakan Placeholder
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control form-control-sm" rows="4" placeholder="Gunakan jika ingin dinamis"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- content-wrapper ends -->
<!-- main-panel ends -->

@endsection