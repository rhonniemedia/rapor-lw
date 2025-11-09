<div>
    <!-- =============================== -->
    <!-- FILTER & INFO ROMBEL TERPADU -->
    <!-- =============================== -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="page-header mb-0 border-bottom pb-2">
                <div class="d-flex align-items-center">
                    <h5 class="text-dark mb-0">
                        <i class="mdi mdi-filter me-2"></i> Filter Data Kokurikuler
                    </h5>
                </div>
            </div>

            <!-- Filter -->
            <div class="row mt-3">
                <div class="col-sm-4">
                    <label class="form-label">Tahun Ajaran</label>
                    <select class="form-select">
                        <option>-- Pilih Tahun Ajaran --</option>
                        <option selected>2024/2025 (Aktif)</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <label class="form-label">Semester</label>
                    <select class="form-select">
                        <option>-- Pilih Semester --</option>
                        <option selected>Genap (Aktif)</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <label class="form-label">Rombongan Belajar</label>
                    <select class="form-select">
                        <option>-- Pilih Rombel --</option>
                        <option selected>X RPL 1</option>
                    </select>
                </div>
            </div>

            <!-- Info Rombel -->
            <div class="alert alert-success py-3 mt-4 mb-3" role="alert">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                style="width: 36px; height: 36px;">
                                <i class="mdi mdi-book text-white fs-5"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted lh-2">Kurikulum</small>
                                <p class="fw-bold mb-0 text-dark lh-sm">Kurikulum Merdeka</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                style="width: 36px; height: 36px;">
                                <i class="mdi mdi-calendar-clock text-white fs-5"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted lh-2">Tahun Ajaran & Semester</small>
                                <p class="fw-bold mb-0 text-dark lh-sm">2024/2025 ~ Genap</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                style="width: 36px; height: 36px;">
                                <i class="mdi mdi-account-group text-white fs-5"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted lh-2">Rombel</small>
                                <p class="fw-bold mb-0 text-dark lh-sm">X RPL 1</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                style="width: 36px; height: 36px;">
                                <i class="mdi mdi-shield-star text-white fs-5"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted lh-2">Jurusan</small>
                                <p class="fw-bold mb-0 text-dark lh-sm">Rekayasa Perangkat Lunak</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                                style="width: 36px; height: 36px;">
                                <i class="mdi mdi-account-tie text-white fs-5"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted lh-2">Wali Kelas</small>
                                <p class="fw-bold mb-0 text-dark lh-sm">Budi Santoso</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= -->
    <!-- PREVIEW TABEL -->
    <!-- ================= -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="mdi mdi-file-document-outline me-2"></i> Preview Laporan Hasil Belajar</h5>
                <div class="d-flex align-items-center">
                    <div class="input-group" style="width: 250px;">
                        <input type="text" class="form-control" placeholder="Cari nama siswa...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th width="35%">Nama Lengkap</th>
                            <th width="20%">Form</th>
                            <th width="40%">Capaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">1</td>
                            <td>
                                <p class="mb-0">Andi Pratama</p>
                                <small class="text-muted">1001 | 1234567890</small>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-outline-danger btn-sm d-flex align-items-center justify-content-center mx-auto">
                                    <i class="mdi mdi-file-pdf fs-5"></i>
                                </button>
                            </td>
                            <td>
                                Rajin mengikuti kegiatan pramuka dan menunjukkan tanggung jawab yang baik.
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">2</td>
                            <td>
                                <p class="mb-0">Siti Nurhaliza</p>
                                <small class="text-muted">1002 | 1234567891</small>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-outline-danger btn-sm d-flex align-items-center justify-content-center mx-auto">
                                    <i class="mdi mdi-file-pdf fs-5"></i>
                                </button>
                            </td>
                            <td>
                                Aktif dalam kegiatan sosial dan menunjukkan sikap kepemimpinan di lingkungan sekolah.
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">3</td>
                            <td>
                                <p class="mb-0">Rahman Hidayat</p>
                                <small class="text-muted">1003 | 1234567892</small>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-outline-danger btn-sm d-flex align-items-center justify-content-center mx-auto">
                                    <i class="mdi mdi-file-pdf fs-5"></i>
                                </button>
                            </td>
                            <td>
                                Disiplin dan memiliki semangat tinggi dalam mengikuti kegiatan ekstrakurikuler olahraga.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>