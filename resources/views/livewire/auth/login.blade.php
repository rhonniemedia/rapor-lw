@extends('layouts.auth.auth')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <h4 class="text-center mb-4">Login ke Sistem Rapor</h4>

        @if ($errorMessage)
        <div class="alert alert-danger">{{ $errorMessage }}</div>
        @endif

        <form wire:submit.prevent="login">
            <div class="mb-3">
                <label for="identifier" class="form-label">Email atau NIP</label>
                <input type="text" id="identifier" wire:model.defer="identifier"
                    class="form-control" placeholder="Masukkan email atau NIP">
                @error('identifier') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Kata Sandi</label>
                <input type="password" id="password" wire:model.defer="password"
                    class="form-control" placeholder="Masukkan kata sandi">
                @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" id="remember" wire:model="remember" class="form-check-input">
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <span wire:loading.remove>Masuk</span>
                <span wire:loading>Memproses...</span>
            </button>
        </form>
    </div>
</div>
@endsection