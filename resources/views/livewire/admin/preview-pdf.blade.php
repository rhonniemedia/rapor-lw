<div>
    <!-- ========================= -->
    <!-- BAGIAN FILTER DATA -->
    <!-- ========================= -->
    <div class="row mb-2">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="page-header mb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <h5 class="text-dark mb-2"><i class="mdi mdi-filter me-2"></i> Filter Data</h5>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Tahun Ajaran</label>
                            <select class="form-select">
                                <option>2024/2025 (Aktif)</option>
                                <option>2023/2024</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Semester</label>
                            <select class="form-select">
                                <option>Semester Ganjil</option>
                                <option>Semester Genap</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Rombongan Belajar</label>
                            <select class="form-select">
                                <option>X IPA 1</option>
                                <option>X IPA 2</option>
                            </select>
                        </div>
                    </div>

                    <!-- Info Rombel Dummy -->
                    <div class="alert alert-success py-3 mt-4" role="alert">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-success rounded-3 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                        <i class="mdi mdi-book text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Kurikulum</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">Kurikulum Merdeka</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-success rounded-3 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                        <i class="mdi mdi-calendar-clock text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Tahun Ajaran & Semester</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">2024/2025 ~ Semester Ganjil</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-success rounded-3 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                        <i class="mdi mdi-account-group text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Rombel</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">X IPA 1</p>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-success rounded-3 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                        <i class="mdi mdi-shield-star text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Jurusan</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">Ilmu Pengetahuan Alam</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-success rounded-3 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                        <i class="mdi mdi-account-tie text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Wali Kelas</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">Budi Santoso, S.Pd</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end alert -->
                </div>
            </div>
        </div>
    </div>

    <!-- ========================= -->
    <!-- BAGIAN PREVIEW LAPORAN -->
    <!-- ========================= -->
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <!-- Header Preview -->
                    <div class="row align-items-center mb-3">
                        <div class="col-lg-6">
                            <h5 class="text-dark mb-0"><i class="mdi mdi-file-document-outline me-2"></i> Preview Laporan Hasil Belajar</h5>
                        </div>
                        <div class="col-lg-6 d-flex justify-content-end align-items-center">
                            <div class="input-group me-2" style="width: 250px;">
                                <select class="form-select form-select">
                                    <option>Halaman Depan</option>
                                    <option>Halaman Nilai</option>
                                </select>
                            </div>
                            <div class="input-group me-2" style="width: 250px;">
                                <input type="text" class="form-control" placeholder="Cari nama siswa...">
                            </div>
                            <button type="button" class="btn btn-outline-light-muted btn-sm d-flex align-items-center justify-content-center me-1" title="Previous">
                                <i class="mdi mdi-arrow-left-bold-outline text-muted fs-5"></i>
                            </button>

                            <button type="button" class="btn btn-outline-light-muted btn-sm d-flex align-items-center justify-content-center" title="Next">
                                <i class="mdi mdi-arrow-right-bold-outline text-muted fs-5"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Preview PDF -->
                    <div class="ratio ratio-16x9 border rounded shadow-sm mb-3">
                        <iframe src="https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf" title="Preview Laporan Hasil Belajar" frameborder="0"></iframe>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>