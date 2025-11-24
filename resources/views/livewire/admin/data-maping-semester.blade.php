<div>
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-3">
        <h6 class="fw-bold mb-0">📅 Relasi Tahun Ajaran & Semester</h6>
        <button type="button" wire:click="create" class="btn btn-outline-light-muted btn-sm d-flex align-items-center justify-content-center">
            <i class="mdi mdi-plus"></i>
        </button>
    </div>

    <table class="table table-hover mb-0">
        <thead class="bg-light">
            <tr>
                <th style="width: 30%;">
                    <p class="mb-0">Tahun Ajaran & Semester</p>
                    <small>Tahun Ajaran | Semester</small>
                </th>
                <th style="width: 30%;">
                    <p class="mb-0">Periode</p>
                    <small>Tgl Mulai & Tgl Selesai</small>
                </th>
                <th style="width: 30%;">
                    <p class="mb-0">Status</p>
                    <small>Aktif | Nonaktif</small>
                </th>
                <th style="width: 10%;">
                    <p class="mb-0">Aksi</p>
                    <small>Edit | Hapus</small>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tahunAjaranSemesters as $taSemester)
            <tr>
                <td>
                    <strong>{{ $taSemester->tahunAjaran->nama ?? 'N/A' }}</strong> ~ {{ $taSemester->semester->nama ?? 'N/A' }}
                </td>
                <td>
                    {{ \Carbon\Carbon::parse($taSemester->tgl_mulai)->format('d M Y') }}
                    <i class="mdi mdi-arrow-right"></i>
                    {{ \Carbon\Carbon::parse($taSemester->tgl_selesai)->format('d M Y') }}
                </td>
                <td>
                    @if ($taSemester->status === 'aktif')
                    <span class="badge bg-success">Aktif</span>
                    @else
                    <span class="badge bg-secondary">Nonaktif</span>
                    @endif
                </td>
                <td>
                    <button type="button" class="border-0 bg-transparent" title="Edit Periode/Status" wire:click="edit('{{ $taSemester->id }}')">
                        <img src="{{ asset('assets/images/icons/edit.png') }}" width="30" height="30" alt="Edit">
                    </button>
                    <button type="button" class="border-0 bg-transparent" title="Hapus Pemetaan" wire:click="confirmDeleteMappingSemester('{{ $taSemester->id }}')">
                        <img src="{{ asset('assets/images/icons/delete.png') }}" width="30" height="30" alt="Delete">
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted">Belum ada data Pemetaan Semester.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">
        {{ $tahunAjaranSemesters->links() }}
    </div>

    {{-- Modal Form --}}
    <div wire:ignore.self class="modal fade" id="modalMappingSemester" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit Relasi Semester' : 'Tambah Relasi Semester' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    {{-- Pilihan Tahun Ajaran & Semester (Hanya saat Store) --}}
                    @if (!$isEdit)
                    <div class="mb-3">
                        <label for="tahun_ajaran_id" class="form-label">Tahun Ajaran</label>
                        <select id="tahun_ajaran_id" class="form-select" wire:model="tahun_ajaran_id">
                            <option value="" disabled>-- Pilih Tahun Ajaran --</option>
                            @foreach ($listTahunAjarans as $ta)
                            <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                            @endforeach
                        </select>
                        @error('tahun_ajaran_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="semester_id" class="form-label">Semester</label>
                        <select id="semester_id" class="form-select" wire:model="semester_id">
                            <option value="" disabled>-- Pilih Semester --</option>
                            @foreach ($listSemesters as $semester)
                            <option value="{{ $semester->id }}">{{ $semester->nama }}</option>
                            @endforeach
                        </select>
                        @error('semester_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    @else
                    {{-- Tampilkan data yang diedit (Read-Only) --}}
                    <div class="mb-3">
                        <label class="form-label">Tahun Ajaran</label>
                        <input type="text" class="form-control" value="Pemetaan ID: {{ $ta_semester_id }}" disabled>
                        <small class="text-muted">Tahun Ajaran dan Semester tidak bisa diubah setelah dibuat.</small>
                    </div>
                    @endif


                    {{-- Tanggal Mulai dan Selesai --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tgl_mulai" class="form-label">Tanggal Mulai</label>
                            <input type="date" id="tgl_mulai" wire:model="tgl_mulai" class="form-control">
                            @error('tgl_mulai') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tgl_selesai" class="form-label">Tanggal Selesai</label>
                            <input type="date" id="tgl_selesai" wire:model="tgl_selesai" class="form-control">
                            @error('tgl_selesai') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" class="form-select" wire:model="status">
                            <option value="nonaktif">Nonaktif</option>
                            <option value="aktif">Aktif (Mengaktifkan ini akan menonaktifkan yang lain)</option>
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

    @push('scripts')
    <script>
        // Logika untuk membuka dan menutup modal
        window.addEventListener('openModalMappingSemester', () => {
            new bootstrap.Modal(document.getElementById('modalMappingSemester')).show();
        });

        window.addEventListener('closeModalMappingSemester', () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalMappingSemester'));
            if (modal) modal.hide();
        });
    </script>
    @endpush
</div>