<?php

namespace App\Livewire;

use App\Models\Semester; // Asumsi model Semester ada
use App\Models\TahunAjaran; // Asumsi model TahunAjaran ada
use App\Models\TahunAjaranSemester; // Model untuk tabel pivot
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon; // Untuk memproses tanggal

class DataMapingSemester extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔹 Pencarian & pagination
    public $search = '';
    public $perPage = 10;

    // 🔹 Form field
    public $ta_semester_id;
    public $tahun_ajaran_id;
    public $semester_id;
    public $tgl_mulai;
    public $tgl_selesai;
    public $status = 'nonaktif'; // Default sesuai schema

    public $isEdit = false;
    public $listSemesters;
    public $listTahunAjarans;

    // 🔹 Aturan Validasi Dasar
    protected $baseRules = [
        'tahun_ajaran_id' => 'required|uuid',
        'semester_id' => 'required|uuid',
        'tgl_mulai' => 'required|date',
        'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
        'status' => 'required|in:aktif,nonaktif',
    ];

    // 🔹 Listener event konfirmasi hapus
    protected $listeners = ['deleteConfirmedMappingSemester' => 'deleteConfirmedMappingSemester'];

    // 🔹 Hook untuk inisialisasi data select box
    public function mount()
    {
        // Ambil semua Tahun Ajaran dan Semester untuk dropdown
        $this->listTahunAjarans = TahunAjaran::orderBy('nama')->get();
        $this->listSemesters = Semester::orderBy('urutan')->get(); // Asumsi kolom urutan
    }

    // 🔹 Reset pagination jika search/perPage berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    // 🔹 Buka modal tambah
    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->dispatch('openModalMappingSemester');
    }

    // 🔹 Simpan data baru (Store)
    public function store()
    {
        // Gunakan validasi kombinasi unik secara manual
        $this->validate($this->baseRules);

        // Cek unik kombinasi secara manual
        $exists = TahunAjaranSemester::where('tahun_ajaran_id', $this->tahun_ajaran_id)
            ->where('semester_id', $this->semester_id)
            ->exists();
        if ($exists) {
            $this->addError('tahun_ajaran_id', 'Kombinasi Tahun Ajaran dan Semester ini sudah terpetakan.');
            return;
        }

        // Cek hanya boleh 1 status 'aktif'
        if ($this->status === 'aktif') {
            TahunAjaranSemester::where('status', 'aktif')->update(['status' => 'nonaktif']);
        }

        TahunAjaranSemester::create([
            'tahun_ajaran_id' => $this->tahun_ajaran_id,
            'semester_id' => $this->semester_id,
            'tgl_mulai' => $this->tgl_mulai,
            'tgl_selesai' => $this->tgl_selesai,
            'status' => $this->status,
        ]);

        $this->resetForm();
        $this->dispatch('closeModalMappingSemester');
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Pemetaan Semester berhasil ditambahkan!',
        ]);
    }

    // 🔹 Edit data
    public function edit($id)
    {
        $taSemester = TahunAjaranSemester::findOrFail($id);

        $this->ta_semester_id = $taSemester->id;
        $this->tahun_ajaran_id = $taSemester->tahun_ajaran_id;
        $this->semester_id = $taSemester->semester_id;
        $this->tgl_mulai = Carbon::parse($taSemester->tgl_mulai)->format('Y-m-d');
        $this->tgl_selesai = Carbon::parse($taSemester->tgl_selesai)->format('Y-m-d');
        $this->status = $taSemester->status;

        $this->isEdit = true;
        $this->dispatch('openModalMappingSemester');
    }

    // 🔹 Update data
    public function update()
    {
        // Validasi, kecuali untuk ID Tahun Ajaran dan Semester (diasumsikan tidak bisa diubah)
        $this->validate([
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $taSemester = TahunAjaranSemester::findOrFail($this->ta_semester_id);

        // Cek hanya boleh 1 status 'aktif'
        if ($this->status === 'aktif') {
            TahunAjaranSemester::where('status', 'aktif')
                ->where('id', '!=', $this->ta_semester_id)
                ->update(['status' => 'nonaktif']);
        }

        $taSemester->update([
            'tgl_mulai' => $this->tgl_mulai,
            'tgl_selesai' => $this->tgl_selesai,
            'status' => $this->status,
        ]);

        $this->resetForm();
        $this->dispatch('closeModalMappingSemester');
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data Pemetaan Semester berhasil diperbarui!',
        ]);
    }

    // 🔹 Konfirmasi hapus
    public function confirmDeleteMappingSemester($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Hapus Data?',
            'text' => 'Pemetaan Semester ini akan dihapus permanen!',
            'nextEvent' => 'deleteConfirmedMappingSemester',
            'id' => $id,
        ]);
    }

    // 🔹 Hapus data
    public function deleteConfirmedMappingSemester($id)
    {
        TahunAjaranSemester::findOrFail($id)->delete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Pemetaan Semester telah dihapus!',
        ]);
    }

    // 🔹 Reset form
    private function resetForm()
    {
        $this->ta_semester_id = null;
        $this->tahun_ajaran_id = '';
        $this->semester_id = '';
        $this->tgl_mulai = null;
        $this->tgl_selesai = null;
        $this->status = 'nonaktif';
        $this->resetErrorBag();
    }

    // 🔹 Render data
    public function render()
    {
        $query = TahunAjaranSemester::query()
            ->with(['tahunAjaran', 'semester']); // Asumsi relasi ada di model

        if (!empty($this->search)) {
            $query->whereHas('tahunAjaran', function ($q) {
                $q->where('nama', 'like', "%{$this->search}%");
            })->orWhereHas('semester', function ($q) {
                $q->where('nama', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.data-maping-semester', [
            'tahunAjaranSemesters' => $query->orderBy('tgl_mulai', 'desc')->paginate($this->perPage),
        ]);
    }
}
