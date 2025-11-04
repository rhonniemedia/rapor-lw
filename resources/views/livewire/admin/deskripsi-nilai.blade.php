<div>
    @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session()->has('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Tab Nilai -->
    <div class="tab-pane fade show active" id="tab-nilai">
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
                    <input type="text" class="form-control" placeholder="Cari mata pelajaran..."
                        wire:model.live.debounce.500ms="search" style="width:250px;">
                </div>
                <button type="button" wire:click="openModal"
                    class="btn btn-outline-light-muted btn-sm d-flex align-items-center justify-content-center h-100"
                    style="padding: 0 0.75rem;">
                    <i class="mdi mdi-plus"></i>
                </button>
            </div>
        </div>

        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 20%;">Mata Pelajaran</th>
                    <th style="width: 10%;">Tingkat</th>
                    <th style="width: 8%;">Predikat</th>
                    <th style="width: 12%;">Rentang Nilai</th>
                    <th style="width: 40%;">Deskripsi</th>
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
                        <strong>{{ $template['mata_pelajaran_nama'] }}</strong>
                    </td>
                    <td rowspan="{{ $rowspan }}">
                        <span class="badge bg-info">{{ $template['tingkat'] }}</span>
                    </td>
                    @endif
                    <td>
                        <span class="badge {{ $template['predikat'] === 'A' ? 'bg-success' : ($template['predikat'] === 'B' ? 'bg-primary' : ($template['predikat'] === 'C' ? 'bg-warning' : 'bg-danger')) }}">
                            {{ $template['predikat'] }}
                        </span>
                    </td>
                    <td>{{ $template['rentang_min'] }} - {{ $template['rentang_max'] }}</td>
                    <td>{{ $template['deskripsi'] }}</td>
                    @if ($index === 0)
                    <td rowspan="{{ $rowspan }}" class="text-center">
                        <button class="btn btn-sm btn-outline-secondary"
                            wire:click="edit('{{ $template['mata_pelajaran_id'] }}', '{{ $template['tingkat'] }}')">
                            <i class="mdi mdi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger"
                            wire:click="delete('{{ $template['mata_pelajaran_id'] }}', '{{ $template['tingkat'] }}')"
                            wire:confirm="Apakah Anda yakin ingin menghapus template ini?">
                            <i class="mdi mdi-delete"></i>
                        </button>
                    </td>
                    @endif
                </tr>
                @endforeach
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="mdi mdi-information-outline mdi-24px d-block mb-2"></i>
                        Tidak ada data template nilai
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="modalNilai" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <form wire:submit.prevent="save">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editMode ? 'Edit' : 'Tambah' }} Template Nilai</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model="selectedMataPelajaran" {{ $editMode ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Mata Pelajaran --</option>
                                    @foreach ($mataPelajarans as $mapel)
                                    <option value="{{ $mapel->id }}">{{ $mapel->nama }}</option>
                                    @endforeach
                                </select>
                                @error('selectedMataPelajaran') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tingkat Kelas <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model="tingkat" {{ $editMode ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Tingkat --</option>
                                    <option value="X">X</option>
                                    <option value="XI">XI</option>
                                    <option value="XII">XII</option>
                                </select>
                                @error('tingkat') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <hr>

                        @foreach (['A', 'B', 'C', 'D'] as $predikat)
                        <div class="card mb-3">
                            <div class="card-header pl-3 
              {{ $predikat === 'A' ? 'bg-light' : ($predikat === 'B' ? 'bg-light' : ($predikat === 'C' ? 'bg-light' : 'bg-light')) }} 
              text-dark">
                                <strong>Predikat {{ $predikat }}</strong>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-1">
                                    <div class="col-md-6">
                                        <label class="form-label">Rentang Min</label>
                                        <input type="number" class="form-control" wire:model="templates.{{ $predikat }}.rentang_min" min="0" max="100">
                                        @error("templates.{$predikat}.rentang_min") <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Rentang Max</label>
                                        <input type="number" class="form-control" wire:model="templates.{{ $predikat }}.rentang_max" min="0" max="100">
                                        @error("templates.{$predikat}.rentang_max") <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Deskripsi</label>
                                        <textarea class="form-control" wire:model="templates.{{ $predikat }}.deskripsi" rows="4"></textarea>
                                        @error("templates.{$predikat}.deskripsi") <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="modal-footer">
                        {{-- Tombol Batal --}}
                        <button
                            type="button"
                            class="btn btn-labeled btn-secondary"
                            data-bs-dismiss="modal">
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
                                {{-- Icon loading tampil hanya saat proses save aktif --}}
                                <i class="mdi mdi-loading mdi-spin d-none"
                                    wire:loading.class.remove="d-none"
                                    wire:target="save">
                                </i>

                                {{-- Icon simpan hilang saat loading --}}
                                <i class="mdi mdi-content-save"
                                    wire:loading.class="d-none"
                                    wire:target="save">
                                </i>
                            </span>

                            {{-- Teks tombol normal --}}
                            <span class="text-normal" wire:loading.class="d-none" wire:target="save">
                                Simpan
                            </span>

                            {{-- Teks saat loading --}}
                            <span class="text-loading d-none" wire:loading.class.remove="d-none" wire:target="save">
                                Menyimpan...
                            </span>
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>


</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('open-modal-nilai', () => {
            const modalElement = document.getElementById('modalNilai');
            if (modalElement) {
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            }
        });

        Livewire.on('close-modal-nilai', () => {
            const modalElement = document.getElementById('modalNilai');
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