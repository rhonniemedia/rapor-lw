<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="text-center sidebar-brand-wrapper d-flex align-items-center">
        <a class="sidebar-brand brand-logo" href="{{url('admin/dashboard')}}"><img src="{{ asset('assets/images/logo.svg') }}" alt="logo" /></a>
        <a class="sidebar-brand brand-logo-mini pl-4 pt-3" href="{{url('')}}"><img src="{{ asset('assets/images/logo-mini.svg') }}" alt="logo" /></a>
    </div>
    <ul class="nav">
        <li class="nav-item nav-profile">
            <a href="{{url('#')}}" class="nav-link">
                <div class="nav-profile-image">
                    <img src="{{ asset('assets/images/icons/smile.png') }}" alt="profile" />
                    <span class="login-status online"></span>
                    <!--change to offline or busy as needed-->
                </div>
                <div class="nav-profile-text d-flex flex-column pr-3">
                    <span class="font-weight-medium mb-2">
                        {{-- NAMA: Menggunakan 'name' dan memastikan bukan null --}}
                        {{ \App\Helpers\UserHelper::getFirstName(Auth::user()->name ?? '') }}
                    </span>
                    <span class="font-weight-normal">
                        {{-- ROLE: Mengambil role pertama dari Spatie Permission --}}
                        {{ ucwords(Auth::user()->roles->first()->name ?? 'N/A') }}
                    </span>
                </div>
                <span class="badge badge-danger text-white ml-3 rounded">
                    {{-- INISIAL: Mengambil huruf pertama dari role --}}
                    {{ strtoupper(substr(Auth::user()->roles->first()->name ?? 'N', 0, 1)) }}
                </span>
            </a>
        </li>

        <!-- Dashboard -->
        <li class="nav-item">
            <span class="nav-link mt-4">
                <span class="menu-title font-weight-bold">Dashboard</span>
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->url() === url('admin/dashboard') ? 'active' : '' }}" href="{{url('admin/dashboard')}}">
                <i class="mdi mdi-view-dashboard menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        <!-- Mater Data -->
        <li class="nav-item">
            <span class="nav-link mt-4">
                <span class="menu-title font-weight-bold">Mater Data</span>
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->url() === url('admin/master/sync') ? 'active' : '' }}" href="{{url('admin/master/sync')}}">
                <i class="mdi mdi-file-compare menu-icon"></i>
                <span class="menu-title">Sinkronisasi</span>
            </a>
        </li>
        <!-- Sekolah -->
        <li class="nav-item">
            <a class="nav-link {{ request()->is('sekolah*') ? 'active' : '' }}" data-toggle="collapse" href="#ui-school" aria-expanded="false" aria-controls="ui-school">
                <i class="mdi mdi-school menu-icon"></i>
                <span class="menu-title">Data Sekolah</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->is('sekolah*') ? 'show' : '' }}" id="ui-school">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/master/profile') ? 'active' : '' }}" href="{{ url('admin/master/profile') }}">Profil Sekolah</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/master/curriculum') ? 'active' : '' }}" href="{{ url('admin/master/curriculum') }}">Kurikulum</a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Akademik -->
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/academic*') ? 'active' : '' }}"
                data-toggle="collapse"
                href="#ui-academic"
                aria-expanded="false"
                aria-controls="ui-academic">
                <i class="mdi mdi-animation menu-icon"></i>
                <span class="menu-title">Data Akademik</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->is('admin/academic*') ? 'show' : '' }}" id="ui-academic">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/academic/year') ? 'active' : '' }}"
                            href="{{url('admin/academic/year')}}">Tahun Ajaran</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/academic/semester') ? 'active' : '' }}"
                            href="{{url('admin/academic/semester')}}">Semester</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/academic/department') ? 'active' : '' }}"
                            href="{{url('admin/academic/department')}}">Jurusan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/academic/subject') ? 'active' : '' }}"
                            href="{{url('admin/academic/subject')}}">Mata Pelajaran</a>
                    </li>
                    <!-- Menu Akademik -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/academic/manage-extracurricular') ? 'active' : '' }}"
                            href="{{url('admin/academic/manage-extracurricular')}}">Ekstrakurikuler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/academic/manage-description') ? 'active' : '' }}"
                            href="{{url('admin/academic/manage-description')}}">Deskripsi Capaian</a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Rombongan Belajar -->
        <li class="nav-item">
            <span class="nav-link mt-4">
                <span class="menu-title font-weight-bold">Rombongan Belajar</span>
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/class/list*') ? 'active' : '' }}"
                href="{{ url('admin/class/list') }}">
                <i class="mdi mdi-ungroup menu-icon"></i>
                <span class="menu-title">Daftar Rombel</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{url('admin/class/teachers')}}">
                <i class="mdi mdi-human-greeting menu-icon"></i>
                <span class="menu-title">Daftar Pendidik</span>
            </a>
        </li>

        <!-- Pembelajaran -->
        <li class="nav-item">
            <span class="nav-link mt-4">
                <span class="menu-title font-weight-bold">Pembelajaran</span>
            </span>
        </li>

        <!-- Pembelajaran -->
        <li class="nav-item">
            <a class="nav-link {{ request()->is('admin/entry*') ? 'active' : '' }}"
                data-toggle="collapse"
                href="#ui-pembelajaran"
                aria-expanded="false"
                aria-controls="ui-pembelajaran">
                <i class="mdi mdi-finance menu-icon"></i>
                <span class="menu-title">Entri Data</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->is('admin/entry*') ? 'show' : '' }}" id="ui-pembelajaran">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/entry/grades') ? 'active' : '' }}"
                            href="{{url('admin/entry/grades')}}">Nilai Akhir</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/entry/cocurricular') ? 'active' : '' }}"
                            href="{{url('admin/entry/cocurricular')}}">Kokurikuler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/entry/attendance') ? 'active' : '' }}"
                            href="{{url('admin/entry/attendance')}}">Kehadiran</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/entry/class-notes') ? 'active' : '' }}"
                            href="{{url('admin/entry/class-notes')}}">Catatan Wali Kelas</a>
                    </li>
                    <!-- Menu Entri Data -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin/entry/data-extracurricular') ? 'active' : '' }}"
                            href="{{url('admin/entry/data-extracurricular')}}">Ekstrakurikuler</a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Pembelajaran -->
        <li class="nav-item">
            <span class="nav-link mt-4">
                <span class="menu-title font-weight-bold">Finalisasi Rapor</span>
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->url() === url('home/nilai-akhir') ? 'active' : '' }}" href="{{url('home/nilai-akhir')}}">
                <i class="mdi mdi-finance menu-icon"></i>
                <span class="menu-title">Pengaturan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->url() === url('home/kokurikuler') ? 'active' : '' }}" href="{{url('home/kokurikuler')}}">
                <i class="mdi mdi-school-outline menu-icon"></i>
                <span class="menu-title">Preview</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->url() === url('home/keharidan') ? 'active' : '' }}" href="{{url('home/keharidan')}}">
                <i class="mdi mdi-calendar-text-outline menu-icon"></i>
                <span class="menu-title">Generate PDF</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->url() === url('home/catatan-wali-kelas') ? 'active' : '' }}" href="{{url('home/catatan-wali-kelas')}}">
                <i class="mdi mdi-note-text-outline menu-icon"></i>
                <span class="menu-title">Arsip</span>
            </a>
        </li>

        <!-- Akademik -->
        <li class="nav-item">
            <a class="nav-link {{ request()->is('akademik*') ? 'active' : '' }}" data-toggle="collapse" href="#ui-akademik" aria-expanded="false" aria-controls="ui-akademik">
                <i class="mdi mdi-animation menu-icon"></i>
                <span class="menu-title">Akademik</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->is('akademik*') ? 'show' : '' }}" id="ui-akademik">
                <ul class="nav flex-column sub-menu">
                </ul>
            </div>
        </li>

        <!-- Pengguna -->
        <li class="nav-item">
            <span class="nav-link mt-4">
                <span class="menu-title font-weight-bold">Pengguna</span>
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{url('home/user')}}">
                <i class="mdi mdi-account menu-icon"></i>
                <span class="menu-title">Daftar Pengguna</span>
            </a>
        </li>
    </ul>
</nav>