<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MataPelajaran;
use App\Models\TemplateNilaiCapaian;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DeskripsiNilai extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $perPage = 10;
    public $search = '';
    public $activeTab = 'nilai';

    // Data untuk form
    public $selectedMataPelajaran = null;
    public $tingkat = null;
    public $templates = [
        'A' => ['rentang_min' => '', 'rentang_max' => '', 'deskripsi' => ''],
        'B' => ['rentang_min' => '', 'rentang_max' => '', 'deskripsi' => ''],
        'C' => ['rentang_min' => '', 'rentang_max' => '', 'deskripsi' => ''],
        'D' => ['rentang_min' => '', 'rentang_max' => '', 'deskripsi' => ''],
    ];

    public $tahunAjaranSemesterId;
    public $editMode = false;

    // ✅ Event listener
    protected $listeners = [
        'deleteTemplate' => 'deleteTemplate',
    ];

    protected function rules()
    {
        return [
            'selectedMataPelajaran' => 'required|exists:mata_pelajarans,id',
            'tingkat' => 'required|string',
            'templates.A.rentang_min' => 'required|integer|min:0|max:100',
            'templates.A.rentang_max' => 'required|integer|min:0|max:100|gte:templates.A.rentang_min',
            'templates.A.deskripsi' => 'required|string',
            'templates.B.rentang_min' => 'required|integer|min:0|max:100',
            'templates.B.rentang_max' => 'required|integer|min:0|max:100|gte:templates.B.rentang_min',
            'templates.B.deskripsi' => 'required|string',
            'templates.C.rentang_min' => 'required|integer|min:0|max:100',
            'templates.C.rentang_max' => 'required|integer|min:0|max:100|gte:templates.C.rentang_min',
            'templates.C.deskripsi' => 'required|string',
            'templates.D.rentang_min' => 'required|integer|min:0|max:100',
            'templates.D.rentang_max' => 'required|integer|min:0|max:100|gte:templates.D.rentang_min',
            'templates.D.deskripsi' => 'required|string',
        ];
    }

    public function mount()
    {
        $activeSemester = TahunAjaranSemester::aktif()->first();

        if ($activeSemester) {
            $this->tahunAjaranSemesterId = $activeSemester->id;
        } else {
            $latestSemester = TahunAjaranSemester::latest()->first();

            if ($latestSemester) {
                $this->tahunAjaranSemesterId = $latestSemester->id;
                $this->dispatch('swal:warning', [
                    'title' => 'Perhatian!',
                    'text' => 'Tidak ada tahun ajaran semester aktif. Menggunakan data terbaru.',
                ]);
            } else {
                $this->dispatch('swal:error', [
                    'title' => 'Error!',
                    'text' => 'Belum ada data Tahun Ajaran Semester. Silakan buat terlebih dahulu.',
                ]);
                $this->tahunAjaranSemesterId = null;
            }
        }
    }

    public function openModal()
    {
        $this->reset(['selectedMataPelajaran', 'tingkat', 'editMode', 'templates']);
        $this->templates = [
            'A' => ['rentang_min' => '92', 'rentang_max' => '100', 'deskripsi' => ''],
            'B' => ['rentang_min' => '84', 'rentang_max' => '91', 'deskripsi' => ''],
            'C' => ['rentang_min' => '75', 'rentang_max' => '83', 'deskripsi' => ''],
            'D' => ['rentang_min' => '0', 'rentang_max' => '74', 'deskripsi' => ''],
        ];
        $this->dispatch('open-modal-nilai');
    }

    public function save()
    {
        if (!$this->tahunAjaranSemesterId) {
            $this->dispatch('swal:error', ['title' => 'Gagal!', 'text' => 'Tidak ada tahun ajaran semester aktif.']);
            return;
        }

        // Validasi input
        $this->validate();

        try {
            foreach ($this->templates as $predikat => $data) {
                TemplateNilaiCapaian::updateOrCreate(
                    [
                        'tahun_ajaran_semester_id' => $this->tahunAjaranSemesterId,
                        'mata_pelajaran_id' => $this->selectedMataPelajaran,
                        'tingkat' => $this->tingkat,
                        'predikat' => $predikat,
                    ],
                    [
                        'rentang_min' => $data['rentang_min'],
                        'rentang_max' => $data['rentang_max'],
                        'deskripsi' => $data['deskripsi'],
                        'aktif' => true,
                        'created_by' => $this->editMode ? TemplateNilaiCapaian::where('mata_pelajaran_id', $this->selectedMataPelajaran)->where('tingkat', $this->tingkat)->first()->created_by ?? Auth::id() : Auth::id(),
                        'updated_by' => Auth::id(),
                    ]
                );
            }

            $this->dispatch('swal:success', ['title' => 'Berhasil!', 'text' => 'Template nilai berhasil disimpan!']);
            $this->dispatch('close-modal-nilai');
            $this->reset(['selectedMataPelajaran', 'tingkat', 'templates', 'editMode']);
        } catch (\Exception $e) {
            Log::error('Error saving template nilai: ' . $e->getMessage());
            $this->dispatch('swal:error', ['title' => 'Gagal!', 'text' => 'Terjadi kesalahan saat menyimpan data.']);
        }
    }

    public function edit($mataPelajaranId, $tingkat)
    {
        if (!$this->tahunAjaranSemesterId) {
            $this->dispatch('swal:error', ['title' => 'Gagal!', 'text' => 'Tidak ada tahun ajaran semester aktif.']);
            return;
        }

        $this->editMode = true;
        $this->selectedMataPelajaran = $mataPelajaranId;
        $this->tingkat = $tingkat;

        $existingTemplates = TemplateNilaiCapaian::where('tahun_ajaran_semester_id', $this->tahunAjaranSemesterId)
            ->where('mata_pelajaran_id', $mataPelajaranId)
            ->where('tingkat', $tingkat)
            ->get()
            ->keyBy('predikat');

        foreach (['A', 'B', 'C', 'D'] as $predikat) {
            if (isset($existingTemplates[$predikat])) {
                $this->templates[$predikat] = [
                    'rentang_min' => $existingTemplates[$predikat]->rentang_min,
                    'rentang_max' => $existingTemplates[$predikat]->rentang_max,
                    'deskripsi' => $existingTemplates[$predikat]->deskripsi,
                ];
            } else {
                $this->templates[$predikat] = ['rentang_min' => '', 'rentang_max' => '', 'deskripsi' => ''];
            }
        }

        $this->dispatch('open-modal-nilai');
    }

    // ✅ Method untuk delete template (dipanggil dari JavaScript)
    public function deleteTemplate($mataPelajaranId = null, $tingkat = null): void
    {
        // Handle jika parameter datang sebagai array dari JavaScript
        if (is_array($mataPelajaranId)) {
            // Jika array dengan key 'mataPelajaranId' dan 'tingkat'
            if (isset($mataPelajaranId['mataPelajaranId']) && isset($mataPelajaranId['tingkat'])) {
                $tingkat = $mataPelajaranId['tingkat'];
                $mataPelajaranId = $mataPelajaranId['mataPelajaranId'];
            }
            // Jika array index biasa [0, 1]
            elseif (isset($mataPelajaranId[0]) && isset($mataPelajaranId[1])) {
                $tingkat = $mataPelajaranId[1];
                $mataPelajaranId = $mataPelajaranId[0];
            }
        }

        // Log untuk debugging
        Log::info('Delete Template Called', [
            'mataPelajaranId' => $mataPelajaranId,
            'tingkat' => $tingkat,
            'tahunAjaranSemesterId' => $this->tahunAjaranSemesterId,
        ]);

        if (!$mataPelajaranId || !$tingkat || !$this->tahunAjaranSemesterId) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Data template tidak valid. (ID: ' . $mataPelajaranId . ', Tingkat: ' . $tingkat . ')',
            ]);
            return;
        }

        try {
            $deletedCount = TemplateNilaiCapaian::where('tahun_ajaran_semester_id', $this->tahunAjaranSemesterId)
                ->where('mata_pelajaran_id', $mataPelajaranId)
                ->where('tingkat', $tingkat)
                ->delete();

            if ($deletedCount > 0) {
                $this->dispatch('swal:success', [
                    'title' => 'Berhasil!',
                    'text' => 'Template nilai berhasil dihapus!',
                ]);
            } else {
                $this->dispatch('swal:info', [
                    'title' => 'Info',
                    'text' => 'Template nilai tidak ditemukan.',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting template nilai: ' . $e->getMessage(), [
                'mata_pelajaran_id' => $mataPelajaranId,
                'tingkat' => $tingkat,
                'user_id' => Auth::id(),
            ]);

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat menghapus data.',
            ]);
        }
    }

    public function render()
    {
        if (!$this->tahunAjaranSemesterId) {
            return view('livewire.admin.deskripsi-nilai', [
                'templatesGrouped' => [],
                'mataPelajarans' => collect([]),
                'paginator' => null,
            ]);
        }

        $query = TemplateNilaiCapaian::with('mataPelajaran')
            ->where('tahun_ajaran_semester_id', $this->tahunAjaranSemesterId)
            ->when($this->search, function ($q) {
                $q->whereHas('mataPelajaran', function ($subQ) {
                    $subQ->where('nama', 'like', '%' . $this->search . '%');
                });
            })
            ->select('mata_pelajaran_id', 'tingkat')
            ->groupBy('mata_pelajaran_id', 'tingkat')
            ->paginate($this->perPage);

        $paginatedKeys = $query->map(function ($item) {
            return $item->mata_pelajaran_id . '_' . $item->tingkat;
        })->toArray();

        $allTemplates = TemplateNilaiCapaian::with('mataPelajaran')
            ->where('tahun_ajaran_semester_id', $this->tahunAjaranSemesterId)
            ->get();

        $templatesGrouped = [];
        foreach ($allTemplates as $template) {
            $key = $template->mata_pelajaran_id . '_' . $template->tingkat;

            if (in_array($key, $paginatedKeys)) {
                if (!isset($templatesGrouped[$key])) {
                    $templatesGrouped[$key] = [];
                }

                $templatesGrouped[$key][] = [
                    'id' => $template->id,
                    'mata_pelajaran_id' => $template->mata_pelajaran_id,
                    'mata_pelajaran_nama' => $template->mataPelajaran->nama ?? 'Umum',
                    'tingkat' => $template->tingkat,
                    'predikat' => $template->predikat,
                    'rentang_min' => $template->rentang_min,
                    'rentang_max' => $template->rentang_max,
                    'deskripsi' => $template->deskripsi,
                ];
            }
        }

        foreach ($templatesGrouped as $key => $group) {
            usort($templatesGrouped[$key], function ($a, $b) {
                $order = ['A' => 1, 'B' => 2, 'C' => 3, 'D' => 4];
                return $order[$a['predikat']] <=> $order[$b['predikat']];
            });
        }

        $mataPelajarans = MataPelajaran::where('status', 'aktif')->orderBy('nama')->get();

        return view('livewire.admin.deskripsi-nilai', [
            'templatesGrouped' => $templatesGrouped,
            'mataPelajarans' => $mataPelajarans,
            'paginator' => $query,
        ]);
    }
}
