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
            <input type="search" class="form-control" placeholder="Cari ekstrakurikuler..."
                wire:model.live.debounce.500ms="search" style="width:250px;">
        </div>
    </div>

    <table class="table table-hover mb-0">
        <thead class="bg-light">
            <tr>

                <th style="width: 30%;">
                    <p class="mb-0">Ekstrakurikuler</p>
                    <small>Nama Ekstrakurikuler | Pembina</small>
                </th>
                <th style="width: 30%;">
                    <p class="mb-0">Deskripsi</p>
                    <small>Kegiatan Ekstrakurikuler</small>
                </th>
                <th style="width: 30%;">
                    <p class="mb-0">Status</p>
                    <small>Aktif | Non Aktif</small>
                </th>
                <th style="width: 10%;">
                    <p class="mb-0">Aksi</p>
                    <small>Edit | Delete</small>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ekstrakurikulers as $ekstra)
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/icons/ekskul.png') }}" alt="image" />
                        <div class="table-user-name ml-3">
                            <p class="mb-0 font-weight-medium"> {{ $ekstra->nama ?? '-' }} </p>
                            <small> {{ $ekstra->pembina?->name ?? '-' }}</small>
                        </div>
                    </div>
                </td>
                <td>{{ Str::limit($ekstra->deskripsi, 50) }}</td>
                <td>
                    <span class="badge bg-{{ $ekstra->status === 'aktif' ? 'success' : 'secondary' }}">
                        {{ ucfirst($ekstra->status) }}
                    </span>
                </td>
                <td>
                    <button type="button" class="border-0 bg-transparent" title="Update"
                        wire:click="edit('{{ $ekstra->id }}')">
                        <img src="{{ asset('assets/images/icons/edit.png') }}" width="28" height="28" alt="Edit">
                    </button>
                    <button type="button" class="border-0 bg-transparent" title="Delete"
                        wire:click="confirmDelete('{{ $ekstra->id }}')">
                        <img src="{{ asset('assets/images/icons/delete.png') }}" width="28" height="28" alt="Delete">
                    </button>
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
        {{ $ekstrakurikulers->onEachSide(1)->links() }}
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="modalEkstrakurikuler" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit Ekstrakurikuler' : 'Tambah Ekstrakurikuler' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Ekstrakurikuler</label>
                        <input type="text" class="form-control" wire:model="nama">
                        @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label>Deskripsi</label>
                        <textarea class="form-control" rows="3" wire:model="deskripsi"></textarea>
                        @error('deskripsi') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label>Pembina</label>
                        <select class="form-select" wire:model="pembina_id">
                            <option value="">-- Pilih Pembina --</option>
                            @foreach ($pembinas as $pembina)
                            <option value="{{ $pembina->id }}">{{ $pembina->name }}</option>
                            @endforeach
                        </select>
                        @error('pembina_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select class="form-select" wire:model="status">
                            <option value="aktif">Aktif</option>
                            <option value="arsip">Arsip</option>
                        </select>
                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
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
        new bootstrap.Modal(document.getElementById('modalEkstrakurikuler')).show();
    });

    window.addEventListener('closeModal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEkstrakurikuler'));
        if (modal) modal.hide();
    });
</script>
@endpush