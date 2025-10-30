<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="text-center sidebar-brand-wrapper d-flex align-items-center">
        <a class="sidebar-brand brand-logo" href="{{url('teacher/dashboard')}}"><img src="{{ asset('assets/images/logo.svg') }}" alt="logo" /></a>
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
            <a class="nav-link {{ request()->url() === url('teacher/dashboard') ? 'active' : '' }}" href="{{url('teacher/dashboard')}}">
                <i class="mdi mdi-view-dashboard menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        <!-- Kelas yang diampu -->
        <li class="nav-item">
            <span class="nav-link mt-4">
                <span class="menu-title font-weight-bold">Kelas Ajar</span>
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->url() === url('teacher/class') ? 'active' : '' }}" href="{{url('teacher/class')}}">
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
            <a class="nav-link" href="{{url('teacher/user')}}">
                <i class="mdi mdi-account menu-icon"></i>
                <span class="menu-title">Profil Pengguna</span>
            </a>
        </li>
    </ul>
</nav>