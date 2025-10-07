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
            <input type="text" class="form-control" placeholder="Cari jurusan..."
                wire:model.debounce.500ms="search" style="width:250px;">
        </div>
    </div>

    <table class="table table-hover mb-0">
        <thead class="bg-light">
            <tr>
                <th style="width: 40%;">
                    <p class="mb-0">Konsentrasi Keahlian</p>
                    <small>Nama Konsentrasi Keahlian</small>
                </th>
                <th style="width: 25%;">
                    <p class="mb-0">Alias</p>
                    <small>Alias Konsentrasi Keahlian</small>
                </th>
                <th style="width: 25%;">
                    <p class="mb-0">Kode</p>
                    <small>Kode Konsentrasi Keahlian</small>
                </th>
                <th style="width: 10%;">
                    <p class="mb-0">Aksi</p>
                    <small>Edit | Delete</small>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($jurusans as $index => $jurusan)
            <tr>
                <td>{{ $jurusan->nama }}</td>
                <td>{{ $jurusan->alias }}</td>
                <td>{{ $jurusan->kode }}</td>
                <td>
                    {{-- Tombol Update --}}
                    <button type="button" class="border-0 bg-transparent" title="Update" wire:click="edit('{{ $jurusan->id }}')">
                        <img src="{{ asset('assets/images/icons/edit.png') }}" width="30" height="30" alt="Edit">
                    </button>

                    <!-- Form Delete -->
                    <form class="d-inline">
                        <button type="button" class="border-0 bg-transparent" title="Delete" wire:click="confirmDelete('{{ $jurusan->id }}')">
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
        {{ $jurusans->onEachSide(1)->links() }}
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="modalJurusan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit Jurusan' : 'Tambah Jurusan' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Jurusan</label>
                        <input type="text" class="form-control" wire:model="nama">
                        @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-3">
                        <label>Alias</label>
                        <input type="text" class="form-control" wire:model="alias">
                        @error('alias') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-3">
                        <label>Kode</label>
                        <input type="text" class="form-control text-uppercase" wire:model="kode" maxlength="10">
                        @error('kode') <small class="text-danger">{{ $message }}</small> @enderror
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
        new bootstrap.Modal(document.getElementById('modalJurusan')).show();
    });

    window.addEventListener('closeModal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalJurusan'));
        if (modal) modal.hide();
    });
</script>
@endpush