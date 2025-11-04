<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TahunAjaranSemester;
use App\Models\TemplateKokurikulerCapaian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DeskripsiKokurikuler extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $perPage = 10;
    public $search = '';

    // Data untuk form (Hanya A, B, C)
    public $selectedSubdimensi = null;
    public $tingkat = null;
    public $templates = [
        'A' => ['deskripsi' => ''], // Mahir
        'B' => ['deskripsi' => ''], // Cakap
        'C' => ['deskripsi' => ''], // Berkembang
    ];

    public $tahunAjaranSemesterId;
    public $editMode = false;

    // Event listener
    protected $listeners = [
        'deleteKokurikulerTemplate' => 'deleteTemplate',
    ];

    protected function rules()
    {
        return [
            'selectedSubdimensi' => 'required|string|max:191',
            'tingkat' => 'required|string|in:10,11,12',
            'templates.A.deskripsi' => 'required|string',
            'templates.B.deskripsi' => 'required|string',
            'templates.C.deskripsi' => 'required|string',
            // Predikat D dihapus dari rules
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
            } else {
                $this->tahunAjaranSemesterId = null;
            }
        }
    }

    public function openModal()
    {
        $this->reset(['selectedSubdimensi', 'tingkat', 'editMode', 'templates']);
        // Mengisi default hanya untuk A, B, C
        $this->templates = [
            'A' => ['deskripsi' => 'Menunjukkan **kemahiran** (Mahir)...'],
            'B' => ['deskripsi' => 'Menunjukkan **kecakapan** (Cakap)...'],
            'C' => ['deskripsi' => 'Menunjukkan **perkembangan** (Berkembang)...'],
        ];
        $this->dispatch('open-modal-kokurikuler');
    }

    public function save()
    {
        if (!$this->tahunAjaranSemesterId) {
            $this->dispatch('swal:error', ['title' => 'Gagal!', 'text' => 'Tidak ada tahun ajaran semester aktif.']);
            return;
        }

        // Normalisasi string input sebelum validasi dan penyimpanan
        $this->selectedSubdimensi = strtolower(trim($this->selectedSubdimensi));

        // Validasi input (hanya A, B, C)
        $this->validate();

        try {
            // Loop hanya untuk A, B, C
            foreach ($this->templates as $predikat => $data) {
                TemplateKokurikulerCapaian::updateOrCreate(
                    [
                        'tahun_ajaran_semester_id' => $this->tahunAjaranSemesterId,
                        'subdimensi' => $this->selectedSubdimensi,
                        'tingkat' => $this->tingkat,
                        'predikat' => $predikat,
                    ],
                    [
                        'deskripsi' => $data['deskripsi'],
                        'aktif' => true,
                        // Logika created_by/updated_by
                        'created_by' => $this->editMode ?
                            TemplateKokurikulerCapaian::where('subdimensi', $this->selectedSubdimensi)
                            ->where('tingkat', $this->tingkat)
                            ->where('tahun_ajaran_semester_id', $this->tahunAjaranSemesterId)
                            ->first()->created_by ?? Auth::id()
                            : Auth::id(),
                        'updated_by' => Auth::id(),
                    ]
                );
            }

            $message = $this->editMode ? 'Template Kokurikuler berhasil diperbarui!' : 'Template Kokurikuler berhasil disimpan!';

            $this->dispatch('swal:success', ['title' => 'Berhasil!', 'text' => $message]);
            $this->dispatch('close-modal-kokurikuler');
            $this->reset(['selectedSubdimensi', 'tingkat', 'templates', 'editMode']);
        } catch (\Exception $e) {
            Log::error('Error saving template kokurikuler: ' . $e->getMessage(), [
                'subdimensi_input' => $this->selectedSubdimensi,
                'tingkat_input' => $this->tingkat,
                'semester_id' => $this->tahunAjaranSemesterId,
            ]);

            $this->dispatch('swal:error', ['title' => 'Gagal!', 'text' => 'Terjadi kesalahan saat menyimpan data. (Lihat log server untuk detail)']);
        }
    }

    public function edit($subdimensi, $tingkat)
    {
        if (!$this->tahunAjaranSemesterId) {
            $this->dispatch('swal:error', ['title' => 'Gagal!', 'text' => 'Tidak ada tahun ajaran semester aktif.']);
            return;
        }

        $this->editMode = true;
        // Normalisasi data yang diterima
        $this->selectedSubdimensi = strtolower(trim($subdimensi));
        $this->tingkat = $tingkat;

        $existingTemplates = TemplateKokurikulerCapaian::where('tahun_ajaran_semester_id', $this->tahunAjaranSemesterId)
            ->where('subdimensi', $this->selectedSubdimensi)
            ->where('tingkat', $tingkat)
            ->get()
            ->keyBy('predikat');

        // Loop hanya untuk A, B, C
        foreach (['A', 'B', 'C'] as $predikat) {
            if (isset($existingTemplates[$predikat])) {
                $this->templates[$predikat] = [
                    'deskripsi' => $existingTemplates[$predikat]->deskripsi,
                ];
            } else {
                $this->templates[$predikat] = ['deskripsi' => ''];
            }
        }

        // Pastikan predikat D direset/dibuang jika ada di state sebelumnya
        unset($this->templates['D']);

        $this->dispatch('open-modal-kokurikuler');
    }

    public function deleteTemplate($subdimensi = null, $tingkat = null): void
    {
        if (is_array($subdimensi)) {
            $tingkat = $subdimensi[1] ?? null;
            $subdimensi = $subdimensi[0] ?? null;
        }

        if (!$subdimensi || !$tingkat || !$this->tahunAjaranSemesterId) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Data template tidak valid. (Subdimensi: ' . $subdimensi . ', Tingkat: ' . $tingkat . ')',
            ]);
            return;
        }

        // Normalisasi data yang diterima
        $subdimensi = strtolower(trim($subdimensi));

        try {
            // Hapus semua 3 predikat (A, B, C) sekaligus
            $deletedCount = TemplateKokurikulerCapaian::where('tahun_ajaran_semester_id', $this->tahunAjaranSemesterId)
                ->where('subdimensi', $subdimensi)
                ->where('tingkat', $tingkat)
                ->delete();

            // ... (Logika success/info/error)
            if ($deletedCount > 0) {
                $this->dispatch('swal:success', [
                    'title' => 'Berhasil!',
                    'text' => 'Template kokurikuler untuk ' . $subdimensi . ' berhasil dihapus!',
                ]);
            } else {
                $this->dispatch('swal:info', [
                    'title' => 'Info',
                    'text' => 'Template kokurikuler tidak ditemukan.',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error deleting template kokurikuler: ' . $e->getMessage());

            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat menghapus data.',
            ]);
        }
    }

    public function render()
    {
        if (!$this->tahunAjaranSemesterId) {
            return view('livewire.admin.deskripsi-kokurikuler', [
                'templatesGrouped' => [],
                'paginator' => null,
            ]);
        }

        $searchLower = strtolower($this->search);

        $query = TemplateKokurikulerCapaian::query()
            ->where('tahun_ajaran_semester_id', $this->tahunAjaranSemesterId)
            ->when($this->search, function ($q) use ($searchLower) {
                // Pencarian mengabaikan kasus
                $q->whereRaw('LOWER(subdimensi) LIKE ?', ['%' . $searchLower . '%']);
            })
            // Hanya tampilkan predikat A, B, C
            ->whereIn('predikat', ['A', 'B', 'C'])
            ->select('subdimensi', 'tingkat')
            ->groupBy('subdimensi', 'tingkat')
            ->orderBy('subdimensi')
            ->orderBy('tingkat')
            ->paginate($this->perPage);

        $paginatedKeys = $query->map(function ($item) {
            return $item->subdimensi . '_' . $item->tingkat;
        })->toArray();

        // Ambil semua template yang termasuk dalam halaman ini
        $allTemplates = TemplateKokurikulerCapaian::where('tahun_ajaran_semester_id', $this->tahunAjaranSemesterId)
            ->whereIn('predikat', ['A', 'B', 'C']) // Filter di sini juga
            ->get();

        $templatesGrouped = [];
        foreach ($allTemplates as $template) {
            $key = $template->subdimensi . '_' . $template->tingkat;

            if (in_array($key, $paginatedKeys)) {
                if (!isset($templatesGrouped[$key])) {
                    $templatesGrouped[$key] = [];
                }

                $templatesGrouped[$key][] = [
                    'id' => $template->id,
                    'subdimensi' => $template->subdimensi,
                    'tingkat' => $template->tingkat,
                    'predikat' => $template->predikat,
                    'deskripsi' => $template->deskripsi,
                ];
            }
        }

        // Urutkan predikat di setiap group (A, B, C)
        foreach ($templatesGrouped as $key => $group) {
            usort($templatesGrouped[$key], function ($a, $b) {
                $order = ['A' => 1, 'B' => 2, 'C' => 3];
                return $order[$a['predikat']] <=> $order[$b['predikat']];
            });
        }

        return view('livewire.admin.deskripsi-kokurikuler', [
            'templatesGrouped' => $templatesGrouped,
            'paginator' => $query,
        ]);
    }
}
