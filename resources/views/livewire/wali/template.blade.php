<div>

    {{-- Tabel Romobongan Belajar Sesuai dengan kelas yang diampu User --}}
    <div class="row mb-3 align-items-center">
        <div class="col-lg-6">
            <h5 class="text-dark"><i class="mdi mdi-account-multiple me-2"></i> Rombongan Belajar yang Diampu</h5>
        </div>
        <div class="col-lg-6 d-flex justify-content-end">
            <div class="input-group w-50">
                <input type="text"
                    wire:model.live.debounce.300ms="searchRombel"
                    class="form-control"
                    placeholder="Cari nama, atau nomor induk...">
                @if($searchRombel)
                <div class="input-group-append">
                    <button type="button"
                        class="btn btn-secondary"
                        wire:click="$set('searchRombel', '')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th style="width: 30%;">
                        <p class="mb-0">Rombongan Belajar</p>
                        <small>Rombel | Mata Pelajaran</small>
                    </th>
                    <th style="width: 30%;">
                        <p class="mb-0">Wali Kelas</p>
                        <small>Nama | Telepon</small>
                    </th>
                    <th style="width: 30%;">
                        <p class="mb-0">Peserta Didik</p>
                        <small>Jumlah Penilaian Per Total</small>
                    </th>
                    <th style="width: 10%;">
                        <p class="mb-0">Aksi</p>
                        <small>Detil Rombel</small>
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rombels as $rombel)
                <tr>
                    <td>
                        <a class="hyper-link text-decoration-none" href="{{ route('admin.class.detail', $rombel->id) }}">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/icons/school.png') }}"
                                    alt="image" />
                                <div class="table-user-name ml-3">
                                    <p class="mb-0 font-weight-medium"> {{ $rombel->nama }} </p>
                                    <small>{{ Mata Pelajaran }}</small>
                                </div>
                            </div>
                        </a>
                    </td>
                    <td>
                        <p class="mb-0 font-weight-medium">{{ $rombel->walikelas_name ?? 'Belum Ditentukan' }}</p>
                        <small>NIP {{ telephone ?? '~' }}</small>
                    </td>
                    <td>
                        <div class="d-flex gap-1 align-items-center">
                            <span class="badge badge-inverse-dark d-flex align-items-center gap-1">
                                <i class="mdi mdi-plus"></i><strong>{{ selesai dinilai ?? 0 }}</strong>
                            </span>
                            <span class="badge badge-inverse-dark d-flex align-items-center gap-1">
                                <i class="mdi mdi-plus"></i><strong>{{ $rombel->total_pelajar ?? 0 }}</strong>
                            </span>
                        </div>
                    </td>
                    <td>
                        <button type="button" class="border-0 bg-transparent" title="Edit" wire:click="detail('{{ $rombel->id }}')">
                            <img src="{{ asset('assets/images/icons/edit.png') }}" width="30" height="30" alt="Edit">
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">Belum ada data rombongan belajar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Loading Overlay - Hanya untuk saveNilai dan actions penting --}}
    <div
        class="position-fixed top-0 start-0 w-100 h-100 align-items-center justify-content-center"
        style="background-color: rgba(0,0,0,0.3); z-index: 9999; display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>