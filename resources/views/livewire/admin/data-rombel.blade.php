<div>
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
        <div>
            <input type="text" class="form-control" placeholder="Cari..."
                wire:model.live.debounce.500ms="search" style="width:250px;">
        </div>
    </div>

    <table class="table table-hover mb-0">
        <thead class="bg-light">
            <tr>
                <th style="width: 30%;">
                    <p class="mb-0">Rombongan Belajar</p>
                    <small>Kelas | Tingkat | Kurikulum</small>
                </th>
                <th style="width: 30%;">
                    <p class="mb-0">Wali Kelas</p>
                    <small>Nama Wali Kelas</small>
                </th>
                <th style="width: 30%;">
                    <p class="mb-0">Jumlah</p>
                    <small>Jumlah Peserta Didik</small>
                </th>
                <th style="width: 10%;">
                    <p class="mb-0">Aksi</p>
                    <small>Edit | Delete</small>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rombels as $rombel)
            <tr>
                <td>
                    <a class="hyper-link text-decoration-none" href="{{ route('admin.class.detail', $rombel->id) }}">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('assets/images/icons/school.png') }}" alt="image" />
                            <div class="table-user-name ml-3">
                                <p class="mb-0 font-weight-medium"> {{ $rombel->nama }} </p>
                                <small>{{ $rombel->jurusan->nama }} ({{ optional(optional($rombel->tahunAjaranKurikulum)->kurikulum)->kode ?? '-' }})</small>
                            </div>
                        </div>
                    </a>
                </td>
                <td>
                    <p class="mb-0 font-weight-medium">{{ $rombel->walikelas_name ?? 'Belum Ditentukan' }}</p>
                    <small>NIP {{ $rombel->waliKelas->nip ?? '~' }}</small>
                </td>
                <td>
                    <div class="d-flex gap-1 align-items-center">
                        <span class="badge badge-inverse-dark d-flex align-items-center gap-1">
                            <i class="mdi mdi-plus"></i><strong>{{ $rombel->total_pelajar ?? 0 }}</strong>
                        </span>
                        <span class="badge badge-inverse-primary d-flex align-items-center gap-1">
                            <i class="mdi mdi-gender-male"></i><strong>{{ $rombel->total_laki ?? 0 }}</strong>
                        </span>
                        <span class="badge badge-inverse-danger d-flex align-items-center gap-1">
                            <i class="mdi mdi-gender-female"></i><strong>{{ $rombel->total_perempuan ?? 0 }}</strong>
                        </span>
                    </div>
                </td>
                <td>
                    <button type="button" class="border-0 bg-transparent" title="Edit" wire:click="edit('{{ $rombel->id }}')">
                        <img src="{{ asset('assets/images/icons/edit.png') }}" width="30" height="30" alt="Edit">
                    </button>
                    <!-- <button type="button" class="border-0 bg-transparent" title="Delete" wire:click="confirmDeleteRombel('{{ $rombel->id }}')">
                        <img src="{{ asset('assets/images/icons/delete.png') }}" width="30" height="30" alt="Delete">
                    </button> -->
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted">Belum ada data rombongan belajar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $rombels->links() }}
    </div>

    {{-- Modal Form --}}
    <div wire:ignore.self class="modal fade" id="modalRombel" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit Rombongan Belajar' : 'Tambah Rombongan Belajar' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row">

                        {{-- Rombongan Belajar --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Rombongan Belajar</label>
                            <input type="text" wire:model="nama" class="form-control" placeholder="Contoh: X RPL1">
                            @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- Tingkat --}}
                        <div class="col-md-6 mb-3">
                            <label for="tingkat" class="form-label">Tingkat Kelas</label>
                            <select id="tingkat" class="form-select" wire:model.live="tingkat">
                                <option value="" disabled>-- Pilih Tingkat --</option>
                                <option value="10">10 (X)</option>
                                <option value="11">11 (XI)</option>
                                <option value="12">12 (XII)</option>
                            </select>
                            @error('tingkat') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- Jurusan ID --}}
                    <div class="mb-3">
                        <label for="jurusan_id" class="form-label">Jurusan</label>
                        <select id="jurusan_id" class="form-select" wire:model.live="jurusan_id">
                            <option value="" disabled>-- Pilih Jurusan --</option>
                            @foreach ($jurusanList as $jurusan)
                            <option value="{{ $jurusan->id }}">{{ $jurusan->nama }}</option>
                            @endforeach
                        </select>
                        @error('jurusan_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Wali Kelas Slug (String) --}}
                    <div class="mb-3">
                        <label for="wali_kelas_slug" class="form-label">Wali Kelas (Opsional)</label>
                        <select id="wali_kelas_slug" class="form-select" wire:model="wali_kelas_slug">
                            <option value="" disabled>-- Belum Ditentukan --</option>
                            @foreach ($walikelasList as $user)
                            <option value="{{ $user->slug }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('wali_kelas_slug') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Tahun Ajaran Kurikulum ID (Nullable) --}}
                    <div class="mb-3">
                        <label for="tahun_ajaran_kurikulum_id" class="form-label">Kurikulum Rombel (Opsional)</label>
                        <select id="tahun_ajaran_kurikulum_id" class="form-select" wire:model="tahun_ajaran_kurikulum_id">
                            <option value="">-- Pilih Kurikulum --</option>
                            @foreach ($tahunAjaranKurikulumList as $tak)
                            <option value="{{ $tak->id }}">{{ $tak->display_name }}</option>
                            @endforeach
                        </select>
                        @error('tahun_ajaran_kurikulum_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-labeled btn-secondary" data-bs-dismiss="modal">
                        <span class="btn-label"><i class="mdi mdi-close-outline"></i></span>Batal
                    </button>
                    <button type="submit" class="btn btn-labeled btn-primary">
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
    window.addEventListener('openModalRombel', () => {
        new bootstrap.Modal(document.getElementById('modalRombel')).show();
    });

    window.addEventListener('closeModalRombel', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalRombel'));
        if (modal) modal.hide();
    });
</script>
@endpush