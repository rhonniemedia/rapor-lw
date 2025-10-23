<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login - Sistem Rapor' }}</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    @livewireStyles
</head>

<body class="bg-light">

    <div class="container d-flex align-items-center justify-content-center vh-100">
        <div class="w-100" style="max-width: 400px;">
            {{-- Konten halaman --}}
            @yield('content')
        </div>
    </div>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    @livewireScripts
</body>

</html>