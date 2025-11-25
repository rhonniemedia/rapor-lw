<?php

namespace App\Livewire\Admin;

use App\Models\Kurikulum; // Asumsi model Kurikulum ada
use App\Models\MataPelajaran; // Asumsi model MataPelajaran ada
use App\Models\MataPelajaranKelompok; // Asumsi model MataPelajaranKelompok ada
use App\Models\KurikulumMataPelajaran; // Asumsi model KurikulumMataPelajaran ada

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class DataMapingKurikulumMapel extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔹 Properti pencarian & pagination
    public $search = '';
    public $perPage = 10;

    public $filterTingkat = '';
    public $filterKelompok = '';

    // 🔹 Field form
    public $kurikulum_mtp_id;
    public $kurikulum_id;
    public $mata_pelajaran_id;
    public $kelompok_id; // nullable
    public $tingkat; // unsignedTinyInteger
    public $urutan; // nullable unsignedTinyInteger

    public $isEdit = false;

    // Data dropdown (dipuat di mount atau render)
    public $kurikulums = [];
    public $mataPelajaranList = [];
    public $kelompokList = [];

    // 🔹 Event listener
    protected $listeners = ['deleteConfirmedKurikulumMtp' => 'deleteConfirmedKurikulumMtp'];

    public function mount()
    {
        // Muat data untuk dropdown saat komponen diinisialisasi
        $this->kurikulums = Kurikulum::orderBy('nama', 'asc')->get();
        $this->mataPelajaranList = MataPelajaran::orderBy('nama', 'asc')->get();
        $this->kelompokList = MataPelajaranKelompok::orderBy('kode', 'asc')->get();
    }

    // 🔹 Validasi dasar
    protected $baseRules = [
        'kurikulum_id' => 'required|uuid|exists:kurikulums,id',
        'mata_pelajaran_id' => 'required|uuid|exists:mata_pelajarans,id',
        'kelompok_id' => 'nullable|uuid|exists:mata_pelajaran_kelompoks,id',
        'tingkat' => 'required|integer|min:1|max:20', // Sesuaikan batas max tingkat
        'urutan' => 'nullable|integer|min:1',
    ];

    // 🔹 Reset pagination saat search berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterTingkat()
    {
        $this->resetPage();
    }

    public function updatingFilterKelompok()
    {
        $this->resetPage();
    }

    // 🔹 Rules dinamis (saat edit & unique constraint)
    public function getRules()
    {
        $rules = $this->baseRules;

        // Rule unik kombinasi: kurikulum + mapel + tingkat
        $rules['tingkat'] = [
            'required',
            'integer',
            'min:1',
            'max:20',
            Rule::unique('kurikulum_mata_pelajarans')->where(function ($query) {
                return $query->where('kurikulum_id', $this->kurikulum_id)
                    ->where('mata_pelajaran_id', $this->mata_pelajaran_id)
                    ->where('tingkat', $this->tingkat);
            })->ignore($this->kurikulum_mtp_id, 'id'),
        ];

        return $rules;
    }

    // 🔹 Buka modal tambah
    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->dispatch('openModalKurikulumMtp');
    }

    // 🔹 Simpan data baru
    public function store()
    {
        $this->validate($this->getRules());

        KurikulumMataPelajaran::create([
            'kurikulum_id' => $this->kurikulum_id,
            'mata_pelajaran_id' => $this->mata_pelajaran_id,
            // Simpan null jika kelompok_id kosong
            'kelompok_id' => $this->kelompok_id ?: null,
            'tingkat' => $this->tingkat,
            'urutan' => $this->urutan,
        ]);

        $this->dispatch('closeModalKurikulumMtp');
        $this->resetForm();
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Mata pelajaran berhasil ditambahkan ke kurikulum!',
        ]);
    }

    // 🔹 Edit data
    public function edit($id)
    {
        $data = KurikulumMataPelajaran::findOrFail($id);

        $this->kurikulum_mtp_id = $data->id;
        $this->kurikulum_id = $data->kurikulum_id;
        $this->mata_pelajaran_id = $data->mata_pelajaran_id;
        $this->kelompok_id = $data->kelompok_id;
        $this->tingkat = $data->tingkat;
        $this->urutan = $data->urutan;

        $this->isEdit = true;
        $this->dispatch('openModalKurikulumMtp');
    }

    // 🔹 Update data
    public function update()
    {
        $this->validate($this->getRules());

        $data = KurikulumMataPelajaran::findOrFail($this->kurikulum_mtp_id);

        $data->update([
            'kurikulum_id' => $this->kurikulum_id,
            'mata_pelajaran_id' => $this->mata_pelajaran_id,
            'kelompok_id' => $this->kelompok_id ?: null,
            'tingkat' => $this->tingkat,
            'urutan' => $this->urutan,
        ]);

        $this->dispatch('closeModalKurikulumMtp');
        $this->resetForm();
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data kurikulum mata pelajaran berhasil diperbarui!',
        ]);
    }

    // 🔹 Konfirmasi hapus
    public function confirmDeleteKurikulumMtp($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Hapus Data?',
            'text' => 'Data ini akan dihapus secara permanen dari kurikulum!',
            'nextEvent' => 'deleteConfirmedKurikulumMtp',
            'id' => $id,
        ]);
    }

    // 🔹 Hapus data
    public function deleteConfirmedKurikulumMtp($id)
    {
        KurikulumMataPelajaran::findOrFail($id)->delete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data kurikulum mata pelajaran berhasil dihapus!',
        ]);
    }

    // 🔹 Reset form input
    private function resetForm()
    {
        $this->kurikulum_mtp_id = null;
        $this->kurikulum_id = '';
        $this->mata_pelajaran_id = '';
        $this->kelompok_id = '';
        $this->tingkat = '';
        $this->urutan = '';
        $this->resetErrorBag();
    }

    // 🔹 Render tabel data
    public function render()
    {
        $query = KurikulumMataPelajaran::with(['kurikulum', 'mataPelajaran', 'kelompok']);

        // 3. LOGIKA FILTER TINGKAT
        if (!empty($this->filterTingkat)) {
            $query->where('tingkat', $this->filterTingkat);
        }

        // 4. LOGIKA FILTER KELOMPOK
        if (!empty($this->filterKelompok)) {
            // Filter langsung berdasarkan foreign key 'kelompok_id'
            $query->where('kelompok_id', $this->filterKelompok);
        }

        // 5. LOGIKA SEARCH (Updated)
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->whereHas('kurikulum', function ($subQ) {
                    $subQ->where('nama', 'like', '%' . $this->search . '%');
                })->orWhereHas('mataPelajaran', function ($subQ) {
                    $subQ->where('nama', 'like', '%' . $this->search . '%');
                })->orWhere('tingkat', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.admin.data-maping-kurikulum-mapel', [
            'kurikulumMataPelajaran' => $query
                ->join('kurikulums as k', 'k.id', '=', 'kurikulum_mata_pelajarans.kurikulum_id')
                ->select('kurikulum_mata_pelajarans.*')
                ->orderBy('k.nama', 'asc')
                ->orderBy('tingkat', 'asc')
                ->orderBy('urutan', 'asc')
                ->paginate($this->perPage),
        ]);
    }
}
