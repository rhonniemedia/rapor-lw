<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Login extends Component
{
    public $username;
    public $password;
    public $remember = false;

    protected $rules = [
        'username' => 'required|string',
        'password' => 'required|string|min:8',
    ];

    public function login()
    {
        $this->validate();

        // Cari user berdasarkan email atau NIP
        $user = User::where(function ($query) {
            if (filter_var($this->username, FILTER_VALIDATE_EMAIL)) {
                $query->where('email', $this->username);
            } else {
                $query->where('nip', $this->username);
            }
        })->first();

        // Cek user ada dan password cocok
        if ($user && Hash::check($this->password, $user->password)) {
            Auth::login($user, $this->remember);
            session()->regenerate();

            // Redirect Livewire-safe (hindari CSRF refresh)
            return redirect()->intended(route('dashboard'));
        }

        $this->addError('auth', 'Nama pengguna atau kata sandi yang Anda inputkan salah!');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
