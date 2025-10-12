<?php

namespace App\Livewire;

use App\Models\Rombel;
use App\Models\Jurusan;
use App\Models\TahunAjaranKurikulum;
use App\Models\User; // Asumsi model User untuk Wali Kelas

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class DataRombel extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // 🔹 Properti pencarian & pagination
    public $search = '';
    public $perPage = 10;

    // 🔹 Field form
    public $rombel_id;
    public $jurusan_id;
    public $tahun_ajaran_kurikulum_id; // nullable
    public $wali_kelas_slug; // 🚨 Diperbarui: Menggunakan slug (string)
    public $tingkat; // unsignedTinyInteger (10, 11, 12)
    public $nama;

    public $isEdit = false;

    // Data dropdown
    public $jurusanList = [];
    public $tahunAjaranKurikulumList = [];
    public $walikelasList = []; // Asumsi ini adalah daftar guru/staf

    // 🔹 Event listener
    protected $listeners = [
        'deleteConfirmedRombel' => 'deleteConfirmedRombel',
        'createRombel' => 'create',
    ];

    public function mount()
    {
        // Muat data untuk dropdown saat komponen diinisialisasi
        $this->jurusanList = Jurusan::orderBy('nama', 'asc')->get();

        $this->tahunAjaranKurikulumList = TahunAjaranKurikulum::with(['kurikulum', 'tahunAjaran'])
            ->get()
            ->map(function ($item) {
                $item->display_name = $item->tahunAjaran->nama . ' (' . ($item->kurikulum->nama ?? 'Tanpa Kurikulum') . ')';
                return $item;
            });

        // Ambil daftar guru/user dan pastikan slug/ID yang dibutuhkan tersedia
        // Asumsi kolom yang digunakan untuk relasi adalah 'slug' dan kolom tampilan adalah 'name'
        $this->walikelasList = User::orderBy('name', 'asc')
            ->select('id', 'slug', 'name')
            ->get();
    }

    // 🔹 Validasi dasar
    protected $baseRules = [
        'jurusan_id' => 'required|uuid|exists:jurusans,id',
        'tahun_ajaran_kurikulum_id' => 'nullable|uuid|exists:tahun_ajaran_kurikulums,id',
        // 🚨 Diperbarui: Validasi slug harus ada di tabel users (atau tabel guru)
        'wali_kelas_slug' => 'nullable|string|exists:users,slug',
        'tingkat' => 'required|integer|in:10,11,12',
        'nama' => 'required|string|min:2',
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

        // Rule 'nama' harus unik di dalam Tingkat dan Jurusan yang sama
        $uniqueRule = Rule::unique('rombels', 'nama')
            ->where(fn($query) => $query->where('tingkat', $this->tingkat)
                ->where('jurusan_id', $this->jurusan_id));

        if ($this->isEdit && $this->rombel_id) {
            $rules['nama'] = ['required', 'string', 'min:2', $uniqueRule->ignore($this->rombel_id, 'id')];
        } elseif (!$this->isEdit) {
            $rules['nama'] = ['required', 'string', 'min:2', $uniqueRule];
        }

        return $rules;
    }

    // 🔹 Buka modal tambah
    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->dispatch('openModalRombel');
    }

    // 🔹 Simpan data baru
    public function store()
    {
        $this->validate($this->getRules());

        Rombel::create([
            'jurusan_id' => $this->jurusan_id,
            'tahun_ajaran_kurikulum_id' => $this->tahun_ajaran_kurikulum_id ?: null,
            // 🚨 Diperbarui
            'wali_kelas_slug' => $this->wali_kelas_slug ?: null,
            'tingkat' => $this->tingkat,
            'nama' => $this->nama,
        ]);

        $this->dispatch('closeModalRombel');
        $this->resetForm();
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data rombongan belajar berhasil ditambahkan!',
        ]);
    }

    // 🔹 Edit data
    public function edit($id)
    {
        $data = Rombel::findOrFail($id);

        $this->rombel_id = $data->id;
        $this->jurusan_id = $data->jurusan_id;
        $this->tahun_ajaran_kurikulum_id = $data->tahun_ajaran_kurikulum_id;
        // 🚨 Diperbarui
        $this->wali_kelas_slug = $data->wali_kelas_slug;
        $this->tingkat = $data->tingkat;
        $this->nama = $data->nama;

        $this->isEdit = true;
        $this->dispatch('openModalRombel');
    }

    // 🔹 Update data
    public function update()
    {
        $this->validate($this->getRules());

        $data = Rombel::findOrFail($this->rombel_id);

        $data->update([
            'jurusan_id' => $this->jurusan_id,
            'tahun_ajaran_kurikulum_id' => $this->tahun_ajaran_kurikulum_id ?: null,
            // 🚨 Diperbarui
            'wali_kelas_slug' => $this->wali_kelas_slug ?: null,
            'tingkat' => $this->tingkat,
            'nama' => $this->nama,
        ]);

        $this->dispatch('closeModalRombel');
        $this->resetForm();
        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data rombongan belajar berhasil diperbarui!',
        ]);
    }

    // 🔹 Konfirmasi hapus
    public function confirmDeleteRombel($id)
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Hapus Data?',
            'text' => 'Rombel ini akan dihapus secara permanen!',
            'nextEvent' => 'deleteConfirmedRombel',
            'id' => $id,
        ]);
    }

    // 🔹 Hapus data
    public function deleteConfirmedRombel($id)
    {
        Rombel::findOrFail($id)->delete();

        $this->dispatch('swal:success', [
            'title' => 'Berhasil!',
            'text' => 'Data rombongan belajar berhasil dihapus!',
        ]);
    }

    // 🔹 Reset form input
    private function resetForm()
    {
        $this->rombel_id = null;
        $this->jurusan_id = '';
        $this->tahun_ajaran_kurikulum_id = '';
        // 🚨 Diperbarui
        $this->wali_kelas_slug = '';
        $this->tingkat = '';
        $this->nama = '';
        $this->resetErrorBag();
    }

    // 🔹 Render tabel data
    public function render()
    {
        // Query dengan eager loading relasi
        // Perlu menambahkan relasi ke User (wali kelas) berdasarkan slug, jika menggunakan Eloquent.
        // Karena relasi menggunakan slug (string), perlu definisi relasi khusus di model Rombel.
        // Di sini kita asumsikan relasi bernama 'waliKelasBySlug' sudah ada di model Rombel.

        $query = Rombel::with(['jurusan', 'tahunAjaranKurikulum.kurikulum', 'tahunAjaranKurikulum.tahunAjaran']);

        // Melakukan join manual untuk walikelas agar bisa difilter/ditampilkan dengan mudah
        // Jika tidak ada relasi di model, ambil data user di view dengan find(slug) atau buat relasi hasOne/belongsTo di model Rombel.
        // Untuk saat ini, kita akan melakukan join/filter pada users secara eksplisit di render.

        $query->leftJoin('users as u', 'u.slug', '=', 'rombels.wali_kelas_slug');

        // Filter pencarian
        if (!empty($this->search)) {
            $query->where('rombels.nama', 'like', '%' . $this->search . '%')
                ->orWhere('rombels.tingkat', 'like', '%' . $this->search . '%')
                ->orWhereHas('jurusan', function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%');
                })
                ->orWhere('u.name', 'like', '%' . $this->search . '%'); // Filter berdasarkan nama wali kelas
        }

        return view('livewire.data-rombel', [
            'rombels' => $query
                ->join('jurusans as j', 'j.id', '=', 'rombels.jurusan_id')
                ->select('rombels.*', 'u.name as walikelas_name') // Ambil nama walikelas dari join
                ->orderBy('rombels.tingkat', 'asc')
                ->orderBy('j.nama', 'asc')
                ->orderBy('rombels.nama', 'asc')
                ->paginate($this->perPage),
        ]);
    }
}
