<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DataPengguna extends Component
{
    use WithPagination;

    // Properti untuk pencarian dan paginasi
    public $search = '';
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';

    // Properti untuk Modal (CRUD)
    public $isModalOpen = false;
    public $isResetPasswordModalOpen = false;
    public $mode = 'create'; // 'create' atau 'edit'
    public $userId;

    // Properti untuk Form Pengguna
    public $name, $email, $telephone, $nip, $roleName;
    public $status = 'aktif';

    // Properti untuk Reset Password
    public $resetUserId;
    public $resetMode = 'manual'; // Default: manual atau default
    public $new_password, $new_password_confirmation;

    // Mendefinisikan aturan validasi
    protected function rules()
    {
        // Mendapatkan nomor telepon yang sudah bersih
        $telephoneCleaned = preg_replace('/[^0-9]/', '', $this->telephone);

        $rules = [
            'name' => 'required|string|max:255',
            // Telepon WAJIB ADA untuk generate password
            'telephone' => [
                'required',
                'digits_between:10,15',
                // Cek unik berdasarkan telephone_hash (asumsi mutator di model sudah ada)
                Rule::unique('users')->where(function ($query) use ($telephoneCleaned) {
                    $telephoneHash = hash('sha256', $telephoneCleaned);
                    return $query->where('telephone_hash', $telephoneHash);
                })->ignore($this->userId),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                // Cek unik berdasarkan email_hash.
                Rule::unique('users')->where(function ($query) {
                    $emailHash = hash('sha256', Str::lower($this->email));
                    return $query->where('email_hash', $emailHash);
                })->ignore($this->userId),
            ],
            'nip' => [
                'nullable',
                'digits_between:10,18',
                Rule::unique('users', 'nip_hash')->where(function ($query) {
                    $nipHash = hash('sha256', $this->nip);
                    return $query->where('nip_hash', $nipHash);
                })->ignore($this->userId),
            ],
            'status' => 'required|in:aktif,nonaktif',
            'roleName' => 'required|exists:roles,name',
        ];

        return $rules;
    }

    protected $validationAttributes = [
        'name' => 'Nama Lengkap',
        'email' => 'Email',
        'telephone' => 'Telepon',
        'nip' => 'NIP',
        'roleName' => 'Role',
        'new_password' => 'Password Baru',
        'new_password_confirmation' => 'Konfirmasi Password Baru',
    ];

    protected $listeners = [
        'createPengguna' => 'create',
        'editPengguna' => 'edit',
        'resetPassword' => 'openResetPasswordModal',
    ];

    public function mount()
    {
        $this->roleName = Role::first()->name ?? '';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // --- MANAJEMEN MODAL ---

    public function create()
    {
        $this->resetForm();
        $this->mode = 'create';
        $this->isModalOpen = true;
        $this->dispatch('show-create-edit-modal');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->telephone = $user->telephone;
        $this->nip = $user->nip;
        $this->status = $user->status;
        $this->roleName = $user->roles->first()->name ?? '';

        $this->mode = 'edit';
        $this->isModalOpen = true;
        $this->dispatch('show-create-edit-modal');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
        $this->dispatch('close-create-edit-modal');
    }

    public function openResetPasswordModal($id)
    {
        $this->reset('new_password', 'new_password_confirmation');
        $this->resetUserId = $id;
        $this->resetMode = 'default'; // Set default mode saat membuka
        $this->isResetPasswordModalOpen = true;
        $this->dispatch('show-reset-password-modal');
    }

    public function closeResetPasswordModal()
    {
        $this->isResetPasswordModalOpen = false;
        $this->reset('resetUserId', 'new_password', 'new_password_confirmation', 'resetMode');
        $this->resetMode = 'default'; // Reset mode ke default saat ditutup
        $this->dispatch('close-reset-password-modal');
    }

    private function resetForm()
    {
        $this->resetErrorBag();
        $this->reset(
            'userId',
            'name',
            'email',
            'telephone',
            'nip',
            'status',
        );
        $this->status = 'aktif';
    }

    // --- FUNGSI CRUD ---

    public function store()
    {
        $this->validate();

        $telephoneCleaned = preg_replace('/[^0-9]/', '', $this->telephone);

        // 1. Definisikan hanya kolom-kolom inti yang selalu diupdate/dibuat
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'telephone' => $telephoneCleaned,
            'nip' => $this->nip,
            'status' => $this->status,
        ];

        try {
            if ($this->mode === 'create') {
                // 2. Saat CREATE, tambahkan kolom guru dengan nilai default eksplisit
                $data['is_teacher'] = false;
                $data['is_guru_agama'] = false;
                $data['spesialisasi_agama'] = null;

                $generatedPassword = 'Pass' . $telephoneCleaned . '*';
                $data['password'] = Hash::make($generatedPassword);

                $user = User::create($data);
                $user->syncRoles([$this->roleName]);

                $this->dispatch('show-alert', [
                    'type' => 'success',
                    'message' => "Pengguna **{$this->name}** berhasil ditambahkan! Password awal adalah: **{$generatedPassword}**"
                ]);
            } elseif ($this->mode === 'edit') {
                // 3. Saat EDIT, gunakan $data yang hanya berisi kolom inti
                //    Kolom is_teacher, is_guru_agama, dll. TIDAK ADA di array $data,
                //    sehingga nilainya yang ada di database tidak akan ditimpa (unchanged).

                $user = User::findOrFail($this->userId);

                // Note: $user->update($data) hanya akan memengaruhi key yang ada di $data.
                $user->update($data);

                $user->syncRoles([$this->roleName]);

                $this->dispatch('show-alert', ['type' => 'success', 'message' => 'Data pengguna **' . $this->name . '** berhasil diubah!']);
            }

            $this->closeModal();
        } catch (\Exception $e) {
            $this->dispatch('show-alert', ['type' => 'error', 'message' => 'Gagal menyimpan data pengguna.']);
            // Tambahkan Log::error($e->getMessage()) jika perlu debugging
        }
    }

    /**
     * Reset password pengguna dengan opsi default atau manual
     */
    public function updatePassword()
    {
        $user = User::findOrFail($this->resetUserId);

        if ($this->resetMode === 'manual') {
            // Validasi untuk reset manual
            $this->validate([
                'new_password' => 'required|string|min:8|confirmed',
                'new_password_confirmation' => 'required|string|min:8',
            ], [], [
                'new_password' => 'Password Baru',
                'new_password_confirmation' => 'Konfirmasi Password Baru',
            ]);

            $newPassword = $this->new_password;
            $user->password = Hash::make($newPassword);
            $message = 'Password pengguna **' . $user->name . '** berhasil direset secara manual.';
        } elseif ($this->resetMode === 'default') {
            // Reset ke default: Pass<telepon>*
            $telephoneCleaned = preg_replace('/[^0-9]/', '', $user->telephone);

            // Cek apakah nomor telepon tersedia
            if (empty($telephoneCleaned)) {
                $this->dispatch('show-alert', ['type' => 'warning', 'message' => 'Gagal mereset default. Nomor telepon pengguna tidak ditemukan.']);
                return;
            }

            $defaultPassword = 'Pass' . $telephoneCleaned . '*';
            $user->password = Hash::make($defaultPassword);
            $message = "Password pengguna **{$user->name}** berhasil direset ke default. Password barunya adalah: **{$defaultPassword}**";
        } else {
            return; // Mode tidak valid
        }

        try {
            $user->save();
            $this->dispatch('show-alert', ['type' => 'success', 'message' => $message]);
            $this->closeResetPasswordModal();
        } catch (\Exception $e) {
            $this->dispatch('show-alert', ['type' => 'error', 'message' => 'Gagal mereset password.']);
        }
    }

    // --- RENDER ---

    public function render()
    {
        $query = User::query()
            ->with('roles')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email_hash', hash('sha256', Str::lower($this->search)))
                    ->orWhere('nip_hash', hash('sha256', $this->search));
            });

        $users = $query->paginate($this->perPage);
        $roles = Role::pluck('name')->all();

        return view('livewire.admin.data-pengguna', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }
}
