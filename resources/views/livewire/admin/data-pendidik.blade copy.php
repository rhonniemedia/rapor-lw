<div>
    {{-- Pesan Notifikasi --}}
    @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Berhasil!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Filter & Pencarian --}}
    <div class="d-flex justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <span>Tampilkan</span>
            <select class="form-select form-select-sm" wire:model.live="perPage">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <span>data</span>
        </div>
        <div>
            <div class="position-relative">
                <input type="text"
                    class="form-control"
                    placeholder="Cari nama, email, NIP..."
                    style="width:250px;"
                    wire:model.live.debounce.300ms="search">
                <div wire:loading wire:target="search"
                    class="position-absolute end-0 top-50 translate-middle-y me-2">
                    <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Data Tenaga Pendidik --}}
    <div class="table-responsive" wire:loading.class="opacity-50" wire:target="search,perPage,sortBy">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th style="width: 35%;">
                        <a href="#" wire:click.prevent="sortBy('name')" class="text-decoration-none text-dark">
                            Tenaga Pendidik
                            @if($sortBy === 'name')
                            @if($sortDirection === 'asc')
                            <i class="mdi mdi-chevron-up"></i>
                            @else
                            <i class="mdi mdi-chevron-down"></i>
                            @endif
                            @endif
                        </a>
                    </th>
                    <th style="width: 35%;">Penugasan</th>
                    <th style="width: 20%;">
                        <a href="#" wire:click.prevent="sortBy('status')" class="text-decoration-none text-dark">
                            Status
                            @if($sortBy === 'status')
                            @if($sortDirection === 'asc')
                            <i class="mdi mdi-chevron-up"></i>
                            @else
                            <i class="mdi mdi-chevron-down"></i>
                            @endif
                            @endif
                        </a>
                    </th>
                    <th style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tenagaPendidik as $pendidik)
                <tr wire:key="pendidik-{{ $pendidik->id }}">
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('assets/images/icons/pilot.png') }}"
                                alt="{{ $pendidik->name }}"
                                style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 10px;" />
                            <div>
                                <p class="mb-0 fw-medium">{{ $pendidik->name }}</p>
                                <small>
                                    @if($pendidik->telephone)
                                    {{ $pendidik->telephone }}
                                    @elseif($pendidik->email)
                                    {{ $pendidik->email }}
                                    @else
                                    -
                                    @endif
                                </small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($pendidik->is_guru_agama)
                        <span class="badge bg-primary">Guru Agama</span>
                        @if($pendidik->spesialisasi_agama)
                        <p class="mb-0">{{ $pendidik->spesialisasi_agama }}</p>
                        @endif
                        @else
                        <span class="badge bg-secondary">Guru Umum</span>
                        @endif

                        @if($pendidik->nip)
                        <small class="text-muted">NIP: {{ $pendidik->nip }}</small>
                        @endif

                        {{-- Tambahan info kelas jika ada relasi --}}
                        @if($pendidik->kokurikulerDibimbing->count() > 0)
                        <br>
                        <small>
                            Kokurikuler: {{ $pendidik->kokurikulerDibimbing->count() }} kegiatan
                        </small>
                        @endif
                    </td>
                    <td>
                        @if(($pendidik->status ?? 'aktif') == 'aktif')
                        <span class="badge bg-success">Aktif</span>
                        @else
                        <span class="badge bg-danger">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <button type="button"
                            class="btn btn-sm btn-outline-primary"
                            title="Edit Data"
                            wire:click="edit('{{ $pendidik->id }}')"
                            wire:loading.attr="disabled"
                            wire:loading.class="disabled">
                            <span wire:loading.remove wire:target="edit('{{ $pendidik->id }}')">
                                <i class="mdi mdi-pencil"></i>
                            </span>
                            <span wire:loading wire:target="edit('{{ $pendidik->id }}')">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            </span>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                        @if($search)
                        Tidak ada data tenaga pendidik yang sesuai dengan pencarian "{{ $search }}"
                        @else
                        Belum ada data tenaga pendidik.
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($tenagaPendidik->hasPages())
    <div class="mt-3">
        {{ $tenagaPendidik->links() }}
    </div>
    @endif

    {{-- Modal Edit Bootstrap --}}
    <div class="modal fade @if($showEditModal) show @endif"
        id="editTendikModal"
        tabindex="-1"
        aria-labelledby="editTendikModalLabel"
        aria-hidden="true"
        style="@if($showEditModal) display: block; @endif">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Tenaga Pendidik</h5>
                    <button type="button"
                        class="btn-close"
                        wire:click="closeEditModal"
                        aria-label="Close"></button>
                </div>

                <form wire:submit.prevent="update">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('editStatus') is-invalid @enderror"
                                wire:model="editStatus">
                                <option value="">-- Pilih Status --</option>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                            @error('editStatus')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Apakah Guru Agama? <span class="text-danger">*</span></label>
                            <select class="form-select @error('editIsGuruAgama') is-invalid @enderror"
                                wire:model.live="editIsGuruAgama">
                                <option value="">-- Pilih --</option>
                                <option value="1">Ya</option>
                                <option value="0">Tidak</option>
                            </select>
                            @error('editIsGuruAgama')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($editIsGuruAgama == '1')
                        <div class="mb-3">
                            <label class="form-label">Mata Pelajaran Agama <span class="text-danger">*</span></label>
                            <select class="form-select @error('editSpesialisasiAgama') is-invalid @enderror"
                                wire:model="editSpesialisasiAgama">
                                <option value="">-- Pilih --</option>
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Konghucu">Konghucu</option>
                            </select>
                            @error('editSpesialisasiAgama')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-outline-secondary"
                            wire:click="closeEditModal">
                            <i class="mdi mdi-close"></i> Batal
                        </button>
                        <button type="submit"
                            class="btn btn-primary"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="update">
                                <i class="mdi mdi-content-save"></i> Simpan
                            </span>
                            <span wire:loading wire:target="update">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Backdrop --}}
    @if($showEditModal)
    <div class="modal-backdrop fade show" wire:click="closeEditModal"></div>
    @endif

    {{-- Loading indicator hanya saat update --}}
    <div wire:loading wire:target="update"
        class="position-fixed top-50 start-50 translate-middle"
        style="z-index: 9999;">
        <div class="bg-white rounded-3 shadow p-3">
            <div class="d-flex align-items-center">
                <div class="spinner-border text-primary me-2" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span>Menyimpan perubahan...</span>
            </div>
        </div>
    </div>
</div>





<div>
    <!-- Pesan Notifikasi -->
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Berhasil!</strong> Data berhasil disimpan.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Filter & Pencarian -->
    <div class="d-flex justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <span>Tampilkan</span>
            <select class="form-select form-select-sm">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <span>data</span>
        </div>
        <div>
            <div class="position-relative">
                <input type="text" class="form-control" placeholder="Cari nama, email, NIP..." style="width:250px;">
            </div>
        </div>
    </div>

    <!-- Tabel Data Tenaga Pendidik -->
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th style="width: 35%;">Tenaga Pendidik</th>
                    <th style="width: 35%;">Penugasan</th>
                    <th style="width: 20%;">Status</th>
                    <th style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="https://via.placeholder.com/40"
                                alt="Guru 1"
                                style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 10px;" />
                            <div>
                                <p class="mb-0 fw-medium">Ahmad Yani</p>
                                <small>0812-3456-7890</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-primary">Guru Agama</span>
                        <p class="mb-0">Islam</p>
                        <small class="text-muted">NIP: 19790901 200801 1 002</small>
                    </td>
                    <td>
                        <span class="badge bg-success">Aktif</span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTendikModal">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="https://via.placeholder.com/40"
                                alt="Guru 2"
                                style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 10px;" />
                            <div>
                                <p class="mb-0 fw-medium">Siti Rahmawati</p>
                                <small>siti@example.com</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-secondary">Guru Umum</span>
                        <small class="text-muted">NIP: -</small>
                    </td>
                    <td>
                        <span class="badge bg-danger">Nonaktif</span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTendikModal">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination Dummy -->
    <div class="mt-3 text-center">
        <nav>
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item disabled"><span class="page-link">‹</span></li>
                <li class="page-item active"><span class="page-link">1</span></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">›</a></li>
            </ul>
        </nav>
    </div>

    <!-- Modal Edit Bootstrap -->
    <div class="modal fade" id="editTendikModal" tabindex="-1" aria-labelledby="editTendikModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTendikModalLabel">Edit Data Tenaga Pendidik</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select">
                            <option value="">-- Pilih Status --</option>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Apakah Guru Agama? <span class="text-danger">*</span></label>
                        <select class="form-select">
                            <option value="">-- Pilih --</option>
                            <option value="1">Ya</option>
                            <option value="0">Tidak</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mata Pelajaran Agama</label>
                        <select class="form-select">
                            <option value="">-- Pilih --</option>
                            <option value="Islam">Islam</option>
                            <option value="Kristen">Kristen</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Konghucu">Konghucu</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="mdi mdi-close"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary">
                        <i class="mdi mdi-content-save"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>