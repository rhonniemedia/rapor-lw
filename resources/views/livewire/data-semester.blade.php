<div>
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-3">
        <h6 class="fw-bold mb-0">📚 Daftar Semester</h6>
        <button type="button" wire:click="create" class="btn btn-outline-light-muted btn-sm d-flex align-items-center justify-content-center">
            <i class="mdi mdi-plus"></i>
        </button>
    </div>

    <table class="table table-hover mb-0">
        <thead class="bg-light">
            <tr>
                <th style="width: 50%;">
                    <p class="mb-0">Semester</p>
                    <small>Ganjil | Genap</small>
                </th>
                <th style="width: 40%;">
                    <p class="mb-0">Urutan</p>
                    <small>Semester ke (1 atau 2)</small>
                </th>
                <th style="width: 10%;">
                    <p class="mb-0">Aksi</p>
                    <small>Edit | Delete</small>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($semesters as $semester)
            <tr>
                <td>{{ $semester->nama }}</td>
                <td>
                    @if($semester->urutan)
                    <span class="badge bg-info">Semester {{ $semester->urutan }}</span>
                    @else
                    <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <button type="button" class="border-0 bg-transparent" title="Edit" wire:click="edit('{{ $semester->id }}')">
                        <img src="{{ asset('assets/images/icons/edit.png') }}" width="30" height="30" alt="Edit">
                    </button>
                    <button type="button" class="border-0 bg-transparent" title="Delete" wire:click="confirmDeleteSemester('{{ $semester->id }}')">
                        <img src="{{ asset('assets/images/icons/delete.png') }}" width="30" height="30" alt="Delete">
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center text-muted">Belum ada data semester.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $semesters->links() }}
    </div>

    {{-- Modal Form --}}
    <div wire:ignore.self class="modal fade" id="modalSemester" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit Data Semester' : 'Tambah Data Semester' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Semester <span class="text-danger">*</span></label>
                        <input type="text" wire:model="nama" class="form-control" placeholder="Contoh: Semester Ganjil">
                        @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Urutan Semester</label>
                        <select wire:model="urutan" class="form-select">
                            <option value="">-- Pilih Urutan --</option>
                            <option value="1">Semester 1 (Ganjil)</option>
                            <option value="2">Semester 2 (Genap)</option>
                        </select>
                        @error('urutan') <small class="text-danger">{{ $message }}</small> @enderror
                        <small class="text-muted">Opsional: Penanda semester ke-berapa dalam tahun ajaran</small>
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
    window.addEventListener('openModalSemester', () => {
        new bootstrap.Modal(document.getElementById('modalSemester')).show();
    });

    window.addEventListener('closeModalSemester', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalSemester'));
        if (modal) modal.hide();
    });
</script>
@endpush