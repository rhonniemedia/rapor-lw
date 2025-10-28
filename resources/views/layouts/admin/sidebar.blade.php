<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="text-center sidebar-brand-wrapper d-flex align-items-center">
        <a class="sidebar-brand brand-logo" href="{{url('dashboard')}}"><img src="{{ asset('assets/images/logo.svg') }}" alt="logo" /></a>
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
                        Admin
                    </span>
                    <span class="font-weight-normal">A</span>
                </div>
                <span class="badge badge-danger text-white ml-3 rounded">
                    admin
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
            <a class="nav-link {{ request()->url() === url('dashboard') ? 'active' : '' }}" href="{{url('dashboard')}}">
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
            <a class="nav-link {{ request()->url() === url('home/master-data') ? 'active' : '' }}" href="{{url('home/master-data')}}">
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
                        <a class="nav-link {{ request()->is('sekolah/profil') ? 'active' : '' }}" href="{{ url('sekolah/profil') }}">Profil Sekolah</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('sekolah/kurikulum') ? 'active' : '' }}" href="{{ url('sekolah/kurikulum') }}">Kurikulum</a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Akademik -->
        <li class="nav-item">
            <a class="nav-link {{ request()->is('akademik*') ? 'active' : '' }}" data-toggle="collapse" href="#ui-akademik" aria-expanded="false" aria-controls="ui-akademik">
                <i class="mdi mdi-animation menu-icon"></i>
                <span class="menu-title">Data Akademik</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->is('akademik*') ? 'show' : '' }}" id="ui-akademik">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('akademik/tahun-ajaran') ? 'active' : '' }}" href="{{url('akademik/tahun-ajaran')}}">Tahun Ajaran</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('akademik/semester') ? 'active' : '' }}" href="{{url('akademik/semester')}}">Semester</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('akademik/jurusan') ? 'active' : '' }}" href="{{url('akademik/jurusan')}}">Jurusan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('akademik/mata-pelajaran') ? 'active' : '' }}" href="{{url('akademik/mata-pelajaran')}}">Mata Pelajaran</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('akademik/ekstrakurikuler') ? 'active' : '' }}" href="{{url('akademik/ekstrakurikuler')}}">Ekstrakurikuler</a>
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
            <a class="nav-link {{ request()->is('home/rombongan-belajar*') ? 'active' : '' }}"
                href="{{ url('home/rombongan-belajar') }}">
                <i class="mdi mdi-ungroup menu-icon"></i>
                <span class="menu-title">Daftar Rombel</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{url('home/pendidik')}}">
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

        <li class="nav-item">
            <a class="nav-link {{ request()->is('pembelajaran*') ? 'active' : '' }}" data-toggle="collapse" href="#ui-pembelajaran" aria-expanded="false" aria-controls="ui-pembelajaran">
                <i class="mdi mdi-finance menu-icon menu-icon"></i>
                <span class="menu-title">Entri Data</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->is('pembelajaran*') ? 'show' : '' }}" id="ui-pembelajaran">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('pembelajaran/nilai-akhir') ? 'active' : '' }}" href="{{url('pembelajaran/nilai-akhir')}}">Nilai Akhir</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('pembelajaran/kokurikuler') ? 'active' : '' }}" href="{{url('pembelajaran/kokurikuler')}}">Kokurikuler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('pembelajaran/kehadiran') ? 'active' : '' }}" href="{{url('pembelajaran/kehadiran')}}">Kehadiran</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('pembelajaran/catatan-wali-kelas') ? 'active' : '' }}" href="{{url('pembelajaran/catatan-wali-kelas')}}">Catatan Wali Kelas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('pembelajaran/ekstrakurikuler') ? 'active' : '' }}" href="{{url('pembelajaran/ekstrakurikuler')}}">Ekstrakurikuler</a>
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