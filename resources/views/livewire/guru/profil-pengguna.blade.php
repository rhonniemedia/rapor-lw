<div>
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-avatar">
            @if($user)
            {{ strtoupper(substr($user->name, 0, 2)) }}
            @else
            --
            @endif
        </div>
        <div class="profile-info">
            <h2>{{ $user->name ?? 'User' }}</h2>
            <p>
                @if($user && $user->roles->isNotEmpty())
                {{ ucwords($user->roles->first()->name) }}
                @else
                User
                @endif
            </p>
        </div>
    </div>

    <!-- Upload Photo Section -->
    <div class="upload-section">
        <h5 class="section-title">Upload new photo</h5>
        <p class="text-muted mb-3">
            At least 800×900 px recommended.<br />JPG or PNG is allowed.
        </p>
        <p><small class="text-muted">Fitur ini sedang dalam tahap pengembangan.</small></p>
        <div class="upload-btn">
            <div class="upload-icon">
                <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <div>Click to upload or drag and drop</div>
        </div>
    </div>

    <!-- Personal Info Section -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-6">
            <h5 class="section-title">Personal Info</h5>
        </div>
        <div class="col-md-6 d-flex justify-content-end">
            <!-- Dropdown Edit Button -->
            <div class="dropdown">
                <button
                    type="button"
                    class="btn btn-outline-light-muted btn-sm"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    title="Options"
                    style="padding: 0.25rem 0.5rem; width: 2.25rem; height: calc(2.25rem + 2px); display: flex; align-items: center; justify-content: center;">
                    <i class="mdi mdi-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <button class="dropdown-item" wire:click="openEditDataModal">
                            <i class="mdi mdi-pencil me-2"></i>Edit Data Pengguna
                        </button>
                    </li>
                    <li>
                        <button class="dropdown-item" wire:click="openEditPasswordModal">
                            <i class="mdi mdi-lock-reset me-2"></i>Edit Password
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <label class="form-label">Full Name</label>
            <input
                type="text"
                class="form-control"
                value="{{ $user->name ?? '' }}"
                readonly />
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Email</label>
            <input
                type="email"
                class="form-control"
                value="{{ $user->email ?? '' }}"
                readonly />
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Phone</label>
            <input
                type="text"
                class="form-control"
                value="{{ $user->telephone ?? '-' }}"
                readonly />
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">NIP</label>
            <input
                type="text"
                class="form-control"
                value="{{ $user->nip ?? '-' }}"
                readonly />
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Status</label>
            <input
                type="text"
                class="form-control"
                value="{{ ucfirst($user->status ?? 'aktif') }}"
                readonly />
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Role</label>
            <input
                type="text"
                class="form-control"
                value="@if($user && $user->roles->isNotEmpty()){{ $user->roles->pluck('name')->join(', ') }}@else-@endif"
                readonly />
        </div>
    </div>

    {{-- MODAL EDIT DATA PENGGUNA --}}
    <div wire:ignore.self class="modal fade" id="editDataModal" tabindex="-1" aria-labelledby="editDataModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDataModalLabel">
                        <i class="mdi mdi-pencil-circle me-2"></i>Edit Data Pengguna
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="updateData">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input
                                type="email"
                                wire:model="email"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="Masukkan email">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telepon</label>
                            <input
                                type="text"
                                wire:model="telephone"
                                class="form-control @error('telephone') is-invalid @enderror"
                                placeholder="Masukkan nomor telepon (10-15 digit)">
                            @error('telephone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format: angka saja, 10-15 digit</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-labeled btn-outline-secondary"
                            data-bs-dismiss="modal">
                            <span class="btn-label">
                                <i class="mdi mdi-close"></i>
                            </span>
                            Batal
                        </button>

                        <button type="button"
                            class="btn btn-labeled btn-primary"
                            wire:click="updateData"
                            wire:loading.attr="disabled"
                            wire:target="updateData">

                            <span class="btn-label">
                                <i class="mdi mdi-loading mdi-spin d-none"
                                    wire:loading.class.remove="d-none"
                                    wire:target="updateData"></i>

                                <i class="mdi mdi-content-save"
                                    wire:loading.class="d-none"
                                    wire:target="updateData"></i>
                            </span>

                            <span wire:loading.class="d-none" wire:target="updateData">
                                Simpan
                            </span>

                            <span class="d-none"
                                wire:loading.class.remove="d-none"
                                wire:target="updateData">
                                Menyimpan...
                            </span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT PASSWORD DENGAN TOGGLE EYE --}}
    <div wire:ignore.self class="modal fade" id="editPasswordModal" tabindex="-1" aria-labelledby="editPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPasswordModalLabel">
                        <i class="mdi mdi-lock-reset me-2"></i>Edit Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="updatePassword">
                    <div class="modal-body">
                        {{-- Password Lama --}}
                        <div class="mb-3">
                            <label class="form-label">Password Lama <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input
                                    type="password"
                                    id="current_password"
                                    wire:model="current_password"
                                    class="form-control pe-5 @error('current_password') is-invalid @enderror"
                                    placeholder="Masukkan password lama">
                                <button
                                    type="button"
                                    class="btn btn-sm position-absolute end-0 top-50 translate-middle-y me-2"
                                    onclick="togglePasswordVisibility('current_password')"
                                    style="background: none; border: none; padding: 0.25rem 0.5rem;">
                                    <i class="mdi mdi-eye" id="current_password_icon"></i>
                                </button>
                                @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Password Baru --}}
                        <div class="mb-3">
                            <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input
                                    type="password"
                                    id="new_password"
                                    wire:model="new_password"
                                    class="form-control pe-5 @error('new_password') is-invalid @enderror"
                                    placeholder="Masukkan password baru">
                                <button
                                    type="button"
                                    class="btn btn-sm position-absolute end-0 top-50 translate-middle-y me-2"
                                    onclick="togglePasswordVisibility('new_password')"
                                    style="background: none; border: none; padding: 0.25rem 0.5rem;">
                                    <i class="mdi mdi-eye" id="new_password_icon"></i>
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
                                    id="new_password_confirmation"
                                    wire:model="new_password_confirmation"
                                    class="form-control pe-5 @error('new_password_confirmation') is-invalid @enderror"
                                    placeholder="Masukkan ulang password baru">
                                <button
                                    type="button"
                                    class="btn btn-sm position-absolute end-0 top-50 translate-middle-y me-2"
                                    onclick="togglePasswordVisibility('new_password_confirmation')"
                                    style="background: none; border: none; padding: 0.25rem 0.5rem;">
                                    <i class="mdi mdi-eye" id="new_password_confirmation_icon"></i>
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
                                <li>Jangan gunakan informasi pribadi</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-labeled btn-outline-secondary"
                            data-bs-dismiss="modal">
                            <span class="btn-label">
                                <i class="mdi mdi-close"></i>
                            </span>
                            Batal
                        </button>

                        <button type="button"
                            class="btn btn-labeled btn-primary"
                            wire:click="updatePassword"
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

    @push('scripts')
    <script>
        // Show edit data modal
        window.addEventListener('show-edit-data-modal', event => {
            var modal = new bootstrap.Modal(document.getElementById('editDataModal'));
            modal.show();
        });

        // Close edit data modal
        window.addEventListener('close-edit-data-modal', event => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('editDataModal'));
            if (modal) {
                modal.hide();
            }
        });

        // Show edit password modal
        window.addEventListener('show-edit-password-modal', event => {
            var modal = new bootstrap.Modal(document.getElementById('editPasswordModal'));
            modal.show();
        });

        // Close edit password modal
        window.addEventListener('close-edit-password-modal', event => {
            var modal = bootstrap.Modal.getInstance(document.getElementById('editPasswordModal'));
            if (modal) {
                modal.hide();
            }
        });

        // SweetAlert2 Handler
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
                text: data.message,
                confirmButtonText: 'OK',
                confirmButtonColor: '#0d6efd'
            });
        });
    </script>
    <script>
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

        // Reset password visibility when modal is closed
        document.getElementById('editPasswordModal').addEventListener('hidden.bs.modal', function() {
            // Reset all password fields to hidden
            ['current_password', 'new_password', 'new_password_confirmation'].forEach(function(fieldId) {
                const passwordField = document.getElementById(fieldId);
                const iconElement = document.getElementById(fieldId + '_icon');

                if (passwordField && passwordField.type === 'text') {
                    passwordField.type = 'password';
                    iconElement.classList.remove('mdi-eye-off');
                    iconElement.classList.add('mdi-eye');
                }
            });
        });
    </script>
    @endpush
</div>