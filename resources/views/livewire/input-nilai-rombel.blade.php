<div>
    <div class="row">
        {{-- Kolom Pilih Mata Pelajaran --}}
        <div class="col-md-4">
            <div class="form-group">
                <label for="mapelSelect">Pilih Mata Pelajaran:</label>
                <select wire:model.live="selectedRombelPengajarId" id="mapelSelect" class="form-control">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach ($mataPelajaranList as $item)
                    <option value="{{ $item->id }}">
                        {{ $item->mataPelajaran->nama }}
                    </option>
                    @endforeach
                </select>
                {{-- Pastikan hanya muncul jika ada mapel terpilih DAN guruName sudah dimuat --}}
                @if ($selectedRombelPengajarId)
                <small class="form-text text-muted">Guru Pengajar: **{{ $guruName ?? 'Memuat...' }}**</small>
                @endif
            </div>
        </div>

        {{-- Kolom Pencarian Pelajar --}}
        <div class="col-md-4 offset-md-4">
            <div class="form-group">
                <label for="searchPelajar">Cari Pelajar:</label>
                <input wire:model.live.debounce.300ms="searchPelajar" type="text" id="searchPelajar" class="form-control" placeholder="Nama atau NISN Pelajar...">
            </div>
        </div>
    </div>

    @if (!$selectedRombelPengajarId)
    <div class="alert alert-info mt-3">
        Silakan pilih mata pelajaran yang akan diisi nilainya.
    </div>
    @elseif ($pelajarData->isEmpty())
    <div class="alert alert-warning mt-3">
        Tidak ada pelajar ditemukan di rombel ini atau tidak ada mata pelajaran yang dipilih.
    </div>
    @else
    <form wire:submit.prevent="saveNilai" class="mt-4">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th class="text-center" style="width: 5%;">#</th>
                        <th style="width: 55%;">Nama Pelajar | Nomor Induk</th>
                        <th style="width: 20%;" class="text-center">Nilai Saat Ini</th>
                        <th style="width: 20%;" class="text-center">Input Nilai (0-100)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pelajarData as $index => $pelajar)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $pelajar->nama_lengkap }} | {{ $pelajar->nomor_induk }}</td>
                        <td class="text-center">
                            <span class="badge badge-{{ $pelajar->nilai_sekarang !== null ? 'primary' : 'secondary' }}">
                                {{ $pelajar->nilai_sekarang ?? 'Belum Ada' }}
                            </span>
                        </td>
                        <td>
                            <input
                                wire:model.defer="nilaiInput.{{ $pelajar->pelajar_id }}"
                                type="number"
                                min="0"
                                max="100"
                                class="form-control form-control-sm text-center"
                                placeholder="Nilai">
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
                wire:click="resetNilai"
                wire:loading.attr="disabled"
                wire:target="resetNilai">
                <span class="btn-label">
                    <i class="mdi mdi-delete-sweep-outline"></i>
                </span>
                Reset
            </button>

            {{-- Tombol Simpan --}}
            <button
                type="button"
                class="btn btn-labeled btn-primary"
                wire:click="saveNilai"
                wire:loading.attr="disabled"
                wire:target="saveNilai">
                <span class="btn-label">
                    {{-- Icon loading tampil hanya saat saveNilai aktif --}}
                    <i class="mdi mdi-loading mdi-spin d-none"
                        wire:loading.class.remove="d-none"
                        wire:target="saveNilai">
                    </i>

                    {{-- Icon simpan hilang saat loading --}}
                    <i class="mdi mdi-content-save"
                        wire:loading.class="d-none"
                        wire:target="saveNilai">
                    </i>
                </span>

                {{-- Teks tombol normal --}}
                <span class="text-normal" wire:loading.class="d-none" wire:target="saveNilai">
                    Simpan
                </span>

                {{-- Teks saat loading --}}
                <span class="text-loading d-none" wire:loading.class.remove="d-none" wire:target="saveNilai">
                    Menyimpan...
                </span>
            </button>

        </div>


    </form>
    @endif
</div>