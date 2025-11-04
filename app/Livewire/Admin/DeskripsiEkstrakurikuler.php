<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TahunAjaranSemester;
use App\Models\Ekstrakurikuler;
use App\Models\TemplateEkstrakurikulerDeskripsi; // Asumsi nama model
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DeskripsiEkstrakurikuler extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $perPage = 10;
    public $search = '';

    // Data untuk form
    public $selectedEkstrakurikuler = null; // Mirip selectedMataPelajaran
    public $templates = [
        'A' => ['deskripsi' => ''], // Sangat Baik
        'B' => ['deskripsi' => ''], // Baik
        'C' => ['deskripsi' => ''], // Cukup
    ];
    public $gunakanPlaceholder = false;

    public $tahunAjaranSemesterId;
    public $editMode = false;

    // Data Master
    public $ekstrakurikulerList;

    // Event listener
    protected $listeners = [
        'deleteEkstrakurikulerTemplate' => 'deleteTemplate',
    ];

    protected function rules()
    {
        return [
            'selectedEkstrakurikuler' => 'nullable|exists:ekstrakurikulers,id', // Bisa null untuk template umum
            'templates.A.deskripsi' => 'required|string',
            'templates.B.deskripsi' => 'required|string',
            'templates.C.deskripsi' => 'required|string',
            'gunakanPlaceholder' => 'boolean',
        ];
    }

    public function mount()
    {
        $activeSemester = TahunAjaranSemester::aktif()->first();

        if ($activeSemester) {
            $this->tahunAjaranSemesterId = $activeSemester->id;
        } else {
            $latestSemester = TahunAjaranSemester::latest()->first();
            $this->tahunAjaranSemesterId = $latestSemester->id ?? null;
        }

        // Ambil daftar Ekstrakurikuler
        $this->ekstrakurikulerList = Ekstrakurikuler::orderBy('nama')->get();
    }

    public function openModal()
    {
        $this->reset(['selectedEkstrakurikuler', 'editMode', 'templates', 'gunakanPlaceholder']);
        // Isi default
        $this->templates = [
            'A' => ['deskripsi' => 'Sangat Baik dalam...'],
            'B' => ['deskripsi' => 'Baik dalam...'],
            'C' => ['deskripsi' => 'Cukup dalam...'],
        ];
        $this->dispatch('open-modal-ekskul');
    }

    public function save()
    {
        if (!$this->tahunAjaranSemesterId) {
            $this->dispatch('swal:error', ['title' => 'Gagal!', 'text' => 'Tidak ada tahun ajaran semester aktif.']);
            return;
        }

        // Validasi
        $this->validate();

        try {
            // Predikat yang digunakan (A, B, C)
            $predikats = ['A', 'B', 'C'];

            // Kolom ini akan menjadi NULL jika user memilih 'Template Umum'
            $ekskulId = $this->selectedEkstrakurikuler === 'general' ? null : $this->selectedEkstrakurikuler;

            // Pastikan tidak ada duplikasi antara Template Umum (ekskul_id=null) dan spesifik (ekskul_id!=null)
            // Cek unique constraint di model: tahun_ajaran_semester_id, ekstrakurikuler_id, predikat

            foreach ($predikats as $predikat) {
                // Pola updateOrCreate
                TemplateEkstrakurikulerDeskripsi::updateOrCreate(
                    [
                        'tahun_ajaran_semester_id' => $this->tahunAjaranSemesterId,
                        'ekstrakurikuler_id' => $ekskulId,
                        'predikat' => $predikat,
                    ],
                    [
                        'deskripsi' => $this->templates[$predikat]['deskripsi'],
                        'gunakan_placeholder' => $this->gunakanPlaceholder,
                        'aktif' => true,
                        // Logika created_by/updated_by
                        'created_by' => $this->editMode ?
                            TemplateEkstrakurikulerDeskripsi::where('ekstrakurikuler_id', $ekskulId)
                            ->where('tahun_ajaran_semester_id', $this->tahunAjaranSemesterId)
                            ->first()->created_by ?? Auth::id()
                            : Auth::id(),
                        'updated_by' => Auth::id(),
                    ]
                );
            }

            $message = $this->editMode ? 'Template Ekstrakurikuler berhasil diperbarui!' : 'Template Ekstrakurikuler berhasil disimpan!';

            $this->dispatch('swal:success', ['title' => 'Berhasil!', 'text' => $message]);
            $this->dispatch('close-modal-ekskul');
            $this->reset(['selectedEkstrakurikuler', 'templates', 'editMode', 'gunakanPlaceholder']);
        } catch (\Exception $e) {
            Log::error('Error saving template ekstrakurikuler: ' . $e->getMessage());
            $this->dispatch('swal:error', ['title' => 'Gagal!', 'text' => 'Terjadi kesalahan saat menyimpan data.']);
        }
    }

    public function edit($ekskulId) // EkskulId bisa string 'general' atau UUID
    {
        if (!$this->tahunAjaranSemesterId) {
            $this->dispatch('swal:error', ['title' => 'Gagal!', 'text' => 'Tidak ada tahun ajaran semester aktif.']);
            return;
        }

        $ekskulIdForQuery = $ekskulId === 'general' ? null : $ekskulId;

        $this->editMode = true;
        $this->selectedEkstrakurikuler = $ekskulId; // Simpan 'general' atau UUID

        $existingTemplates = TemplateEkstrakurikulerDeskripsi::where('tahun_ajaran_semester_id', $this->tahunAjaranSemesterId)
            ->where('ekstrakurikuler_id', $ekskulIdForQuery)
            ->get()
            ->keyBy('predikat');

        $firstTemplate = $existingTemplates->first();
        if ($firstTemplate) {
            $this->gunakanPlaceholder = $firstTemplate->gunakan_placeholder;
        } else {
            $this->gunakanPlaceholder = false;
        }

        foreach (['A', 'B', 'C'] as $predikat) {
            if (isset($existingTemplates[$predikat])) {
                $this->templates[$predikat] = [
                    'deskripsi' => $existingTemplates[$predikat]->deskripsi,
                ];
            } else {
                $this->templates[$predikat] = ['deskripsi' => ''];
            }
        }

        $this->dispatch('open-modal-ekskul');
    }

    public function deleteTemplate($ekskulId = null): void
    {
        if (is_array($ekskulId)) {
            $ekskulId = $ekskulId[0] ?? null;
        }

        if (!$ekskulId || !$this->tahunAjaranSemesterId) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Data template tidak valid.',
            ]);
            return;
        }

        $ekskulIdForQuery = $ekskulId === 'general' ? null : $ekskulId;
        $namaEkskul = $ekskulId === 'general' ? 'Template Umum' : (Ekstrakurikuler::find($ekskulId)->nama ?? 'Ekskul Tidak Ditemukan');

        try {
            $deletedCount = TemplateEkstrakurikulerDeskripsi::where('tahun_ajaran_semester_id', $this->tahunAjaranSemesterId)
                ->where('ekstrakurikuler_id', $ekskulIdForQuery)
                ->delete();

            if ($deletedCount > 0) {
                $this->dispatch('swal:success', [
                    'title' => 'Berhasil!',
                    'text' => 'Template ' . $namaEkskul . ' berhasil dihapus!',
                ]);
            } else {
                $this->dispatch('swal:info', [
                    'title' => 'Info',
                    'text' => 'Template ekstrakurikuler tidak ditemukan.',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting template ekstrakurikuler: ' . $e->getMessage());

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat menghapus data.',
            ]);
        }
    }

    public function render()
    {
        if (!$this->tahunAjaranSemesterId) {
            return view('livewire.admin.deskripsi-ekstrakurikuler', [
                'templatesGrouped' => collect([]),
                'paginator' => null,
            ]);
        }

        // Query unik (ekstrakurikuler_id) yang sudah dipaginasi
        $query = TemplateEkstrakurikulerDeskripsi::with('ekstrakurikuler')
            ->where('tahun_ajaran_semester_id', $this->tahunAjaranSemesterId)
            ->when($this->search, function ($q) {
                $q->whereHas('ekstrakurikuler', function ($subQ) {
                    $subQ->where('nama', 'like', '%' . $this->search . '%');
                })->orWhereNull('ekstrakurikuler_id'); // Selalu tampilkan Template Umum jika ada
            })
            ->select('ekstrakurikuler_id')
            ->groupBy('ekstrakurikuler_id')
            ->orderByRaw('CASE WHEN ekstrakurikuler_id IS NULL THEN 0 ELSE 1 END') // Template Umum di atas
            ->orderBy('ekstrakurikuler_id')
            ->paginate($this->perPage);

        $paginatedKeys = $query->pluck('ekstrakurikuler_id')->map(function ($id) {
            return $id ?? 'general';
        })->toArray();

        // Ambil detail template untuk halaman ini
        $allTemplates = TemplateEkstrakurikulerDeskripsi::with('ekstrakurikuler')
            ->where('tahun_ajaran_semester_id', $this->tahunAjaranSemesterId)
            ->whereIn('ekstrakurikuler_id', array_filter($paginatedKeys)) // Ekskul Spesifik
            ->orWhere(function ($q) use ($paginatedKeys) {
                if (in_array('general', $paginatedKeys)) {
                    $q->whereNull('ekstrakurikuler_id'); // Template Umum
                }
            })
            ->get();

        $templatesGrouped = [];
        foreach ($allTemplates as $template) {
            $id = $template->ekstrakurikuler_id ?? 'general';
            $key = $id;

            if (!isset($templatesGrouped[$key])) {
                $templatesGrouped[$key] = [];
            }

            $templatesGrouped[$key][] = [
                'id' => $template->id,
                'ekstrakurikuler_id' => $template->ekstrakurikuler_id,
                'ekstrakurikuler_nama' => $template->ekstrakurikuler->nama ?? 'Template Umum',
                'predikat' => $template->predikat,
                'deskripsi' => $template->deskripsi,
                'placeholder' => $template->gunakan_placeholder,
            ];
        }

        // Urutkan predikat di setiap group (A, B, C)
        foreach ($templatesGrouped as $key => $group) {
            usort($templatesGrouped[$key], function ($a, $b) {
                $order = ['A' => 1, 'B' => 2, 'C' => 3];
                return $order[$a['predikat']] <=> $order[$b['predikat']];
            });
        }

        return view('livewire.admin.deskripsi-ekstrakurikuler', [
            'templatesGrouped' => $templatesGrouped,
            'paginator' => $query,
        ]);
    }
}
