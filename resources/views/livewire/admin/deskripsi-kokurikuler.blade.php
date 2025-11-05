<div>
    {{-- Input Search & Per Page (Sama seperti sebelumnya) --}}
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
                    placeholder="Cari subdimensi..."
                    wire:model.live.debounce.500ms="search" style="width:250px;">
            </div>
            <button type="button" wire:click="openModal"
                class="btn btn-outline-light-muted btn-sm d-flex align-items-center justify-content-center h-100"
                style="padding: 0 0.75rem;">
                <i class="mdi mdi-plus"></i>
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 25%;">Subdimensi</th>
                    <th style="width: 10%;">Tingkat</th>
                    <th style="width: 10%;">Predikat</th>
                    <th style="width: 45%;">Deskripsi</th>
                    <th style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($templatesGrouped as $key => $group)
                @php
                $rowspan = count($group);
                @endphp

                @foreach ($group as $index => $template)
                <tr>
                    @if ($index === 0)
                    <td rowspan="{{ $rowspan }}">
                        {{-- Gunakan ucwords agar tampilan Subdimensi tetap rapi meskipun di Livewire disimpan lowercase --}}
                        <strong>{{ ucwords($template['subdimensi']) }}</strong>
                    </td>
                    <td rowspan="{{ $rowspan }}">
                        <span class="badge bg-info">{{ $template['tingkat'] }}</span>
                    </td>
                    @endif
                    <td>
                        <span class="badge {{ $template['predikat'] === 'A' ? 'bg-success' : ($template['predikat'] === 'B' ? 'bg-primary' : 'bg-warning') }}">
                            {{ $template['predikat'] }}
                        </span>
                    </td>
                    <td style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word; max-width: 300px;">
                        {{ $template['deskripsi'] }}
                    </td>
                    @if ($index === 0)
                    <td rowspan="{{ $rowspan }}">
                        <button class="btn btn-sm btn-outline-secondary"
                            wire:click="edit('{{ $template['subdimensi'] }}', '{{ $template['tingkat'] }}')">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                        <button
                            wire:key="delete-btn-{{ $template['subdimensi'] }}-{{ $template['tingkat'] }}"
                            id="delete-btn-{{ $template['subdimensi'] }}-{{ $template['tingkat'] }}"
                            class="btn btn-sm btn-outline-danger"
                            x-data
                            @click="confirmDeleteKokurikuler(
                                        '{{ e(ucwords($template['subdimensi'])) }}',
                                        '{{ e($template['tingkat']) }}'
                                    )"
                            title="Hapus Template">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </td>
                    @endif
                </tr>
                @endforeach
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="mdi mdi-information-outline mdi-24px d-block mb-2"></i>
                        Tidak ada data template kokurikuler
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination (Sama seperti sebelumnya) --}}
    @if($paginator && $paginator->hasPages())
    <div class="mt-3">
        {{ $paginator->links() }}
    </div>
    @endif

    <div wire:ignore.self class="modal fade" id="modalKokurikuler" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $editMode ? 'Edit' : 'Tambah' }} Template Kokurikuler</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Subdimensi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" wire:model.defer="selectedSubdimensi" placeholder="Misal: Kepedulian Sosial" {{ $editMode ? 'disabled' : '' }}>
                            @error('selectedSubdimensi') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tingkat Kelas <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model.defer="tingkat" {{ $editMode ? 'disabled' : '' }}>
                                <option value="">-- Pilih Tingkat --</option>
                                <option value="10">X</option>
                                <option value="11">XI</option>
                                <option value="12">XII</option>
                            </select>
                            @error('tingkat') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <hr>

                    {{-- REVISI: Hanya loop untuk A, B, C --}}
                    @foreach (['A' => 'Mahir', 'B' => 'Cakap', 'C' => 'Berkembang'] as $predikat => $label)
                    <div class="card mb-3">
                        <div class="card-header pl-3 bg-light text-dark">
                            <strong>Predikat {{ $predikat }} ({{ $label }})</strong>
                        </div>
                        <div class="card-body p-1">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                                <textarea class="form-control" wire:model.defer="templates.{{ $predikat }}.deskripsi" rows="4"></textarea>
                                @error("templates.{$predikat}.deskripsi") <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    {{-- Tombol Batal --}}
                    <button type="button" class="btn btn-labeled btn-secondary" data-bs-dismiss="modal">
                        <span class="btn-label">
                            <i class="mdi mdi-close-circle-outline"></i>
                        </span>
                        Batal
                    </button>
                    {{-- Tombol Simpan --}}
                    <button
                        type="button"
                        class="btn btn-labeled btn-primary"
                        wire:click="save"
                        wire:loading.attr="disabled"
                        wire:target="save">
                        <span class="btn-label">
                            <i class="mdi mdi-loading mdi-spin d-none"
                                wire:loading.class.remove="d-none"
                                wire:target="save">
                            </i>

                            <i class="mdi mdi-content-save"
                                wire:loading.class="d-none"
                                wire:target="save">
                            </i>
                        </span>

                        <span class="text-normal" wire:loading.class="d-none" wire:target="save">
                            Simpan
                        </span>

                        <span class="text-loading d-none" wire:loading.class.remove="d-none" wire:target="save">
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ✅ Function untuk handle delete confirmation dengan loading state
        window.confirmDeleteKokurikuler = function(subdimensi, tingkat) {
            Swal.fire({
                icon: 'warning',
                title: 'Hapus Template Kokurikuler?',
                // Pesan disesuaikan: menghapus A, B, dan C
                html: `Anda yakin ingin menghapus semua template predikat (A, B, C) untuk <strong>${subdimensi}</strong> tingkat <strong>${tingkat}</strong>?`,
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then(result => {
                if (result.isConfirmed) {
                    // Logika tombol loading
                    const btn = document.getElementById(`delete-btn-${subdimensi}-${tingkat}`);
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i>';
                    }

                    // Dispatch ke backend - kirim sebagai 2 parameter
                    Livewire.dispatch('deleteKokurikulerTemplate', [subdimensi, tingkat]);
                }
            });
        };
    });

    // ✅ Modal handlers untuk Livewire (Sama seperti sebelumnya)
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('open-modal-kokurikuler', () => {
            const modalElement = document.getElementById('modalKokurikuler');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        });

        Livewire.on('close-modal-kokurikuler', () => {
            const modalElement = document.getElementById('modalKokurikuler');
            if (modalElement) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            }
        });
    });
</script>
@endpush