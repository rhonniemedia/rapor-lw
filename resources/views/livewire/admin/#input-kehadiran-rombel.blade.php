<div>
    {{-- Header Info --}}
    <div class="alert alert-info mb-3">
        <div class="d-flex align-items-center">
            <i class="mdi mdi-information-outline mdi-24px me-2"></i>
            <div>
                <strong>Input Kehadiran Rombel</strong>
                <p class="mb-0 small">Data kehadiran untuk seluruh siswa di rombel ini dalam satu semester</p>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        {{-- Kolom Pencarian Pelajar --}}
        <div class="col-md-6 offset-md-6">
            <div class="form-group">
                <label for="searchPelajar">Cari Pelajar:</label>
                <input wire:model.live.debounce.300ms="searchPelajar"
                    type="text"
                    id="searchPelajar"
                    class="form-control"
                    placeholder="Nama atau Nomor Induk Pelajar...">
            </div>
        </div>
    </div>

    @if ($pelajarData->isEmpty())
    <div class="alert alert-warning mt-3">
        Tidak ada pelajar ditemukan di rombel ini.
    </div>
    @else
    <form wire:submit.prevent="saveKehadiran" class="mt-4">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th class="text-center" style="width: 5%;">#</th>
                        <th style="width: 35%;">Nama Pelajar | Nomor Induk</th>
                        <th style="width: 15%;" class="text-center">Sakit</th>
                        <th style="width: 15%;" class="text-center">Izin</th>
                        <th style="width: 15%;" class="text-center">Tanpa Keterangan</th>
                        <th style="width: 15%;" class="text-center">Total Absen</th>
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
                        <td>
                            <input
                                wire:model.defer="kehadiranInput.{{ $pelajar->pelajar_id }}.sakit"
                                type="number"
                                min="0"
                                class="form-control form-control-sm text-center"
                                placeholder="0">
                        </td>
                        <td>
                            <input
                                wire:model.defer="kehadiranInput.{{ $pelajar->pelajar_id }}.izin"
                                type="number"
                                min="0"
                                class="form-control form-control-sm text-center"
                                placeholder="0">
                        </td>
                        <td>
                            <input
                                wire:model.defer="kehadiranInput.{{ $pelajar->pelajar_id }}.tanpa_keterangan"
                                type="number"
                                min="0"
                                class="form-control form-control-sm text-center"
                                placeholder="0">
                        </td>
                        <td class="text-center">
                            @php
                            $sakit = $kehadiranInput[$pelajar->pelajar_id]['sakit'] ?? 0;
                            $izin = $kehadiranInput[$pelajar->pelajar_id]['izin'] ?? 0;
                            $tanpaKet = $kehadiranInput[$pelajar->pelajar_id]['tanpa_keterangan'] ?? 0;
                            $total = $sakit + $izin + $tanpaKet;
                            @endphp
                            <span class="badge badge-{{ $total > 0 ? 'warning' : 'success' }}">
                                {{ $total }} hari
                            </span>
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
                wire:click="confirmResetKehadiran"
                wire:loading.attr="disabled"
                wire:target="resetKehadiran">
                <span class="btn-label">
                    <i class="mdi mdi-delete-sweep-outline"></i>
                </span>
                Reset
            </button>

            {{-- Tombol Simpan --}}
            <button
                type="button"
                class="btn btn-labeled btn-primary"
                wire:click="confirmSaveKehadiran"
                wire:loading.attr="disabled"
                wire:target="saveKehadiran">
                <span class="btn-label">
                    <i class="mdi mdi-loading mdi-spin d-none"
                        wire:loading.class.remove="d-none"
                        wire:target="saveKehadiran">
                    </i>
                    <i class="mdi mdi-content-save"
                        wire:loading.class="d-none"
                        wire:target="saveKehadiran">
                    </i>
                </span>
                <span class="text-normal" wire:loading.class="d-none" wire:target="saveKehadiran">
                    Simpan
                </span>
                <span class="text-loading d-none" wire:loading.class.remove="d-none" wire:target="saveKehadiran">
                    Menyimpan...
                </span>
            </button>
        </div>
    </form>
    @endif
</div>