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

        // Cari user menggunakan helper method dari Model
        $user = null;

        if (filter_var($this->username, FILTER_VALIDATE_EMAIL)) {
            // Login menggunakan email
            $user = User::findByEmail($this->username);
        } else {
            // Login menggunakan NIP
            $user = User::findByNip($this->username);
        }

        // Cek user ada dan password cocok
        if ($user && Hash::check($this->password, $user->password)) {
            Auth::login($user, $this->remember);
            session()->regenerate();

            // Redirect berdasarkan role
            $roleRoutes = [
                'superadmin' => 'admin.dashboard',
                'admin'      => 'admin.dashboard',
                'walikelas'  => 'walikelas.dashboard',
                'guru'       => 'guru.dashboard',
            ];

            foreach ($roleRoutes as $role => $route) {
                if ($user->hasRole($role)) {
                    return redirect()->intended(route($route));
                }
            }

            // Fallback
            return redirect()->intended(route('admin.dashboard'));
        }

        $this->addError('auth', 'Nama pengguna atau kata sandi yang Anda inputkan salah!');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
