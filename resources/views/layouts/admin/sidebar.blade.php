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
            <a class="nav-link {{ request()->url() === url('dashboard') ? 'active' : '' }}" href="{{url('dashboard')}}">
                <i class="mdi mdi-view-dashboard menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        <!-- Mater Data -->
        <li class="nav-item">
            <a class="nav-link {{ request()->url() === url('home/master-data') ? 'active' : '' }}" href="{{url('home/master-data')}}">
                <i class="mdi mdi-file-compare menu-icon"></i>
                <span class="menu-title">Master Data</span>
            </a>
        </li>

        <!-- Sekolah -->
        <li class="nav-item">
            <a class="nav-link {{ request()->is('sekolah*') ? 'active' : '' }}" data-toggle="collapse" href="#ui-school" aria-expanded="false" aria-controls="ui-school">
                <i class="mdi mdi-school menu-icon"></i>
                <span class="menu-title">Sekolah</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->is('sekolah*') ? 'show' : '' }}" id="ui-school">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('sekolah/profil') ? 'active' : '' }}" href="{{ url('sekolah/profil') }}">Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('sekolah/jurusan') ? 'active' : '' }}" href="{{ url('sekolah/jurusan') }}">Jurusan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('sekolah/ekstrakurikuler') ? 'active' : '' }}" href="{{ url('sekolah/ekstrakurikuler') }}">Ekstrakurikuler</a>
                    </li>
                </ul>
            </div>
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
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('akademik/data-akademik') ? 'active' : '' }}" href="{{url('akademik/data-akademik')}}">Data Akademik</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('akademik/mata-pelajaran') ? 'active' : '' }}" href="{{url('akademik/mata-pelajaran')}}">Mata Pelajaran</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{url('home/pendidik')}}">
                <i class="mdi mdi-human-greeting menu-icon"></i>
                <span class="menu-title">Pendidik</span>
            </a>
        </li>
        <li class="nav-item">
            <span class="nav-link mt-4">
                <span class="menu-title font-weight-bold">Peserta Didik</span>
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{url('home/rombongan-belajar')}}">
                <i class="mdi mdi-ungroup menu-icon"></i>
                <span class="menu-title">Rombongan Belajar</span>
            </a>
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