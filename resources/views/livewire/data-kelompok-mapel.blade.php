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
                <th style="width: 45%;">
                    <p class="mb-0">Kelompok</p>
                    <small>Kelompok Mata Pelajaran</small>
                </th>
                <th style="width: 45%;">
                    <p class="mb-0">Kode</p>
                    <small>Kode Kelompok</small>
                </th>
                <th style="width: 10%;">
                    <p class="mb-0">Aksi</p>
                    <small>Edit | Delete</small>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($kelompoks as $kelompok)
            <tr>
                <td>{{ $kelompok->nama }}</td>
                <td>{{ $kelompok->kode }}</td>
                <td>
                    <button type="button" class="border-0 bg-transparent" title="Edit" wire:click="edit('{{ $kelompok->id }}')">
                        <img src="{{ asset('assets/images/icons/edit.png') }}" width="30" height="30" alt="Edit">
                    </button>
                    <button type="button" class="border-0 bg-transparent" title="Delete" wire:click="confirmDeleteKelompok('{{ $kelompok->id }}')">
                        <img src="{{ asset('assets/images/icons/delete.png') }}" width="30" height="30" alt="Delete">
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center text-muted">Belum ada data kelompok mata pelajaran.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $kelompoks->links() }}
    </div>

    {{-- Modal Form --}}
    <div wire:ignore.self class="modal fade" id="modalKelompok" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit Kelompok Mata Pelajaran' : 'Tambah Kelompok Mata Pelajaran' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Kelompok</label>
                        <input type="text" wire:model="kode" class="form-control" placeholder="Contoh: A">
                        @error('kode') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Kelompok</label>
                        <input type="text" wire:model="nama" class="form-control" placeholder="Contoh: Kelompok A">
                        @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
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
    // Pastikan modal bootstrap terinisialisasi dengan benar
    window.addEventListener('openModalKelompok', () => {
        new bootstrap.Modal(document.getElementById('modalKelompok')).show();
    });

    window.addEventListener('closeModalKelompok', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalKelompok'));
        if (modal) modal.hide();
    });
</script>
@endpush