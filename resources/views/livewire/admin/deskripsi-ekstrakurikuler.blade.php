<div>
    {{-- Input Search & Per Page --}}
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
                    placeholder="Cari ekstrakurikuler..."
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
                    <th style="width: 25%;">Ekstrakurikuler</th>
                    <th style="width: 10%;">Predikat</th>
                    <th style="width: 55%;">Deskripsi</th>
                    <th style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($templatesGrouped as $key => $group)
                @php
                $rowspan = count($group);
                $ekskulId = $group[0]['ekstrakurikuler_id'] ?? 'general';
                @endphp

                @foreach ($group as $index => $template)
                <tr>
                    @if ($index === 0)
                    <td rowspan="{{ $rowspan }}">
                        <strong>{{ $template['ekstrakurikuler_nama'] }}</strong>
                    </td>
                    @endif
                    <td>
                        <span class="badge {{ $template['predikat'] === 'A' ? 'bg-success' : ($template['predikat'] === 'B' ? 'bg-primary' : 'bg-warning') }}">
                            {{ $template['predikat'] }}
                        </span>
                    </td>
                    <td style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word; max-width: 300px;">
                        <p class="mb-0"><span class="text-muted">{{ $template['deskripsi'] }}</span></p>
                    </td>
                    @if ($index === 0)
                    <td rowspan="{{ $rowspan }}">
                        <button class="btn btn-sm btn-outline-secondary"
                            wire:click="edit('{{ $ekskulId }}')">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                        <button
                            wire:key="delete-btn-{{ $ekskulId }}"
                            id="delete-btn-{{ $ekskulId }}"
                            class="btn btn-sm btn-outline-danger"
                            x-data
                            @click="confirmDeleteEkskul(
                                        '{{ $ekskulId }}',
                                        '{{ e($template['ekstrakurikuler_nama']) }}'
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
                        Tidak ada data template ekstrakurikuler
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($paginator && $paginator->hasPages())
    <div class="mt-3">
        {{ $paginator->links() }}
    </div>
    @endif

    <div wire:ignore.self class="modal fade" id="modalEkskul" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $editMode ? 'Edit' : 'Tambah' }} Template Ekstrakurikuler</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3 align-items-end">
                        <div class="col">
                            <label class="form-label">Ekstrakurikuler <span class="text-danger">*</span></label>
                            <select class="form-select" wire:model.defer="selectedEkstrakurikuler" {{ $editMode ? 'disabled' : '' }}>
                                <option value="">-- Pilih Ekstrakurikuler --</option>
                                <option value="general">Template Umum</option>
                                @foreach ($ekstrakurikulerList as $ekskul)
                                <option value="{{ $ekskul->id }}">{{ $ekskul->nama }}</option>
                                @endforeach
                            </select>
                            @error('selectedEkstrakurikuler') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <hr>

                    @foreach (['A' => 'Sangat Baik', 'B' => 'Baik', 'C' => 'Cukup'] as $predikat => $label)
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
        window.confirmDeleteEkskul = function(ekskulId, namaEkskul) {
            Swal.fire({
                icon: 'warning',
                title: 'Hapus Template Ekstrakurikuler?',
                html: `Anda yakin ingin menghapus semua template predikat (A, B, C) untuk <strong>${namaEkskul}</strong>?`,
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
            }).then(result => {
                if (result.isConfirmed) {
                    // Tampilkan loading pada tombol spesifik
                    const btn = document.getElementById(`delete-btn-${ekskulId}`);
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i>';
                    }

                    // Dispatch ke backend
                    Livewire.dispatch('deleteEkstrakurikulerTemplate', [ekskulId]);
                }
            });
        };
    });

    // ✅ Modal handlers untuk Livewire
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('open-modal-ekskul', () => {
            const modalElement = document.getElementById('modalEkskul');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        });

        Livewire.on('close-modal-ekskul', () => {
            const modalElement = document.getElementById('modalEkskul');
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