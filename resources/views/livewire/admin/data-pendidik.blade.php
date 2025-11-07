<div>
    <div x-data="{
        init() {
            // Inisialisasi modal Bootstrap
            let modal = new bootstrap.Modal(document.getElementById('editTendikModal'));

            // Livewire 3: Mendengarkan event dari komponen untuk membuka modal
            Livewire.on('show-edit-modal', () => {
                modal.show();
            });

            // Livewire 3: Mendengarkan event dari komponen untuk menutup modal
            Livewire.on('hide-edit-modal', () => {
                modal.hide();
            });
            
            // Tambahan: Reset properti Livewire saat modal ditutup oleh user (tombol close/escape)
            // Ini penting karena wire:ignore.self mencegah Livewire mendeteksi penutupan modal dari sisi klien.
            document.getElementById('editTendikModal').addEventListener('hidden.bs.modal', (event) => {
                @this.call('closeEditModal');
            });
        }
    }">
        @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show">
            <strong>Gagal!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="d-flex justify-content-between mb-3">
            <div class="d-flex align-items-center gap-2">
                <span>Tampilkan</span>
                <select class="form-select form-select-sm" wire:model.live="perPage">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span>data</span>
            </div>
            <div>
                <div class="position-relative">
                    <input type="text" class="form-control" placeholder="Cari nama, email, NIP..." style="width:250px;" wire:model.live="search">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 35%;">
                            <p class="mb-0">Tenaga Pendidik</p>
                            <small>Nama | Kontak</small>

                        </th>
                        <th style="width: 35%;">
                            <p class="mb-0">Penugasan</p>
                            <small>Mata Pelajaran | Rombel</small>

                        </th>
                        <th style="width: 20%;">
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
                    @forelse ($tenagaPendidik as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/icons/pilot.png') }}"
                                    alt="{{ $user->name }}"
                                    style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 10px;" />
                                <div class="table-user-name">
                                    <p class="mb-0 font-weight-medium">{{ $user->name }}</p>
                                    <small>{{ $user->telephone ?? $user->email ?? '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if ($user->is_guru_agama)
                            <span class="badge bg-primary">Guru Agama</span>
                            <p class="mb-0">{{ $user->spesialisasi_agama ?? 'Belum Ditentukan' }}</p>
                            @else
                            <span class="badge bg-secondary">Guru Umum</span>
                            <p class="mb-0">Umum</p>
                            @endif
                        </td>
                        <td>
                            @if ($user->status == 'aktif')
                            <span class="badge bg-success">Aktif</span>
                            @else
                            <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="edit('{{ $user->id }}')">
                                <i class="mdi mdi-pencil"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Data tenaga pendidik tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 text-center">
            {{ $tenagaPendidik->onEachSide(1)->links() }}
        </div>

        <div class="modal fade" id="editTendikModal" tabindex="-1" aria-labelledby="editTendikModalLabel" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog">
                <form wire:submit.prevent="update">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editTendikModalLabel">Edit Data Tenaga Pendidik</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @error('editStatus') <div class="alert alert-danger">{{ $message }}</div> @enderror
                            <div class="mb-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model.live="editStatus">
                                    <option value="">-- Pilih Status --</option>
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                            </div>

                            @error('editIsGuruAgama') <div class="alert alert-danger">{{ $message }}</div> @enderror
                            <div class="mb-3">
                                <label class="form-label">Apakah Guru Agama? <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model.live="editIsGuruAgama">
                                    <option value="">-- Pilih --</option>
                                    <option value="1">Ya</option>
                                    <option value="0">Tidak</option>
                                </select>
                            </div>

                            @if ($editIsGuruAgama === '1')
                            @error('editSpesialisasiAgama') <div class="alert alert-danger">{{ $message }}</div> @enderror
                            <div class="mb-3">
                                <label class="form-label">Mata Pelajaran Agama <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model.live="editSpesialisasiAgama">
                                    <option value="">-- Pilih --</option>
                                    <option value="Islam">Islam</option>
                                    <option value="Kristen">Kristen</option>
                                    <option value="Katolik">Katolik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Buddha">Buddha</option>
                                    <option value="Konghucu">Konghucu</option>
                                </select>
                            </div>
                            @endif
                        </div>

                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-labeled btn-outline-secondary"
                                data-bs-dismiss="modal">
                                <span class="btn-label">
                                    <i class="mdi mdi-close"></i>
                                </span>
                                Batal
                            </button>

                            <button
                                type="submit"
                                class="btn btn-labeled btn-primary"
                                wire:loading.attr="disabled"
                                wire:target="update">

                                <span class="btn-label">
                                    {{-- Icon loading tampil saat update aktif --}}
                                    <i class="mdi mdi-loading mdi-spin d-none"
                                        wire:loading.class.remove="d-none"
                                        wire:target="update">
                                    </i>

                                    {{-- Icon simpan/check hilang saat loading. Asumsi menggunakan mdi-content-save --}}
                                    <i class="mdi mdi-content-save"
                                        wire:loading.class="d-none"
                                        wire:target="update">
                                    </i>
                                </span>

                                {{-- Teks tombol normal --}}
                                <span wire:loading.class="d-none" wire:target="update">
                                    Simpan
                                </span>

                                {{-- Teks saat loading --}}
                                <span class="d-none" wire:loading.class.remove="d-none" wire:target="update">
                                    Memproses...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>