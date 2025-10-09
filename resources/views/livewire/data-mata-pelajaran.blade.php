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
        <div>
            <input type="text" class="form-control" placeholder="Cari Mata Pelajaran..."
                wire:model.live.debounce.500ms="search" style="width:250px;">
        </div>
    </div>

    <table class="table table-hover mb-0">
        <thead class="bg-light">
            <tr>
                <th style="width: 40%;">
                    <p class="mb-0">Mata Pelajaran</p>
                    <small>Nama Mata Pelajaran</small>
                </th>
                <th style="width: 25%;">
                    <p class="mb-0">Kodes</p>
                    <small>Mata Pelajaran</small>
                </th>
                <th style="width: 25%;">
                    <p class="mb-0">Status</p>
                    <small>Aktif | Arsip</small>
                </th>
                <th style="width: 10%;">
                    <p class="mb-0">Aksi</p>
                    <small>Edit | Delete</small>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($mata_pelajarans as $index => $mapel)
            <tr>
                <td>{{ $mapel->nama }}</td>
                <td>{{ $mapel->kode }}</td>
                <td>
                    <span class="badge bg-{{ $mapel->status === 'aktif' ? 'success' : 'secondary' }}">
                        {{ ucfirst($mapel->status) }}
                    </span>
                </td>
                <td>
                    {{-- Tombol Update --}}
                    <button type="button" class="border-0 bg-transparent" title="Update" wire:click="edit('{{ $mapel->id }}')">
                        <img src="{{ asset('assets/images/icons/edit.png') }}" width="30" height="30" alt="Edit">
                    </button>

                    <!-- Form Delete -->
                    <form class="d-inline">
                        <button type="button" class="border-0 bg-transparent" title="Delete" wire:click="confirmDelete('{{ $mapel->id }}')">
                            <img src="{{ asset('assets/images/icons/delete.png') }}" width="30" height="30" alt="Delete">
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="mt-2">
        {{ $mata_pelajarans->onEachSide(1)->links() }}
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="modalMapel" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit Mata Pelajaran' : 'Tambah Mata Pelajaran' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Mata Pelajaran</label>
                        <input type="text" class="form-control" placeholder="Input mata pelajaran" wire:model="nama">
                        @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-3">
                        <label>Kode Mata Pelajaran</label>
                        <input type="text" class="form-control" placeholder="Input kode mata pelajaran" wire:model="kode">
                        @error('kode') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status Mata Pelajaran</label>
                        <select id="status" class="form-select" wire:model="status">
                            <option value="" disabled>-- Pilih Status --</option>
                            <option value="aktif">Aktif</option>
                            <option value="arsip">Arsip</option>
                        </select>
                        @error('status')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
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
    window.addEventListener('openModal', () => {
        new bootstrap.Modal(document.getElementById('modalMapel')).show();
    });

    window.addEventListener('closeModal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalMapel'));
        if (modal) modal.hide();
    });
</script>
@endpush