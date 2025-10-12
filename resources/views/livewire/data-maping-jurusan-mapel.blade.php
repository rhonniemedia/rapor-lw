<div>
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-3">
        <h6 class="fw-bold mb-0">🏷️ Mata Pelajaran per Jurusan</h6>
        <button type="button" wire:click="create" class="btn btn-outline-light-muted btn-sm d-flex align-items-center justify-content-center">
            <i class="mdi mdi-plus"></i>
        </button>
    </div>

    <table class="table table-hover mb-0">
        <thead class="bg-light">
            <tr>
                <th style="width: 30%;">
                    <p class="mb-0">Jurusan</p>
                    <small>Konsentrasi Keahlian</small>
                </th>
                <th style="width: 30%;">
                    <p class="mb-0">Mata Pelajaran</p>
                    <small>Mata Pelajaran Jurusan</small>
                </th>
                <th style="width: 30%;">
                    <p class="mb-0">Kurikulum & Status</p>
                    <small>Wajib | Pilihan</small>
                </th>
                <th style="width: 10%;">
                    <p class="mb-0">Aksi</p>
                    <small>Edit | Delete</small>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($jurusanMataPelajaran as $data)
            <tr>
                <td>{{ $data->jurusan->nama ?? '-' }}</td>
                <td>
                    <strong>{{ $data->mataPelajaran->nama ?? '-' }}</strong>
                </td>
                <td>
                    <span class="badge bg-{{ $data->status === 'wajib' ? 'success' : 'primary' }}">{{ ucfirst($data->status) }}</span><br>
                    <small class="text-muted">{{ $data->kurikulum->nama ?? 'Semua Kurikulum' }}</small>
                </td>
                <td>
                    <button type="button" class="border-0 bg-transparent" title="Edit" wire:click="edit('{{ $data->id }}')">
                        <img src="{{ asset('assets/images/icons/edit.png') }}" width="30" height="30" alt="Edit">
                    </button>
                    <button type="button" class="border-0 bg-transparent" title="Delete" wire:click="confirmDeleteJurusanMtp('{{ $data->id }}')">
                        <img src="{{ asset('assets/images/icons/delete.png') }}" width="30" height="30" alt="Delete">
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted">Belum ada data mata pelajaran yang terikat pada jurusan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $jurusanMataPelajaran->links() }}
    </div>

    {{-- Modal Form --}}
    <div wire:ignore.self class="modal fade" id="modalJurusanMtp" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit Mapel Jurusan' : 'Tambah Mapel ke Jurusan' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{-- Jurusan ID --}}
                    <div class="mb-3">
                        <label for="jurusan_id" class="form-label">Jurusan</label>
                        <select id="jurusan_id" class="form-select" wire:model.live="jurusan_id">
                            <option value="" disabled>-- Pilih Jurusan --</option>
                            @foreach ($jurusanList as $jurusan)
                            <option value="{{ $jurusan->id }}">{{ $jurusan->nama }}</option>
                            @endforeach
                        </select>
                        @error('jurusan_id') <small class="text-danger">{{ $message }}</small> @enderror
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

                    {{-- Kurikulum ID (Nullable) --}}
                    <div class="mb-3">
                        <label for="kurikulum_id" class="form-label">Kurikulum (Opsional)</label>
                        <select id="kurikulum_id" class="form-select" wire:model.live="kurikulum_id">
                            <option value="">-- Semua Kurikulum --</option>
                            @foreach ($kurikulumList as $kurikulum)
                            <option value="{{ $kurikulum->id }}">{{ $kurikulum->nama }}</option>
                            @endforeach
                        </select>
                        @error('kurikulum_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label for="status" class="form-label">Status Mata Pelajaran</label>
                        <select id="status" class="form-select" wire:model="status">
                            <option value="wajib">Wajib</option>
                            <option value="pilihan">Pilihan</option>
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
    // Event listener untuk membuka dan menutup modal
    window.addEventListener('openModalJurusanMtp', () => {
        new bootstrap.Modal(document.getElementById('modalJurusanMtp')).show();
    });

    window.addEventListener('closeModalJurusanMtp', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalJurusanMtp'));
        if (modal) modal.hide();
    });
</script>
@endpush