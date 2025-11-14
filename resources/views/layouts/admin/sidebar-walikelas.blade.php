<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="text-center sidebar-brand-wrapper d-flex align-items-center">
        <a class="sidebar-brand brand-logo" href="{{url('homeroom/dashboard')}}"><img src="{{ asset('assets/images/logo.svg') }}" alt="logo" /></a>
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
            <a class="nav-link {{ request()->url() === url('homeroom/dashboard') ? 'active' : '' }}" href="{{url('homeroom/dashboard')}}">
                <i class="mdi mdi-view-dashboard menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        <!-- Wali Kelas -->
        <li class="nav-item">
            <span class="nav-link mt-4">
                <span class="menu-title font-weight-bold">Kelas Binaan</span>
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->url() === url('homeroom/students') ? 'active' : '' }}" href="{{url('homeroom/students')}}">
                <i class="mdi mdi-account-group menu-icon"></i>
                <span class="menu-title">Data Pelajar</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->is('homeroom/entry*') ? 'active' : '' }}"
                data-toggle="collapse"
                href="#ui-pembelajaran"
                aria-expanded="false"
                aria-controls="ui-pembelajaran">
                <i class="mdi mdi-finance menu-icon"></i>
                <span class="menu-title">Entri Data</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse {{ request()->is('homeroom/entry*') ? 'show' : '' }}" id="ui-pembelajaran">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('homeroom/entry/grades') ? 'active' : '' }}"
                            href="{{url('homeroom/entry/grades')}}">Nilai Akhir</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('homeroom/entry/cocurricular') ? 'active' : '' }}"
                            href="{{url('homeroom/entry/cocurricular')}}">Kokurikuler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('homeroom/entry/attendance') ? 'active' : '' }}"
                            href="{{url('homeroom/entry/attendance')}}">Absensi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('homeroom/entry/class-notes') ? 'active' : '' }}"
                            href="{{url('homeroom/entry/class-notes')}}">Catatan Wali Kelas</a>
                    </li>
                    <!-- Menu Entri Data -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('homeroom/entry/extracurricular') ? 'active' : '' }}"
                            href="{{url('homeroom/entry/extracurricular')}}">Ekstrakurikuler</a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Kelas yang Diampu -->
        <li class="nav-item">
            <span class="nav-link mt-4">
                <span class="menu-title font-weight-bold">Kelas Ajar</span>
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Request::is('homeroom/teaching*') ? 'active' : '' }}"
                href="{{ url('homeroom/teaching') }}">
                <i class="mdi mdi-ungroup menu-icon"></i>
                <span class="menu-title">Rombongan Belajar</span>
            </a>
        </li>

        <!-- Finalisasi -->
        <li class="nav-item">
            <span class="nav-link mt-4">
                <span class="menu-title font-weight-bold">Finalisasi Rapor</span>
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->url() === url('homeroom/finalization/preview') ? 'active' : '' }}" href="{{url('homeroom/finalization/preview')}}">
                <i class="mdi mdi-school-outline menu-icon"></i>
                <span class="menu-title">Preview</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->url() === url('homeroom/finalization/generate') ? 'active' : '' }}" href="{{url('homeroom/finalization/generate')}}">
                <i class="mdi mdi-file-pdf menu-icon"></i>
                <span class="menu-title">Generate PDF</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->url() === url('homeroom/finalization/ledger') ? 'active' : '' }}" href="{{url('homeroom/finalization/ledger')}}">
                <i class="mdi mdi-note-text-outline menu-icon"></i>
                <span class="menu-title">Leger</span>
            </a>
        </li>

        <!-- Pengguna -->
        <li class="nav-item">
            <span class="nav-link mt-4">
                <span class="menu-title font-weight-bold">Pengguna</span>
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{url('homeroom/user')}}">
                <i class="mdi mdi-account menu-icon"></i>
                <span class="menu-title">Profil Pengguna</span>
            </a>
        </li>
    </ul>
</nav>