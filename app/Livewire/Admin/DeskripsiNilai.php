<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MataPelajaran;
use App\Models\TemplateNilaiCapaian;
use App\Models\TahunAjaranSemester;
use Illuminate\Support\Facades\Auth;

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
        // Menggunakan scope aktif()
        $activeSemester = TahunAjaranSemester::aktif()->first();

        if ($activeSemester) {
            $this->tahunAjaranSemesterId = $activeSemester->id;
        } else {
            // Fallback ke yang terbaru
            $latestSemester = TahunAjaranSemester::latest()->first();

            if ($latestSemester) {
                $this->tahunAjaranSemesterId = $latestSemester->id;
                session()->flash('warning', 'Tidak ada tahun ajaran semester aktif. Menggunakan data terbaru.');
            } else {
                session()->flash('error', 'Belum ada data Tahun Ajaran Semester. Silakan buat terlebih dahulu.');
                $this->tahunAjaranSemesterId = null;
            }
        }
    }

    public function openModal()
    {
        $this->reset(['selectedMataPelajaran', 'tingkat', 'editMode']);
        $this->templates = [
            'A' => ['rentang_min' => '90', 'rentang_max' => '100', 'deskripsi' => ''],
            'B' => ['rentang_min' => '80', 'rentang_max' => '89', 'deskripsi' => ''],
            'C' => ['rentang_min' => '70', 'rentang_max' => '79', 'deskripsi' => ''],
            'D' => ['rentang_min' => '0', 'rentang_max' => '69', 'deskripsi' => ''],
        ];
        $this->dispatch('open-modal-nilai');
    }

    public function save()
    {
        if (!$this->tahunAjaranSemesterId) {
            session()->flash('error', 'Tidak ada tahun ajaran semester aktif.');
            return;
        }

        $this->validate();

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
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]
            );
        }

        session()->flash('success', 'Template nilai berhasil disimpan!');
        $this->dispatch('close-modal-nilai');
        $this->reset(['selectedMataPelajaran', 'tingkat', 'templates']);
    }

    public function edit($mataPelajaranId, $tingkat)
    {
        if (!$this->tahunAjaranSemesterId) {
            session()->flash('error', 'Tidak ada tahun ajaran semester aktif.');
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
            }
        }

        $this->dispatch('open-modal-nilai');
    }

    public function delete($mataPelajaranId, $tingkat)
    {
        if (!$this->tahunAjaranSemesterId) {
            session()->flash('error', 'Tidak ada tahun ajaran semester aktif.');
            return;
        }

        TemplateNilaiCapaian::where('tahun_ajaran_semester_id', $this->tahunAjaranSemesterId)
            ->where('mata_pelajaran_id', $mataPelajaranId)
            ->where('tingkat', $tingkat)
            ->delete();

        session()->flash('success', 'Template nilai berhasil dihapus!');
    }

    public function render()
    {
        // Jika tidak ada tahun ajaran semester, tampilkan kosong
        if (!$this->tahunAjaranSemesterId) {
            return view('livewire.admin.deskripsi-capaian', [
                'templatesGrouped' => [],
                'mataPelajarans' => collect([]),
            ]);
        }

        // Ambil semua templates dan group manual
        $allTemplates = TemplateNilaiCapaian::with('mataPelajaran')
            ->where('tahun_ajaran_semester_id', $this->tahunAjaranSemesterId)
            ->when($this->search, function ($query) {
                $query->whereHas('mataPelajaran', function ($q) {
                    $q->where('nama', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('predikat')
            ->get();

        // Group dan convert ke array sederhana
        $templatesGrouped = [];
        foreach ($allTemplates as $template) {
            $key = $template->mata_pelajaran_id . '_' . $template->tingkat;

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

        $mataPelajarans = MataPelajaran::where('status', 'aktif')->orderBy('nama')->get();

        return view('livewire.admin.deskripsi-nilai', [
            'templatesGrouped' => $templatesGrouped,
            'mataPelajarans' => $mataPelajarans,
        ]);
    }
}
