<!-- Header dengan gradient -->
<div class="student-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div class="d-flex align-items-center mb-2 mb-md-0">
            <div class="student-avatar rounded-4 d-flex justify-content-center align-items-center">
                @if(!empty($pesertaDidik->pas_foto) && Storage::disk('public')->exists($pesertaDidik->pas_foto))
                <img src="{{ asset('storage/' . $pesertaDidik->pas_foto) }}"
                    alt="Pas Foto {{ $pesertaDidik->nama }}"
                    class="img-fluid rounded-4"
                    style="max-height: 120px; object-fit: cover;">
                @else
                <i class="mdi mdi-account mdi-48px"></i>
                @endif
            </div>
            <div class="ms-3">
                <h4 class="mb-1 fw-bold">{{ $pesertaDidik->nama }}</h4>
                <p class="mb-0 opacity-75">
                    <i class="mdi {{ $pesertaDidik->jk === 'P' ? 'mdi-gender-female' : 'mdi-gender-male' }} fs-5 me-2"></i>
                    {{ $pesertaDidik->jk_label }}
                </p>
            </div>
        </div>
        <div class="text-md-end">
            <div class="badge-modern mb-2 d-flex justify-content-between">
                <strong>NISN: </strong>&nbsp; {{ $pesertaDidik->nisn }}
            </div>
            <div class="badge-modern d-flex justify-content-between">
                <strong>NIS:</strong> {{ $pesertaDidik->nis }}
            </div>
        </div>
    </div>
</div>

<!-- Modern Tabs -->
<ul class="nav nav-tabs nav-tabs-modern" id="studentDetailTab" role="tablist">
    <li class="nav-item flex-fill">
        <a class="nav-link active text-center" data-bs-toggle="tab" href="#profileDetail">
            <i class="mdi mdi-account fs-5 me-2"></i>Profil
        </a>
    </li>
    <li class="nav-item flex-fill">
        <a class="nav-link text-center" data-bs-toggle="tab" href="#addressDetail">
            <i class="mdi mdi-map-marker fs-5 me-2"></i>Alamat
        </a>
    </li>
    <li class="nav-item flex-fill">
        <a class="nav-link text-center" data-bs-toggle="tab" href="#parentsDetail">
            <i class="mdi mdi-account-group fs-5 me-2"></i>Orang Tua
        </a>
    </li>
    <li class="nav-item flex-fill">
        <a class="nav-link text-center" data-bs-toggle="tab" href="#schoolDetail">
            <i class="mdi mdi-school fs-5 me-2"></i>Sekolah
        </a>
    </li>
</ul>


<!-- Tab Content -->
<div class="tab-content fade-in" id="studentDetailTabContent">

    <!-- Profil Tab -->
    <div class="tab-pane fade show active" id="profileDetail">
        <div class="row">
            <div class="col-md-12">
                <div class="info-card">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="mdi mdi-account-box fs-5"></i>
                        </div>
                        Identitas Personal
                    </div>
                    <div class="info-list">
                        <div class="info-item">
                            <div class="info-label">NIK</div>
                            <div class="info-value">{{ $pesertaDidik->nik }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Agama</div>
                            <div class="info-value">{{ $agamaMapping[$pesertaDidik->agama] ?? 'Tidak diketahui' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tempat Lahir</div>
                            <div class="info-value">{{ $pesertaDidik->tempat_lahir }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tanggal Lahir</div>
                            <div class="info-value">{{ $pesertaDidik->tgl_lahir }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Anak ke</div>
                            <div class="info-value">{{ $pesertaDidik->anak_ke }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Jumlah Saudara</div>
                            <div class="info-value">{{ $pesertaDidik->jumlah_saudara }}</div>
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
                    <div class="info-list">
                        <div class="info-item">
                            <div class="info-label">Kondisi Khusus</div>
                            <div class="info-value">{{ $pesertaDidik->status_kondisi_khusus === 'ya' ? 'Ya' : 'Tidak ada' }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Jenis Kondisi Khusus</div>
                            <div class="info-value">{{ $pesertaDidik->jenis_kondisi_khusus ?? 'Tidak ada' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Deskripsi</div>
                            <div class="info-value">{{ $pesertaDidik->deskripsi_kondisi ?? 'Tidak ada' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alamat Tab -->
    <div class="tab-pane fade" id="addressDetail">
        <div class="row">
            <div class="col-md-12 mx-auto">
                <div class="info-card">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="mdi mdi-map-marker-radius fs-5"></i>
                        </div>
                        Alamat Tempat Tinggal
                    </div>
                    <div class="info-list g-3">
                        <div class="info-item">
                            <div class="info-label">Alamat Lengkap</div>
                            <div class="info-value">{{ $pesertaDidik->a_jal ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">RT / RW</div>
                            <div class="info-value">
                                {{ ($pesertaDidik->a_rt || $pesertaDidik->a_rw) ? ($pesertaDidik->a_rt ?: '-') . ' / ' . ($pesertaDidik->a_rw ?: '-') : '-' }}
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Desa/Kelurahan</div>
                            <div class="info-value">{{ $pesertaDidik->a_kel ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Kecamatan</div>
                            <div class="info-value">{{ $pesertaDidik->a_kec ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Kab/Kota</div>
                            <div class="info-value">{{ $pesertaDidik->a_kab ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Provinsi</div>
                            <div class="info-value">{{ $pesertaDidik->a_prov ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Kode Pos</div>
                            <div class="info-value">{{ $pesertaDidik->kode_pos ?: '-' }}</div>
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
                    <div class="info-list">
                        <div class="info-item">
                            <div class="info-label">Email</div>
                            <div class="info-value">{{ $pesertaDidik->email ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Telepon</div>
                            <div class="info-value">{{ $pesertaDidik->telepon ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Jenis Tinggal</div>
                            <div class="info-value">{{ $pesertaDidik->j_tinggal ?: 'Tidak diketahui' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Transportasi</div>
                            <div class="info-value">{{ $pesertaDidik->transportasi ?: 'Tidak diketahui' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orang Tua Tab -->
    <div class="tab-pane fade" id="parentsDetail">
        @foreach($pesertaDidik->orangTuaWalis as $ortu)
        <div class="row">
            <div class="col-md-12">
                <div class="info-card">
                    <div class="section-title">
                        <div class="section-icon">
                            @if(strtolower($ortu->hubungan_label) === 'ayah')
                            <i class="mdi mdi-face fs-5"></i>
                            @elseif(strtolower($ortu->hubungan_label) === 'ibu')
                            <i class="mdi mdi-face-woman fs-5"></i>
                            @else
                            <i class="mdi mdi-account fs-5"></i>
                            @endif
                        </div>
                        {{ $ortu->hubungan_label }}
                    </div>
                    <div class="info-list">
                        <div class="info-item">
                            <div class="info-label">Nama</div>
                            <div class="info-value">{{ $ortu->nama_ortu ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Status</div>
                            <div class="info-value">{{ $ortu->status ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">NIK</div>
                            <div class="info-value">{{ $ortu->nik ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tahun Lahir</div>
                            <div class="info-value">{{ $ortu->tahun_lahir ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Pendidikan</div>
                            <div class="info-value">{{ $ortu->pendidikan_label ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Pekerjaan</div>
                            <div class="info-value">{{ $ortu->pekerjaan_label ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Penghasilan</div>
                            <div class="info-value">{{ $ortu->penghasilan_label ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Alamat</div>
                            <div class="info-value">{{ $ortu->alamat ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Telepon</div>
                            <div class="info-value">{{ $ortu->telepon ?: '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Sekolah Tab -->
    <div class="tab-pane fade" id="schoolDetail">
        <div class="row">
            <div class="col-md-12">
                <div class="info-card">
                    <div class="section-title">
                        <div class="section-icon">
                            <i class="mdi mdi-school fs-5"></i>
                        </div>
                        Sekolah Asal
                    </div>
                    <div class="info-list">
                        <div class="info-item">
                            <div class="info-label">Nama Sekolah Asal</div>
                            <div class="info-value">{{ $pesertaDidik->sekolah_asal ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Kab/kota Sekolah Asal</div>
                            <div class="info-value">{{ $pesertaDidik->kab_kota_sekolah_asal ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Provinsi Sekolah Asal</div>
                            <div class="info-value">{{ $pesertaDidik->prov_sekolah_asal ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">NPSN Sekolah Asal</div>
                            <div class="info-value">{{ $pesertaDidik->npsn_sekolah_asal ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Nomor Ijazah</div>
                            <div class="info-value">{{ $pesertaDidik->nomor_ijazah ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tahun Lulus</div>
                            <div class="info-value">{{ $pesertaDidik->tahun_lulus ?: '-' }}</div>
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
                    <div class="info-list">
                        <div class="info-item">
                            <div class="info-label">Tanggal Diterima</div>
                            <div class="info-value">{{ $pesertaDidik->tgl_masuk ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Kelas Awal</div>
                            <div class="info-value">{{ $pesertaDidik->kelas ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Jurusan</div>
                            <div class="info-value">{{ optional($pesertaDidik->dataJurusan)->jurusan ?: '-' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Status</div>
                            <div class="info-value">
                                {{ optional($pesertaDidik->dataMutasi)->status ? ucfirst(optional($pesertaDidik->dataMutasi)->status) : 'Peserta Didik Baru' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>