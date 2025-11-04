<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use App\Models\MataPelajaran;

class DataMataPelajaran extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔹 Pencarian & pagination
    public $search = '';
    public $perPage = 10;

    // 🔹 Form field BARU
    public $mapel_id;
    public $nama;
    public $kode;
    public $status = 'aktif';
    public $is_mapel_agama = false; // Default: Mata Pelajaran Umum
    public $agama_terkait; // Hanya diisi jika is_mapel_agama = true

    // 🔹 List Agama untuk Dropdown
    public $agamaList = [
        'islam',
        'kristen',
        'katolik',
        'hindu',
        'buddha',
        'khonghucu'
    ];

    public $isEdit = false;

    // 🔹 Aturan Dasar Validasi
    protected $baseRules = [
        'nama' => 'required|min:3',
        'kode' => 'required|unique:mata_pelajarans,kode|max:10',
        'status' => 'required|in:aktif,arsip',
        'is_mapel_agama' => 'required|boolean', // BARU: Harus boolean
        // BARU: Aturan bersyarat untuk agama_terkait
        'agama_terkait' => 'nullable|required_if:is_mapel_agama,true|in:islam,kristen,katolik,hindu,buddha,khonghucu',
    ];

    // 🔹 Event listener
    protected $listeners = [
        'deleteConfirmed',
        'createMapel' => 'create',
    ];

    // 🔹 Reset pagination saat search/perPage berubah
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    // 🔹 Rules dinamis untuk edit vs tambah
    public function getRules()
    {
        $rules = $this->baseRules;

        if ($this->isEdit && $this->mapel_id) {
            $rules['kode'] = 'required|unique:mata_pelajarans,kode,' . $this->mapel_id . ',id|max:10';
        }

        return $rules;
    }

    // 🔹 Buka modal tambah
    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->dispatch('openModal');
    }

    // 🔹 Simpan data baru
    public function store()
    {
        $this->validate($this->getRules());

        MataPelajaran::create([
            'nama' => $this->nama,
            'kode' => strtoupper($this->kode),
            'status' => $this->status,
            'is_mapel_agama' => $this->is_mapel_agama, // BARU
            'agama_terkait' => $this->is_mapel_agama ? Str::lower($this->agama_terkait) : null, // BARU: Simpan lowercase jika mapel agama
        ]);

        $this->resetForm();
        $this->dispatch('closeModal');
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data mata pelajaran berhasil ditambahkan!',
        ]);
    }

    // 🔹 Edit data
    public function edit($id)
    {
        $mapel = MataPelajaran::findOrFail($id);

        $this->mapel_id = $mapel->id;
        $this->nama = $mapel->nama;
        $this->kode = $mapel->kode;
        $this->status = $mapel->status;
        $this->is_mapel_agama = (bool)$mapel->is_mapel_agama; // BARU: Pastikan boolean
        $this->agama_terkait = $mapel->agama_terkait; // BARU

        $this->isEdit = true;
        $this->dispatch('openModal');
    }

    // 🔹 Update data
    public function update()
    {
        $this->validate($this->getRules());

        $mapel = MataPelajaran::findOrFail($this->mapel_id);

        $mapel->update([
            'nama' => $this->nama,
            'kode' => strtoupper($this->kode),
            'status' => $this->status,
            'is_mapel_agama' => $this->is_mapel_agama, // BARU
            'agama_terkait' => $this->is_mapel_agama ? Str::lower($this->agama_terkait) : null, // BARU
        ]);

        $this->resetForm();
        $this->dispatch('closeModal');
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data mata pelajaran berhasil diperbarui!',
        ]);
    }

    // 🔹 Konfirmasi hapus
    public function confirmDelete($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Hapus Data?',
            'text' => 'Data mata pelajaran ini akan dihapus secara permanen!',
            'nextEvent' => 'deleteConfirmed',
            'id' => $id,
        ]);
    }

    // 🔹 Hapus data
    public function deleteConfirmed($id)
    {
        MataPelajaran::findOrFail($id)->delete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data mata pelajaran berhasil dihapus!',
        ]);
    }

    // 🔹 Reset form
    private function resetForm()
    {
        $this->mapel_id = null;
        $this->nama = '';
        $this->kode = '';
        $this->status = 'aktif'; // Set default status
        $this->is_mapel_agama = false; // BARU: Reset ke default
        $this->agama_terkait = null; // BARU: Reset ke null
        $this->resetErrorBag();
    }

    // 🔹 Handle perubahan is_mapel_agama
    public function updatedIsMapelAgama($value)
    {
        if (!$value) {
            // Jika diubah menjadi mapel umum, reset agama terkait
            $this->agama_terkait = null;
        }
    }

    // 🔹 Render data
    public function render()
    {
        $query = MataPelajaran::query();

        // Filter pencarian (jika ada)
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nama', 'like', '%' . $this->search . '%')
                    ->orWhere('kode', 'like', '%' . $this->search . '%')
                    ->orWhere('status', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.admin.data-mata-pelajaran', [
            'mata_pelajarans' => $query->orderBy('nama', 'asc')->paginate($this->perPage)
        ]);
    }
}
