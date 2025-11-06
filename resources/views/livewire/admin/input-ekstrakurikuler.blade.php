<div>
    {{-- Filter Data Ekstrakurikuler --}}
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    {{-- Header Filter --}}
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="page-header mb-0 border-bottom">
                                <div class="d-flex align-items-center">
                                    <h5 class="text-dark"><i class="mdi mdi-filter me-2"></i> Filter Data Ekstrakurikuler</h5>
                                </div>
                            </div>
                        </div>

                        {{-- Form Filter --}}
                        <div class="col-lg-12">
                            <div class="mb-3 row">
                                <div class="col-sm-3">
                                    <label class="form-label">Tahun Ajaran</label>
                                    <select class="form-select">
                                        <option value="">-- Pilih Tahun Ajaran --</option>
                                        <option>2024/2025 (Aktif)</option>
                                    </select>
                                </div>

                                <div class="col-sm-3">
                                    <label class="form-label">Semester</label>
                                    <select class="form-select">
                                        <option value="">-- Pilih Semester --</option>
                                        <option>Semester Ganjil (Aktif)</option>
                                    </select>
                                </div>

                                <div class="col-sm-3">
                                    <label class="form-label">Rombongan Belajar</label>
                                    <select class="form-select">
                                        <option value="">-- Pilih Rombel --</option>
                                        <option>X IPA 1</option>
                                    </select>
                                </div>

                                <div class="col-sm-3">
                                    <label class="form-label">Ekstrakurikuler</label>
                                    <select class="form-select">
                                        <option value="">-- Pilih Ekstrakurikuler --</option>
                                        <option>Pramuka</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Info Rombel & Ekstrakurikuler --}}
                    <div class="alert alert-success py-3 mt-3" role="alert">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 bg-success rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-book text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Kurikulum</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">Kurikulum Merdeka</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 bg-success rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-calendar-clock text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Tahun Ajaran & Semester</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">2024/2025 ~ Ganjil</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 bg-success rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-account-group text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Rombel</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">X IPA 1</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 bg-success rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-account-tie text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Wali Kelas</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">Ibu Rini Astuti</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 bg-success rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-trophy text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Ekstrakurikuler</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">Pramuka</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 bg-success rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="mdi mdi-account-star text-white fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <small class="text-muted lh-2">Pembina</small>
                                        <p class="fw-bold mb-0 text-dark lh-sm">Pak Sutrisno</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    {{-- Tabel Entri Ekstrakurikuler --}}
                    <div class="mt-4">
                        <div class="row mb-3 align-items-center">
                            <div class="col-lg-6">
                                <h5 class="text-dark">
                                    <i class="mdi mdi-account-multiple me-2"></i>
                                    Entri Data Kokurikuler
                                </h5>
                            </div>

                            <div class="col-lg-6 d-flex justify-content-end align-items-center">
                                <!-- Input Pencarian -->
                                <div class="input-group w-50">
                                    <input type="text"
                                        class="form-control"
                                        placeholder="Cari nama, atau nomor induk...">
                                    <button type="button" class="btn btn-secondary">
                                        <i class="mdi mdi-close"></i>
                                    </button>
                                </div>

                                <!-- Tombol Generate Capaian -->
                                <button type="button"
                                    class="btn btn-outline-primary btn-sm ms-2 d-flex align-items-center justify-content-center"
                                    title="Buat Capaian Kokurikuler"
                                    style="padding: 0.25rem 0.5rem; width: 2.25rem; height: calc(2.25rem + 2px);">
                                    <i class="mdi mdi-auto-fix"></i>
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" width="5%">No</th>
                                        <th width="35%">Nama Lengkap</th>
                                        <th width="10%">Form</th>
                                        <th width="10%">Predikat</th>
                                        <th width="35%">Capaian</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td>Ahmad Rizky<br><small class="text-muted">12001 | 0098776</small></td>
                                        <td>
                                            <select class="form-select form-select-sm">
                                                <option value="">-- Nilai --</option>
                                                <option>A - Mahir</option>
                                                <option>B - Cakap</option>
                                                <option>C - Berkembang</option>
                                            </select>
                                        </td>
                                        <td><span class="badge bg-success">A</span></td>
                                        <td>
                                            <a href="javascript:void(0)" class="text-muted text-decoration-none" onclick="showFullDeskripsi('Ahmad Rizky', 'Menunjukkan semangat dan tanggung jawab tinggi dalam kegiatan Pramuka, serta mampu bekerja sama dengan baik.')">
                                                Menunjukkan semangat dan tanggung jawab tinggi dalam kegiatan Pramuka...
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-outline-danger btn-sm"><i class="mdi mdi-delete"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">2</td>
                                        <td>Sinta Dewi<br><small class="text-muted">12002 | 0098778</small></td>
                                        <td>
                                            <select class="form-select form-select-sm">
                                                <option value="">-- Nilai --</option>
                                                <option>A - Mahir</option>
                                                <option>B - Cakap</option>
                                                <option>C - Berkembang</option>
                                            </select>
                                        </td>
                                        <td><span class="badge bg-warning text-dark">B</span></td>
                                        <td>
                                            <a href="javascript:void(0)" class="text-muted text-decoration-none" onclick="showFullDeskripsi('Sinta Dewi', 'Cukup aktif dalam kegiatan Pramuka dan menunjukkan sikap tanggung jawab serta semangat belajar keterampilan dasar.')">
                                                Cukup aktif dalam kegiatan Pramuka dan menunjukkan sikap tanggung jawab...
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-outline-danger btn-sm"><i class="mdi mdi-delete"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button class="btn btn-outline-secondary me-2"><i class="mdi mdi-delete-sweep-outline me-1"></i> Reset</button>
                            <button class="btn btn-primary"><i class="mdi mdi-content-save me-1"></i> Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>