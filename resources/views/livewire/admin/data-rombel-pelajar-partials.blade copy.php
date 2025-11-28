{{-- Modal Detail Modifikasi --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header align-items-start">
                <div class="d-flex flex-column align-items-start">
                    <h4 class="mb-0">Detail Pelajar</h4>
                    <p class="text-muted mb-0"><small>Informasi lengkap data Pelajar</small></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                {{-- Cek if selectedStudent ada untuk mencegah error saat modal loading --}}
                @if($selectedStudent)

                <ul class="nav nav-tabs nav-fill mb-3 gap-2" id="detailTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="tab-button nav-link active" id="pelajar-tab" data-bs-toggle="tab" data-bs-target="#tab-pelajar" type="button" role="tab" aria-controls="tab-pelajar" aria-selected="true">
                            <i class="mdi mdi-school me-2"></i> Data Pelajar
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="tab-button nav-link" id="orangtua-tab" data-bs-toggle="tab" data-bs-target="#tab-orangtua" type="button" role="tab" aria-controls="tab-orangtua" aria-selected="false">
                            <i class="mdi mdi-account me-2"></i> Data Keluarga
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="detailTabContent">

                    <div class="tab-pane fade show active" id="tab-pelajar" role="tabpanel" aria-labelledby="pelajar-tab">

                        <div class="card shadow-sm personal-details-card mb-4">
                            <div class="card-header-modifikasi">
                                <h5 class="card-header-title">
                                    <i class="mdi mdi-account-card-details-outline me-2"></i> Identitas Pelajar
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="personal-details-list">
                                    <div class="personal-detail-item">
                                        <span class="detail-label">Nama Lengkap</span>
                                        <span class="detail-value">{{ $selectedStudent->nama_lengkap ?? '-' }}</span>
                                    </div>
                                    <div class="personal-detail-item">
                                        <span class="detail-label">Nomor Induk (NIS)</span>
                                        <span class="detail-value">{{ $selectedStudent->nomor_induk ?? '-' }}</span>
                                    </div>
                                    <div class="personal-detail-item">
                                        <span class="detail-label">NISN</span>
                                        <span class="detail-value">{{ $selectedStudent->nisn ?? '-' }}</span>
                                    </div>
                                    <div class="personal-detail-item">
                                        <span class="detail-label">Jenis Kelamin</span>
                                        <span class="detail-value">
                                            @if($selectedStudent->jenis_kelamin == 'L')
                                            Laki-laki
                                            @elseif($selectedStudent->jenis_kelamin == 'P')
                                            Perempuan
                                            @else
                                            -
                                            @endif
                                        </span>
                                    </div>
                                    <div class="personal-detail-item">
                                        <span class="detail-label">Tempat & Tanggal Lahir</span>
                                        <span class="detail-value">
                                            {{ $selectedStudent->tempat_lahir ?? '-' }},
                                            {{ $selectedStudent->tanggal_lahir_formatted ?? '-' }}
                                        </span>
                                    </div>
                                    <div class="personal-detail-item">
                                        <span class="detail-label">Agama</span>
                                        <span class="detail-value">
                                            {{ enum_label('agama', $selectedStudent->agama) }}
                                        </span>
                                    </div>
                                    <div class="personal-detail-item">
                                        <span class="detail-label">Status Keluarga</span>
                                        <span class="detail-value">
                                            @php
                                            $status = $selectedStudent->status_dalam_keluarga ?? '-';
                                            $formattedStatus = ($status !== '-')
                                            ? ucwords(str_replace(['_', '-'], ' ', $status))
                                            : '-';
                                            @endphp

                                            {{ $formattedStatus }}
                                            (Anak ke {{ $selectedStudent->anak_ke ?? '-' }})
                                        </span>
                                    </div>
                                    <div class="personal-detail-item">
                                        <span class="detail-label">Telepon Pelajar</span>
                                        <span class="detail-value">{{ $selectedStudent->telepon ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm personal-details-card mb-4">
                            <div class="card-header-modifikasi">
                                <h5 class="card-header-title">
                                    <i class="mdi mdi-map-marker-outline me-2"></i> Alamat Domisili
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="personal-details-list">
                                    <div class="personal-detail-item">
                                        <span class="detail-label">Alamat Lengkap</span>
                                        <span class="detail-value">{{ $selectedStudent->alamat ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-sm personal-details-card">
                            <div class="card-header-modifikasi">
                                <h5 class="card-header-title">
                                    <i class="mdi mdi-school-outline me-2"></i> Informasi Sekolah
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="personal-details-list">
                                    <div class="personal-detail-item">
                                        <span class="detail-label">Sekolah Asal</span>
                                        <span class="detail-value">{{ $selectedStudent->sekolah_asal ?? '-' }}</span>
                                    </div>
                                    <div class="personal-detail-item">
                                        <span class="detail-label">Diterima di Kelas</span>
                                        <span class="detail-value">{{ $selectedStudent->diterima_di_kelas ?? '-' }}</span>
                                    </div>
                                    <div class="personal-detail-item">
                                        <span class="detail-label">Pada Tanggal</span>
                                        <span class="detail-value">
                                            {{ $selectedStudent->pada_tanggal_formatted ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="tab-pane fade" id="tab-orangtua" role="tabpanel" aria-labelledby="orangtua-tab">
                        <div class="row g-4">

                            @php
                            $ayah = $selectedStudent->orangTuaWalis->where('hubungan', 'ayah')->first();
                            $ibu = $selectedStudent->orangTuaWalis->where('hubungan', 'ibu')->first();
                            $wali = $selectedStudent->orangTuaWalis->where('hubungan', 'wali')->first();
                            @endphp

                            {{-- Card Ayah, Ibu, Wali (Sesuai kode asli) --}}
                            <div class="col-12">
                                <div class="card shadow-sm personal-details-card h-100">
                                    <div class="card-header-modifikasi">
                                        <h5 class="card-header-title"><i class="mdi mdi-account-tie me-2"></i> Ayah</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        @if($ayah)
                                        <div class="personal-details-list">
                                            <div class="personal-detail-item"><span class="detail-label">Nama</span><span class="detail-value">{{ $ayah->nama ?? '-' }}</span></div>
                                            <div class="personal-detail-item"><span class="detail-label">Pekerjaan</span><span class="detail-value">{{ config("enums.pekerjaan.{$ayah->pekerjaan}", '-') }}</span></div>
                                            <div class="personal-detail-item"><span class="detail-label">Telepon</span><span class="detail-value">{{ $ayah->telepon ?? '-' }}</span></div>
                                            <div class="personal-detail-item"><span class="detail-label">Alamat</span><span class="detail-value">{{ $ayah->alamat ?? '-' }}</span></div>
                                        </div>
                                        @else
                                        <p class="text-muted">Data ayah belum tersedia</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card shadow-sm personal-details-card h-100">
                                    <div class="card-header-modifikasi">
                                        <h5 class="card-header-title"><i class="mdi mdi-account-heart me-2"></i> Ibu</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        @if($ibu)
                                        <div class="personal-details-list">
                                            <div class="personal-detail-item"><span class="detail-label">Nama</span><span class="detail-value">{{ $ibu->nama ?? '-' }}</span></div>
                                            <div class="personal-detail-item"><span class="detail-label">Pekerjaan</span><span class="detail-value">{{ config("enums.pekerjaan.{$ibu->pekerjaan}", '-') }}</span></div>
                                            <div class="personal-detail-item"><span class="detail-label">Telepon</span><span class="detail-value">{{ $ibu->telepon ?? '-' }}</span></div>
                                            <div class="personal-detail-item"><span class="detail-label">Alamat</span><span class="detail-value">{{ $ibu->alamat ?? '-' }}</span></div>
                                        </div>
                                        @else
                                        <p class="text-muted">Data ibu belum tersedia</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card shadow-sm personal-details-card h-100">
                                    <div class="card-header-modifikasi">
                                        <h5 class="card-header-title"><i class="mdi mdi-account-group-outline me-2"></i> Wali</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        @if($wali)
                                        <div class="personal-details-list">
                                            <div class="personal-detail-item"><span class="detail-label">Nama</span><span class="detail-value">{{ $wali->nama ?? '-' }}</span></div>
                                            <div class="personal-detail-item"><span class="detail-label">Pekerjaan</span><span class="detail-value">{{ config("enums.pekerjaan.{$wali->pekerjaan}", '-') }}</span></div>
                                            <div class="personal-detail-item"><span class="detail-label">Telepon</span><span class="detail-value">{{ $wali->telepon ?? '-' }}</span></div>
                                            <div class="personal-detail-item"><span class="detail-label">Alamat</span><span class="detail-value">{{ $wali->alamat ?? '-' }}</span></div>
                                        </div>
                                        @else
                                        <p class="text-muted">Data wali belum tersedia</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-labeled btn-outline-secondary me-2" data-bs-dismiss="modal">
                    <span class="btn-label"><i class="mdi mdi-close-outline"></i></span> Tutup
                </button>
            </div>

        </div>
    </div>
</div>

{{-- Modal Edit (Dan Create) --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            {{-- MODAL HEADER: Dinamis warna & text --}}
            <div class="modal-header {{ $isEditStudent ? 'bg-warning' : 'bg-primary' }}">
                <h5 class="modal-title {{ $isEditStudent ? '' : 'text-white' }}" id="editModalLabel">
                    <i class="mdi {{ $isEditStudent ? 'mdi-pencil-circle' : 'mdi-account-plus' }} me-2"></i>
                    {{ $isEditStudent ? 'Edit Data Pelajar' : 'Tambah Pelajar Baru' }}
                </h5>
                <button type="button" class="btn-close {{ $isEditStudent ? '' : 'btn-close-white' }}" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form wire:submit.prevent="saveStudent" id="editStudentForm">

                    {{-- Tab Navigation --}}
                    <ul class="nav nav-tabs nav-fill mb-3 gap-2" id="detailTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="tab-button nav-link active" id="edit-pelajar-tab" data-bs-toggle="tab" data-bs-target="#edit-pelajar" type="button" role="tab" aria-controls="tab-pelajar" aria-selected="true">
                                <i class="mdi mdi-school me-2"></i> Data Pelajar
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="tab-button nav-link" id="orangtua-tab" data-bs-toggle="tab" data-bs-target="#edit-orangtua" type="button" role="tab" aria-controls="tab-orangtua" aria-selected="false">
                                <i class="mdi mdi-account me-2"></i> Data Keluarga
                            </button>
                        </li>
                    </ul>

                    {{-- Tab Content --}}
                    <div class="tab-content" id="editTabContent">

                        {{-- Tab Data Pelajar --}}
                        <div class="tab-pane fade show active" id="edit-pelajar" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" wire:model="studentData.nama_lengkap" class="form-control @error('studentData.nama_lengkap') is-invalid @enderror" placeholder="Masukkan nama lengkap">
                                    @error('studentData.nama_lengkap') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nomor Induk</label>
                                    <input type="text" wire:model="studentData.nomor_induk" class="form-control @error('studentData.nomor_induk') is-invalid @enderror" placeholder="Masukkan nomor induk">
                                    @error('studentData.nomor_induk') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">NISN</label>
                                    <input type="text" wire:model="studentData.nisn" class="form-control @error('studentData.nisn') is-invalid @enderror" maxlength="10" placeholder="Masukkan NISN">
                                    @error('studentData.nisn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <select wire:model="studentData.jenis_kelamin" class="form-select @error('studentData.jenis_kelamin') is-invalid @enderror">
                                        <option value="">-- Pilih Jenis Kelamin --</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                    @error('studentData.jenis_kelamin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tempat Lahir</label>
                                    <input type="text" wire:model="studentData.tempat_lahir" class="form-control @error('studentData.tempat_lahir') is-invalid @enderror" placeholder="Masukkan tempat lahir">
                                    @error('studentData.tempat_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" wire:model="studentData.tanggal_lahir" class="form-control @error('studentData.tanggal_lahir') is-invalid @enderror">
                                    @error('studentData.tanggal_lahir') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Agama</label>
                                    <select wire:model="studentData.agama" class="form-select @error('studentData.agama') is-invalid @enderror">
                                        @foreach(enum_options('agama', true) as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('studentData.agama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status Dalam Keluarga</label>

                                    <select
                                        wire:model="studentData.status_dalam_keluarga"
                                        class="form-select @error('studentData.status_dalam_keluarga') is-invalid @enderror">
                                        {{-- TAMBAHKAN OPSI INI PALING ATAS --}}
                                        <option value="">-- Pilih status dalam keluarga --</option>

                                        <option value="anak-kandung">Anak Kandung</option>
                                        <!-- <option value="anak_angkat">Anak Angkat</option> -->
                                        <!-- <option value="anak_tiri">Anak Tiri</option> -->
                                        <!-- <option value="famili_lain">Famili Lain</option> -->
                                        <!-- <option value="lainnya">Lainnya</option> -->
                                    </select>

                                    @error('studentData.status_dalam_keluarga')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Anak Ke</label>
                                    <input type="number" wire:model="studentData.anak_ke" class="form-control @error('studentData.anak_ke') is-invalid @enderror" min="1" placeholder="Contoh: 1">
                                    @error('studentData.anak_ke') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Telepon</label>
                                    <input type="text" wire:model="studentData.telepon" class="form-control @error('studentData.telepon') is-invalid @enderror" placeholder="Masukkan nomor telepon">
                                    @error('studentData.telepon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Alamat</label>
                                    <textarea wire:model="studentData.alamat" class="form-control @error('studentData.alamat') is-invalid @enderror" rows="3" placeholder="Masukkan alamat lengkap"></textarea>
                                    @error('studentData.alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Sekolah Asal</label>
                                    <input type="text" wire:model="studentData.sekolah_asal" class="form-control @error('studentData.sekolah_asal') is-invalid @enderror" placeholder="Masukkan sekolah asal">
                                    @error('studentData.sekolah_asal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Diterima di Kelas</label>
                                    <input type="text" wire:model="studentData.diterima_di_kelas" class="form-control @error('studentData.diterima_di_kelas') is-invalid @enderror" placeholder="Contoh: X IPA 1">
                                    @error('studentData.diterima_di_kelas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pada Tanggal</label>
                                    <input type="date" wire:model="studentData.pada_tanggal" class="form-control @error('studentData.pada_tanggal') is-invalid @enderror">
                                    @error('studentData.pada_tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Tab Data Orang Tua --}}
                        <div class="tab-pane fade" id="edit-orangtua" role="tabpanel">
                            {{-- Data Ayah --}}
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="mdi mdi-account-tie me-2 text-primary"></i>Data Ayah</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Nama</label>
                                            <input type="text" wire:model="ayahData.nama" class="form-control @error('ayahData.nama') is-invalid @enderror" placeholder="Masukkan nama ayah">
                                            @error('ayahData.nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Status</label>
                                            <select wire:model="ayahData.status" class="form-select">
                                                <option value="masih-hidup">Masih Hidup</option>
                                                <option value="sudah-meninggal">Sudah Meninggal</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Pekerjaan</label>
                                            <select wire:model="ayahData.pekerjaan" class="form-select">
                                                <option value="">-- Pilih Pekerjaan --</option>
                                                @foreach($pekerjaanOptions ?? config('enums.pekerjaan') as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Telepon</label>
                                            <input type="text" wire:model="ayahData.telepon" class="form-control" placeholder="Masukkan nomor telepon ayah">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Alamat</label>
                                            <textarea wire:model="ayahData.alamat" class="form-control" rows="2" placeholder="Masukkan alamat ayah"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Data Ibu --}}
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="mdi mdi-account-tie-woman me-2 text-pink"></i>Data Ibu</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Nama</label>
                                            <input type="text" wire:model="ibuData.nama" class="form-control" placeholder="Masukkan nama ibu">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Status</label>
                                            <select wire:model="ibuData.status" class="form-select">
                                                <option value="masih-hidup">Masih Hidup</option>
                                                <option value="sudah-meninggal">Sudah Meninggal</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Pekerjaan</label>
                                            <select wire:model="ibuData.pekerjaan" class="form-select">
                                                <option value="">-- Pilih Pekerjaan --</option>
                                                @foreach($pekerjaanOptions ?? config('enums.pekerjaan') as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Telepon</label>
                                            <input type="text" wire:model="ibuData.telepon" class="form-control" placeholder="Masukkan nomor telepon ibu">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Alamat</label>
                                            <textarea wire:model="ibuData.alamat" class="form-control" rows="2" placeholder="Masukkan alamat ibu"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Data Wali --}}
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="mdi mdi-account-supervisor me-2 text-warning"></i>Data Wali</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Nama</label>
                                            <input type="text" wire:model="waliData.nama" class="form-control" placeholder="Masukkan nama wali">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Status</label>
                                            <select wire:model="waliData.status" class="form-select">
                                                <option value="masih-hidup">Masih Hidup</option>
                                                <option value="sudah-meninggal">Sudah Meninggal</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Pekerjaan</label>
                                            <select wire:model="waliData.pekerjaan" class="form-select">
                                                <option value="">-- Pilih Pekerjaan --</option>
                                                @foreach($pekerjaanOptions ?? config('enums.pekerjaan') as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Telepon</label>
                                            <input type="text" wire:model="waliData.telepon" class="form-control" placeholder="Masukkan nomor telepon wali">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Alamat</label>
                                            <textarea wire:model="waliData.alamat" class="form-control" rows="2" placeholder="Masukkan alamat wali"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- MODAL FOOTER --}}
            <div class="modal-footer">
                <button type="button" class="btn btn-labeled btn-outline-secondary" data-bs-dismiss="modal">
                    <span class="btn-label"><i class="mdi mdi-close"></i></span> Batal
                </button>

                <button type="submit" form="editStudentForm" class="btn btn-labeled {{ $isEditStudent ? 'btn-primary' : 'btn-success' }}" wire:loading.attr="disabled" wire:target="saveStudent">
                    <span class="btn-label">
                        <i class="mdi mdi-loading mdi-spin d-none" wire:loading.class.remove="d-none" wire:target="saveStudent"></i>
                        <i class="mdi mdi-content-save" wire:loading.class="d-none" wire:target="saveStudent"></i>
                    </span>
                    <span wire:loading.class="d-none" wire:target="saveStudent">{{ $isEditStudent ? 'Simpan' : 'Simpan Baru' }}</span>
                    <span class="d-none" wire:loading.class.remove="d-none" wire:target="saveStudent">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* CSS ASLI ANDA */
    .personal-details-card {
        background: white;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .card-header-modifikasi {
        background-color: #f7fafc;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    .card-header-title {
        font-size: 1.15rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0;
    }

    .card-header-title i.mdi {
        font-size: 1.25rem;
        vertical-align: middle;
        color: #0d6efd;
    }

    .personal-details-list {
        display: flex;
        flex-direction: column;
    }

    .personal-detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
        padding: .75rem 0;
        gap: 16px;
    }

    .personal-detail-item:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-size: 0.95rem;
        font-weight: 500;
        color: #718096;
        min-width: 40%;
    }

    .detail-value {
        font-size: 0.95rem;
        font-weight: medium;
        color: #2d3748;
        text-align: left;
        flex: 1;
        line-height: 1.5rem;
    }

    .modal-content .nav-tabs {
        border-bottom: none !important;
    }

    .modal-content .nav-item {
        margin-bottom: 0 !important;
    }

    .tab-button {
        border: 1px solid #e2e8f0 !important;
        border-radius: 2px !important;
        padding: .5rem 1rem !important;
        margin-right: .5rem !important;
        background: #f7fafc !important;
        font-weight: 500 !important;
        transition: .15s !important;
        color: #4a5568 !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }

    .tab-button.active {
        background: #9b07a8ff !important;
        color: white !important;
        border-color: #9b07a8ff !important;
    }

    .tab-button:hover:not(.active) {
        background: #ebedf0 !important;
        border-color: #e2e8f0 !important;
    }

    .modal-content {
        border-radius: 12px;
        border: none;
    }
</style>
@endpush

@push('scripts')
<script>
    // Script handling Modal agar Create/Edit/Detail berfungsi
    document.addEventListener('livewire:initialized', () => {

        let detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        let editModal = new bootstrap.Modal(document.getElementById('editModal'));

        // Event Open Detail
        Livewire.on('openModalDetail', () => {
            detailModal.show();
        });

        // Event Open Edit/Create
        Livewire.on('openModalEditStudent', () => {
            editModal.show();
        });

        // Event Close Edit/Create
        Livewire.on('closeModalEditStudent', () => {
            editModal.hide();
        });
    });
</script>
@endpush