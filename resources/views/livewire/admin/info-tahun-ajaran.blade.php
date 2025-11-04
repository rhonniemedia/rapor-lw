<div class="row align-items-center">
    <!-- Kolom 1 -->
    <div class="col-md-4">
        <div class="d-flex align-items-center my-3">
            <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                style="width: 36px; height: 36px;">
                <i class="mdi mdi-book text-white fs-5"></i>
            </div>
            <div class="ms-3 d-flex flex-column justify-content-center">
                <small class="text-muted d-block">Kurikulum:</small>
                <p class="mb-0 font-weight-bold">{{ $kurikulum->nama ?? 'Belum diatur' }}</p>
            </div>
        </div>
    </div>
    <!-- Kolom 2 -->
    <div class="col-md-4">
        <div class="d-flex align-items-center my-3">
            <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                style="width: 36px; height: 36px;">
                <i class="mdi mdi-calendar-clock text-white fs-5"></i>
            </div>
            <div class="ms-3 d-flex flex-column justify-content-center">
                <small class="text-muted d-block">Tahun Ajaran:</small>
                <p class="mb-0 font-weight-bold">{{ $tahunAjaran?->tahunAjaran?->nama ?? '-' }}</p>
            </div>
        </div>
    </div>
    <!-- Kolom 3 -->
    <div class="col-md-4">
        <div class="d-flex align-items-center my-3">
            <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-success rounded-3"
                style="width: 36px; height: 36px;">
                <i class="mdi mdi-calendar-range text-white fs-5"></i>
            </div>
            <div class="ms-3 d-flex flex-column justify-content-center">
                <small class="text-muted d-block">Semester</small>
                <p class="mb-0 font-weight-bold">
                    {{ ucfirst($tahunAjaran?->semester->nama ?? '-') }} ({{ $tahunAjaran?->semester->urutan ?? '-' }})
                </p>
            </div>
        </div>
    </div>
</div>