<div>
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="page-header pb-3 mb-4 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper position-relative">
                                <span class="bg-gradient-primary p-2 rounded-3 shadow-sm me-3 d-inline-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-settings mdi-24px text-white"></i>
                                </span>
                            </div>
                            <div>
                                <h4 class="mb-1 text-dark fw-bold">Pengaturan Rapor</h4>
                                <div class="d-flex align-items-center gap-2">
                                    <small class="text-muted">Kelola pengaturan tahun ajaran, kepala sekolah, dan tanggal rapor</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span>Show</span>
                            <select class="form-select form-select-sm h-100" wire:model.live="perPage">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <span>entries</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div>
                                <input type="text" class="form-control"
                                    wire:model.live.debounce.300ms="search"
                                    placeholder="Cari Kepala Sekolah...">
                            </div>
                            <button type="button" wire:click="create" class="btn btn-outline-light-muted btn-sm d-flex align-items-center justify-content-center h-100" style="padding: 0 0.75rem;">
                                <i class="mdi mdi-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="30%">
                                        <p class="mb-0">Tahun Ajaran</p>
                                        <small>Tahun Ajaran | Semester</small>
                                    </th>
                                    <th width="30%">
                                        <p class="mb-0">Kepala Sekolah</p>
                                        <small>Nama | Nomor Induk Pegawai</small>
                                    </th>
                                    <th width="30%">
                                        <p class="mb-0">Tanggal Rapor</p>
                                        <small>Tanggal Pengesahan</small>
                                    </th>
                                    <th width="10%">
                                        <p class="mb-0">Aksi</p>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pengaturans as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('assets/images/icons/icon_2.png') }}" alt="image">
                                            <div class="table-user-name ml-3">
                                                <p class="mb-0 font-weight-medium">
                                                    {{ $item->tahunAjaranSemester->tahunAjaran->nama ?? '-' }}
                                                </p>
                                                <small>
                                                    {{ $item->tahunAjaranSemester->semester->nama ?? '-' }} ({{ $item->tahunAjaranSemester->semester->urutan ?? '-' }})
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="mb-0 font-weight-medium">{{ $item->kepalaSekolah->name ?? 'Unknown' }}</p>
                                        <small class="text-muted">
                                            <span> NIP </span>
                                            <span class="font-weight-medium">{{ $item->kepalaSekolah->nip ?? $item->kepalaSekolah->id }}</span>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning">{{ \Carbon\Carbon::parse($item->tanggal_rapor)->format('d-m-Y') }}</span>
                                    </td>
                                    <td>
                                        <button
                                            type="button"
                                            class="btn btn-outline-light-muted btn-sm"
                                            wire:click="edit('{{ $item->id }}')"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false"
                                            title="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">Tidak ada data pengaturan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $pengaturans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pengaturanModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit Pengaturan' : 'Buat Pengaturan' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="{{ $this->actionMethod }}">
                    <!-- <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}"> -->
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran & Semester</label>
                            <select class="form-select @error('tahun_ajaran_semester_id') is-invalid @enderror" wire:model="tahun_ajaran_semester_id">
                                <option value="">-- Pilih --</option>
                                @foreach($listTahunSemester as $ta)
                                <option value="{{ $ta['id'] }}">{{ $ta['nama'] }}</option>
                                @endforeach
                            </select>
                            @error('tahun_ajaran_semester_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kepala Sekolah</label>
                            <select class="form-select @error('kepala_sekolah_id') is-invalid @enderror" wire:model="kepala_sekolah_id">
                                <option value="">-- Pilih Kepala Sekolah --</option>
                                @foreach($listKepsek as $ks)
                                <option value="{{ $ks->id }}">{{ $ks->name }}</option>
                                @endforeach
                            </select>
                            @error('kepala_sekolah_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Rapor</label>
                            <input type="date" class="form-control @error('tanggal_rapor') is-invalid @enderror" wire:model="tanggal_rapor">
                            @error('tanggal_rapor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                            class="btn btn-labeled btn-secondary"
                            data-bs-dismiss="modal">

                            <span class="btn-label">
                                <i class="mdi mdi-close"></i>
                            </span>

                            Tutup
                        </button>
                        <button type="submit"
                            class="btn btn-labeled btn-primary"
                            wire:loading.attr="disabled"
                            wire:target="{{ $this->actionMethod }}">

                            <span class="btn-label">
                                <i class="mdi mdi-loading mdi-spin d-none"
                                    wire:loading.class.remove="d-none"
                                    wire:target="{{ $this->actionMethod }}"></i>

                                <i class="mdi mdi-content-save"
                                    wire:loading.class="d-none"
                                    wire:target="{{ $this->actionMethod }}"></i>
                            </span>

                            <span wire:loading.class="d-none"
                                wire:target="{{ $this->actionMethod }}">
                                Simpan
                            </span>

                            <span class="d-none"
                                wire:loading.class.remove="d-none"
                                wire:target="{{ $this->actionMethod }}">
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {

        console.log('Livewire initialized - Delete feature loaded');

        // ------------------------ 1. Modal Helper ------------------------
        const modalElement = document.getElementById('pengaturanModal');
        if (modalElement) {
            const modalInstance = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false
            });

            Livewire.on('modal:show', () => modalInstance.show());
            Livewire.on('modal:hide', () => modalInstance.hide());
        }

        // ------------------------ 2. SweetAlert: Success ------------------------
        Livewire.on('swal:success', (data) => {
            console.log('Success event received:', data);

            Swal.close();
            Swal.fire({
                icon: 'success',
                title: data.title || 'Berhasil!',
                text: data.text || '',
                showConfirmButton: false,
                timer: 1500
            });
        });

        // ------------------------ 3. SweetAlert: Error ------------------------
        Livewire.on('swal:error', (data) => {
            console.log('Error event received:', data);

            Swal.close();
            Swal.fire({
                icon: 'error',
                title: data.title || 'Oops...',
                text: data.text || ''
            });
        });
    });
</script>
@endpush