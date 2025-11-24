<div>
    {{-- ... Kode Header, Search, dan Tabel Pengguna (TIDAK BERUBAH) ... --}}
    <div class="page-header pb-3 mb-4 border-bottom">
        <div class="d-flex align-items-center">
            <div class="icon-wrapper position-relative">
                <span class="bg-gradient-primary p-2 rounded-3 shadow-sm me-3 d-inline-flex align-items-center justify-content-center">
                    <i class="mdi mdi-account-multiple-outline mdi-24px text-white"></i>
                </span>
            </div>

            <div>
                <h4 class="mb-1 text-dark fw-bold">Daftar Pengguna</h4>
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted">Manajemen data pengguna</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Input Search & Per Page --}}
    <div class="d-flex justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <span>Show</span>
            <select class="form-select form-select-sm" wire:model.live="perPage">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <span>entries</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div>
                <input type="text" class="form-control"
                    placeholder="Cari pengguna..."
                    wire:model.live.debounce.500ms="search" style="width:250px;">
            </div>
        </div>
    </div>

    {{-- Tabel Pengguna --}}
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th style="width: 30%;">
                        <p class="mb-0">Pengguna</p>
                        <small>Nama | Jenis Role</small>
                    </th>
                    <th style="width: 30%;">
                        <p class="mb-0">Kontak</p>
                        <small>Telepon | Email</small>
                    </th>
                    <th style="width: 30%;">
                        <p class="mb-0">Status</p>
                        <small>Aktif | Nonaktif</small>
                    </th>
                    <th style="width: 10%;">
                        <p class="mb-0">Aksi</p>
                        <small>Edit | Reset</small>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <a class="hyper-link text-decoration-none" href="#">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/icons/pilot.png') }}" alt="image" />
                                <div class="table-user-name ml-3">
                                    <p class="mb-0 font-weight-medium"> {{ $user->name }} </p>
                                    <small class="text-muted font-weight-medium">
                                        @if($user->roles->isNotEmpty())
                                        {{ ucwords($user->roles->pluck('name')->join(', ')) }}
                                        @else
                                        -
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </a>
                    </td>
                    <td>
                        <p class="mb-0 font-weight-medium">{{ $user->telephone ?? '-' }}</p>
                        <small class="text-muted">{{ $user->email ?? '-' }}</small>
                    </td>
                    <td>
                        @if($user->status == 'aktif')
                        <span class="badge bg-success">Aktif</span>
                        @else
                        <span class="badge bg-danger">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <button
                            class="btn btn-sm btn-outline-warning"
                            title="Edit"
                            wire:click="$dispatch('editPengguna', { id: '{{ $user->id }}' })">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                        <button
                            class="btn btn-sm btn-outline-success"
                            title="Reset Password"
                            wire:click="$dispatch('resetPassword', { id: '{{ $user->id }}' })">
                            <i class="mdi mdi-lock-reset"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Data pengguna tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $users->onEachSide(1)->links() }}
    </div>

    {{-- MODAL TAMBAH/EDIT PENGGUNA (TIDAK BERUBAH) --}}
    <div wire:ignore.self class="modal fade" id="createEditModal" tabindex="-1" aria-labelledby="createEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createEditModalLabel">
                        <i class="mdi mdi-account-plus me-2"></i>
                        @if($mode == 'create') Tambah Pengguna Baru @else Edit Data Pengguna @endif
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="closeModal"></button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="Masukkan nama lengkap">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select wire:model="roleName" class="form-select @error('roleName') is-invalid @enderror">
                                    <option value="">Pilih Role</option>
                                    @foreach($roles as $role)
                                    <option value="{{ $role }}">{{ ucwords($role) }}</option>
                                    @endforeach
                                </select>
                                @error('roleName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan email">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telepon <span class="text-danger">*</span></label>
                                <input type="text" wire:model="telephone" class="form-control @error('telephone') is-invalid @enderror" placeholder="Masukkan nomor telepon">
                                @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @if($mode == 'create')
                                <small class="text-muted fst-italic mt-1">
                                    **Password Awal akan di-generate otomatis: `Pass` + Telepon + `*`**.
                                </small>
                                @else
                                <small class="text-muted">Format: angka saja, 10-15 digit</small>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIP</label>
                                <input type="text" wire:model="nip" class="form-control @error('nip') is-invalid @enderror" placeholder="Masukkan NIP (jika ada)">
                                @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select wire:model="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-labeled btn-outline-secondary" data-bs-dismiss="modal" wire:click="closeModal">
                            <span class="btn-label"><i class="mdi mdi-close"></i></span>Batal
                        </button>
                        <button type="submit" class="btn btn-labeled btn-primary" wire:loading.attr="disabled" wire:target="store">
                            <span class="btn-label">
                                <i class="mdi mdi-loading mdi-spin d-none" wire:loading.class.remove="d-none" wire:target="store"></i>
                                <i class="mdi mdi-content-save" wire:loading.class="d-none" wire:target="store"></i>
                            </span>
                            <span wire:loading.class="d-none" wire:target="store">Simpan</span>
                            <span class="d-none" wire:loading.class.remove="d-none" wire:target="store">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- MODAL RESET PASSWORD BARU DENGAN OPSI DROPDOWN --}}
    <div wire:ignore.self class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resetPasswordModalLabel">
                        <i class="mdi mdi-lock-reset me-2"></i>Reset Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="closeResetPasswordModal"></button>
                </div>
                <form wire:submit.prevent="updatePassword">
                    <div class="modal-body">
                        {{-- Dropdown Pilihan Mode Reset --}}
                        <div class="mb-3">
                            <label class="form-label">Opsi Reset Password <span class="text-danger">*</span></label>
                            <select wire:model.live="resetMode" class="form-select">
                                {{-- URUTAN OPSI DIUBAH: Default di atas --}}
                                <option value="default">Reset Default (Pass+Telepon+*)</option>
                                <option value="manual">Reset Manual (Tentukan Password Baru)</option>
                            </select>
                        </div>

                        {{-- Tampilan berdasarkan resetMode --}}
                        @if($resetMode === 'default')
                        <hr class="my-3">
                        <div class="alert alert-warning">
                            <i class="mdi mdi-alert-outline me-2"></i>
                            <strong>Perhatian:</strong>
                            <p class="mb-0">Password akan direset ke format default: "Pass + Nomor Telepon + *". Pastikan nomor telepon sudah benar.</p>
                        </div>
                        @else
                        {{-- Form reset manual --}}
                        <hr class="my-3">
                        <p class="text-muted">Masukkan password baru secara manual.</p>
                        {{-- Password Baru --}}
                        <div class="mb-3">
                            <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input
                                    type="password"
                                    id="new_password_reset"
                                    wire:model="new_password"
                                    class="form-control pe-5 @error('new_password') is-invalid @enderror"
                                    placeholder="Masukkan password baru">
                                <button
                                    type="button"
                                    class="btn btn-sm position-absolute end-0 top-50 translate-middle-y me-2"
                                    onclick="togglePasswordVisibility('new_password_reset')"
                                    style="background: none; border: none; padding: 0.25rem 0.5rem;">
                                    <i class="mdi mdi-eye" id="new_password_reset_icon"></i>
                                </button>
                                @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Minimal 8 karakter</small>
                        </div>

                        {{-- Konfirmasi Password Baru --}}
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input
                                    type="password"
                                    id="new_password_confirmation_reset"
                                    wire:model="new_password_confirmation"
                                    class="form-control pe-5 @error('new_password_confirmation') is-invalid @enderror"
                                    placeholder="Masukkan ulang password baru">
                                <button
                                    type="button"
                                    class="btn btn-sm position-absolute end-0 top-50 translate-middle-y me-2"
                                    onclick="togglePasswordVisibility('new_password_confirmation_reset')"
                                    style="background: none; border: none; padding: 0.25rem 0.5rem;">
                                    <i class="mdi mdi-eye" id="new_password_confirmation_reset_icon"></i>
                                </button>
                                @error('new_password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Tips Keamanan --}}
                        <div class="alert alert-info">
                            <i class="mdi mdi-information-outline me-2"></i>
                            <strong>Tips Keamanan:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Gunakan kombinasi huruf besar, kecil, angka dan simbol</li>
                                <li>Minimal 8 karakter</li>
                            </ul>
                        </div>
                        @endif

                    </div>
                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-labeled btn-outline-secondary"
                            data-bs-dismiss="modal" wire:click="closeResetPasswordModal">
                            <span class="btn-label">
                                <i class="mdi mdi-close"></i>
                            </span>
                            Batal
                        </button>

                        <button type="submit"
                            class="btn btn-labeled btn-primary"
                            wire:loading.attr="disabled"
                            wire:target="updatePassword">

                            <span class="btn-label">
                                <i class="mdi mdi-loading mdi-spin d-none"
                                    wire:loading.class.remove="d-none"
                                    wire:target="updatePassword"></i>

                                <i class="mdi mdi-content-save"
                                    wire:loading.class="d-none"
                                    wire:target="updatePassword"></i>
                            </span>

                            <span wire:loading.class="d-none" wire:target="updatePassword">
                                Simpan
                            </span>

                            <span class="d-none"
                                wire:loading.class.remove="d-none"
                                wire:target="updatePassword">
                                Menyimpan...
                            </span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Show/Close Modal Create/Edit
    window.addEventListener('show-create-edit-modal', event => {
        var modal = new bootstrap.Modal(document.getElementById('createEditModal'));
        modal.show();
    });

    window.addEventListener('close-create-edit-modal', event => {
        var modal = bootstrap.Modal.getInstance(document.getElementById('createEditModal'));
        if (modal) {
            modal.hide();
        }
    });

    // Show/Close Modal Reset Password
    window.addEventListener('show-reset-password-modal', event => {
        var modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
        modal.show();
    });

    window.addEventListener('close-reset-password-modal', event => {
        var modal = bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal'));
        if (modal) {
            modal.hide();
        }
    });

    // SweetAlert2 Handler (Sama seperti sebelumnya)
    window.addEventListener('show-alert', event => {
        const data = event.detail[0];
        const icons = {
            success: 'success',
            error: 'error',
            warning: 'warning',
            info: 'info'
        };

        const titles = {
            success: 'Berhasil!',
            error: 'Gagal!',
            warning: 'Peringatan!',
            info: 'Informasi'
        };

        Swal.fire({
            icon: icons[data.type] || 'info',
            title: titles[data.type] || 'Notifikasi',
            html: data.message,
            confirmButtonText: 'OK',
            confirmButtonColor: '#0d6efd'
        });
    });

    // Function untuk toggle password visibility (Diambil dari profil-pengguna.blade.php)
    function togglePasswordVisibility(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const iconElement = document.getElementById(fieldId + '_icon');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            iconElement.classList.remove('mdi-eye');
            iconElement.classList.add('mdi-eye-off');
        } else {
            passwordField.type = 'password';
            iconElement.classList.remove('mdi-eye-off');
            iconElement.classList.add('mdi-eye');
        }
    }

    // Reset password visibility when reset modal is closed
    document.getElementById('resetPasswordModal').addEventListener('hidden.bs.modal', function() {
        // Reset only the manual password fields if they exist
        ['new_password_reset', 'new_password_confirmation_reset'].forEach(function(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const iconElement = document.getElementById(fieldId + '_icon');

            if (passwordField && passwordField.type === 'text') {
                passwordField.type = 'password';
                iconElement.classList.remove('mdi-eye-off');
                iconElement.classList.add('mdi-eye');
            }
        });
        // Reset Livewire state for input fields (optional, since it's handled by closeModal, but safer for UI)
        Livewire.dispatch('reset-password-mode-reset');
    });
</script>
@endpush