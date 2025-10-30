<div>
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
                <input type="text" class="form-control" placeholder="Cari..."
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
                <th style="width: 30%;">
                    <p class="mb-0">Mata Pelajaran</p>
                    <small>Nama Mapel | Kode Mapel</small>
                </th>
                <th style="width: 30%;">
                    <p class="mb-0">Kelompok</p>
                    <small>Urutan | Kelompok</small>
                </th>
                <th style="width: 30%;">
                    <p class="mb-0">Kurikulum & Tingkat</p>
                    <small>Kelas | Kurikulum</small>
                </th>
                <th style="width: 10%;">
                    <p class="mb-0">Aksi</p>
                    <small>Edit | Delete</small>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kurikulumMataPelajaran as $data)
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/icons/icon_13.png') }}" alt="guru" style="width: 32px; height: 32px; object-fit: cover;">
                        <div class="table-user-name ml-3">
                            <p class="mb-0 font-weight-medium">{{ $data->mataPelajaran->nama ?? '-' }}</p>
                            <small class="text-muted">{{ $data->mataPelajaran->kode ?? '-' }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <p class="mb-0">Urutan: {{ $data->urutan ?? '-' }}</p>
                    <span class="text-muted"><small>{{ $data->kelompok->nama ?? 'Tidak Ada Kelompok' }}</small></span>
                </td>
                <td>
                    <p class="mb-0">Kelas: {{ $data->tingkat }} </p>
                    <span class="text-muted"><small>{{ $data->kurikulum->nama ?? '-' }}</small></span>
                </td>
                <td>
                    <button type="button" class="border-0 bg-transparent" title="Edit" wire:click="edit('{{ $data->id }}')">
                        <img src="{{ asset('assets/images/icons/edit.png') }}" width="30" height="30" alt="Edit">
                    </button>
                    <button type="button" class="border-0 bg-transparent" title="Delete" wire:click="confirmDeleteKurikulumMtp('{{ $data->id }}')">
                        <img src="{{ asset('assets/images/icons/delete.png') }}" width="30" height="30" alt="Delete">
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted">Belum ada data mata pelajaran yang terikat pada kurikulum.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $kurikulumMataPelajaran->links() }}
    </div>

    {{-- Modal Form --}}
    <div wire:ignore.self class="modal fade" id="modalKurikulumMtp" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit Mapel Kurikulum' : 'Tambah Mapel ke Kurikulum' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{-- Kurikulum ID --}}
                    <div class="mb-3">
                        <label for="kurikulum_id" class="form-label">Kurikulum</label>
                        <select id="kurikulum_id" class="form-select" wire:model.live="kurikulum_id">
                            <option value="" disabled>-- Pilih Kurikulum --</option>
                            @foreach ($kurikulums as $kurikulum)
                            <option value="{{ $kurikulum->id }}">{{ $kurikulum->nama }}</option>
                            @endforeach
                        </select>
                        @error('kurikulum_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Mata Pelajaran ID --}}
                    <div class="mb-3">
                        <label for="mata_pelajaran_id" class="form-label">Mata Pelajaran</label>
                        <select id="mata_pelajaran_id" class="form-select" wire:model.live="mata_pelajaran_id">
                            <option value="" disabled>-- Pilih Mata Pelajaran --</option>
                            @foreach ($mataPelajaranList as $mapel)
                            <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
                            @endforeach
                        </select>
                        @error('mata_pelajaran_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="row">
                        {{-- Tingkat --}}
                        <div class="col-md-6 mb-3">
                            <label for="tingkat" class="form-label">Tingkat Kelas</label>
                            <input type="number" id="tingkat" wire:model.live="tingkat" class="form-control" placeholder="Contoh: 10">
                            @error('tingkat') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        {{-- Urutan --}}
                        <div class="col-md-6 mb-3">
                            <label for="urutan" class="form-label">Urutan Tampil (Opsional)</label>
                            <input type="number" id="urutan" wire:model="urutan" class="form-control" placeholder="Contoh: 1">
                            @error('urutan') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>


                    {{-- Kelompok ID (Nullable) --}}
                    <div class="mb-3">
                        <label for="kelompok_id" class="form-label">Kelompok Mata Pelajaran (Opsional)</label>
                        <select id="kelompok_id" class="form-select" wire:model="kelompok_id">
                            <option value="">-- Tidak Ada Kelompok --</option>
                            @foreach ($kelompokList as $kelompok)
                            <option value="{{ $kelompok->id }}">{{ $kelompok->kode }} - {{ $kelompok->nama }}</option>
                            @endforeach
                        </select>
                        @error('kelompok_id') <small class="text-danger">{{ $message }}</small> @enderror
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
    window.addEventListener('openModalKurikulumMtp', () => {
        new bootstrap.Modal(document.getElementById('modalKurikulumMtp')).show();
    });

    window.addEventListener('closeModalKurikulumMtp', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalKurikulumMtp'));
        if (modal) modal.hide();
    });
</script>
@endpush