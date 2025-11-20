@extends('layouts.auth.login')

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h2>Login</h2>
            <p>
                Silakan login dengan kredensial Anda untuk mengakses sistem penilaian peserta didik.
            </p>
        </div>

        <!-- Livewire Login -->
        <livewire:auth.login />

        <div class="login-footer">
            <p class="mb-0 text-center text-muted">
                &copy; {{ date('Y') }} E-Rapor | SMK Negeri 1 Rejang Lebong
            </p>
        </div>
    </div>
</div>
@endsection