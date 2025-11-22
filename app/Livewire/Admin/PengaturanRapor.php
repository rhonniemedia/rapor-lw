<?php

namespace App\Livewire\Admin;

use App\Models\Pengaturan;
use App\Models\TahunAjaranSemester;
use App\Models\User; // Asumsi User adalah tabel untuk Kepala Sekolah
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use Illuminate\Validation\Rule as ValidationRule;
use Livewire\Attributes\On;
use Exception;

class PengaturanRapor extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public $search = '';
    public $perPage = 10;

    // Form Properties
    public $pengaturanId;

    // #[Rule('required|exists:tahun_ajaran_semesters,id')]
    public $tahun_ajaran_semester_id;

    #[Rule('required|exists:users,id')]
    public $kepala_sekolah_id;

    #[Rule('required|date')]
    public $tanggal_rapor;

    // State Modal
    public $isEdit = false;

    protected $listeners = [
        'delete-confirmed' => 'deletePengaturan', // Listener untuk konfirmasi delete dari JS
    ];

    // Reset pagination saat search berubah
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Query Data Pengaturan dengan Relasi
        $pengaturans = Pengaturan::with(['tahunAjaranSemester.tahunAjaran', 'tahunAjaranSemester.semester', 'kepalaSekolah'])
            ->when($this->search, function ($query) {
                $query->whereHas('kepalaSekolah', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%'); // Sesuaikan kolom nama user
                });
            })
            ->paginate($this->perPage);

        // Data untuk Dropdown Form
        $listTahunSemester = TahunAjaranSemester::with(['tahunAjaran', 'semester'])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'nama' => $item->tahunAjaran->nama . ' (' . $item->semester->nama . ')'
                ];
            });

        // Ambil User yang role-nya guru/kepsek (sesuaikan logic ini)
        $listKepsek = User::orderBy('name')->get();

        return view('livewire.admin.pengaturan-rapor', [
            'pengaturans' => $pengaturans,
            'listTahunSemester' => $listTahunSemester,
            'listKepsek' => $listKepsek
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isEdit = false;
        $this->dispatch('modal:show');
    }

    public function store()
    {
        // Validasi Manual
        $this->validate([
            'kepala_sekolah_id' => 'required|exists:users,id',
            'tanggal_rapor' => 'required|date',
            // Validasi Unique: Pastikan tahun_ajaran_semester_id belum ada di tabel pengaturans
            'tahun_ajaran_semester_id' => [
                'required',
                'exists:tahun_ajaran_semesters,id',
                'unique:pengaturans,tahun_ajaran_semester_id'
            ],
        ], [
            // Custom Error Message (Opsional)
            'tahun_ajaran_semester_id.unique' => 'Pengaturan untuk Tahun Ajaran & Semester ini sudah dibuat sebelumnya.',
        ]);

        Pengaturan::create([
            'tahun_ajaran_semester_id' => $this->tahun_ajaran_semester_id,
            'kepala_sekolah_id' => $this->kepala_sekolah_id,
            'tanggal_rapor' => $this->tanggal_rapor,
            'konfigurasi_tampilan' => []
        ]);

        $this->dispatch('modal:hide');

        // Format dispatch sama dengan ProfilSekolah - menggunakan named parameters title dan text
        $this->dispatch(
            'swal:success',
            title: 'Berhasil!',
            text: 'Data pengaturan rapor berhasil disimpan.'
        );

        $this->resetInputFields();
    }

    public function getActionMethodProperty()
    {
        return $this->isEdit ? 'update' : 'store';
    }

    public function edit($id)
    {
        $pengaturan = Pengaturan::findOrFail($id);

        $this->pengaturanId = $id;
        $this->tahun_ajaran_semester_id = $pengaturan->tahun_ajaran_semester_id;
        $this->kepala_sekolah_id = $pengaturan->kepala_sekolah_id;
        $this->tanggal_rapor = $pengaturan->tanggal_rapor->format('Y-m-d');

        $this->isEdit = true;
        $this->dispatch('modal:show');
    }

    public function update()
    {
        // Validasi Manual dengan pengecualian (Ignore current ID)
        $this->validate([
            'kepala_sekolah_id' => 'required|exists:users,id',
            'tanggal_rapor' => 'required|date',
            'tahun_ajaran_semester_id' => [
                'required',
                'exists:tahun_ajaran_semesters,id',
                // Cek unique, tapi abaikan ID pengaturan yang sedang diedit
                ValidationRule::unique('pengaturans', 'tahun_ajaran_semester_id')->ignore($this->pengaturanId)
            ],
        ], [
            'tahun_ajaran_semester_id.unique' => 'Pengaturan untuk Tahun Ajaran & Semester ini sudah dibuat sebelumnya.',
        ]);

        if ($this->pengaturanId) {
            $pengaturan = Pengaturan::findOrFail($this->pengaturanId);
            $pengaturan->update([
                'tahun_ajaran_semester_id' => $this->tahun_ajaran_semester_id,
                'kepala_sekolah_id' => $this->kepala_sekolah_id,
                'tanggal_rapor' => $this->tanggal_rapor,
            ]);

            $this->dispatch('modal:hide');

            // Format dispatch sama dengan ProfilSekolah
            $this->dispatch(
                'swal:success',
                title: 'Berhasil!',
                text: 'Data pengaturan rapor berhasil diperbarui.'
            );

            $this->resetInputFields();
        }
    }

    // Metode untuk konfirmasi delete
    public function confirmDelete($id)
    {
        $this->dispatch('swal:confirm-delete', [
            'id' => $id,
            'title' => 'Hapus Data?',
            'text' => 'Data ini akan dihapus secara permanen.'
        ]);
    }

    /**
     * 2. Method ini dipanggil OLEH JAVASCRIPT setelah user klik "Ya, Hapus"
     * Perhatikan atribut #[On] di bawah ini.
     */
    // Metode untuk menghapus data
    public function deletePengaturan($data)
    {
        $id = $data['id'];
        $pengaturan = PengaturanRapor::find($id);
        if ($pengaturan) {
            $pengaturan->delete();
            $this->dispatch('swal:success', [
                'title' => 'Berhasil!',
                'text' => 'Pengaturan rapor berhasil dihapus.',
            ]);
        } else {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Data tidak ditemukan.',
            ]);
        }
        // Reset pagination jika diperlukan
        $this->resetPage();
    }

    private function resetInputFields()
    {
        $this->pengaturanId = null;
        $this->tahun_ajaran_semester_id = null;
        $this->kepala_sekolah_id = null;
        $this->tanggal_rapor = null;
    }
}
