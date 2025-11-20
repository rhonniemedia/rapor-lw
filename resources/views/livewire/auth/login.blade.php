<div class="login-body">
    @if ($errors->has('auth'))
    <div class="alert alert-danger d-flex gap-2" role="alert">
        <i class="mdi mdi-shield-alert-outline fs-2"></i>
        <div>
            <p class="mt-1 mb-0 fw-bold">Login gagal</p>
            <small class="text-muted fs-9">{{ $errors->first('auth') }}</small>
        </div>
    </div>
    @endif

    <form wire:submit.prevent="login">
        <div class="form-group">
            <label for="username" class="form-label">Nama Pengguna</label>
            <div class="input-container">
                <div class="input-icon">
                    <i class="mdi mdi-account-outline"></i>
                </div>
                <input
                    wire:model.defer="username"
                    type="text"
                    class="input-field"
                    id="username"
                    placeholder="Email atau Nomor Induk Pegawai"
                    autocomplete="current-password">
            </div>
            @error('username')
            <div class="validation-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group" x-data="{ show: false }">
            <label for="password" class="form-label">Kata Sandi</label>
            <div class="input-container">
                <div class="input-icon">
                    <i class="mdi mdi-lock-outline"></i>
                </div>
                <input
                    wire:model.defer="password"
                    :type="show ? 'text' : 'password'"
                    class="input-field"
                    id="password"
                    placeholder="Masukkan kata sandi"
                    autocomplete="off">
                <div class="password-toggle" @click="show = !show" style="cursor:pointer;">
                    <i :class="show ? 'mdi mdi-eye-outline' : 'mdi mdi-eye-off-outline'"></i>
                </div>
            </div>
            @error('password')
            <div class="validation-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-options">
            <div class="form-check">
                <input wire:model="remember" type="checkbox" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>
            <a href="#" class="forgot-password">Lupa kata sandi?</a>
        </div>

        <button type="submit" class="btn btn-login" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="login">
                <i class="mdi mdi-login me-2"></i> Login
            </span>

            <span wire:loading wire:target="login" style="display: none;">
                <i class="mdi mdi-loading mdi-spin me-2"></i> Memproses...
            </span>
        </button>
    </form>
</div>