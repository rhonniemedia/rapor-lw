<div>
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-3">
        <h6 class="fw-bold mb-0">📅 Daftar Tahun Ajaran</h6>
        <button type="button" wire:click="create" class="btn btn-outline-light-muted btn-sm d-flex align-items-center justify-content-center">
            <i class="mdi mdi-plus"></i>
        </button>
    </div>

    <table class="table table-hover mb-0">
        <thead class="bg-light">
            <tr>
                <th style="width: 30%;">
                    <p class="mb-0">Tahun Ajaran</p>
                    <small>Contoh: 2025/2026</small>
                </th>
                <th style="width: 30%;">
                    <p class="mb-0">Periode</p>
                    <small>Tanggal Mulai & Selesai</small>
                </th>
                <th style="width: 30%;">
                    <p class="mb-0">Status</p>
                    <small>Aktif | Nonaktif</small>
                </th>
                <th style="width: 10%;">
                    <p class="mb-0">Aksi</p>
                    <small>Edit | Delete</small>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tahunAjarans as $tahun)
            <tr>
                <td>{{ $tahun->nama }}</td>
                <td>{{ \Carbon\Carbon::parse($tahun->tgl_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tahun->tgl_selesai)->format('d/m/Y') }}</td>
                <td>
                    @if ($tahun->status === 'aktif')
                    <span class="badge bg-success">Aktif</span>
                    @else
                    <span class="badge bg-secondary">Nonaktif</span>
                    @endif
                </td>
                <td>
                    <button type="button" class="border-0 bg-transparent" title="Edit" wire:click="edit('{{ $tahun->id }}')">
                        <img src="{{ asset('assets/images/icons/edit.png') }}" width="30" height="30" alt="Edit">
                    </button>
                    <button type="button" class="border-0 bg-transparent" title="Delete" wire:click="confirmDeleteTahunAjaran('{{ $tahun->id }}')">
                        <img src="{{ asset('assets/images/icons/delete.png') }}" width="30" height="30" alt="Delete">
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted">Belum ada data tahun ajaran.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $tahunAjarans->links() }}
    </div>

    {{-- Modal Form --}}
    <div wire:ignore.self class="modal fade" id="modalTahunAjaran" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit Tahun Ajaran' : 'Tambah Tahun Ajaran' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Tahun Ajaran</label>
                        <input type="text" wire:model="nama" class="form-control" placeholder="Contoh: 2025/2026">
                        @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" wire:model="tgl_mulai" class="form-control">
                            @error('tgl_mulai') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" wire:model="tgl_selesai" class="form-control">
                            @error('tgl_selesai') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" class="form-select" wire:model="status">
                            <option value="" disabled>-- Pilih Status --</option>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
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
    window.addEventListener('openModalTahunAjaran', () => {
        new bootstrap.Modal(document.getElementById('modalTahunAjaran')).show();
    });

    window.addEventListener('closeModalTahunAjaran', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalTahunAjaran'));
        if (modal) modal.hide();
    });
</script>
@endpush