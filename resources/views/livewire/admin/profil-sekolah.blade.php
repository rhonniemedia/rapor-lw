<div>
    <div class="card mb-4">
        <div class="profile-header">
            <div class="d-flex flex-column flex-md-row align-items-center">
                <div class="profile-avatar me-md-4">
                    @if($sekolah && $sekolah->logo_sekolah_path)
                    <img src="{{ Storage::disk('public')->url($sekolah->logo_sekolah_path) }}" alt="Logo Sekolah" style="width: 100%; height: 100%; object-fit: contain;">
                    @else
                    <i class="mdi mdi-building"></i>
                    @endif
                </div>
                <div class="profile-info text-center text-md-start">
                    <h2>{{ $sekolah->nama_sekolah ?? 'Belum Ada Data Sekolah' }}</h2>
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-3 mt-2">
                        @if($sekolah && $sekolah->status_akreditasi)
                        <span class="badge badge-success">Akreditasi {{ strtoupper($sekolah->status_akreditasi) }}</span>
                        @else
                        <span class="badge badge-secondary">Belum Ada Akreditasi</span>
                        @endif
                        @if($sekolah && $sekolah->status_sekolah)
                        <span class="badge badge-info">{{ ucfirst($sekolah->status_sekolah) }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="upload-section">
                <h5 class="section-title">Upload Logo</h5>
                <p class="text-muted mb-4">
                    Upload dan kelola logo sekolah serta logo pemerintah daerah. Format yang didukung: JPG, PNG, SVG.
                    Ukuran maksimal 2MB per file.
                </p>

                <div class="logo-upload-grid">
                    <div class="logo-upload-card">
                        <div class="logo-upload-icon">
                            @if($sekolah && $sekolah->logo_sekolah_path)
                            <img src="{{ Storage::disk('public')->url($sekolah->logo_sekolah_path) }}" alt="Logo Sekolah" style="width: 80px; height: 80px; object-fit: contain;">
                            @else
                            <i class="mdi mdi-school"></i>
                            @endif
                        </div>
                        <h6 class="logo-upload-title">Logo Sekolah</h6>
                        <p class="logo-upload-desc">
                            Rekomendasi: 300×300 px<br>
                            Format: PNG dengan background transparan
                        </p>

                        <input type="file" wire:model="logo_sekolah" id="logoSekolahInput" accept="image/jpeg,image/jpg,image/png,image/svg+xml" style="display: none;">

                        <button class="btn btn-primary btn-sm"
                            onclick="document.getElementById('logoSekolahInput').click()"
                            wire:loading.attr="disabled"
                            wire:target="logo_sekolah">

                            <span wire:loading.class="d-none" wire:target="logo_sekolah">
                                @if($sekolah && $sekolah->logo_sekolah_path)
                                <i class="mdi mdi-pencil me-2"></i>Edit Logo Sekolah
                                @else
                                <i class="mdi mdi-cloud-upload me-2"></i>Upload Logo Sekolah
                                @endif
                            </span>

                            <span class="d-none" wire:loading.class.remove="d-none" wire:target="logo_sekolah">
                                <div class="spinner-border spinner-border-sm me-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                Memproses...
                            </span>
                        </button>

                        @error('logo_sekolah')
                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                        @enderror

                        @if($sekolah && $sekolah->logo_sekolah_path)
                        <button class="btn btn-danger btn-sm ms-2" wire:click="confirmDeleteLogo('sekolah')">
                            <i class="mdi mdi-trash-can me-2"></i>Hapus Logo
                        </button>
                        @endif
                    </div>

                    <div class="logo-upload-card">
                        <div class="logo-upload-icon">
                            @if($sekolah && $sekolah->logo_pemda_path)
                            <img src="{{ Storage::disk('public')->url($sekolah->logo_pemda_path) }}" alt="Logo Pemda" style="width: 80px; height: 80px; object-fit: contain;">
                            @else
                            <i class="mdi mdi-shield-check"></i>
                            @endif
                        </div>
                        <h6 class="logo-upload-title">Logo Pemerintah Daerah</h6>
                        <p class="logo-upload-desc">
                            Rekomendasi: 300×300 px<br>
                            Format: PNG dengan background transparan
                        </p>

                        <input type="file" wire:model="logo_pemda" id="logoPemdaInput" accept="image/jpeg,image/jpg,image/png,image/svg+xml" style="display: none;">

                        <button class="btn btn-primary btn-sm"
                            onclick="document.getElementById('logoPemdaInput').click()"
                            wire:loading.attr="disabled"
                            wire:target="logo_pemda">

                            <span wire:loading.class="d-none" wire:target="logo_pemda">
                                @if($sekolah && $sekolah->logo_pemda_path)
                                <i class="mdi mdi-pencil me-2"></i>Edit Logo Pemda
                                @else
                                <i class="mdi mdi-cloud-upload me-2"></i>Upload Logo Pemda
                                @endif
                            </span>

                            <span class="d-none" wire:loading.class.remove="d-none" wire:target="logo_pemda">
                                <div class="spinner-border spinner-border-sm me-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                Memproses...
                            </span>
                        </button>

                        @error('logo_pemda')
                        <small class="text-danger d-block mt-2">{{ $message }}</small>
                        @enderror

                        @if($sekolah && $sekolah->logo_pemda_path)
                        <button class="btn btn-danger btn-sm ms-2" wire:click="confirmDeleteLogo('pemda')">
                            <i class="mdi mdi-trash-can me-2"></i>Hapus Logo
                        </button>
                        @endif
                    </div>
                </div>

                <div class="mt-4">
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="mdi mdi-information me-3 fs-5"></i>
                        <div>
                            <strong>Tips Upload Logo:</strong> Gunakan format PNG dengan background transparan untuk hasil terbaik.
                            Pastikan logo tidak terdistorsi dan memiliki kualitas yang jelas.
                        </div>
                    </div>
                </div>
            </div>

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

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">Status Sekolah</label>
                        <input type="text" class="form-control" value="{{ $sekolah->status_sekolah ?? '-' }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">Jenjang Pendidikan</label>
                        <input type="text" class="form-control" value="{{ $sekolah->jenjang_pendidikan ?? '-' }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">Status Akreditasi</label>
                        <input type="text" class="form-control" value="{{ strtoupper($sekolah->status_akreditasi ?? '-') }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">Tahun Akreditasi</label>
                        <input type="text" class="form-control" value="{{ $sekolah->tahun_akreditasi ?? '-' }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">SK Pendirian Sekolah</label>
                        <input type="text" class="form-control" value="{{ $sekolah->sk_pendirian_sekolah ?? '-' }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">Tanggal SK Pendirian</label>
                        <input type="text" class="form-control" value="{{ $sekolah->tanggal_sk_pendirian ? \Carbon\Carbon::parse($sekolah->tanggal_sk_pendirian)->format('d/m/Y') : '-' }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">SK Izin Operasional</label>
                        <input type="text" class="form-control" value="{{ $sekolah->sk_izin_operasional ?? '-' }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">Tanggal SK Izin Operasional</label>
                        <input type="text" class="form-control" value="{{ $sekolah->tanggal_sk_izin_operasional ? \Carbon\Carbon::parse($sekolah->tanggal_sk_izin_operasional)->format('d/m/Y') : '-' }}" readonly />
                    </div>
                </div>
            </div>

            <div class="contact-section">
                <div class="row mb-4 align-items-center">
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
                                        <i class="mdi mdi-pencil me-2"></i>Edit Informasi Kontak
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">Alamat</label>
                        <input type="text" class="form-control" value="{{ $sekolah->alamat }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">Kode Pos</label>
                        <input type="text" class="form-control" value="{{ $sekolah->kode_pos }}" readonly />
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
                        <label class="form-label text-muted small fw-bold">Telepon</label>
                        <input type="text" class="form-control" value="{{ $sekolah->telepon }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">Email</label>
                        <input type="email" class="form-control" value="{{ $sekolah->email }}" readonly />
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label text-muted small fw-bold">Website</label>
                        <input type="text" class="form-control" value="{{ $sekolah->website }}" readonly />
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Modal Edit Data Sekolah -->
    <div class="modal fade" id="editDataModal" wire:key="editDataModal" tabindex="-1" role="dialog" aria-labelledby="editDataModalLabel" aria-hidden="true" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDataModalLabel">Edit Data Sekolah</h5>
                    <button type="button" class="btn-close" wire:click="closeModal('data')" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="updateData">
                    <div class="modal-body">

                        <!-- Nama Sekolah (tetap satu baris penuh) -->
                        <div class="mb-3">
                            <label class="form-label">Nama Sekolah <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_sekolah') is-invalid @enderror" wire:model.defer="nama_sekolah">
                            @error('nama_sekolah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- NPSN & NIS -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NPSN</label>
                                <input type="text" class="form-control @error('npsn') is-invalid @enderror" wire:model.defer="npsn">
                                @error('npsn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIS</label>
                                <input type="text" class="form-control @error('nis') is-invalid @enderror" wire:model.defer="nis">
                                @error('nis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- NSS & NDS -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NSS</label>
                                <input type="text" class="form-control @error('nss') is-invalid @enderror" wire:model.defer="nss">
                                @error('nss') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NDS</label>
                                <input type="text" class="form-control @error('nds') is-invalid @enderror" wire:model.defer="nds">
                                @error('nds') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Status sekolah & Jenjang -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Sekolah</label>
                                <select class="form-select @error('status_sekolah') is-invalid @enderror" wire:model.defer="status_sekolah">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="negeri">Negeri</option>
                                    <option value="swasta">Swasta</option>
                                </select>
                                @error('status_sekolah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenjang Pendidikan</label>
                                <input type="text" class="form-control @error('jenjang_pendidikan') is-invalid @enderror"
                                    wire:model.defer="jenjang_pendidikan" placeholder="SD, SMP, SMA, SMK, dll">
                                @error('jenjang_pendidikan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Akreditasi & Tahun -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Akreditasi</label>
                                <select class="form-select @error('status_akreditasi') is-invalid @enderror" wire:model.defer="status_akreditasi">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="a">A</option>
                                    <option value="b">B</option>
                                    <option value="c">C</option>
                                </select>
                                @error('status_akreditasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tahun Akreditasi</label>
                                <input type="number" class="form-control @error('tahun_akreditasi') is-invalid @enderror"
                                    wire:model.defer="tahun_akreditasi" min="2000" max="{{ date('Y') }}">
                                @error('tahun_akreditasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- SK Pendirian & Tanggal SK -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">SK Pendirian Sekolah</label>
                                <input type="text" class="form-control @error('sk_pendirian_sekolah') is-invalid @enderror" wire:model.defer="sk_pendirian_sekolah">
                                @error('sk_pendirian_sekolah') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal SK Pendirian</label>
                                <input type="date" class="form-control @error('tanggal_sk_pendirian') is-invalid @enderror" wire:model.defer="tanggal_sk_pendirian">
                                @error('tanggal_sk_pendirian') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- SK Izin Operasional & Tanggal -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">SK Izin Operasional</label>
                                <input type="text" class="form-control @error('sk_izin_operasional') is-invalid @enderror" wire:model.defer="sk_izin_operasional">
                                @error('sk_izin_operasional') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal SK Izin Operasional</label>
                                <input type="date" class="form-control @error('tanggal_sk_izin_operasional') is-invalid @enderror" wire:model.defer="tanggal_sk_izin_operasional">
                                @error('tanggal_sk_izin_operasional') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-labeled btn-outline-secondary"
                            wire:click="closeModal('data')">
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

    <!-- Modal Edit Informasi Kontak Sekolah -->
    <div class="modal fade" id="editContactModal" wire:key="editContactModal" tabindex="-1"
        role="dialog" aria-labelledby="editContactModalLabel" aria-hidden="true"
        wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false">

        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="editContactModalLabel">Edit Informasi Kontak</h5>
                    <button type="button" class="btn-close" wire:click="closeModal('contact')" aria-label="Close"></button>
                </div>

                <form wire:submit.prevent="updateContact">
                    <div class="modal-body">

                        <!-- Alamat (single full-width field) -->
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <input type="text" class="form-control @error('alamat') is-invalid @enderror"
                                wire:model.defer="alamat">
                            @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Kelurahan & Kecamatan -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kelurahan</label>
                                <input type="text" class="form-control @error('kelurahan') is-invalid @enderror"
                                    wire:model.defer="kelurahan">
                                @error('kelurahan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kecamatan</label>
                                <input type="text" class="form-control @error('kecamatan') is-invalid @enderror"
                                    wire:model.defer="kecamatan">
                                @error('kecamatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Kota/Kabupaten & Provinsi -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kota/Kabupaten</label>
                                <input type="text" class="form-control @error('kota_kabupaten') is-invalid @enderror"
                                    wire:model.defer="kota_kabupaten">
                                @error('kota_kabupaten') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Provinsi</label>
                                <input type="text" class="form-control @error('provinsi') is-invalid @enderror"
                                    wire:model.defer="provinsi">
                                @error('provinsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Kode Pos & Telepon -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Pos</label>
                                <input type="text" class="form-control @error('kode_pos') is-invalid @enderror"
                                    wire:model.defer="kode_pos">
                                @error('kode_pos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telepon</label>
                                <input type="text" class="form-control @error('telepon') is-invalid @enderror"
                                    wire:model.defer="telepon">
                                @error('telepon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Email & Website -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    wire:model.defer="email">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Website</label>
                                <input type="text" class="form-control @error('website') is-invalid @enderror"
                                    wire:model.defer="website">
                                @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-labeled btn-outline-secondary"
                            wire:click="closeModal('contact')">

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

                            <span wire:loading.class="d-none" wire:target="updateContact">Simpan</span>

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

</div>

@push('scripts')
<script>
    // --- 1. INISIALISASI MODAL DAN VARIABEL ---
    let editDataModalInstance;
    let editContactModalInstance;

    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi Bootstrap Modal
        const editDataModalElement = document.getElementById('editDataModal');
        const editContactModalElement = document.getElementById('editContactModal');

        if (editDataModalElement) {
            editDataModalInstance = new bootstrap.Modal(editDataModalElement, {
                backdrop: 'static',
                keyboard: false
            });
        }
        if (editContactModalElement) {
            editContactModalInstance = new bootstrap.Modal(editContactModalElement, {
                backdrop: 'static',
                keyboard: false
            });
        }

        // Auto-upload event listener untuk Logo Sekolah
        const logoSekolahInput = document.getElementById('logoSekolahInput');
        if (logoSekolahInput) {
            logoSekolahInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    // Cukup tampilkan loading saja.
                    // JANGAN panggil @this.call disini. 
                    // wire:model akan memicu updatedLogoSekolah di PHP secara otomatis.
                    Swal.fire({
                        title: 'Mengupload...',
                        text: 'Mohon tunggu',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                }
            });
        }

        // Auto-upload event listener untuk Logo Pemda
        const logoPemdaInput = document.getElementById('logoPemdaInput');
        if (logoPemdaInput) {
            logoPemdaInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    // Sama seperti di atas, cukup loading saja.
                    Swal.fire({
                        title: 'Mengupload...',
                        text: 'Mohon tunggu',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                }
            });
        }
    });

    // --- 2. LOGIKA BUKA/TUTUP MODAL DARI LIVEWIRE (MENGGUNAKAN DISPATCH) ---
    document.addEventListener('modal:show', (event) => {
        if (event.detail.id === 'editDataModal' && editDataModalInstance) {
            editDataModalInstance.show();
        } else if (event.detail.id === 'editContactModal' && editContactModalInstance) {
            editContactModalInstance.show();
        }
    });

    document.addEventListener('modal:hide', (event) => {
        if (event.detail.id === 'editDataModal' && editDataModalInstance) {
            editDataModalInstance.hide();
        } else if (event.detail.id === 'editContactModal' && editContactModalInstance) {
            editContactModalInstance.hide();
        }
    });

    // --- 3. RESET INPUT FILE (PENTING UNTUK FIX UPLOAD) ---
    document.addEventListener('file:reset', () => {
        const logoSekolahInput = document.getElementById('logoSekolahInput');
        const logoPemdaInput = document.getElementById('logoPemdaInput');
        if (logoSekolahInput) logoSekolahInput.value = '';
        if (logoPemdaInput) logoPemdaInput.value = '';
    });

    // --- 4. SWEETALERT HANDLERS ---
    document.addEventListener('swal:success', (event) => {
        Swal.close();
        Swal.fire({
            icon: 'success',
            title: event.detail.title || 'Berhasil!', // HAPUS [0], langsung ke properti
            text: event.detail.text,
            showConfirmButton: false,
            timer: 1500
        });
    });

    document.addEventListener('swal:error', (event) => {
        Swal.close();
        Swal.fire({
            icon: 'error',
            title: event.detail.title || 'Oops...', // HAPUS [0]
            text: event.detail.text
        });
    });

    document.addEventListener('swal:info', (event) => {
        // Tutup SweetAlert loading jika terbuka
        Swal.close();
        Swal.fire({
            icon: 'info',
            title: event.detail[0].title,
            text: event.detail[0].text,
            showConfirmButton: false,
            timer: 1500
        });
    });

    // SweetAlert Confirm Delete Handler
    document.addEventListener('swal:confirm-delete', (event) => {
        Swal.fire({
            title: event.detail.title, // HAPUS [0]
            text: event.detail.text, // HAPUS [0]
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Menghapus logo, mohon tunggu.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // HAPUS [0] di sini agar logika if berjalan benar
                if (event.detail.type === 'sekolah') {
                    @this.call('deleteLogoSekolah');
                } else {
                    @this.call('deleteLogoPemda');
                }
            }
        });
    });

    // Pastikan hook ini ada untuk menutup Loading Spinner jika validasi gagal
    document.addEventListener('livewire:init', () => {
        Livewire.hook('commit', ({
            succeed
        }) => {
            succeed(() => {
                // Jika request selesai (baik sukses atau validasi error), tutup loading
                if (Swal.isVisible() && Swal.isLoading()) {
                    Swal.close();
                }
            });
        });
    });
</script>
@endpush