<div>
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-3">
        {{-- Menampilkan detil rombel --}}
        <h6 class="fw-bold mb-0">👥 Anggota Rombel: {{ $rombel->nama ?? 'N/A' }}</h6>
        <button type="button" wire:click="create" class="btn btn-outline-light-muted btn-sm d-flex align-items-center justify-content-center">
            <i class="mdi mdi-plus"></i> Tambah Pelajar
        </button>
    </div>

    {{-- Info Rombel --}}
    <div class="card mb-3">
        <div class="card-body">
            <p class="mb-1"><strong>Tingkat & Jurusan:</strong> Kelas {{ $rombel->tingkat ?? '-' }} - {{ $rombel->jurusan->nama ?? 'Tidak Dikenal' }}</p>
            <p class="mb-1"><strong>Wali Kelas:</strong> {{ $rombel->walikelas_name ?? 'Belum Ditentukan' }}</p>
            <p class="mb-0"><strong>Kurikulum:</strong>
                @if ($rombel->tahunAjaranKurikulum)
                {{ $rombel->tahunAjaranKurikulum->tahunAjaran->nama ?? '?' }} ({{ $rombel->tahunAjaranKurikulum->kurikulum->nama ?? '?' }})
                @else
                Global
                @endif
            </p>
        </div>
    </div>

    {{-- Input Search & Per Page --}}
    <div class="d-flex justify-content-end mb-3">
        <input type="text" wire:model.live.debounce.300ms="search" class="form-control w-50" placeholder="Cari Nama atau NISN Pelajar...">
    </div>

    <table class="table table-hover mb-0">
        <thead class="bg-light">
            <tr>
                <th style="width: 10%;">#</th>
                <th style="width: 40%;">
                    <p class="mb-0">Nama Pelajar</p>
                </th>
                <th style="width: 40%;">
                    <p class="mb-0">NISN / NIS</p>
                </th>
                <th style="width: 10%;">
                    <p class="mb-0">Aksi</p>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rombelPelajars as $index => $data)
            <tr>
                <td>{{ $rombelPelajars->firstItem() + $index }}</td>
                <td>{{ $data->pelajar->nama_lengkap ?? '-' }}</td>
                <td>{{ $data->pelajar->nisn ?? 'N/A' }} / {{ $data->pelajar->nis ?? 'N/A' }}</td>
                <td>
                    {{-- Tombol hapus (keluarkan dari rombel) --}}
                    <button type="button" class="border-0 bg-transparent" title="Keluarkan" wire:click="confirmDeleteRombelPelajar('{{ $data->id }}')">
                        <img src="{{ asset('assets/images/icons/delete.png') }}" width="30" height="30" alt="Delete">
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted">Belum ada pelajar yang terdaftar dalam rombel ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $rombelPelajars->links() }}
    </div>

    {{-- Modal Form Tambah Pelajar --}}
    <div wire:ignore.self class="modal fade" id="modalRombelPelajar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form wire:submit.prevent="store" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pelajar ke Rombel {{ $rombel->nama ?? '-' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pelajar_id" class="form-label">Pilih Pelajar</label>
                        <select id="pelajar_id" class="form-select" wire:model="pelajar_id">
                            <option value="">-- Pilih Pelajar yang Tersedia --</option>
                            @if (!empty($availablePelajars))
                            @forelse ($availablePelajars as $pelajar)
                            <option value="{{ $pelajar->id }}">{{ $pelajar->nama_lengkap }} ({{ $pelajar->nisn }})</option>
                            @empty
                            <option value="" disabled>Semua pelajar sudah terdaftar di rombel</option>
                            @endforelse
                            @else
                            <option value="" disabled>Daftar pelajar belum dimuat</option>
                            @endif
                        </select>
                        @error('pelajar_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-labeled btn-secondary" data-bs-dismiss="modal">
                        <span class="btn-label"><i class="mdi mdi-close-outline"></i></span>Batal
                    </button>
                    <button type="submit" class="btn btn-labeled btn-primary" {{ empty($availablePelajars) ? 'disabled' : '' }}>
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
    window.addEventListener('openModalRombelPelajar', () => {
        new bootstrap.Modal(document.getElementById('modalRombelPelajar')).show();
    });

    window.addEventListener('closeModalRombelPelajar', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalRombelPelajar'));
        if (modal) modal.hide();
    });
</script>
@endpush