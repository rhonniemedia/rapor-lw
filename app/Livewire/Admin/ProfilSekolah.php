<?php

namespace App\Livewire\Admin;

use App\Models\DataSekolah;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
// TAMBAH: Import ini untuk memastikan reset objek file
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ProfilSekolah extends Component
{
    use WithFileUploads;

    // Properties untuk data sekolah
    public $sekolah;
    public $sekolahId;

    // Properties untuk form data sekolah
    public $nama_sekolah;
    public $npsn;
    public $nis;
    public $nss;
    public $nds;
    public $status_sekolah;
    public $jenjang_pendidikan;
    public $status_akreditasi;
    public $tahun_akreditasi;
    public $sk_pendirian_sekolah;
    public $tanggal_sk_pendirian;
    public $sk_izin_operasional;
    public $tanggal_sk_izin_operasional;

    // Properties untuk form kontak
    public $alamat;
    public $kode_pos;
    public $kelurahan;
    public $kecamatan;
    public $kota_kabupaten;
    public $provinsi;
    public $telepon;
    public $website;
    public $email;

    // Properties untuk upload logo
    // Pastikan ini tetap public untuk Livewire
    public $logo_sekolah;
    public $logo_pemda;

    // Modal states - Dipertahankan tapi kontrol utama ada di dispatch
    public $showEditDataModal = false;
    public $showEditContactModal = false;

    // Hapus originalDataSekolah/Kontak agar fokus ke perbaikan modal/upload.
    // Logika deteksi perubahan dipindahkan ke method updateData/Contact (lebih ringkas).

    protected $listeners = [
        'refreshComponent' => '$refresh'
    ];

    public function mount()
    {
        $this->loadSekolahData();
    }

    // Fungsi ini otomatis jalan setelah $logo_sekolah terisi/berubah
    public function updatedLogoSekolah()
    {
        $this->uploadLogoSekolah();
    }

    // Fungsi ini otomatis jalan setelah $logo_pemda terisi/berubah
    public function updatedLogoPemda()
    {
        $this->uploadLogoPemda();
    }

    public function loadSekolahData()
    {
        $this->sekolah = DataSekolah::first();

        if ($this->sekolah) {
            $this->sekolahId = $this->sekolah->id;
            $this->fillFormData();
        }
    }

    protected function fillFormData()
    {
        if (!$this->sekolah) return;

        // Fill data sekolah
        $this->nama_sekolah = $this->sekolah->nama_sekolah;
        $this->npsn = $this->sekolah->npsn;
        $this->nis = $this->sekolah->nis;
        $this->nss = $this->sekolah->nss;
        $this->nds = $this->sekolah->nds;
        $this->status_sekolah = $this->sekolah->status_sekolah;
        $this->jenjang_pendidikan = $this->sekolah->jenjang_pendidikan;
        $this->status_akreditasi = $this->sekolah->status_akreditasi;
        $this->tahun_akreditasi = $this->sekolah->tahun_akreditasi;
        $this->sk_pendirian_sekolah = $this->sekolah->sk_pendirian_sekolah;
        $this->tanggal_sk_pendirian = $this->sekolah->tanggal_sk_pendirian;
        $this->sk_izin_operasional = $this->sekolah->sk_izin_operasional;
        $this->tanggal_sk_izin_operasional = $this->sekolah->tanggal_sk_izin_operasional;

        // Fill data kontak
        $this->alamat = $this->sekolah->alamat;
        $this->kode_pos = $this->sekolah->kode_pos;
        $this->kelurahan = $this->sekolah->kelurahan;
        $this->kecamatan = $this->sekolah->kecamatan;
        $this->kota_kabupaten = $this->sekolah->kota_kabupaten;
        $this->provinsi = $this->sekolah->provinsi;
        $this->telepon = $this->sekolah->telepon;
        $this->website = $this->sekolah->website;
        $this->email = $this->sekolah->email;
    }

    // MEMPERBAIKI OPEN MODAL DENGAN DISPATCH
    public function openEditDataModal()
    {
        $this->fillFormData();
        $this->showEditDataModal = true;
        // Dispatch untuk memicu JavaScript
        $this->dispatch('modal:show', id: 'editDataModal');
    }

    // MEMPERBAIKI OPEN MODAL DENGAN DISPATCH
    public function openEditContactModal()
    {
        $this->fillFormData();
        $this->showEditContactModal = true;
        // Dispatch untuk memicu JavaScript
        $this->dispatch('modal:show', id: 'editContactModal');
    }

    // FUNGSI BARU UNTUK TUTUP MODAL
    public function closeModal($type)
    {
        if ($type === 'data') {
            $this->showEditDataModal = false;
            $this->dispatch('modal:hide', id: 'editDataModal');
        } elseif ($type === 'contact') {
            $this->showEditContactModal = false;
            $this->dispatch('modal:hide', id: 'editContactModal');
        }
    }

    public function updateData()
    {
        // ... (Logika deteksi perubahan dihilangkan sementara agar fokus ke perbaikan inti)

        $this->validate([
            'nama_sekolah' => 'required|string|max:100',
            'npsn' => 'nullable|string|max:15|unique:data_sekolahs,npsn,' . $this->sekolahId,
            'nis' => 'nullable|string|max:20',
            'nss' => 'nullable|string|max:20',
            'nds' => 'nullable|string|max:20',
            'status_sekolah' => 'nullable|string|max:20',
            'jenjang_pendidikan' => 'nullable|string|max:20',
            'status_akreditasi' => 'nullable|string|max:5',
            'tahun_akreditasi' => 'nullable|integer|digits:4',
            'sk_pendirian_sekolah' => 'nullable|string|max:100',
            'tanggal_sk_pendirian' => 'nullable|date',
            'sk_izin_operasional' => 'nullable|string|max:100',
            'tanggal_sk_izin_operasional' => 'nullable|date',
        ], [
            'nama_sekolah.required' => 'Nama sekolah wajib diisi',
            'nama_sekolah.max' => 'Nama sekolah maksimal 100 karakter',
            'npsn.max' => 'NPSN maksimal 15 karakter',
            'npsn.unique' => 'NPSN sudah terdaftar',
            'nis.max' => 'NIS maksimal 20 karakter',
            'nss.max' => 'NSS maksimal 20 karakter',
            'nds.max' => 'NDS maksimal 20 karakter',
            'tahun_akreditasi.digits' => 'Tahun akreditasi harus 4 digit',
        ]);

        try {
            $this->sekolah->update([
                'nama_sekolah' => $this->nama_sekolah,
                'npsn' => $this->npsn,
                'nis' => $this->nis,
                'nss' => $this->nss,
                'nds' => $this->nds,
                'status_sekolah' => $this->status_sekolah,
                'jenjang_pendidikan' => $this->jenjang_pendidikan,
                'status_akreditasi' => $this->status_akreditasi,
                'tahun_akreditasi' => $this->tahun_akreditasi,
                'sk_pendirian_sekolah' => $this->sk_pendirian_sekolah,
                'tanggal_sk_pendirian' => $this->tanggal_sk_pendirian,
                'sk_izin_operasional' => $this->sk_izin_operasional,
                'tanggal_sk_izin_operasional' => $this->tanggal_sk_izin_operasional,
            ]);

            $this->showEditDataModal = false;
            $this->dispatch('modal:hide', id: 'editDataModal'); // Tutup modal
            $this->loadSekolahData();

            $this->dispatch(
                'swal:success',
                title: 'Berhasil!',
                text: 'Data sekolah berhasil diperbarui'
            );
        } catch (\Exception $e) {
            $this->dispatch(
                'swal:error',
                title: 'Oops...',
                text: 'Terjadi kesalahan: ' . $e->getMessage()
            );
        }
    }

    public function updateContact()
    {
        // ... (Logika deteksi perubahan dihilangkan sementara agar fokus ke perbaikan inti)

        $this->validate([
            'alamat' => 'nullable|string|max:255',
            'kode_pos' => 'nullable|string|max:10',
            'kelurahan' => 'nullable|string|max:50',
            'kecamatan' => 'nullable|string|max:50',
            'kota_kabupaten' => 'nullable|string|max:50',
            'provinsi' => 'nullable|string|max:50',
            'telepon' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100',
        ], [
            'alamat.max' => 'Alamat maksimal 255 karakter',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 100 karakter',
            'website.max' => 'Website maksimal 100 karakter',
            'telepon.max' => 'Telepon maksimal 20 karakter',
        ]);

        try {
            $this->sekolah->update([
                'alamat' => $this->alamat,
                'kode_pos' => $this->kode_pos,
                'kelurahan' => $this->kelurahan,
                'kecamatan' => $this->kecamatan,
                'kota_kabupaten' => $this->kota_kabupaten,
                'provinsi' => $this->provinsi,
                'telepon' => $this->telepon,
                'website' => $this->website,
                'email' => $this->email,
            ]);

            $this->showEditContactModal = false;
            $this->dispatch('modal:hide', id: 'editContactModal'); // Tutup modal
            $this->loadSekolahData();

            $this->dispatch(
                'swal:success',
                title: 'Berhasil!',
                text: 'Informasi kontak berhasil diperbarui'
            );
        } catch (\Exception $e) {
            $this->dispatch(
                'swal:error',
                title: 'Oops...',
                text: 'Terjadi kesalahan: ' . $e->getMessage()
            );
        }
    }

    // PERBAIKAN FUNGSI UPLOAD LOGO SEKOLAH
    public function uploadLogoSekolah()
    {
        // Validasi dipastikan aman karena file sudah diterima via hook updated
        $this->validate([
            'logo_sekolah' => 'required|image|mimes:jpeg,jpg,png,svg|max:2048',
        ], [
            'logo_sekolah.required' => 'File logo wajib dipilih',
            'logo_sekolah.image' => 'File harus berupa gambar',
            'logo_sekolah.mimes' => 'Format file harus JPG, JPEG, PNG, atau SVG',
            'logo_sekolah.max' => 'Ukuran file maksimal 2MB',
        ]);

        try {
            $this->sekolah->refresh();

            if ($this->sekolah->logo_sekolah_path && Storage::disk('public')->exists($this->sekolah->logo_sekolah_path)) {
                Storage::disk('public')->delete($this->sekolah->logo_sekolah_path);
            }

            $path = $this->logo_sekolah->store('logos/sekolah', 'public');
            $this->sekolah->update(['logo_sekolah_path' => $path]);

            if ($this->logo_sekolah instanceof TemporaryUploadedFile) {
                $this->logo_sekolah->delete();
            }

            $this->logo_sekolah = null;
            $this->loadSekolahData();

            // Dispatch reset untuk membersihkan input file HTML
            $this->dispatch('file:reset');

            $this->dispatch(
                'swal:success',
                title: 'Berhasil!',
                text: 'Logo sekolah berhasil diupload'
            );
        } catch (\Exception $e) {
            $this->dispatch(
                'swal:error',
                title: 'Oops...',
                text: 'Terjadi kesalahan: ' . $e->getMessage()
            );
        }
    }

    // PERBAIKAN FUNGSI UPLOAD LOGO PEMDA
    public function uploadLogoPemda()
    {
        // Validasi dipastikan aman karena file sudah diterima via hook updated
        $this->validate([
            'logo_pemda' => 'required|image|mimes:jpeg,jpg,png,svg|max:2048',
        ], [
            'logo_pemda.required' => 'File logo wajib dipilih',
            'logo_pemda.image' => 'File harus berupa gambar',
            'logo_pemda.mimes' => 'Format file harus JPG, JPEG, PNG, atau SVG',
            'logo_pemda.max' => 'Ukuran file maksimal 2MB',
        ]);

        try {
            $this->sekolah->refresh();

            if ($this->sekolah->logo_pemda_path && Storage::disk('public')->exists($this->sekolah->logo_pemda_path)) {
                Storage::disk('public')->delete($this->sekolah->logo_pemda_path);
            }

            $path = $this->logo_pemda->store('logos/pemda', 'public');
            $this->sekolah->update(['logo_pemda_path' => $path]);

            if ($this->logo_pemda instanceof TemporaryUploadedFile) {
                $this->logo_pemda->delete();
            }

            $this->logo_pemda = null;
            $this->loadSekolahData();

            // Dispatch reset untuk membersihkan input file HTML
            $this->dispatch('file:reset');

            $this->dispatch(
                'swal:success',
                title: 'Berhasil!',
                text: 'Logo pemerintah daerah berhasil diupload'
            );
        } catch (\Exception $e) {
            $this->dispatch(
                'swal:error',
                title: 'Oops...',
                text: 'Terjadi kesalahan: ' . $e->getMessage()
            );
        }
    }

    // ... (Fungsi deleteLogoSekolah, deleteLogoPemda, confirmDeleteLogo, render tetap sama)

    public function deleteLogoSekolah()
    {
        try {
            $this->sekolah->refresh();

            if ($this->sekolah->logo_sekolah_path && Storage::disk('public')->exists($this->sekolah->logo_sekolah_path)) {
                Storage::disk('public')->delete($this->sekolah->logo_sekolah_path);
            }

            $this->sekolah->update(['logo_sekolah_path' => null]);
            $this->loadSekolahData();
            $this->dispatch('file:reset');

            $this->dispatch(
                'swal:success',
                title: 'Berhasil!',
                text: 'Logo sekolah berhasil dihapus'
            );
        } catch (\Exception $e) {
            $this->dispatch(
                'swal:error',
                title: 'Oops...',
                text: 'Terjadi kesalahan: ' . $e->getMessage()
            );
        }
    }

    public function deleteLogoPemda()
    {
        try {
            $this->sekolah->refresh();

            if ($this->sekolah->logo_pemda_path && Storage::disk('public')->exists($this->sekolah->logo_pemda_path)) {
                Storage::disk('public')->delete($this->sekolah->logo_pemda_path);
            }

            $this->sekolah->update(['logo_pemda_path' => null]);
            $this->loadSekolahData();
            $this->dispatch('file:reset');

            $this->dispatch(
                'swal:success',
                title: 'Berhasil!',
                text: 'Logo pemerintah daerah berhasil dihapus'
            );
        } catch (\Exception $e) {
            $this->dispatch(
                'swal:error',
                title: 'Oops...',
                text: 'Terjadi kesalahan: ' . $e->getMessage()
            );
        }
    }

    public function confirmDeleteLogo($type)
    {
        $this->dispatch(
            'swal:confirm-delete',
            type: $type,
            title: 'Hapus Logo?',
            text: 'Apakah Anda yakin ingin menghapus logo ' . ($type === 'sekolah' ? 'sekolah' : 'pemerintah daerah') . '?'
        );
    }

    public function render()
    {
        return view('livewire.admin.profil-sekolah');
    }
}
