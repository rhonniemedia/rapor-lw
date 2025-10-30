<?php

namespace App\Livewire\Admin;

use App\Models\Jurusan; // Asumsi model Jurusan ada
use App\Models\MataPelajaran; // Asumsi model MataPelajaran ada
use App\Models\Kurikulum; // Asumsi model Kurikulum ada
use App\Models\JurusanMataPelajaran; // Asumsi model JurusanMataPelajaran ada

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class DataMapingJurusanMapel extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔹 Properti pencarian & pagination
    public $search = '';
    public $perPage = 10;

    // 🔹 Field form
    public $jurusan_mtp_id;
    public $jurusan_id;
    public $mata_pelajaran_id;
    public $kurikulum_id; // nullable
    public $status = 'wajib'; // enum: wajib, pilihan

    public $isEdit = false;

    // Data dropdown (dipuat di mount)
    public $jurusanList = [];
    public $mataPelajaranList = [];
    public $kurikulumList = [];

    // 🔹 Event listener
    protected $listeners = ['deleteConfirmedJurusanMtp' => 'deleteConfirmedJurusanMtp'];

    public function mount()
    {
        // Muat data untuk dropdown saat komponen diinisialisasi
        $this->jurusanList = Jurusan::orderBy('nama', 'asc')->get();
        $this->mataPelajaranList = MataPelajaran::orderBy('nama', 'asc')->get();
        $this->kurikulumList = Kurikulum::orderBy('nama', 'asc')->get();
    }

    // 🔹 Validasi dasar
    protected $baseRules = [
        'jurusan_id' => 'required|uuid|exists:jurusans,id',
        'mata_pelajaran_id' => 'required|uuid|exists:mata_pelajarans,id',
        'kurikulum_id' => 'nullable|uuid|exists:kurikulums,id',
        'status' => 'required|in:wajib,pilihan',
    ];

    // 🔹 Reset pagination saat search berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // 🔹 Rules dinamis (saat edit & unique constraint)
    public function getRules()
    {
        $rules = $this->baseRules;

        // Rule unik kombinasi: jurusan + mapel + kurikulum
        $rules['mata_pelajaran_id'] = [
            'required',
            'uuid',
            'exists:mata_pelajarans,id',
            Rule::unique('jurusan_mata_pelajarans')->where(function ($query) {
                return $query->where('jurusan_id', $this->jurusan_id)
                    ->where('kurikulum_id', $this->kurikulum_id);
            })->ignore($this->jurusan_mtp_id, 'id'),
        ];

        return $rules;
    }

    // 🔹 Buka modal tambah
    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->dispatch('openModalJurusanMtp');
    }

    // 🔹 Simpan data baru
    public function store()
    {
        $this->validate($this->getRules());

        JurusanMataPelajaran::create([
            'jurusan_id' => $this->jurusan_id,
            'mata_pelajaran_id' => $this->mata_pelajaran_id,
            // Simpan null jika kurikulum_id kosong
            'kurikulum_id' => $this->kurikulum_id ?: null,
            'status' => $this->status,
        ]);

        $this->dispatch('closeModalJurusanMtp');
        $this->resetForm();
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Mata pelajaran berhasil ditambahkan ke jurusan!',
        ]);
    }

    // 🔹 Edit data
    public function edit($id)
    {
        $data = JurusanMataPelajaran::findOrFail($id);

        $this->jurusan_mtp_id = $data->id;
        $this->jurusan_id = $data->jurusan_id;
        $this->mata_pelajaran_id = $data->mata_pelajaran_id;
        $this->kurikulum_id = $data->kurikulum_id;
        $this->status = $data->status;

        $this->isEdit = true;
        $this->dispatch('openModalJurusanMtp');
    }

    // 🔹 Update data
    public function update()
    {
        $this->validate($this->getRules());

        $data = JurusanMataPelajaran::findOrFail($this->jurusan_mtp_id);

        $data->update([
            'jurusan_id' => $this->jurusan_id,
            'mata_pelajaran_id' => $this->mata_pelajaran_id,
            'kurikulum_id' => $this->kurikulum_id ?: null,
            'status' => $this->status,
        ]);

        $this->dispatch('closeModalJurusanMtp');
        $this->resetForm();
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data jurusan mata pelajaran berhasil diperbarui!',
        ]);
    }

    // 🔹 Konfirmasi hapus
    public function confirmDeleteJurusanMtp($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Hapus Data?',
            'text' => 'Relasi ini akan dihapus secara permanen!',
            'nextEvent' => 'deleteConfirmedJurusanMtp',
            'id' => $id,
        ]);
    }

    // 🔹 Hapus data
    public function deleteConfirmedJurusanMtp($id)
    {
        JurusanMataPelajaran::findOrFail($id)->delete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data jurusan mata pelajaran berhasil dihapus!',
        ]);
    }

    // 🔹 Reset form input
    private function resetForm()
    {
        $this->jurusan_mtp_id = null;
        $this->jurusan_id = '';
        $this->mata_pelajaran_id = '';
        $this->kurikulum_id = '';
        $this->status = 'wajib';
        $this->resetErrorBag();
    }

    // 🔹 Render tabel data
    public function render()
    {
        // Query dengan eager loading relasi
        $query = JurusanMataPelajaran::with(['jurusan', 'mataPelajaran', 'kurikulum']);

        // Filter pencarian
        if (!empty($this->search)) {
            $query->whereHas('jurusan', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            })->orWhereHas('mataPelajaran', function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.admin.data-maping-jurusan-mapel', [
            // Urutkan berdasarkan Jurusan, kemudian Mata Pelajaran
            'jurusanMataPelajaran' => $query
                ->join('jurusans as j', 'j.id', '=', 'jurusan_mata_pelajarans.jurusan_id')
                ->join('mata_pelajarans as m', 'm.id', '=', 'jurusan_mata_pelajarans.mata_pelajaran_id')
                ->select('jurusan_mata_pelajarans.*') // Pilih semua kolom JurusanMataPelajaran
                ->orderBy('j.nama', 'asc')
                ->orderBy('m.nama', 'asc')
                ->paginate($this->perPage),
        ]);
    }
}
