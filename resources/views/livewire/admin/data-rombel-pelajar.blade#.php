<div>
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-3">
        {{-- Menampilkan detil rombel --}}
        <h6 class="fw-bold mb-0">👥 Anggota Rombel: {{ $rombel->nama ?? 'N/A' }}</h6>
        <button type="button" wire:click="create" class="btn btn-outline-light-muted btn-sm d-flex align-items-center justify-content-center">
            <i class="mdi mdi-plus"></i> Tambah Pelajar
        </button>
    </div>

    {{-- Info Rombel --}}
    <div class="card mb-3">
        <div class="card-body">
            <p class="mb-1"><strong>Tingkat & Jurusan:</strong> Kelas {{ $rombel->tingkat ?? '-' }} - {{ $rombel->jurusan->alias ?? 'Tidak Dikenal' }}</p>
            <p class="mb-1"><strong>Wali Kelas:</strong> {{ $rombel->waliKelas->name ?? 'Belum Ditentukan' }}</p>
            <p class="mb-0"><strong>Kurikulum:</strong>
                @if ($rombel->tahunAjaranKurikulum)
                {{ $rombel->tahunAjaranKurikulum->tahunAjaran->nama ?? '?' }} ({{ $rombel->tahunAjaranKurikulum->kurikulum->nama ?? '?' }})
                @else
                Global
                @endif
            </p>
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
                <input type="text" class="form-control" placeholder="Cari Pelajar..."
                    wire:model.live.debounce.500ms="search" style="width:250px;">
            </div>
            <button type="button" wire:click="create" class="btn btn-outline-light-muted btn-sm d-flex align-items-center justify-content-center h-100" style="padding: 0 0.75rem;">
                <i class="mdi mdi-plus"></i>
            </button>
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
                    <small>Edit | Delete</small>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rombelPelajars as $index => $data)
            <tr>
                <td>
                    <a class="hyper-link text-decoration-none" href="">
                        <div class="d-flex align-items-center">
                            <img src="{{ $data->pelajar->icon }}" alt="image">
                            <div class="table-user-name ml-3">
                                <p class="mb-0 font-weight-medium"> {{ $data->pelajar->nama_lengkap ?? '-' }} </p>
                                <small class="text-muted font-weight-medium"> {{ $data->pelajar->jenis_kelamin_label ?? 'N/A' }} </small>
                            </div>
                        </div>
                    </a>
                </td>
                <td>
                    <p class="mb-0 font-weight-medium">{{ $data->pelajar->tempat_lahir ?? 'N/A' }}</p>
                    <small class="text-muted"><strong> Tanggal </strong>{{ $data->pelajar->tanggal_lahir_formatted ?? 'N/A' }}</small>
                </td>
                <td>

                    <div class="badge badge-inverse-success mr-1">{{ $data->pelajar->nomor_induk ?? 'N/A' }}</div>

                    <div class="badge badge-inverse-warning">{{ $data->pelajar->nisn ?? 'N/A' }}</div>

                </td>
                <td></td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted">Belum ada pelajar yang terdaftar dalam rombel ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $rombelPelajars->links() }}
    </div>

    {{-- Modal Form Tambah Pelajar --}}
    <div wire:ignore.self class="modal fade" id="modalRombelPelajar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="store" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pelajar ke Rombel {{ $rombel->nama ?? '-' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pelajar_id" class="form-label">Pilih Pelajar</label>
                        <select id="pelajar_id" class="form-select" wire:model="pelajar_id">
                            <option value="">-- Pilih Pelajar yang Tersedia --</option>
                            @if (!empty($availablePelajars))
                            @forelse ($availablePelajars as $pelajar)
                            <option value="{{ $pelajar->id }}">{{ $pelajar->nama_lengkap }} ({{ $pelajar->nisn }})</option>
                            @empty
                            <option value="" disabled>Semua pelajar sudah terdaftar di rombel</option>
                            @endforelse
                            @else
                            <option value="" disabled>Daftar pelajar belum dimuat</option>
                            @endif
                        </select>
                        @error('pelajar_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-labeled btn-secondary" data-bs-dismiss="modal">
                        <span class="btn-label"><i class="mdi mdi-close-outline"></i></span>Batal
                    </button>
                    <button type="submit" class="btn btn-labeled btn-primary" {{ empty($availablePelajars) ? 'disabled' : '' }}>
                        <span class="btn-label"><i class="mdi mdi-content-save"></i></span>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Event listener untuk membuka dan menutup modal
    window.addEventListener('openModalRombelPelajar', () => {
        new bootstrap.Modal(document.getElementById('modalRombelPelajar')).show();
    });

    window.addEventListener('closeModalRombelPelajar', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalRombelPelajar'));
        if (modal) modal.hide();
    });
</script>
@endpush