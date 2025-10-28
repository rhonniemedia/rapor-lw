<div>
    <div class="row">
        <div class="col-xl-12 col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-12">
                            <div class="page-header mb-0 border-bottom">
                                <div class="d-flex align-items-center">
                                    <h5 class="text-dark"><i class="mdi mdi-filter me-2"></i> Filter Data Catatan</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3 row">
                                <!-- Filter Tahun Ajaran -->
                                <div class="col-sm-4">
                                    <label class="form-label">Tahun Ajaran</label>
                                    <select wire:model.live="tahunAjaranId" class="form-select">
                                        <option value="">-- Pilih Tahun Ajaran --</option>
                                        @foreach($tahunAjaranList as $ta)
                                        <option value="{{ $ta->id }}">
                                            {{ $ta->nama }}
                                            @if($ta->status === 'aktif') (Aktif) @endif
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Filter Semester -->
                                <div class="col-sm-4">
                                    <label class="form-label">Semester</label>
                                    <select wire:model.live="semesterId" class="form-select"
                                        @if(!$tahunAjaranId) disabled @endif>
                                        <option value="">-- Pilih Semester --</option>
                                        @foreach($semesterList as $smt)
                                        <option value="{{ $smt->id }}">
                                            Semester {{ $smt->semester->nama }}
                                            @if($smt->status === 'aktif') (Aktif) @endif
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Filter Rombel -->
                                <div class="col-sm-4">
                                    <label class="form-label">Rombongan Belajar</label>
                                    <select wire:model.live="rombelId" class="form-select"
                                        @if(!$semesterId) disabled @endif>
                                        <option value="">-- Pilih Rombel --</option>
                                        @foreach($rombelList as $rb)
                                        <option value="{{ $rb->id }}">
                                            {{ $rb->nama }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>
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
        <form wire:submit.prevent="saveCatatan" class="mt-4">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center" style="width: 3%;">#</th>
                            <th style="width: 22%;">Nama Pelajar</th>
                            <th style="width: 15%;" class="text-center">Jenis Catatan</th>
                            <th style="width: 45%;">Catatan</th>
                            <th style="width: 15%;" class="text-center">Catatan Terakhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pelajarData as $index => $pelajar)
                        <tr>
                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                            <td class="align-middle">
                                <strong>{{ $pelajar->nama_lengkap }}</strong><br>
                                <small class="text-muted">{{ $pelajar->nomor_induk }}</small>
                            </td>
                            <td class="align-middle">
                                <select
                                    wire:model.defer="catatanInput.{{ $pelajar->pelajar_id }}.jenis_catatan"
                                    class="form-select form-select-sm">
                                    <option value="">-- Pilih Jenis --</option>
                                    @foreach($jenisCatatanOptions as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <textarea
                                    wire:model.defer="catatanInput.{{ $pelajar->pelajar_id }}.catatan"
                                    class="form-control form-control-sm"
                                    rows="3"
                                    placeholder="Tulis catatan untuk siswa..."
                                    maxlength="1000"></textarea>
                                <small class="text-muted">Maksimal 1000 karakter</small>
                            </td>
                            <td class="align-middle">
                                @if($pelajar->catatan_terakhir)
                                <div class="small">
                                    <span class="badge badge-info mb-1">
                                        {{ $jenisCatatanOptions[$pelajar->catatan_terakhir->jenis_catatan] ?? $pelajar->catatan_terakhir->jenis_catatan }}
                                    </span>
                                    <p class="mb-1 text-truncate" style="max-width: 200px;" title="{{ $pelajar->catatan_terakhir->catatan }}">
                                        {{ Str::limit($pelajar->catatan_terakhir->catatan, 50) }}
                                    </p>
                                    <small class="text-muted">
                                        {{ Carbon\Carbon::parse($pelajar->catatan_terakhir->tanggal_input)->format('d M Y') }}
                                    </small>
                                </div>
                                @else
                                <span class="text-muted small">Belum ada catatan</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted small">
                    <i class="mdi mdi-information-outline"></i>
                    Catatan akan tersimpan sebagai history, tidak menimpa catatan sebelumnya
                </div>

                <div>
                    {{-- Tombol Reset --}}
                    <button
                        type="button"
                        class="btn btn-labeled btn-outline-secondary me-2"
                        wire:click="confirmResetCatatan"
                        wire:loading.attr="disabled"
                        wire:target="resetCatatan">
                        <span class="btn-label">
                            <i class="mdi mdi-delete-sweep-outline"></i>
                        </span>
                        Reset
                    </button>

                    {{-- Tombol Simpan --}}
                    <button
                        type="button"
                        class="btn btn-labeled btn-primary"
                        wire:click="confirmSaveCatatan"
                        wire:loading.attr="disabled"
                        wire:target="saveCatatan">
                        <span class="btn-label">
                            <i class="mdi mdi-loading mdi-spin d-none"
                                wire:loading.class.remove="d-none"
                                wire:target="saveCatatan">
                            </i>
                            <i class="mdi mdi-content-save"
                                wire:loading.class="d-none"
                                wire:target="saveCatatan">
                            </i>
                        </span>
                        <span class="text-normal" wire:loading.class="d-none" wire:target="saveCatatan">
                            Simpan Catatan
                        </span>
                        <span class="text-loading d-none" wire:loading.class.remove="d-none" wire:target="saveCatatan">
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </form>
        @endif
    </div>
</div>