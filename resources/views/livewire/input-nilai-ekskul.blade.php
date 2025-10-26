<div>
    <div class="row">
        {{-- Kolom Pilih Ekstrakurikuler --}}
        <div class="col-md-4">
            <div class="form-group">
                <label for="ekskulSelect">Pilih Ekstrakurikuler:</label>
                <select wire:model.live="selectedEkstrakurikulerId" id="ekskulSelect" class="form-select">
                    <option value="">-- Pilih Ekstrakurikuler --</option>

                    @forelse ($ekstrakurikulerList as $item)
                    <option value="{{ $item->id }}">
                        {{ $item->nama }}
                    </option>
                    @empty
                    <option value="" disabled>Tidak ada ekstrakurikuler aktif</option>
                    @endforelse
                </select>

                @if ($selectedEkstrakurikulerId && $selectedEkskulName)
                <small class="form-text text-muted">
                    Ekstrakurikuler: <strong>{{ $selectedEkskulName }}</strong>
                </small>
                @endif
            </div>
        </div>

        {{-- Kolom Pencarian Pelajar --}}
        <div class="col-md-4 offset-md-4">
            <div class="form-group">
                <label for="searchPelajar">Cari Pelajar:</label>
                <input wire:model.live.debounce.300ms="searchPelajar" type="text" id="searchPelajar" class="form-control" placeholder="Nama atau Nomor Induk Pelajar...">
            </div>
        </div>
    </div>

    @if (!$selectedEkstrakurikulerId)
    <div class="alert alert-info mt-3">
        Silakan pilih ekstrakurikuler yang akan diisi nilainya.
    </div>
    @elseif ($pelajarData->isEmpty())
    <div class="alert alert-warning mt-3">
        Tidak ada pelajar ditemukan di rombel ini.
    </div>
    @else
    <form wire:submit.prevent="saveNilaiEkskul" class="mt-4">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th class="text-center" style="width: 5%;">#</th>
                        <th style="width: 30%;">Nama Pelajar | Nomor Induk</th>
                        <th style="width: 15%;" class="text-center">Nilai Saat Ini</th>
                        <th style="width: 15%;" class="text-center">Input Nilai</th>
                        <th style="width: 35%;">Deskripsi (Opsional)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pelajarData as $index => $pelajar)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $pelajar->nama_lengkap }}</strong><br>
                            <small class="text-muted">{{ $pelajar->nomor_induk }}</small>
                        </td>
                        <td class="text-center">
                            @if($pelajar->nilai_sekarang)
                            <span class="badge badge-{{ $pelajar->nilai_sekarang->nilai === 'A' ? 'success' : ($pelajar->nilai_sekarang->nilai === 'B' ? 'primary' : 'warning') }}">
                                {{ $pelajar->nilai_sekarang->nilai }} - {{ $predikatOptions[$pelajar->nilai_sekarang->nilai] ?? '' }}
                            </span>
                            @else
                            <span class="badge badge-secondary">Belum Ada</span>
                            @endif
                        </td>
                        <td>
                            <select
                                wire:model.defer="nilaiInput.{{ $pelajar->pelajar_id }}"
                                class="form-select form-select-sm">
                                <option value="">-- Pilih --</option>
                                @foreach($predikatOptions as $key => $label)
                                <option value="{{ $key }}">{{ $key }} - {{ $label }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <textarea
                                wire:model.defer="deskripsiInput.{{ $pelajar->pelajar_id }}"
                                class="form-control form-control-sm"
                                rows="2"
                                placeholder="Deskripsi singkat..."
                                maxlength="500"></textarea>
                            <small class="text-muted">Maks 500 karakter</small>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-3">
            {{-- Tombol Reset --}}
            <button
                type="button"
                class="btn btn-labeled btn-outline-secondary me-2"
                wire:click="confirmResetNilaiEkskul"
                wire:loading.attr="disabled"
                wire:target="resetNilaiEkskul">
                <span class="btn-label">
                    <i class="mdi mdi-delete-sweep-outline"></i>
                </span>
                Reset
            </button>

            {{-- Tombol Simpan --}}
            <button
                type="button"
                class="btn btn-labeled btn-primary"
                wire:click="confirmSaveNilaiEkskul"
                wire:loading.attr="disabled"
                wire:target="saveNilaiEkskul">
                <span class="btn-label">
                    <i class="mdi mdi-loading mdi-spin d-none"
                        wire:loading.class.remove="d-none"
                        wire:target="saveNilaiEkskul">
                    </i>
                    <i class="mdi mdi-content-save"
                        wire:loading.class="d-none"
                        wire:target="saveNilaiEkskul">
                    </i>
                </span>
                <span class="text-normal" wire:loading.class="d-none" wire:target="saveNilaiEkskul">
                    Simpan
                </span>
                <span class="text-loading d-none" wire:loading.class.remove="d-none" wire:target="saveNilaiEkskul">
                    Menyimpan...
                </span>
            </button>
        </div>
    </form>
    @endif
</div>