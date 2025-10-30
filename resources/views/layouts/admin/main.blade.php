<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Rapor Digital | {{ $title }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/materialdesignicons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/flag-icon.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/vendor.bundle.base.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/select2-bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datepicker.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
    <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}" />

    <!-- Demo CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/button.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/other-style.css') }}" />

</head>

<body>
    <div class="container-scroller">

        <!-- Left Sidebar -->
        @if (Auth::user()->hasRole('superadmin'))
        @include('layouts.admin.sidebar')

        @elseif (Auth::user()->hasRole('admin'))
        @include('layouts.admin.sidebar')

        @elseif (Auth::user()->hasRole('walikelas'))
        @include('layouts.admin.sidebar-walikelas')

        @elseif (Auth::user()->hasRole('guru'))
        @include('layouts.admin.sidebar-guru')

        @endif

        <!-- End of left sidebar -->

        <div class="container-fluid page-body-wrapper">
            <div id="theme-settings" class="settings-panel">
                <i class="settings-close mdi mdi-close"></i>
                <p class="settings-heading">SIDEBAR SKINS</p>
                <div class="sidebar-bg-options selected" id="sidebar-default-theme">
                    <div class="img-ss rounded-circle bg-light border mr-3"></div> Default
                </div>
                <div class="sidebar-bg-options" id="sidebar-dark-theme">
                    <div class="img-ss rounded-circle bg-dark border mr-3"></div> Dark
                </div>
                <p class="settings-heading mt-2">HEADER SKINS</p>
                <div class="color-tiles mx-0 px-4">
                    <div class="tiles light"></div>
                    <div class="tiles dark"></div>
                </div>
            </div>
            <nav class="navbar col-lg-12 col-12 p-lg-0 fixed-top d-flex flex-row">
                <div class="navbar-menu-wrapper d-flex align-items-stretch justify-content-between">
                    <a class="navbar-brand brand-logo-mini align-self-center d-lg-none" href="index.html"><img src="{{ asset('assets/images/logo-mini.svg') }}" alt="logo" /></a>
                    <button class="navbar-toggler navbar-toggler align-self-center mr-2" type="button" data-toggle="minimize">
                        <i class="mdi mdi-menu"></i>
                    </button>

                    <!-- Notifications -->

                    <ul class="navbar-nav navbar-nav-right ml-lg-auto">
                        <!-- Setting -->
                        <li class="nav-item dropdown d-none d-xl-flex border-0">
                            <a class="nav-link dropdown-toggle" id="languageDropdown" href="{{ url('#') }}" data-toggle="dropdown">
                                <i class="mdi mdi-settings"></i> Pengaturan </a>
                            <div class="dropdown-menu navbar-dropdown" aria-labelledby="languageDropdown">
                                <a class="dropdown-item" href="{{url('master-data/school-data')}}">Data Sekolah</a>
                            </div>
                        </li>
                        <li class="nav-item nav-profile dropdown border-0">
                            <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-toggle="dropdown">
                                <img class="nav-profile-img mr-2" alt="" src="{{ asset('assets/images/icons/smile.png') }}" />
                                <span class="profile-name">{{ Auth::user()->name ?? '' }}</span>
                            </a>
                            <div class="dropdown-menu navbar-dropdown w-100" aria-labelledby="profileDropdown">
                                <button type="submit" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalProfile">
                                    <i class="mdi mdi-account-badge mr-2 text-success"></i>Profil </button>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="mdi mdi-logout mr-2 text-primary"></i> Signout
                                    </button>
                                </form>
                            </div>
                        </li>
                    </ul>
                    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
                        <span class="mdi mdi-menu"></span>
                    </button>
                </div>
            </nav>
            <div class="main-panel">

                <!-- Content -->

                @yield('container')

                <!-- End of content -->

                <!-- Footer -->
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © {{ date('Y') }} SMK Negeri 1 Rejang Lebong.</span>
                    </div>
                </footer>
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- plugins:js -->
    <script src="{{ asset('assets/js/vendor.bundle.base.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS Select2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="{{ asset('assets/js/select2.min.js') }}"></script>

    <!-- file inisialisasi kamu: jangan beri nama sama dengan plugin (mis. select2-init.js) -->
    <script src="{{ asset('assets/js/select2-init.js') }}"></script>

    <!-- Chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Flot, datepicker, dll -->
    <script src="{{ asset('assets/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.flot.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.flot.resize.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.flot.categories.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.flot.fillbetween.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.flot.stack.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.flot.pie.js') }}"></script>

    <!-- Custom js -->
    <!-- <script src="{{ asset('assets/js/dashboard.js') }}"></script> -->
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- endinject -->

    @livewireScripts

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- swal:success ---
            window.addEventListener('swal:success', event => {
                // Logika pengambilan detail Livewire V2/V3 yang fleksibel
                let detail = event.detail.params ?? event.detail[0] ?? event.detail;

                if (typeof detail === 'string') {
                    // Jika dikirim sebagai string
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: detail,
                        showConfirmButton: true,
                        // Tambahkan timer jika diinginkan: timer: 2000, timerProgressBar: true,
                    });
                } else if (typeof detail === 'object' && detail !== null) {
                    // Jika dikirim sebagai object (title & text)
                    Swal.fire({
                        icon: 'success',
                        title: detail.title ?? 'Berhasil!',
                        text: detail.text ?? '',
                        showConfirmButton: true,
                        // Tambahkan timer jika diinginkan: timer: 2000, timerProgressBar: true,
                    });
                }
            });

            // --- swal:error ---
            window.addEventListener('swal:error', event => {
                // Logika pengambilan detail Livewire V2/V3 yang fleksibel
                let detail = event.detail.params ?? event.detail[0] ?? event.detail;

                if (typeof detail === 'string') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: detail,
                        confirmButtonText: 'Tutup'
                    });
                } else if (typeof detail === 'object' && detail !== null) {
                    Swal.fire({
                        icon: 'error',
                        title: detail.title ?? 'Error!',
                        text: detail.text ?? '',
                        confirmButtonText: 'Tutup'
                    });
                }
            });

            // --- swal:confirm ---
            window.addEventListener('swal:confirm', event => {
                let detail = event.detail.params ?? event.detail[0] ?? event.detail;

                if (typeof detail === 'object' && detail !== null && detail.nextEvent) {
                    Swal.fire({
                        icon: detail.icon ?? 'question',
                        title: detail.title ?? 'Konfirmasi',
                        text: detail.text ?? 'Apakah Anda yakin melanjutkan aksi ini?',
                        showCancelButton: true,
                        confirmButtonText: detail.confirmButtonText ?? 'Ya',
                        cancelButtonText: detail.cancelButtonText ?? 'Batal',
                        confirmButtonColor: detail.confirmButtonColor ?? '#3085d6',
                        cancelButtonColor: detail.cancelButtonColor ?? '#d33',
                    }).then(result => {
                        if (result.isConfirmed) {
                            // ✅ Kirim parameter dalam array, bukan langsung nilai tunggal
                            Livewire.dispatch(detail.nextEvent, [detail.id]);
                        }
                    });
                }
            });

        });
    </script>
</body>

</html>