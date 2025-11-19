<div>
    <div class="page-header flex-wrap">
        <h3 class="mb-0">
            Dashboard E-Rapor
            <span class="pl-0 h6 pl-sm-2 text-muted d-inline-block">Sistem Manajemen Nilai Sekolah</span>
        </h3>
        <div class="d-flex">
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-success">
                    Tahun Ajaran 2024/2025
                </button>
                <button
                    type="button"
                    class="btn btn-sm btn-success dropdown-toggle dropdown-toggle-split"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    style="
                      width: 32px;
                      display: flex;
                      align-items: center;
                      justify-content: center;
                      padding: 0;
                    ">
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#">2023/2024</a>
                    <a class="dropdown-item" href="#">2022/2023</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="row">
        <div class="col-xl-3 col-lg-12 stretch-card grid-margin">
            <div class="row">
                <div
                    class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 pb-sm-3">
                    <div class="card bg-danger">
                        <div class="card-body px-3 py-4">
                            <div
                                class="d-flex justify-content-between align-items-start">
                                <div class="color-card">
                                    <p class="mb-0 color-card-head">Total Siswa</p>
                                    <h2 class="text-white">1,245</h2>
                                </div>
                                <i
                                    class="card-icon-indicator mdi mdi-account-multiple bg-inverse-icon-danger"></i>
                            </div>
                            <h6 class="text-white">+5.2% dari semester lalu</h6>
                        </div>
                    </div>
                </div>
                <div
                    class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 pb-sm-3">
                    <div class="card bg-warning">
                        <div class="card-body px-3 py-4">
                            <div
                                class="d-flex justify-content-between align-items-start">
                                <div class="color-card">
                                    <p class="mb-0 color-card-head">Total Guru</p>
                                    <h2 class="text-white">68</h2>
                                </div>
                                <i
                                    class="card-icon-indicator mdi mdi-teach bg-inverse-icon-warning"></i>
                            </div>
                            <h6 class="text-white">+2 guru baru</h6>
                        </div>
                    </div>
                </div>
                <div
                    class="col-xl-12 col-md-6 stretch-card grid-margin grid-margin-sm-0 pb-sm-3 pb-lg-0 pb-xl-3">
                    <div class="card bg-success">
                        <div class="card-body px-3 py-4">
                            <div
                                class="d-flex justify-content-between align-items-start">
                                <div class="color-card">
                                    <p class="mb-0 color-card-head">Rombel Aktif</p>
                                    <h2 class="text-white">42</h2>
                                </div>
                                <i
                                    class="card-icon-indicator mdi mdi-google-classroom bg-inverse-icon-success"></i>
                            </div>
                            <h6 class="text-white">12 IPA, 15 IPS, 15 Bahasa</h6>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12 col-md-6 stretch-card pb-sm-3 pb-lg-0">
                    <div class="card bg-info">
                        <div class="card-body px-3 py-4">
                            <div
                                class="d-flex justify-content-between align-items-start">
                                <div class="color-card">
                                    <p class="mb-0 color-card-head">Mata Pelajaran</p>
                                    <h2 class="text-white">58</h2>
                                </div>
                                <i
                                    class="card-icon-indicator mdi mdi-book-open-variant bg-inverse-icon-info"></i>
                            </div>
                            <h6 class="text-white">Kurikulum Merdeka 2024</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Input Nilai - REVISI -->
        <div class="col-xl-9 stretch-card grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-8">
                            <h5>
                                Matriks Kelengkapan Data Final Rapor per Jenjang
                            </h5>
                            <p class="text-muted">
                                Perbandingan proporsi kelengkapan Nilai, Kokurikuler,
                                Kehadiran, dan Ekstrakurikuler.
                            </p>
                        </div>
                        <div class="col-sm-4 text-md-right">
                            <div class="dropdown dropleft d-block">
                                <button
                                    class="btn btn-sm btn-outline-secondary btn-icon"
                                    id="dropdownProgressAction"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                    style="width: 32px; height: 32px; line-height: 1">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </button>
                                <div
                                    class="dropdown-menu"
                                    aria-labelledby="dropdownProgressAction">
                                    <a class="dropdown-item" href="#">Lihat Detail Rombel</a>
                                    <a class="dropdown-item text-danger" href="#">Kirim Peringatan Wali Kelas</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div
                                class="card p-3 bg-light mapel-progress-card danger">
                                <div class="d-flex align-items-center">
                                    <i
                                        class="mdi mdi-alpha-x mdi-36px text-danger mr-3"></i>
                                    <div>
                                        <p class="mb-0 text-muted font-weight-bold">
                                            Tingkat Paling Kritis
                                        </p>
                                        <h4 class="mb-0 text-danger">Kelas X</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div
                                class="card p-3 bg-light mapel-progress-card warning">
                                <div class="d-flex align-items-center">
                                    <i
                                        class="mdi mdi-alpha-y mdi-36px text-warning mr-3"></i>
                                    <div>
                                        <p class="mb-0 text-muted font-weight-bold">
                                            Tingkat Sedang
                                        </p>
                                        <h4 class="mb-0 text-warning">Kelas XI</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div
                                class="card p-3 bg-light mapel-progress-card success">
                                <div class="d-flex align-items-center">
                                    <i
                                        class="mdi mdi-alpha-z mdi-36px text-success mr-3"></i>
                                    <div>
                                        <p class="mb-0 text-muted font-weight-bold">
                                            Tingkat Hampir Selesai
                                        </p>
                                        <h4 class="mb-0 text-success">Kelas XII</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="mt-4 mb-3 text-black">
                        Proporsi Kelengkapan Rapor per Jenjang
                    </h6>
                    <div class="row">
                        <div class="col-md-4 grid-margin stretch-card">
                            <div class="card p-3 mapel-progress-card">
                                <h6 class="card-title text-black mb-3 text-center">
                                    Kelas X
                                </h6>
                                <div style="height: 250px; position: relative">
                                    <canvas id="chart_kelas_x"></canvas>
                                </div>
                                <div class="chart-legend mt-3">
                                    <div class="legend-item">
                                        <span
                                            class="legend-color"
                                            style="background-color: #a0b9ff"></span>
                                        Nilai:
                                        <span class="legend-value font-weight-bold">70%</span>
                                    </div>
                                    <div class="legend-item">
                                        <span
                                            class="legend-color"
                                            style="background-color: #92ddcc"></span>
                                        Kokurikuler:
                                        <span class="legend-value font-weight-bold">60%</span>
                                    </div>
                                    <div class="legend-item">
                                        <span
                                            class="legend-color"
                                            style="background-color: #ffc0cb"></span>
                                        Kehadiran:
                                        <span class="legend-value font-weight-bold">85%</span>
                                    </div>
                                    <div class="legend-item">
                                        <span
                                            class="legend-color"
                                            style="background-color: #dda0dd"></span>
                                        Ekstrakurikuler:
                                        <span class="legend-value font-weight-bold">55%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 grid-margin stretch-card">
                            <div class="card p-3 mapel-progress-card">
                                <h6 class="card-title text-black mb-3 text-center">
                                    Kelas XI
                                </h6>
                                <div style="height: 250px; position: relative">
                                    <canvas id="chart_kelas_xi"></canvas>
                                </div>
                                <div class="chart-legend mt-3">
                                    <div class="legend-item">
                                        <span
                                            class="legend-color"
                                            style="background-color: #a0b9ff"></span>
                                        Nilai:
                                        <span class="legend-value font-weight-bold">80%</span>
                                    </div>
                                    <div class="legend-item">
                                        <span
                                            class="legend-color"
                                            style="background-color: #92ddcc"></span>
                                        Kokurikuler:
                                        <span class="legend-value font-weight-bold">90%</span>
                                    </div>
                                    <div class="legend-item">
                                        <span
                                            class="legend-color"
                                            style="background-color: #ffc0cb"></span>
                                        Kehadiran:
                                        <span class="legend-value font-weight-bold">95%</span>
                                    </div>
                                    <div class="legend-item">
                                        <span
                                            class="legend-color"
                                            style="background-color: #dda0dd"></span>
                                        Ekstrakurikuler:
                                        <span class="legend-value font-weight-bold">88%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 grid-margin stretch-card">
                            <div class="card p-3 mapel-progress-card">
                                <h6 class="card-title text-black mb-3 text-center">
                                    Kelas XII
                                </h6>
                                <div style="height: 250px; position: relative">
                                    <canvas id="chart_kelas_xii"></canvas>
                                </div>
                                <div class="chart-legend mt-3">
                                    <div class="legend-item">
                                        <span
                                            class="legend-color"
                                            style="background-color: #a0b9ff"></span>
                                        Nilai:
                                        <span class="legend-value font-weight-bold">95%</span>
                                    </div>
                                    <div class="legend-item">
                                        <span
                                            class="legend-color"
                                            style="background-color: #92ddcc"></span>
                                        Kokurikuler:
                                        <span class="legend-value font-weight-bold">100%</span>
                                    </div>
                                    <div class="legend-item">
                                        <span
                                            class="legend-color"
                                            style="background-color: #ffc0cb"></span>
                                        Kehadiran:
                                        <span class="legend-value font-weight-bold">99%</span>
                                    </div>
                                    <div class="legend-item">
                                        <span
                                            class="legend-color"
                                            style="background-color: #dda0dd"></span>
                                        Ekstrakurikuler:
                                        <span class="legend-value font-weight-bold">100%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Rombel dan Nilai -->
    <div class="row">
        <div class="col-xl-8 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body px-0 overflow-auto">
                    <h4 class="card-title pl-4">
                        Rombel dengan Progress Terendah
                    </h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="bg-light">
                                <tr>
                                    <th>Rombel</th>
                                    <th>Wali Kelas</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i
                                                class="mdi mdi-google-classroom text-primary mr-3"></i>
                                            <div class="table-user-name ml-3">
                                                <p class="mb-0 font-weight-medium">X IPA 3</p>
                                                <small>42 siswa</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Budi Santoso</td>
                                    <td>
                                        <div class="progress" style="height: 6px">
                                            <div
                                                class="progress-bar bg-danger"
                                                role="progressbar"
                                                style="width: 65%"
                                                aria-valuenow="65"
                                                aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                        <small>65%</small>
                                    </td>
                                    <td>
                                        <div class="badge badge-inverse-danger">
                                            Perlu Perhatian
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i
                                                class="mdi mdi-google-classroom text-primary mr-3"></i>
                                            <div class="table-user-name ml-3">
                                                <p class="mb-0 font-weight-medium">
                                                    XI IPS 2
                                                </p>
                                                <small>38 siswa</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Siti Rahayu</td>
                                    <td>
                                        <div class="progress" style="height: 6px">
                                            <div
                                                class="progress-bar bg-warning"
                                                role="progressbar"
                                                style="width: 72%"
                                                aria-valuenow="72"
                                                aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                        <small>72%</small>
                                    </td>
                                    <td>
                                        <div class="badge badge-inverse-warning">
                                            Sedang
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i
                                                class="mdi mdi-google-classroom text-primary mr-3"></i>
                                            <div class="table-user-name ml-3">
                                                <p class="mb-0 font-weight-medium">
                                                    XII Bahasa 1
                                                </p>
                                                <small>35 siswa</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Ahmad Fauzi</td>
                                    <td>
                                        <div class="progress" style="height: 6px">
                                            <div
                                                class="progress-bar bg-warning"
                                                role="progressbar"
                                                style="width: 70%"
                                                aria-valuenow="70"
                                                aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                        <small>70%</small>
                                    </td>
                                    <td>
                                        <div class="badge badge-inverse-warning">
                                            Sedang
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i
                                                class="mdi mdi-google-classroom text-primary mr-3"></i>
                                            <div class="table-user-name ml-3">
                                                <p class="mb-0 font-weight-medium">X IPS 4</p>
                                                <small>40 siswa</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Dewi Lestari</td>
                                    <td>
                                        <div class="progress" style="height: 6px">
                                            <div
                                                class="progress-bar bg-info"
                                                role="progressbar"
                                                style="width: 80%"
                                                aria-valuenow="80"
                                                aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                        <small>80%</small>
                                    </td>
                                    <td>
                                        <div class="badge badge-inverse-info">Baik</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i
                                                class="mdi mdi-google-classroom text-primary mr-3"></i>
                                            <div class="table-user-name ml-3">
                                                <p class="mb-0 font-weight-medium">
                                                    XI IPA 1
                                                </p>
                                                <small>45 siswa</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Rina Handayani</td>
                                    <td>
                                        <div class="progress" style="height: 6px">
                                            <div
                                                class="progress-bar bg-success"
                                                role="progressbar"
                                                style="width: 95%"
                                                aria-valuenow="95"
                                                aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                        <small>95%</small>
                                    </td>
                                    <td>
                                        <div class="badge badge-inverse-success">
                                            Sangat Baik
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <a class="text-black mt-3 d-block pl-4" href="#">
                        <span class="font-weight-medium h6">Lihat semua rombel</span>
                        <i class="mdi mdi-chevron-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="card-title font-weight-medium">
                        Distribusi Nilai Rata-rata
                    </div>
                    <p class="text-muted">Semester Ganjil 2024/2025</p>
                    <div
                        class="d-flex flex-wrap border-bottom py-2 border-top justify-content-between">
                        <div class="pt-2">
                            <h5 class="mb-0">90-100 (A)</h5>
                            <p class="mb-0 text-muted">Sangat Baik</p>
                            <h5 class="mb-0">15%</h5>
                        </div>
                        <div class="pt-2">
                            <div class="badge badge-inverse-success mt-3">
                                187 siswa
                            </div>
                        </div>
                    </div>
                    <div
                        class="d-flex flex-wrap border-bottom py-2 justify-content-between">
                        <div class="pt-2">
                            <h5 class="mb-0">80-89 (B)</h5>
                            <p class="mb-0 text-muted">Baik</p>
                            <h5 class="mb-0">42%</h5>
                        </div>
                        <div class="pt-2">
                            <div class="badge badge-inverse-info mt-3">
                                523 siswa
                            </div>
                        </div>
                    </div>
                    <div
                        class="d-flex flex-wrap border-bottom py-2 justify-content-between">
                        <div class="pt-2">
                            <h5 class="mb-0">70-79 (C)</h5>
                            <p class="mb-0 text-muted">Cukup</p>
                            <h5 class="mb-0">32%</h5>
                        </div>
                        <div class="pt-2">
                            <div class="badge badge-inverse-warning mt-3">
                                398 siswa
                            </div>
                        </div>
                    </div>
                    <div
                        class="d-flex flex-wrap border-bottom py-2 justify-content-between">
                        <div class="pt-2">
                            <h5 class="mb-0">0-69 (D)</h5>
                            <p class="mb-0 text-muted">Perlu Perbaikan</p>
                            <h5 class="mb-0">11%</h5>
                        </div>
                        <div class="pt-2">
                            <div class="badge badge-inverse-danger mt-3">
                                137 siswa
                            </div>
                        </div>
                    </div>
                    <a
                        class="text-black mt-3 d-block font-weight-medium h6"
                        href="#">Lihat detail <i class="mdi mdi-chevron-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Aktivitas Terbaru dan Kalender -->
    <div class="row">
        <div class="col-xl-4 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-black">Aktivitas Terbaru</h4>
                    <p class="text-muted">Update sistem terakhir</p>
                    <div class="list-wrapper">
                        <ul
                            class="d-flex flex-column-reverse todo-list todo-list-custom">
                            <li>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <span class="text-primary">Budi Santoso</span>
                                        menginput nilai Matematika X IPA 1
                                    </label>
                                    <span class="list-time">2 jam lalu</span>
                                </div>
                            </li>
                            <li>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <span class="text-primary">Siti Rahayu</span>
                                        memperbarui data kehadiran XI IPS 2
                                    </label>
                                    <span class="list-time">4 jam lalu</span>
                                </div>
                            </li>
                            <li>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <span class="text-primary">Admin</span>
                                        menambahkan 3 siswa baru
                                    </label>
                                    <span class="list-time">6 jam lalu</span>
                                </div>
                            </li>
                            <li>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <span class="text-primary">Ahmad Fauzi</span>
                                        menginput nilai Bahasa Indonesia XII Bahasa 1
                                    </label>
                                    <span class="list-time">1 hari lalu</span>
                                </div>
                            </li>
                            <li>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <span class="text-primary">Dewi Lestari</span>
                                        menambahkan catatan untuk 5 siswa
                                    </label>
                                    <span class="list-time">1 hari lalu</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-black">
                        Guru dengan Input Terbanyak
                    </h4>
                    <p class="text-muted">Semester Ganjil 2024/2025</p>
                    <div class="row pt-2 pb-1">
                        <div class="col-12 col-sm-7">
                            <div class="row">
                                <div class="col-4 col-md-4">
                                    <img
                                        class="customer-img"
                                        src="assets/images/faces/face22.jpg"
                                        alt="" />
                                </div>
                                <div class="col-8 col-md-8 p-sm-0">
                                    <h6 class="mb-0">Rina Handayani</h6>
                                    <p class="text-muted font-12">Matematika</p>
                                    <div class="progress" style="height: 6px">
                                        <div
                                            class="progress-bar bg-success"
                                            role="progressbar"
                                            style="width: 95%"
                                            aria-valuenow="95"
                                            aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5 pl-0 text-right">
                            <h4 class="mb-0">95%</h4>
                            <p class="text-muted">6 rombel</p>
                        </div>
                    </div>
                    <div class="row py-1">
                        <div class="col-sm-7">
                            <div class="row">
                                <div class="col-4 col-sm-4">
                                    <img
                                        class="customer-img"
                                        src="assets/images/faces/face25.jpg"
                                        alt="" />
                                </div>
                                <div class="col-8 col-sm-8 p-sm-0">
                                    <h6 class="mb-0">Ahmad Fauzi</h6>
                                    <p class="text-muted font-12">Bahasa Indonesia</p>
                                    <div class="progress" style="height: 6px">
                                        <div
                                            class="progress-bar bg-info"
                                            role="progressbar"
                                            style="width: 88%"
                                            aria-valuenow="88"
                                            aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5 pl-0 text-right">
                            <h4 class="mb-0">88%</h4>
                            <p class="text-muted">5 rombel</p>
                        </div>
                    </div>
                    <div class="row py-1">
                        <div class="col-sm-7">
                            <div class="row">
                                <div class="col-4 col-sm-4">
                                    <img
                                        class="customer-img"
                                        src="assets/images/faces/face15.jpg"
                                        alt="" />
                                </div>
                                <div class="col-8 col-sm-8 p-sm-0">
                                    <h6 class="mb-0">Dewi Lestari</h6>
                                    <p class="text-muted font-12">IPA</p>
                                    <div class="progress" style="height: 6px">
                                        <div
                                            class="progress-bar bg-warning"
                                            role="progressbar"
                                            style="width: 75%"
                                            aria-valuenow="75"
                                            aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5 pl-0 text-right">
                            <h4 class="mb-0">75%</h4>
                            <p class="text-muted">4 rombel</p>
                        </div>
                    </div>
                    <a
                        class="text-black mt-3 d-block font-weight-medium h6"
                        href="#">Lihat semua guru <i class="mdi mdi-chevron-right"></i></a>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 grid-margin stretch-card">
            <!--datepicker-->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-black">Kalender Akademik</h4>
                    <p class="text-muted pb-2">Desember 2024</p>
                    <div
                        id="inline-datepicker"
                        class="datepicker table-responsive"></div>
                    <div class="mt-3">
                        <h6 class="text-black">Jadwal Penting</h6>
                        <ul class="list-ticked">
                            <li>15 Des - Deadline Input Nilai</li>
                            <li>20-22 Des - Verifikasi Nilai</li>
                            <li>25 Des - Generate Rapor</li>
                            <li>28 Des - Pembagian Rapor</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--datepicker ends-->
        </div>
    </div>

    <style>
        .progress-ring {
            position: relative;
            width: 80px;
            height: 80px;
        }

        .progress-ring-circle {
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }

        .progress-ring-value {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-weight: bold;
        }

        .mapel-progress-card {
            transition: all 0.3s ease;
        }

        .mapel-progress-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .mapel-progress-card.warning {
            border-left: 4px solid #ffee00;
        }

        .mapel-progress-card.danger {
            border-left: 4px solid #f44336;
        }

        .mapel-progress-card.success {
            border-left: 4px solid #4caf50;
        }

        .chart-legend {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 15px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            margin: 5px 10px;
            font-size: 0.85em;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
            display: inline-block;
        }
    </style>

    <script>
        // Countdown Timer
        function updateCountdown() {
            const deadline = new Date("December 15, 2024 23:59:59").getTime();
            const now = new Date().getTime();
            const timeLeft = deadline - now;

            const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
            const hours = Math.floor(
                (timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
            );
            const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

            document.getElementById("days").textContent = days;
            document.getElementById("hours").textContent = hours;
            document.getElementById("minutes").textContent = minutes;
            document.getElementById("seconds").textContent = seconds;
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof Chart === "undefined") {
                console.error(
                    "Chart.js is not loaded. Ensure assets/vendors/chart.js/Chart.min.js is linked correctly."
                );
                return;
            }

            const categoryColors = [
                "#A0B9FF", // Nilai
                "#92DDCC", // Kokurikuler
                "#FFC0CB", // Kehadiran
                "#DDA0DD", // Ekstrakurikuler
            ];

            const progressDataByJenjang = {
                kelas_x: {
                    labels: ["Nilai", "Kokurikuler", "Kehadiran", "Ekstrakurikuler"],
                    data: [70, 60, 85, 55],
                },
                kelas_xi: {
                    labels: ["Nilai", "Kokurikuler", "Kehadiran", "Ekstrakurikuler"],
                    data: [80, 90, 95, 88],
                },
                kelas_xii: {
                    labels: ["Nilai", "Kokurikuler", "Kehadiran", "Ekstrakurikuler"],
                    data: [95, 100, 99, 100],
                },
            };

            function createJenjangDonutChart(elementId, jenjangData) {
                const ctx = document.getElementById(elementId);
                if (!ctx) return;

                new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        labels: jenjangData.labels,
                        datasets: [{
                            data: jenjangData.data,
                            backgroundColor: categoryColors,
                            borderColor: "#ffffff",
                            borderWidth: 2,
                        }, ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: "70%",
                        plugins: {
                            legend: {
                                display: false, // Ini yang menyembunyikan legenda bawaan Chart.js
                            },
                            tooltip: {
                                enabled: true,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || "";
                                        if (label) {
                                            label += ": ";
                                        }
                                        if (context.parsed !== null) {
                                            label += context.parsed + "%";
                                        }
                                        return label;
                                    },
                                },
                            },
                        },
                    },
                });

                // Update nilai di legenda HTML (yang ada di bawah)
                const legendContainer = ctx
                    .closest(".card")
                    .querySelector(".chart-legend");
                if (legendContainer) {
                    jenjangData.labels.forEach((label, index) => {
                        const valueElement = legendContainer.querySelector(
                            `.legend-item:nth-child(${index + 1}) .legend-value`
                        );
                        if (valueElement) {
                            valueElement.textContent = jenjangData.data[index] + "%";
                        }
                    });
                }
            }

            // Inisialisasi charts setelah DOM dimuat dan Chart.js tersedia
            setTimeout(() => {
                createJenjangDonutChart(
                    "chart_kelas_x",
                    progressDataByJenjang.kelas_x
                );
                createJenjangDonutChart(
                    "chart_kelas_xi",
                    progressDataByJenjang.kelas_xi
                );
                createJenjangDonutChart(
                    "chart_kelas_xii",
                    progressDataByJenjang.kelas_xii
                );
            }, 200);
        });
    </script>
</div>