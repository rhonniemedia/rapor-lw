<div>
    <div class="card mb-4">
        <div class="profile-header">
            <div class="d-flex flex-column flex-md-row align-items-center">
                <div class="profile-avatar me-md-4">
                    @if($sekolah && $sekolah->logo_sekolah_url)
                    <img src="{{ $sekolah->logo_sekolah_url }}" alt="Logo Sekolah" style="width: 100%; height: 100%; object-fit: contain;">
                    @else
                    <i class="mdi mdi-building"></i>
                    @endif
                </div>
                <div class="profile-info text-center text-md-start">
                    <h2>{{ $sekolah->nama_sekolah ?? 'Belum Ada Data Sekolah' }}</h2>
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-3 mt-2">
                        <span class="badge-accreditation">Akreditasi A</span>
                        <span class="status-badge">Aktif</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Upload Logo Section -->
            <div class="upload-section">
                <h5 class="section-title">Upload Logo</h5>
                <p class="text-muted mb-4">
                    Upload dan kelola logo sekolah serta logo pemerintah daerah. Format yang didukung: JPG, PNG, SVG.
                    Ukuran maksimal 2MB per file.
                </p>

                <div class="logo-upload-grid">
                    <!-- Logo Sekolah -->
                    <div class="logo-upload-card">
                        <div class="logo-upload-icon">
                            @if($sekolah && $sekolah->logo_sekolah_url)
                            <img src="{{ $sekolah->logo_sekolah_url }}" alt="Logo Sekolah" style="width: 80px; height: 80px; object-fit: contain;">
                            @else
                            <i class="mdi mdi-building"></i>
                            @endif
                        </div>
                        <h6 class="logo-upload-title">Logo Sekolah</h6>
                        <p class="logo-upload-desc">
                            Rekomendasi: 300×300 px<br>
                            Format: PNG dengan background transparan
                        </p>

                        <div wire:loading wire:target="logo_sekolah" class="mb-2">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <small class="text-muted d-block">Memproses...</small>
                        </div>

                        <input type="file" wire:model="logo_sekolah" id="logoSekolahInput" accept="image/jpeg,image/jpg,image/png,image/svg+xml" style="display: none;">

                        <button class="btn btn-primary btn-sm" onclick="document.getElementById('logoSekolahInput').click()" wire:loading.attr="disabled" wire:target="logo_sekolah">
                            <i class="mdi mdi-cloud-arrow-up me-2"></i>Upload Logo Sekolah
                        </button>

                        @error('logo_sekolah')
                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                        @enderror

                        @if($sekolah && $sekolah->logo_sekolah_path)
                        <button class="btn btn-danger btn-sm mt-2" wire:click="confirmDeleteLogo('sekolah')">
                            <i class="mdi mdi-trash me-2"></i>Hapus Logo
                        </button>
                        @endif
                    </div>

                    <!-- Logo Pemda -->
                    <div class="logo-upload-card">
                        <div class="logo-upload-icon">
                            @if($sekolah && $sekolah->logo_pemda_url)
                            <img src="{{ $sekolah->logo_pemda_url }}" alt="Logo Pemda" style="width: 80px; height: 80px; object-fit: contain;">
                            @else
                            <i class="mdi mdi-shield-check"></i>
                            @endif
                        </div>
                        <h6 class="logo-upload-title">Logo Pemerintah Daerah</h6>
                        <p class="logo-upload-desc">
                            Rekomendasi: 300×300 px<br>
                            Format: PNG dengan background transparan
                        </p>

                        <div wire:loading wire:target="logo_pemda" class="mb-2">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <small class="text-muted d-block">Memproses...</small>
                        </div>

                        <input type="file" wire:model="logo_pemda" id="logoPemdaInput" accept="image/jpeg,image/jpg,image/png,image/svg+xml" style="display: none;">

                        <button class="btn btn-primary btn-sm" onclick="document.getElementById('logoPemdaInput').click()" wire:loading.attr="disabled" wire:target="logo_pemda">
                            <i class="mdi mdi-cloud-arrow-up me-2"></i>Upload Logo Pemda
                        </button>

                        @error('logo_pemda')
                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                        @enderror

                        @if($sekolah && $sekolah->logo_pemda_path)
                        <button class="btn btn-danger btn-sm mt-2" wire:click="confirmDeleteLogo('pemda')">
                            <i class="mdi mdi-trash me-2"></i>Hapus Logo
                        </button>
                        @endif
                    </div>
                </div>

                <div class="mt-4">
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="mdi mdi-info-circle me-3 fs-5"></i>
                        <div>
                            <strong>Tips Upload Logo:</strong> Gunakan format PNG dengan background transparan untuk hasil terbaik.
                            Pastikan logo tidak terdistorsi dan memiliki kualitas yang jelas.
                        </div>
                    </div>
                </div>
            </div>

            <!-- School Info Section -->
            @if($sekolah)
            <div class="info-section">
                <div class="row mb-4 align-items-center">
                    <div class="col-md-11">
                        <h5 class="section-title">Informasi Sekolah</h5>
                    </div>
                    <div class="col-md-1 d-flex justify-content-end">
                        <div class="dropdown">
                            <button
                                type="button"
                                class="btn btn-outline-light-muted btn-sm"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                title="Options"
                                style="padding: 0.5rem; width: 2.5rem; height: 2.5rem; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                <i class="mdi mdi-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <button class="dropdown-item" wire:click="openEditDataModal">
                                        <i class="mdi mdi-pencil me-2"></i>Edit Data Sekolah
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Informasi Sekolah dalam Grid Row -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">Nama Sekolah</label>
                        <input type="text" class="form-control" value="{{ $sekolah->nama_sekolah }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">NPSN</label>
                        <input type="text" class="form-control" value="{{ $sekolah->npsn }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">NIS</label>
                        <input type="text" class="form-control" value="{{ $sekolah->nis }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">NSS</label>
                        <input type="text" class="form-control" value="{{ $sekolah->nss }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">NDS</label>
                        <input type="text" class="form-control" value="{{ $sekolah->nds }}" readonly />
                    </div>
                </div>

                <div class="row mb-2 align-items-center">
                    <div class="col-md-11">
                        <h5 class="section-title">Informasi Kontak</h5>
                    </div>
                    <div class="col-md-1 d-flex justify-content-end">
                        <div class="dropdown">
                            <button
                                type="button"
                                class="btn btn-outline-light-muted btn-sm"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                title="Options"
                                style="padding: 0.5rem; width: 2.5rem; height: 2.5rem; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                <i class="mdi mdi-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li>
                                    <button class="dropdown-item" wire:click="openEditContactModal">
                                        <i class="mdi mdi-geo-alt me-2"></i>Edit Informasi Kontak
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Informasi Kontak -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">Alamat</label>
                        <input type="text" class="form-control" value="{{ $sekolah->alamat }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">Kelurahan</label>
                        <input type="text" class="form-control" value="{{ $sekolah->kelurahan }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">Kecamatan</label>
                        <input type="text" class="form-control" value="{{ $sekolah->kecamatan }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">Kota/Kabupaten</label>
                        <input type="text" class="form-control" value="{{ $sekolah->kota_kabupaten }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">Provinsi</label>
                        <input type="text" class="form-control" value="{{ $sekolah->provinsi }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">Kode Pos</label>
                        <input type="text" class="form-control" value="{{ $sekolah->kode_pos }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">Telepon</label>
                        <input type="text" class="form-control" value="{{ $sekolah->telepon }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">Email</label>
                        <input type="text" class="form-control" value="{{ $sekolah->email }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">Website</label>
                        <input type="text" class="form-control" value="{{ $sekolah->website }}" readonly />
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-warning">
                <i class="mdi mdi-exclamation-triangle me-2"></i>
                Belum ada data sekolah. Silakan hubungi administrator untuk menambahkan data.
            </div>
            @endif
        </div>
    </div>

    <!-- Modal Edit Data Sekolah -->
    @if($showEditDataModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="mdi mdi-pencil me-2"></i>Edit Data Sekolah
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showEditDataModal', false)"></button>
                </div>
                <form wire:submit.prevent="updateData">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Sekolah <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_sekolah') is-invalid @enderror" wire:model.defer="nama_sekolah">
                            @error('nama_sekolah')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NPSN</label>
                            <input type="text" class="form-control @error('npsn') is-invalid @enderror" wire:model.defer="npsn">
                            @error('npsn')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NIS</label>
                            <input type="text" class="form-control @error('nis') is-invalid @enderror" wire:model.defer="nis">
                            @error('nis')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NSS</label>
                            <input type="text" class="form-control @error('nss') is-invalid @enderror" wire:model.defer="nss">
                            @error('nss')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NDS</label>
                            <input type="text" class="form-control @error('nds') is-invalid @enderror" wire:model.defer="nds">
                            @error('nds')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-labeled btn-outline-secondary"
                            wire:click="$set('showEditDataModal', false)">
                            <span class="btn-label">
                                <i class="mdi mdi-close"></i>
                            </span>
                            Batal
                        </button>

                        <button type="submit"
                            class="btn btn-labeled btn-primary"
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
    @endif

    <!-- Modal Edit Kontak -->
    @if($showEditContactModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="mdi mdi-pencil me-2"></i>Edit Informasi Kontak
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showEditContactModal', false)"></button>
                </div>
                <form wire:submit.prevent="updateContact">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" wire:model.defer="alamat" rows="3"></textarea>
                            @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kelurahan</label>
                            <input type="text" class="form-control @error('kelurahan') is-invalid @enderror" wire:model.defer="kelurahan">
                            @error('kelurahan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kecamatan</label>
                            <input type="text" class="form-control @error('kecamatan') is-invalid @enderror" wire:model.defer="kecamatan">
                            @error('kecamatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kota/Kabupaten</label>
                                    <input type="text" class="form-control @error('kota_kabupaten') is-invalid @enderror" wire:model.defer="kota_kabupaten">
                                    @error('kota_kabupaten')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Provinsi</label>
                                    <input type="text" class="form-control @error('provinsi') is-invalid @enderror" wire:model.defer="provinsi">
                                    @error('provinsi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kode Pos</label>
                            <input type="text" class="form-control @error('kode_pos') is-invalid @enderror" wire:model.defer="kode_pos">
                            @error('kode_pos')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telepon</label>
                            <input type="text" class="form-control @error('telepon') is-invalid @enderror" wire:model.defer="telepon">
                            @error('telepon')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model.defer="email">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Website</label>
                            <input type="text" class="form-control @error('website') is-invalid @enderror" wire:model.defer="website">
                            @error('website')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-labeled btn-outline-secondary"
                            wire:click="$set('showEditContactModal', false)">
                            <span class="btn-label">
                                <i class="mdi mdi-close"></i>
                            </span>
                            Batal
                        </button>

                        <button type="submit"
                            class="btn btn-labeled btn-primary"
                            wire:loading.attr="disabled"
                            wire:target="updateContact">

                            <span class="btn-label">
                                <i class="mdi mdi-loading mdi-spin d-none"
                                    wire:loading.class.remove="d-none"
                                    wire:target="updateContact"></i>

                                <i class="mdi mdi-content-save"
                                    wire:loading.class="d-none"
                                    wire:target="updateContact"></i>
                            </span>

                            <span wire:loading.class="d-none" wire:target="updateContact">
                                Simpan
                            </span>

                            <span class="d-none"
                                wire:loading.class.remove="d-none"
                                wire:target="updateContact">
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    // SweetAlert Success Handler - Livewire 3
    document.addEventListener('swal:success', (event) => {
        Swal.fire({
            icon: 'success',
            title: event.detail[0].title || 'Berhasil!',
            text: event.detail[0].text,
            showConfirmButton: false,
            timer: 1500
        });
    });

    // SweetAlert Error Handler - Livewire 3
    document.addEventListener('swal:error', (event) => {
        Swal.fire({
            icon: 'error',
            title: event.detail[0].title || 'Oops...',
            text: event.detail[0].text
        });
    });

    // SweetAlert Confirm Delete Handler - Livewire 3
    document.addEventListener('swal:confirm-delete', (event) => {
        Swal.fire({
            title: event.detail[0].title,
            text: event.detail[0].text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                if (event.detail[0].type === 'sekolah') {
                    Livewire.find(@this.getId()).call('deleteLogoSekolah');
                } else {
                    Livewire.find(@this.getId()).call('deleteLogoPemda');
                }
            }
        });
    });

    // Auto-upload when file is selected
    document.addEventListener('DOMContentLoaded', function() {
        // Logo Sekolah Upload
        const logoSekolahInput = document.getElementById('logoSekolahInput');
        if (logoSekolahInput) {
            logoSekolahInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    Swal.fire({
                        title: 'Mengupload...',
                        text: 'Mohon tunggu',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    Livewire.find(@this.getId()).uploadLogoSekolah();
                }
            });
        }

        // Logo Pemda Upload
        const logoPemdaInput = document.getElementById('logoPemdaInput');
        if (logoPemdaInput) {
            logoPemdaInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    Swal.fire({
                        title: 'Mengupload...',
                        text: 'Mohon tunggu',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    Livewire.find(@this.getId()).uploadLogoPemda();
                }
            });
        }
    });

    // Close loading when upload completes - Livewire 3
    document.addEventListener('livewire:init', () => {
        Livewire.hook('commit', ({
            component,
            commit,
            respond,
            succeed,
            fail
        }) => {
            succeed(({
                snapshot,
                effect
            }) => {
                if (Swal.isVisible() && Swal.isLoading()) {
                    Swal.close();
                }
            });
        });
    });
</script>
@endpush