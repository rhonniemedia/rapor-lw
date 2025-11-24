<div>
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-3">
        <h6 class="fw-bold mb-0">📘 Daftar Kurikulum</h6>
        <button type="button" wire:click="create" class="btn btn-outline-light-muted btn-sm d-flex align-items-center justify-content-center">
            <i class="mdi mdi-plus"></i>
        </button>
    </div>

    <table class="table table-hover mb-0">
        <thead class="bg-light">
            <tr>
                <th style="width: 30%;">
                    <p class="mb-0">Kurikulum</p>
                    <small>Nama | Kode</small>
                </th>
                <th style="width: 30%;">
                    <p class="mb-0">Deskripsi</p>
                    <small>Deskripsi Kurikulum</small>
                </th>
                <th style="width: 30%;">
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
            @forelse ($kurikulums as $kurikulum)
            <tr>
                <td>{{ $kurikulum->nama }} ({{ $kurikulum->kode }})</td>
                <td>{{ $kurikulum->deskripsi ?? '-' }}</td>
                <td>
                    @if ($kurikulum->status === 'aktif')
                    <span class="badge bg-success">Aktif</span>
                    @else
                    <span class="badge bg-secondary">Arsip</span>
                    @endif
                </td>
                <td>
                    <button type="button" class="border-0 bg-transparent" title="Edit" wire:click="edit('{{ $kurikulum->id }}')">
                        <img src="{{ asset('assets/images/icons/edit.png') }}" width="30" height="30" alt="Edit">
                    </button>
                    <button type="button" class="border-0 bg-transparent" title="Delete" wire:click="confirmDeleteKurikulum('{{ $kurikulum->id }}')">
                        <img src="{{ asset('assets/images/icons/delete.png') }}" width="30" height="30" alt="Delete">
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted">Belum ada data kurikulum.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">
        {{ $kurikulums->links() }}
    </div>

    {{-- Modal Form --}}
    <div wire:ignore.self class="modal fade" id="modalKurikulum" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit Data Kurikulum' : 'Tambah Data Kurikulum' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kurikulum</label>
                        <input type="text" wire:model="nama" class="form-control" placeholder="Input nama kurikulum">
                        @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kode Kurikulum</label>
                        <input type="text" wire:model="kode" class="form-control" placeholder="Input kode kurikulum">
                        @error('kode') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea wire:model="deskripsi" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
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
    window.addEventListener('openModalKurikulum', () => {
        new bootstrap.Modal(document.getElementById('modalKurikulum')).show();
    });

    window.addEventListener('closeModalKurikulum', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalKurikulum'));
        if (modal) modal.hide();
    });
</script>
@endpush