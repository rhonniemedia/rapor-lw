<div>
    {{-- Input Search & Per Page --}}
    <div class="d-flex justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <span>Show</span>
            <select class="form-select form-select-sm" wire:model.live="perPage">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <span>entries</span>
        </div>
        <div>
            <input type="text" class="form-control" placeholder="Cari..."
                wire:model.live.debounce.500ms="search" style="width:250px;">
        </div>
    </div>

    <table class="table table-hover mb-0">
        <thead class="bg-light">
            <tr>
                <th style="width: 40%;">
                    <p class="mb-0">Tenaga Pendidik</p>
                    <small>Nama | Telepon</small>
                </th>
                <th style="width: 40%;">
                    <p class="mb-0">Penugasan</p>
                    <small>Mata Pelajaran | Rombel</small>
                </th>
                <th style="width: 10%;">
                    <p class="mb-0">Aksi</p>
                    <small>Edit | Delete</small>
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($tendiks as $tendik)
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/icons/pilot.png') }}"
                            alt="image" />
                        <div class="table-user-name ml-3">
                            <p class="mb-0 font-weight-medium"> {{ $tendik->name }} </p>
                            <small>{{ $tendik->telephone }}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <p class="mb-0 font-weight-medium">Mata Pelajaran</p>
                    <small>Rombel</small>
                </td>
                <td>

                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted">Belum ada data tenaga pendidik.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $tendiks->onEachSide(1)->links() }}
    </div>
</div>