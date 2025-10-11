<?php

namespace App\Livewire;

use App\Models\Kurikulum; // Asumsi model Kurikulum ada
use App\Models\TahunAjaran; // Asumsi model TahunAjaran ada
use App\Models\TahunAjaranKurikulum; // Model untuk tabel pivot
use Livewire\Component;
use Livewire\WithPagination;

class DataMapingKurikulum extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔹 Pencarian & pagination
    public $search = '';
    public $perPage = 10;

    // 🔹 Form field
    public $ta_kurikulum_id;
    public $kurikulum_id;
    public $tahun_ajaran_id;
    public $status = 'aktif';

    public $isEdit = false;
    public $listKurikulums;
    public $listTahunAjarans;

    // 🔹 Aturan Validasi Dasar
    protected $baseRules = [
        'kurikulum_id' => 'required|uuid',
        'tahun_ajaran_id' => 'required|uuid',
        'status' => 'required|in:aktif,nonaktif',
    ];

    // 🔹 Listener event konfirmasi hapus
    protected $listeners = ['deleteConfirmedTahunAjaranKurikulum' => 'deleteConfirmedTahunAjaranKurikulum'];

    // 🔹 Hook untuk inisialisasi data select box
    public function mount()
    {
        // Ambil semua Kurikulum dan Tahun Ajaran untuk dropdown
        $this->listKurikulums = Kurikulum::orderBy('nama')->get();
        $this->listTahunAjarans = TahunAjaran::orderBy('nama')->get(); // Asumsi kolom nama
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

    // 🔹 Rules dinamis untuk store/update (unique combination)
    public function getRules()
    {
        $rules = $this->baseRules;

        if (!$this->isEdit) {
            // Unik untuk kombinasi kurikulum_id dan tahun_ajaran_id saat store
            $rules['kurikulum_id'] .= '|unique:tahun_ajaran_kurikulums,kurikulum_id,NULL,id,tahun_ajaran_id,' . $this->tahun_ajaran_id;
            $rules['tahun_ajaran_id'] .= '|unique:tahun_ajaran_kurikulums,tahun_ajaran_id,NULL,id,kurikulum_id,' . $this->kurikulum_id;
        }

        // Catatan: Karena validasi unik kombinasi di Livewire agak tricky,
        // kita akan menggunakan validasi kustom di metode store/update untuk kepastian.

        return $rules;
    }

    // 🔹 Buka modal tambah
    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->dispatch('openModalTahunAjaranKurikulum');
    }

    // 🔹 Simpan data baru
    public function store()
    {
        // Gunakan validasi kombinasi unik secara manual untuk memastikan
        $this->validate($this->baseRules);

        // Cek unik kombinasi secara manual
        $exists = TahunAjaranKurikulum::where('kurikulum_id', $this->kurikulum_id)
            ->where('tahun_ajaran_id', $this->tahun_ajaran_id)
            ->exists();
        if ($exists) {
            $this->addError('kurikulum_id', 'Kombinasi Kurikulum dan Tahun Ajaran ini sudah ada.');
            return;
        }

        TahunAjaranKurikulum::create([
            'kurikulum_id' => $this->kurikulum_id,
            'tahun_ajaran_id' => $this->tahun_ajaran_id,
            'status' => $this->status,
        ]);

        $this->resetForm();
        $this->dispatch('closeModalTahunAjaranKurikulum');
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Tahun Ajaran Kurikulum berhasil ditambahkan!',
        ]);
    }

    // 🔹 Edit data (Hanya status yang boleh diubah, kurikulum & tahun ajaran tidak)
    public function edit($id)
    {
        $taKurikulum = TahunAjaranKurikulum::findOrFail($id);

        $this->ta_kurikulum_id = $taKurikulum->id;
        $this->kurikulum_id = $taKurikulum->kurikulum_id;
        $this->tahun_ajaran_id = $taKurikulum->tahun_ajaran_id;
        $this->status = $taKurikulum->status;

        $this->isEdit = true;
        $this->dispatch('openModalTahunAjaranKurikulum');
    }

    // 🔹 Update data
    public function update()
    {
        // Hanya validasi status, karena ID tidak boleh diubah saat edit
        $this->validate(['status' => 'required|in:aktif,nonaktif']);

        $taKurikulum = TahunAjaranKurikulum::findOrFail($this->ta_kurikulum_id);
        $taKurikulum->update([
            'status' => $this->status,
        ]);

        $this->resetForm();
        $this->dispatch('closeModalTahunAjaranKurikulum');
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Status Tahun Ajaran Kurikulum berhasil diperbarui!',
        ]);
    }

    // 🔹 Konfirmasi hapus
    public function confirmDeleteTahunAjaranKurikulum($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Hapus Data?',
            'text' => 'Relasi Tahun Ajaran Kurikulum ini akan dihapus permanen!',
            'nextEvent' => 'deleteConfirmedTahunAjaranKurikulum',
            'id' => $id,
        ]);
    }

    // 🔹 Hapus data
    public function deleteConfirmedTahunAjaranKurikulum($id)
    {
        TahunAjaranKurikulum::findOrFail($id)->delete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Relasi Tahun Ajaran Kurikulum telah dihapus!',
        ]);
    }

    // 🔹 Reset form
    private function resetForm()
    {
        $this->ta_kurikulum_id = null;
        $this->kurikulum_id = '';
        $this->tahun_ajaran_id = '';
        $this->status = '';
        $this->resetErrorBag();
    }

    // 🔹 Render data
    public function render()
    {
        $query = TahunAjaranKurikulum::query()
            ->with(['kurikulum', 'tahunAjaran']); // Asumsi relasi ada di model

        if (!empty($this->search)) {
            $query->whereHas('kurikulum', function ($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                    ->orWhere('kode', 'like', "%{$this->search}%");
            })->orWhereHas('tahunAjaran', function ($q) {
                $q->where('nama', 'like', "%{$this->search}%"); // Asumsi kolom nama
            });
        }

        return view('livewire.data-maping-kurikulum', [
            'tahunAjaranKurikulums' => $query->latest()->paginate($this->perPage),
        ]);
    }
}
