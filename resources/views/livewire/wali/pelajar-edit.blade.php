<!-- Modern Tabs -->
<ul class="nav nav-tabs nav-tabs-modern" id="studentEditTab" role="tablist">
    <li class="nav-item flex-fill">
        <a class="nav-link active text-center" data-bs-toggle="tab" href="#profileEdit">
            <i class="mdi mdi-account fs-5 me-2"></i>Profil
        </a>
    </li>
    <li class="nav-item flex-fill">
        <a class="nav-link text-center" data-bs-toggle="tab" href="#addressEdit">
            <i class="mdi mdi-map-marker fs-5 me-2"></i>Alamat
        </a>
    </li>
    <li class="nav-item flex-fill">
        <a class="nav-link text-center" data-bs-toggle="tab" href="#parentsEdit">
            <i class="mdi mdi-account-group fs-5 me-2"></i>Orang Tua/Wali
        </a>
    </li>
    <li class="nav-item flex-fill">
        <a class="nav-link text-center" data-bs-toggle="tab" href="#schoolEdit">
            <i class="mdi mdi-school fs-5 me-2"></i>Sekolah
        </a>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content fade-in" id="studentEditTabContent">

    <!-- Profil Tab -->
    <div class="tab-pane fade show active" id="profileEdit">
        <div class="row">
            <div class="col-md-12">
                <div class="info-card">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="mdi mdi-account-box fs-5"></i>
                        </div>
                        Identitas Personal
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <div class="row">

                                <!-- Nama Siswa -->
                                <div class="col-md-6 mb-3">
                                    <label for="namaSiswa" class="form-label">Nama Siswa</label>
                                    <input type="text" name="nama" id="namaSiswa" value="{{ $siswa->nama }}" class="form-control" placeholder="Nama Siswa">
                                </div>

                                <!-- NIK -->
                                <div class="col-md-6">
                                    <label for="nikSiswa" class="form-label">Nomor Induk Kependudukan (NIK)</label>
                                    <input
                                        type="text"
                                        name="nik"
                                        id="nikSiswa"
                                        class="form-control"
                                        value="{{ old('nik', $siswa->nik ?? '') }}"
                                        placeholder="Nomor Induk Kependudukan"
                                        maxlength="16"
                                        pattern="\d{16}"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);" />
                                </div>

                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="row">
                                <!-- Jenis Kelamin -->
                                <div class="col-md-6 mb-3">
                                    <label for="jkSiswa" class="form-label">Jenis Kelamin</label>
                                    <select name="jk" class="form-select">
                                        <option value="L" {{ $siswa->jk == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ $siswa->jk == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="agamaSiswa" class="form-label">Agama</label>
                                    <select name="agama" id="agamaSiswa" class="form-select">
                                        @foreach (config('enums.agama') as $key => $label)
                                        <option value="{{ $key }}" {{ old('agama', $siswa->agama) === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="row">
                                <!-- Tempat Lahir -->
                                <div class="col-md-6 mb-3">
                                    <label for="tempatLahirSiswa" class="form-label">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" value="{{ $siswa->tempat_lahir }}" id="tempatLahirSiswa" class="form-control" placeholder="Tempat Lahir">
                                </div>

                                <!-- Tanggal Lahir -->
                                <div class="col-md-6">
                                    <label for="tglLahirSiswa" class="form-label">Tanggal Lahir</label>
                                    <input type="date" name="tgl_lahir" value="{{ $siswa->tgl_lahir }}" id="tglLahirSiswa" class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- NIS -->
                        <div class="mb-3">
                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label for="nisSiswa" class="form-label">Nomor Induk Siswa (NIS)</label>
                                    <input type="number" name="nis" id="nisSiswa" value="{{ $siswa->nis }}" class="form-control" placeholder="Nomor Induk Siswa">
                                </div>

                                <!-- NISN -->
                                <div class="col-md-6">
                                    <label for="nisnSiswa" class="form-label">Nomor Induk Siswa Nasional (NISN)</label>
                                    <input
                                        type="text"
                                        name="nisn"
                                        id="nisnSiswa"
                                        class="form-control"
                                        value="{{ old('nisn', $siswa->nisn ?? '') }}"
                                        placeholder="Nomor Induk Siswa Nasional"
                                        maxlength="10"
                                        pattern="\d{10}"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" />
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan Keluarga dan Pas Foto -->
                        <div class="mb-3">
                            <div class="row">
                                <!-- Anak ke -->
                                <div class="col-md-3 mb-3">
                                    <label for="anakKeSiswa" class="form-label">Anak Ke</label>
                                    <input type="number" name="anak_ke" value="{{ $siswa->anak_ke }}" id="anakKeSiswa" class="form-control" placeholder="Anak Ke">
                                </div>

                                <!-- Jumlah Saudara -->
                                <div class="col-md-3 mb-3">
                                    <label for="jmlSaudara" class="form-label">Jumlah Saudara</label>
                                    <input type="number" name="jumlah_saudara" value="{{ $siswa->jumlah_saudara }}" placeholder="Jumlah Saudara" id="jmlSaudara" class="form-control">
                                </div>

                                <!-- Pas Foto -->
                                <div class="col-md-6">
                                    <label for="pasFoto" class="form-label">Pas Foto</label>
                                    <input class="form-control" name="pas_foto" type="file" id="pasFoto">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="info-card">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="mdi mdi-wheelchair-accessibility fs-5"></i>
                        </div>
                        Kondisi Khusus Peserta Didik
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <div class="row">
                                <!-- Kondisi Khusus -->
                                <div class="col-md-6 mb-3">
                                    <label for="status_kondisi_khusus" class="form-label">Kondisi Khusus</label>
                                    <select name="status_kondisi_khusus" id="status_kondisi_khusus" class="form-select" onchange="toggleKondisiKhusus()">
                                        <option value="ya" {{ old('status_kondisi_khusus', $siswa->status_kondisi_khusus ?? '') == 'ya' ? 'selected' : '' }}>Ya</option>
                                        <option value="tidak" {{ old('status_kondisi_khusus', $siswa->status_kondisi_khusus ?? 'tidak') == 'tidak' ? 'selected' : '' }}>Tidak</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <!-- Deskripsi Kondisi -->
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="deskripsi_kondisi" class="form-label">Deskripsi Kondisi Khusus</label>
                                    <div class="form-floating">
                                        <textarea
                                            name="deskripsi_kondisi"
                                            id="deskripsi_kondisi"
                                            rows="3"
                                            class="form-control"
                                            placeholder="Deskripsikan kondisi..."
                                            {{ old('status_kondisi_khusus', $siswa->status_kondisi_khusus ?? 'tidak') == 'ya' ? '' : 'disabled' }}>{{ old('deskripsi_kondisi', $siswa->deskripsi_kondisi ?? '') }}</textarea>
                                        <label for="deskripsi_kondisi">Deskripsikan Kondisi Kebutuhan Khusus</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alamat Tab -->
    <div class="tab-pane fade" id="addressEdit">
        <div class="row">
            <div class="col-md-12 mx-auto">
                <div class="info-card">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="mdi mdi-map-marker-radius fs-5"></i>
                        </div>
                        Alamat Tempat Tinggal
                    </div>
                    <div class="row">
                        <!-- Alamat Jalan -->
                        <div class="col-12 mb-3">
                            <label for="alamatJalan" class="form-label">Alamat Jalan</label>
                            <input type="text" name="a_jal" value="{{ $siswa->a_jal }}" id="alamatJalan" class="form-control" placeholder="Alamat Jalan">
                        </div>
                    </div>

                    <div class="row">
                        <!-- Kelurahan -->
                        <div class="col-md-6 mb-3">
                            <label for="kelurahanSiswa" class="form-label">Desa/Kelurahan</label>
                            <input type="text" name="a_kel" value="{{ $siswa->a_kel }}" id="kelurahanSiswa" class="form-control" placeholder="Kelurahan">
                        </div>

                        <!-- Alamat RT -->
                        <div class="col-md-3 mb-3">
                            <label for="alamatRt" class="form-label">RT</label>
                            <input type="number" name="a_rt" value="{{ $siswa->a_rt }}" id="alamatRt" class="form-control" placeholder="RT">
                        </div>

                        <!-- Alamat RW -->
                        <div class="col-md-3 mb-3">
                            <label for="alamatRw" class="form-label">RW</label>
                            <input type="number" name="a_rw" value="{{ $siswa->a_rw }}" id="alamatRw" class="form-control" placeholder="RW">
                        </div>
                    </div>

                    <div class="row">
                        <!-- Kecamatan -->
                        <div class="col-md-6 mb-3">
                            <label for="kecamatanSiswa" class="form-label">Kecamatan</label>
                            <input type="text" name="a_kec" value="{{ $siswa->a_kec }}" id="kecamatanSiswa" class="form-control" placeholder="Kecamatan">
                        </div>
                        <!-- Kabupaten/Kota -->
                        <div class="col-md-6 mb-3">
                            <label for="kabupatenSiswa" class="form-label">Kabupaten/Kota</label>
                            <input type="text" name="a_kab" value="{{ $siswa->a_kab }}" id="kabupatenSiswa" class="form-control" placeholder="Kabupaten/Kota">
                        </div>
                    </div>

                    <div class="row">
                        <!-- Provinsi -->
                        <div class="col-md-6 mb-3">
                            <label for="provinsiSiswa" class="form-label">Provinsi</label>
                            <input type="text" name="a_prov" value="{{ $siswa->a_prov }}" id="provinsiSiswa" class="form-control" placeholder="Provinsi">
                        </div>

                        <!-- Kode Pos -->
                        <div class="col-md-6 mb-3">
                            <label for="kodePos" class="form-label">Kode Pos</label>
                            <input type="text" name="kode_pos" value="{{ $siswa->kode_pos }}" id="kodePos" class="form-control" placeholder="Kode Pos">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="info-card">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="mdi mdi-phone fs-5"></i>
                        </div>
                        Kontak & Lainnya
                    </div>
                    <div class="row">
                        <div class="mb-3">
                            <div class="row">
                                <!-- Telepon -->
                                <div class="col-md-6 mb-3">
                                    <label for="teleponSiswa" class="form-label">Telepon</label>
                                    <input type="number" name="telepon" value="{{ $siswa->telepon }}" id="teleponSiswa" class="form-control" placeholder="Telepon">
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="emailSiswa" class="form-label">Email</label>
                                    <input type="email" name="email" value="{{ $siswa->email }}" id="emailSiswa" class="form-control" placeholder="Email">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orang Tua Tab -->
    <div class="tab-pane fade" id="parentsEdit">
        <div class="row">
            <div class="col-md-12">
                <div class="info-card">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="mdi mdi-face fs-5"></i>
                        </div>
                        Ayah
                    </div>
                    <div class="row">
                        <!-- Nama Ayah -->
                        <div class="col-md-6 mb-3">
                            <label for="nama_ayah" class="form-label">Nama Ayah</label>
                            <input type="text" name="nama_ayah" id="nama_ayah" class="form-control"
                                value="{{ old('nama_ayah', $ayah->nama_ortu ?? '') }}" placeholder="Nama Ayah">
                        </div>

                        <!-- Status Ayah -->
                        <div class="col-md-6 mb-3">
                            <label for="status_ayah" class="form-label">Status Hidup</label>
                            <select name="status_ayah" id="status_ayah" class="form-select">
                                <option selected disabled value="">Pilih Status</option>
                                <option value="masih-hidup" {{ old('status_ayah', $ayah->status ?? '') === 'masih-hidup' ? 'selected' : '' }}>Masih Hidup</option>
                                <option value="sudah-meninggal" {{ old('status_ayah', $ayah->status ?? '') === 'sudah-meninggal' ? 'selected' : '' }}>Sudah Meninggal</option>
                            </select>
                        </div>

                        <!-- Pekerjaan Ayah -->
                        <select name="pekerjaan_ayah" id="pekerjaan_ayah" class="form-select">
                            <option selected disabled value="">Pilih Pekerjaan</option>
                            @foreach(config('enums.pekerjaan') ?? [] as $val => $label)
                            <option value="{{ $val }}" {{ old('pekerjaan_ayah', $ayah->pekerjaan ?? '') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>

                        <!-- Telepon Ayah -->
                        <div class="col-md-6 mb-3">
                            <label for="telepon_ayah" class="form-label">Nomor Telepon</label>
                            <input type="text" name="telepon_ayah" id="telepon_ayah" class="form-control"
                                value="{{ old('telepon_ayah', $ayah->telepon ?? '') }}" placeholder="Nomor Telepon">
                        </div>

                        <!-- Alamat Ayah -->
                        <div class="col-md-12 mb-3">
                            <label for="alamat_ayah" class="form-label">Alamat Tempat Tinggal</label>
                            <textarea name="alamat_ayah" id="alamat_ayah" rows="3" class="form-control">{{ old('alamat_ayah', $ayah->alamat ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="info-card">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="mdi mdi-face-woman fs-5"></i>
                        </div>
                        Ibu
                    </div>
                    <div class="row">
                        <!-- Nama ibu -->
                        <div class="col-md-6 mb-3">
                            <label for="nama_ibu" class="form-label">Nama Ibu</label>
                            <input type="text" name="nama_ibu" id="nama_ibu" class="form-control"
                                value="{{ old('nama_ibu', $ibu->nama_ortu ?? '') }}" placeholder="Nama Ibu">
                        </div>

                        <!-- Status ibu -->
                        <div class="col-md-6 mb-3">
                            <label for="status_ibu" class="form-label">Status Hidup</label>
                            <select name="status_ibu" id="status_ibu" class="form-select">
                                <option selected disabled value="">Pilih Status</option>
                                <option value="masih-hidup" {{ old('status_ibu', $ibu->status ?? '') === 'masih-hidup' ? 'selected' : '' }}>Masih Hidup</option>
                                <option value="sudah-meninggal" {{ old('status_ibu', $ibu->status ?? '') === 'sudah-meninggal' ? 'selected' : '' }}>Sudah Meninggal</option>
                            </select>
                        </div>

                        <!-- Pekerjaan ibu -->
                        <div class="col-md-6 mb-3">
                            <label for="pekerjaan_ibu" class="form-label">Pekerjaan</label>
                            <select name="pekerjaan_ibu" id="pekerjaan_ibu" class="form-select">
                                <option selected disabled value="">Pilih Pekerjaan</option>
                                {{-- GANTI BARIS INI (Baris 396) --}}
                                @foreach(config('enums.pekerjaan') ?? [] as $val => $label)
                                <option value="{{ $val }}" {{ old('pekerjaan_ibu', $ibu->pekerjaan ?? '') === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Telepon ibu -->
                        <div class="col-md-6 mb-3">
                            <label for="telepon_ibu" class="form-label">Nomor Telepon</label>
                            <input type="text" name="telepon_ibu" id="telepon_ibu" class="form-control"
                                value="{{ old('telepon_ibu', $ibu->telepon ?? '') }}" placeholder="Nomor Telepon">
                        </div>

                        <!-- Alamat ibu -->
                        <div class="col-md-12 mb-3">
                            <label for="alamat_ibu" class="form-label">Alamat Tempat Tinggal</label>
                            <textarea name="alamat_ibu" id="alamat_ibu" rows="3" class="form-control">{{ old('alamat_ibu', $ibu->alamat ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row wali-section" id="wali-section">
            <div class="col-md-12">
                <div class="info-card">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="mdi mdi-face-profile fs-5"></i>
                        </div>
                        Wali<br>
                        <small>(Jika tinggal menumpang/kos atau bersama wali)</small>
                    </div>
                    <div class="row">
                        <!-- Nama Wali -->
                        <div class="col-md-6 mb-3">
                            <label for="nama_wali" class="form-label">Nama Wali</label>
                            <input type="text" name="nama_wali" id="nama_wali" class="form-control"
                                value="{{ old('nama_wali', $wali->nama_ortu ?? '') }}" placeholder="Nama Wali">
                        </div>

                        <!-- Status wali -->
                        <div class="col-md-6 mb-3">
                            <label for="status_wali" class="form-label">Status Hidup</label>
                            <select name="status_wali" id="status_wali" class="form-select">
                                <option value="masih-hidup" selected>
                                    {{ old('status_wali', $wali->status ?? 'Masih Hidup') === 'masih-hidup' ? 'Masih Hidup' : 'Masih Hidup' }}
                                </option>
                            </select>
                        </div>

                        <!-- Pekerjaan wali -->
                        <div class="col-md-6 mb-3">
                            <label for="pekerjaan_wali" class="form-label">Pekerjaan</label>
                            <select name="pekerjaan_wali" id="pekerjaan_wali" class="form-select">
                                <option selected disabled value="">Pilih Pekerjaan</option>
                                {{-- FIX: Use ?? [] to safely handle a null config value --}}
                                @foreach(config('enums.pekerjaan') ?? [] as $val => $label)
                                <option value="{{ $val }}" {{ old('pekerjaan_wali', $wali->pekerjaan ?? '') === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Telepon wali -->
                        <div class="col-md-6 mb-3">
                            <label for="telepon_wali" class="form-label">Nomor Telepon</label>
                            <input type="text" name="telepon_wali" id="telepon_wali" class="form-control"
                                value="{{ old('telepon_wali', $wali->telepon ?? '') }}" placeholder="Nomor Telepon">
                        </div>

                        <!-- Alamat wali -->
                        <div class="col-md-12 mb-3">
                            <label for="alamat_wali" class="form-label">Alamat Tempat Tinggal</label>
                            <textarea name="alamat_wali" id="alamat_wali" rows="3" class="form-control">{{ old('alamat_wali', $wali->alamat ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sekolah Tab -->
    <div class="tab-pane fade" id="schoolEdit">
        <div class="row">
            <div class="col-md-12">
                <div class="info-card">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="mdi mdi-school fs-5"></i>
                        </div>
                        Sekolah Asal
                    </div>
                    <div class="row">

                        <!-- Sekolah Asal -->
                        <div class="col-md-6 mb-3">
                            <label for="sekolahAsal" class="form-label">Nama Sekolah Asal</label>
                            <input type="text" name="sekolah_asal" id="sekolahAsal" value="{{ $siswa->sekolah_asal }}" class="form-control" placeholder="Nama Sekolah Asal">
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="info-card">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="mdi mdi-calendar-text fs-5"></i>
                        </div>
                        Informasi Penerimaan
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 mb-3">
                            <label for="tglMasuk" class="form-label">Tanggal Masuk</label>
                            <input type="date" name="tgl_masuk" id="tglMasuk" value="{{ $siswa->tgl_masuk }}" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="kelasMasuk" class="form-label">Diterima di Kelas</label>
                            <input type="text" name="kelas" id="kelasMasuk" value="{{ $siswa->kelas }}" class="form-control" placeholder="Contoh: X">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>