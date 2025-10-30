<div>
    {{-- Header & Search --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <input
                type="text"
                wire:model.live="search"
                placeholder="Cari nama, NIS, atau tempat lahir..."
                class="form-control">
        </div>
        <div class="col-md-2">
            <select wire:model.live="perPage" class="form-select">
                <option value="10">10 per halaman</option>
                <option value="25">25 per halaman</option>
                <option value="50">50 per halaman</option>
                <option value="100">100 per halaman</option>
            </select>
        </div>
        <div class="col-md-4 text-end">
            <button wire:click="create" class="btn btn-primary">
                <i class="ti ti-plus"></i> Tambah Data
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Nama</th>
                    <th>Tempat Lahir</th>
                    <th>Tanggal Lahir</th>
                    <th>Jenis Kelamin</th>
                    <th>NIK</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pelajars as $index => $pelajar)
                <tr>
                    <td>{{ $pelajars->firstItem() + $index }}</td>
                    <td>{{ $pelajar->nis }}</td>
                    <td>{{ $pelajar->nama }}</td>
                    <td>{{ $pelajar->tempat_lahir }}</td>
                    <td>{{ date('d/m/Y', strtotime($pelajar->tgl_lahir)) }}</td>
                    <td>{{ $pelajar->jk == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                    <td>{{ $pelajar->nik }}</td>
                    <td>
                        <button wire:click="edit('{{ $pelajar->id }}')" class="btn btn-sm btn-warning">
                            <i class="mdi mdi-file-edit"></i>
                        </button>
                        <button
                            wire:click="confirmDelete('{{ $pelajar->id }}')"
                            class="btn btn-sm btn-danger">
                            <i class="mdi mdi-file-remove"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">Data tidak ditemukan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-2">
        {{ $pelajars->onEachSide(1)->links() }}
    </div>

    {{-- Modal Form --}}
    <div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit' : 'Tambah' }} Data Pelajar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">NIS</label>
                            <input type="text" wire:model="nis" class="form-control @error('nis') is-invalid @enderror">
                            @error('nis') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text" wire:model="nik" class="form-control @error('nik') is-invalid @enderror" placeholder="Nomor Induk Kependudukan">
                            @error('nik') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" wire:model="nama" class="form-control @error('nama') is-invalid @enderror">
                            @error('nama') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tempat Lahir</label>
                            <input type="text" wire:model="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror">
                            @error('tempat_lahir') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" wire:model="tgl_lahir" class="form-control @error('tgl_lahir') is-invalid @enderror">
                            @error('tgl_lahir') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select wire:model="jk" class="form-select @error('jk') is-invalid @enderror">
                                <option value="">Pilih...</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            @error('jk') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea wire:model="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3"></textarea>
                            @error('alamat') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update' : 'Simpan' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.addEventListener('openModal', event => {
        var modalEl = document.getElementById('modalForm');
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    });

    window.addEventListener('closeModal', event => {
        var modalEl = document.getElementById('modalForm');
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }
    });

    // Reset form ketika modal ditutup
    document.addEventListener('DOMContentLoaded', function() {
        var modalEl = document.getElementById('modalForm');
        if (modalEl) {
            modalEl.addEventListener('hidden.bs.modal', function() {
                Livewire.dispatch('resetForm'); // atau panggil method Livewire
            });
        }
    });
</script>
@endpush