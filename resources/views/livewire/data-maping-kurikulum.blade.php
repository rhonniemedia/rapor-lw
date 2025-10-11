<div class="card my-4" style="width: 100%;">
    <div class="card-body">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-3">
            <h6 class="fw-bold mb-0">🔗 Relasi Tahun Ajaran & Kurikulum</h6>
            <button type="button" wire:click="create" class="btn btn-outline-light-muted btn-sm d-flex align-items-center justify-content-center">
                <i class="mdi mdi-plus"></i>
            </button>
        </div>

        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th style="width: 30%;">
                        <p class="mb-0">Tahun Ajaran</p>
                        <small>Tahun Ajaran Aktif</small>
                    </th>
                    <th style="width: 30%;">
                        <p class="mb-0">Kurikulum</p>
                        <small>Nama | Kode</small>
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
                @forelse ($tahunAjaranKurikulums as $taKurikulum)
                <tr>
                    <td>{{ $taKurikulum->tahunAjaran->nama ?? 'N/A' }}</td>
                    <td>{{ $taKurikulum->kurikulum->nama ?? 'N/A' }} ({{ $taKurikulum->kurikulum->kode ?? 'N/A' }})</td>
                    <td>
                        @if ($taKurikulum->status === 'aktif')
                        <span class="badge bg-success">Aktif</span>
                        @else
                        <span class="badge bg-danger">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <button type="button" class="border-0 bg-transparent" title="Edit Status" wire:click="edit('{{ $taKurikulum->id }}')">
                            <img src="{{ asset('assets/images/icons/edit.png') }}" width="30" height="30" alt="Edit">
                        </button>
                        <button type="button" class="border-0 bg-transparent" title="Hapus Relasi" wire:click="confirmDeleteTahunAjaranKurikulum('{{ $taKurikulum->id }}')">
                            <img src="{{ asset('assets/images/icons/delete.png') }}" width="30" height="30" alt="Delete">
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">Belum ada data relasi Tahun Ajaran Kurikulum.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $tahunAjaranKurikulums->links() }}
        </div>
    </div>

    {{-- Modal Form --}}
    <div wire:ignore.self class="modal fade" id="modalTahunAjaranKurikulum" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            {{-- Saat edit, kita hanya meng-update status --}}
            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit Status Relasi' : 'Tambah Relasi Tahun Ajaran & Kurikulum' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Saat Edit, tampilkan data yang diedit dan disable dropdown --}}
                    @if ($isEdit)
                    <div class="mb-3">
                        <label class="form-label">Kurikulum</label>
                        <input type="text" class="form-control" value="{{ optional(optional($tahunAjaranKurikulums->where('id', $ta_kurikulum_id)->first())->kurikulum)->nama ?? 'Loading...' }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tahun Ajaran</label>
                        <input type="text" class="form-control" value="{{ optional(optional($tahunAjaranKurikulums->where('id', $ta_kurikulum_id)->first())->tahunAjaran)->nama ?? 'Loading...' }}" disabled>
                    </div>
                    @else
                    {{-- Saat Store, tampilkan dropdown pilihan --}}
                    <div class="mb-3">
                        <label for="kurikulum_id" class="form-label">Kurikulum</label>
                        <select id="kurikulum_id" class="form-select" wire:model="kurikulum_id">
                            <option value="" disabled>-- Pilih Kurikulum --</option>
                            @foreach ($listKurikulums as $kurikulum)
                            <option value="{{ $kurikulum->id }}">{{ $kurikulum->nama }} ({{ $kurikulum->kode }})</option>
                            @endforeach
                        </select>
                        @error('kurikulum_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran</label>
                        <select id="tahun_ajaran_id" class="form-select" wire:model="tahun_ajaran_id">
                            <option value="" disabled>-- Pilih Tahun Ajaran --</option>
                            @foreach ($listTahunAjarans as $tahunAjaran)
                            <option value="{{ $tahunAjaran->id }}">{{ $tahunAjaran->nama }}</option>
                            @endforeach
                        </select>
                        @error('tahun_ajaran_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    @endif

                    {{-- Status selalu ada --}}
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" class="form-select" wire:model="status">
                            <option value="" disabled>-- Pilih Status --</option>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
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
    // Pastikan Bootstrap tersedia (seperti pada contoh sebelumnya)
    window.addEventListener('openModalTahunAjaranKurikulum', () => {
        new bootstrap.Modal(document.getElementById('modalTahunAjaranKurikulum')).show();
    });

    window.addEventListener('closeModalTahunAjaranKurikulum', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalTahunAjaranKurikulum'));
        if (modal) modal.hide();
    });
</script>
@endpush