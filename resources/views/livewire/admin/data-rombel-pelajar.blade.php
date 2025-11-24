<div>
    <div class="page-header pb-3 mb-4 border-bottom">
        <div class="d-flex align-items-center">
            <div class="icon-wrapper position-relative">
                <span class="bg-gradient-primary p-2 rounded-3 shadow-sm me-3 d-inline-flex align-items-center justify-content-center">
                    <i class="mdi mdi-account-group mdi-24px text-white"></i>
                </span>
            </div>

            <div>
                <h4 class="mb-1 text-dark fw-bold">Manajemen Rombongan Belajar</h4>
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted">Kelola data rombongan belajar</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Rombel --}}

    <div class="alert alert-success py-2" role="alert">
        <div class="row align-items-center">

            <div class="col-sm-4 py-3">
                <small class="text-muted d-block">Tingkat & Jurusan:</small>
                <p class="mb-0 font-weight-bold">Kelas {{ $rombel->tingkat ?? '-' }} - {{ $rombel->jurusan->alias ?? 'Tidak Dikenal' }}</p>
            </div>

            <div class="col-sm-4 py-3">
                <small class="text-muted d-block">Wali Kelas:</small>
                <p class="mb-0 font-weight-bold">{{ $rombel->waliKelas->name ?? 'Belum Ditentukan' }}</p>
            </div>

            <div class="col-sm-4 py-3">
                <small class="text-muted d-block">Kurikulum:</small>
                <p class="mb-0 font-weight-bold">
                    @if ($rombel->tahunAjaranKurikulum)
                    {{ $rombel->tahunAjaranKurikulum->tahunAjaran->nama ?? '?' }} ({{ $rombel->tahunAjaranKurikulum->kurikulum->nama ?? '?' }})
                    @else
                    Global
                    @endif
                </p>
            </div>

        </div>
    </div>


    {{-- Tab Navigation --}}
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button
                class="nav-link {{ $activeTab === 'pelajar' ? 'active' : '' }} d-flex align-items-center gap-2"
                wire:click="switchTab('pelajar')"
                type="button">
                <i class="mdi mdi-account-group"></i> Daftar Pelajar
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button
                class="nav-link {{ $activeTab === 'mapel' ? 'active' : '' }} d-flex align-items-center gap-2"
                wire:click="switchTab('mapel')"
                type="button">
                <i class="mdi mdi-book-open-variant"></i> Mata Pelajaran & Pengajar
            </button>
        </li>
    </ul>

    <!-- Tab Content: Daftar Pelajar -->
    @if($activeTab === 'pelajar')

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
                    placeholder="{{ $activeTab === 'mapel' ? 'Cari Mata Pelajaran/Guru...' : 'Cari Pelajar...' }}"
                    wire:model.live.debounce.500ms="search" style="width:250px;">
            </div>
            @if($activeTab === 'mapel')
            <button type="button" wire:click="createMapel" class="btn btn-outline-light-muted btn-sm d-flex align-items-center justify-content-center h-100" style="padding: 0 0.75rem;">
                <i class="mdi mdi-plus"></i>
            </button>
            @endif
        </div>
    </div>

    <table class="table table-hover mb-0">
        <thead class="bg-light">
            <tr>
                <th style="width: 36%;">
                    <p class="mb-0">Peserta Didik</p>
                    <small>Nama | Jenis Kelamin</small>
                </th>
                <th style="width: 27%;">
                    <p class="mb-0">Kelahiran</p>
                    <small>Tempat | Tanggal</small>
                </th>
                <th style="width: 27%;">
                    <p class="mb-0">Nomor Induk Siswa</p>
                    <small>Sekolah | Nasional</small>
                </th>
                <th style="width: 10%;">
                    <p class="mb-0">Aksi</p>
                    <small>Delete</small>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $item)
            <tr>
                <td>
                    <a class="hyper-link text-decoration-none" href="">
                        <div class="d-flex align-items-center">
                            <img src="{{ $item->pelajar->icon }}" alt="image">
                            <div class="table-user-name ml-3">
                                <p class="mb-0 font-weight-medium"> {{ $item->pelajar->nama_lengkap ?? '-' }} </p>
                                <small class="text-muted font-weight-medium"> {{ $item->pelajar->jenis_kelamin_label ?? 'N/A' }} </small>
                            </div>
                        </div>
                    </a>
                </td>
                <td>
                    <p class="mb-0 font-weight-medium">{{ $item->pelajar->tempat_lahir ?? 'N/A' }}</p>
                    <small class="text-muted"><span class="font-weight-medium"> Tanggal </span>{{ $item->pelajar->tanggal_lahir_formatted ?? 'N/A' }}</small>
                </td>
                <td>
                    <div class="badge badge-inverse-success mr-1">{{ $item->pelajar->nomor_induk ?? 'N/A' }}</div>
                    <div class="badge badge-inverse-warning">{{ $item->pelajar->nisn ?? 'N/A' }}</div>
                </td>
                <td>
                    <!-- <button wire:click="confirmDeleteRombelPelajar('{{ $item->id }}')"
                        class="btn btn-sm btn-outline-danger" title="Hapus">
                        <i class="mdi mdi-delete"></i>
                    </button> -->
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted">Belum ada pelajar yang terdaftar dalam rombel ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $data->links() }}
    </div>

    @endif

    <!-- Tab Content: Mata Pelajaran & Pengajar -->
    @if($activeTab === 'mapel')

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
                    placeholder="{{ $activeTab === 'mapel' ? 'Cari Mata Pelajaran/Guru...' : 'Cari Pelajar...' }}"
                    wire:model.live.debounce.500ms="search" style="width:250px;">
            </div>
            @if($activeTab === 'mapel')
            <button type="button" wire:click="createMapel" class="btn btn-outline-light-muted btn-sm d-flex align-items-center justify-content-center h-100" style="padding: 0 0.75rem;">
                <i class="mdi mdi-plus"></i>
            </button>
            @endif
        </div>
    </div>

    <table class="table table-hover mb-0">
        <thead class="bg-light">
            <tr>
                <th style="width: 40%;">
                    <p class="mb-0">Mata Pelajaran</p>
                    <small>Nama Mapel</small>
                </th>
                <th style="width: 40%;">
                    <p class="mb-0">Pengajar</p>
                    <small>Nama Guru</small>
                </th>
                <th style="width: 10%;">
                    <p class="mb-0">Aksi</p>
                    <small>Edit | Delete</small>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $index => $item)
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="{{ $item->guru->icon ?? asset('assets/images/icons/icon_13.png') }}" alt="guru" style="width: 32px; height: 32px; object-fit: cover;">
                        <div class="table-user-name ml-3">
                            <p class="mb-0 font-weight-medium">{{ $item->mataPelajaran->nama ?? '-' }}</p>
                            <small class="text-muted">{{ $item->mataPelajaran->kode ?? '' }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="align-items-center">
                        <p class="mb-0 font-weight-medium">{{ $item->guru->name ?? '-' }}</p>
                        <small class="text-muted"><span class="font-weight-medium"> Telp. </span>{{ $item->guru->telephone ?? 'N/A' }}</small>
                    </div>
                </td>
                <td>
                    <button wire:click="editMapel('{{ $item->id }}')"
                        class="btn btn-sm btn-outline-primary" title="Edit">
                        <i class="mdi mdi-pencil"></i>
                    </button>
                    <button wire:click="confirmDeleteRombelPengajar('{{ $item->id }}')"
                        class="btn btn-sm btn-outline-danger" title="Hapus">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted">Belum ada mata pelajaran yang ditambahkan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $data->links() }}
    </div>

    @endif

    {{-- Modal Form Mata Pelajaran & Pengajar --}}
    <div wire:ignore.self class="modal fade" id="modalRombelPengajar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="{{ $isEdit ? 'updateMapel' : 'storeMapel' }}" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit' : 'Tambah' }} Mata Pelajaran & Pengajar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="mata_pelajaran_id" class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                        <select id="mata_pelajaran_id" class="form-select" wire:model="mata_pelajaran_id">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($mataPelajaranList as $mapel)
                            <option value="{{ $mapel->id }}">{{ $mapel->nama }} @if($mapel->kode) ({{ $mapel->kode }}) @endif</option>
                            @endforeach
                        </select>
                        @error('mata_pelajaran_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="guru_search" class="form-label">Guru Pengajar <span class="text-danger">*</span></label>

                        <div class="position-relative">
                            @if($selectedGuruName)
                            <div class="form-control d-flex align-items-center justify-content-between" style="background-color: #f8f9fa;">
                                <span>
                                    {{ $selectedGuruName }}
                                </span>
                                <button type="button" class="btn btn-sm px-2 py-0" wire:click="clearGuru" style="font-size: 0.75rem;">
                                    <i class="mdi mdi-close"></i>
                                </button>
                            </div>
                            @else
                            <input
                                type="text"
                                id="guru_search"
                                class="form-control"
                                wire:model.live.debounce.300ms="guruSearch"
                                placeholder="Ketik nama guru..."
                                autocomplete="off">

                            @if(!empty($guruSearch) && count($filteredGuruList) > 0)
                            <ul class="list-group position-absolute w-100 shadow-sm" style="z-index: 1050; max-height: 200px; overflow-y: auto; margin-top: 2px;">
                                @foreach($filteredGuruList as $guru)
                                <li class="list-group-item list-group-item-action d-flex align-items-center"
                                    style="cursor: pointer;"
                                    wire:click="selectGuru('{{ $guru->id }}')">
                                    <span>{{ $guru->name }}</span>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                            @endif
                        </div>

                        @error('guru_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-labeled btn-secondary" data-bs-dismiss="modal">
                        <span class="btn-label"><i class="mdi mdi-close-outline"></i></span>Batal
                    </button>
                    <button type="submit" class="btn btn-labeled btn-primary">
                        <span class="btn-label"><i class="mdi mdi-content-save"></i></span>{{ $isEdit ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Event listener untuk membuka dan menutup modal
    window.addEventListener('openModalRombelPengajar', () => {
        new bootstrap.Modal(document.getElementById('modalRombelPengajar')).show();
    });

    window.addEventListener('closeModalRombelPengajar', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalRombelPengajar'));
        if (modal) modal.hide();
    });
</script>
@endpush