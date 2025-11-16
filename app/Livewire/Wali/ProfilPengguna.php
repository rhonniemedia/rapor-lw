<?php

namespace App\Livewire\Wali;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\WithFileUploads;

class ProfilPengguna extends Component
{
    use WithFileUploads;

    public $user;
    public $userId;

    // Data untuk edit
    public $email;
    public $telephone;

    // Data untuk password
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    // Upload photo
    public $photo;

    protected function rules()
    {
        return [
            'email' => 'required|email|unique:users,email_hash,' . $this->userId . ',id',
            'telephone' => 'nullable|numeric|digits_between:10,15',
        ];
    }

    protected $messages = [
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah digunakan oleh user lain.',
        'telephone.numeric' => 'Telepon harus berupa angka.',
        'telephone.digits_between' => 'Telepon harus 10-15 digit.',
    ];

    public function mount($userId = null)
    {
        // Jika userId tidak diberikan, gunakan user yang sedang login
        $this->userId = $userId ?? Auth::id();
        $this->loadUser();
    }

    public function loadUser()
    {
        $this->user = User::with('roles')->find($this->userId);

        if (!$this->user) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'message' => 'User tidak ditemukan.'
            ]);
            return;
        }

        // Load data untuk edit
        $this->email = $this->user->email;
        $this->telephone = $this->user->telephone;
    }

    public function openEditDataModal()
    {
        // Reload data terbaru
        $this->loadUser();
        $this->dispatch('show-edit-data-modal');
    }

    public function openEditPasswordModal()
    {
        // Reset password fields
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->dispatch('show-edit-password-modal');
    }

    public function updateData()
    {
        $this->validate();

        try {
            $user = User::find($this->userId);

            if (!$user) {
                throw new \Exception('User tidak ditemukan.');
            }

            // Cek apakah ada perubahan
            $hasChanges = false;

            if ($this->email !== $user->email) {
                $user->email = $this->email;
                $hasChanges = true;
            }

            if ($this->telephone !== $user->telephone) {
                $user->telephone = $this->telephone;
                $hasChanges = true;
            }

            if (!$hasChanges) {
                $this->dispatch('show-alert', [
                    'type' => 'info',
                    'message' => 'Tidak ada perubahan data.'
                ]);
                $this->dispatch('close-edit-data-modal');
                return;
            }

            $user->save();

            // Reload user data
            $this->loadUser();

            $this->dispatch('show-alert', [
                'type' => 'success',
                'message' => 'Data berhasil diperbarui.'
            ]);

            $this->dispatch('close-edit-data-modal');
        } catch (\Exception $e) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ]);
        }
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|different:current_password',
            'new_password_confirmation' => 'required|same:new_password',
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.different' => 'Password baru harus berbeda dengan password lama.',
            'new_password_confirmation.required' => 'Konfirmasi password wajib diisi.',
            'new_password_confirmation.same' => 'Konfirmasi password tidak cocok.',
        ]);

        try {
            $user = User::find($this->userId);

            if (!$user) {
                throw new \Exception('User tidak ditemukan.');
            }

            // Verifikasi password lama
            if (!Hash::check($this->current_password, $user->password)) {
                $this->addError('current_password', 'Password lama tidak sesuai.');
                return;
            }

            // Update password
            $user->password = Hash::make($this->new_password);
            $user->save();

            // Reset form
            $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

            $this->dispatch('show-alert', [
                'type' => 'success',
                'message' => 'Password berhasil diperbarui.'
            ]);

            $this->dispatch('close-edit-password-modal');
        } catch (\Exception $e) {
            $this->dispatch('show-alert', [
                'type' => 'error',
                'message' => 'Gagal memperbarui password: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.wali.profil-pengguna');
    }
}
