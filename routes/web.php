<?php

use Barryvdh\DomPDF\Facade\Pdf;
use App\Livewire\Wali\KelasBinaan;
use App\Livewire\Admin\GeneratePdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;
use App\Livewire\Template\PreviewRapor;
use App\Http\Controllers\PdfRaporWaliController;

// Redirect root ke login
Route::get('/', function () {
    if (Auth::check()) {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Arahkan sesuai role
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('walikelas')) {
            return redirect()->route('walikelas.dashboard');
        } elseif ($user->hasRole('guru')) {
            return redirect()->route('guru.dashboard');
        } else {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akses tidak dikenali.');
        }
    }

    return redirect()->route('login');
});

Route::get('/auth/login', function () {
    return view('contents.auth.login', [
        'title' => 'Rapor Digital | SMK Negeri 1 Rejang Lebong',
    ]);
})->name('login')->middleware('guest');

// Logout
Route::post('/auth/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('login');
})->middleware('auth')->name('logout');


// Admin Route
Route::middleware(['auth', 'role:superadmin|admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('contents.admin.dashboard', [
            'title' => 'Dashboard',
        ]);
    })->name('dashboard');

    // Master Data
    Route::prefix('master')->group(function () {
        Route::get('/sync', fn() => view('contents.admin.master-data', ['title' => 'Sinkronisasi']));
        Route::get('/school', fn() => view('contents.admin.sekolah', ['title' => 'Data Sekolah']));
        Route::get('/profile', fn() => view('contents.admin.profil', ['title' => 'Profil Sekolah']));
        Route::get('/curriculum', fn() => view('contents.admin.kurikulum', ['title' => 'Kurikulum']));
    });

    // Academic Data
    Route::prefix('academic')->group(function () {
        Route::get('/year', fn() => view('contents.admin.akademik', ['title' => 'Tahun Ajaran']));
        Route::get('/semester', fn() => view('contents.admin.semester', ['title' => 'Semester']));
        Route::get('/department', fn() => view('contents.admin.jurusan', ['title' => 'Jurusan']));
        Route::get('/subject', fn() => view('contents.admin.mata-pelajaran', ['title' => 'Mata Pelajaran']));
        Route::get('/manage-extracurricular', fn() => view('contents.admin.ekstrakurikuler', ['title' => 'Ekstrakurikuler']))
            ->name('academic.extracurricular');
        Route::get('/manage-description', fn() => view('contents.admin.deskripsi-capaian', ['title' => 'Deskripsi Capaian']));
    });

    //  Class Management
    Route::prefix('class')->group(function () {
        Route::get('/list', fn() => view('contents.admin.rombel', ['title' => 'Daftar Rombel']))->name('rombel.list');
        Route::get('/teachers', fn() => view('contents.admin.pendidik', ['title' => 'Daftar Pendidik']));
        Route::get('/detail/{id}', function ($id) {
            return view('contents.admin.rombel-pelajar', [
                'title' => 'Rombongan Belajar',
                'rombelId' => $id,
            ]);
        })->name('class.detail');
    });

    // Data Entry
    Route::prefix('entry')->group(function () {
        Route::get('/grades', fn() => view('contents.admin.nilai-akhir', ['title' => 'Nilai Akhir']));
        Route::get('/cocurricular', fn() => view('contents.admin.kokurikuler', ['title' => 'Kokurikuler']));
        Route::get('/attendance', fn() => view('contents.admin.kehadiran', ['title' => 'Absensi']));
        Route::get('/class-notes', fn() => view('contents.admin.catatan-walas', ['title' => 'Catatan Wali Kelas']));
        Route::get('/data-extracurricular', fn() => view('contents.admin.ekstrakurikuler-input', ['title' => 'Ekstrakurikuler']))->name('entry.extracurricular');
    });

    // Report Finalization
    Route::prefix('finalization')->group(function () {
        Route::get('/settings', fn() => view('contents.admin.pengaturan', ['title' => 'Pengaturan Rapor']));
        Route::get('/preview', fn() => view('contents.admin.preview', ['title' => 'Preview Rapor']));
        Route::get('/generate', fn() => view('contents.admin.generate', ['title' => 'Generate PDF']));
        Route::get('/archive', fn() => view('contents.admin.arsip', ['title' => 'Arsip Rapor']));
    });

    // Users
    Route::prefix('users')->group(function () {
        Route::get('/list', fn() => view('contents.admin.pengguna', ['title' => 'Daftar Pengguna']));
    });
});

// Wali Kelas Routes
Route::middleware(['auth', 'role:walikelas'])->prefix('homeroom')->name('walikelas.')->group(function () {
    Route::get('/dashboard', function () {
        return view('contents.wali.dashboard', ['title' => 'Dashboard Wali Kelas']);
    })->name('dashboard');

    Route::get('/students', function () {
        return view('contents.wali.rombel-pelajar', ['title' => 'Data Pelajar']);
    })->name('students');

    // Data Entry
    Route::prefix('entry')->group(function () {
        Route::get('/grades', fn() => view('contents.wali.entri-nilai', ['title' => 'Nilai Akhir']))->name('entri.nilai');
        Route::get('/cocurricular', fn() => view('contents.wali.entri-kokurikuler', ['title' => 'Data Kokurikuler']))->name('entri.kokurikuler');
        Route::get('/attendance', fn() => view('contents.wali.entri-kehadiran', ['title' => 'Data Absensi']))->name('entri.kehadiran');
        Route::get('/class-notes', fn() => view('contents.wali.entri-catatan', ['title' => 'Catatan Wali Kelas']))->name('entri.catatan');
        Route::get('/extracurricular', fn() => view('contents.wali.entri-ekstrakurikuler', ['title' => 'Data Ekstrakurikuler']))->name('entri.ekstrakurikuler');
    });

    Route::get('/teaching', function () {
        return view('contents.wali.rombel-ajar', ['title' => 'Rombongan Belajar']);
    })->name('kelasajar');

    Route::get('/teaching/detail/{rombelId}/{mataPelajaranId}', function ($rombelId, $mataPelajaranId) {
        return view('contents.wali.rombel-ajar-nilai', [
            'title' => 'Entri Data Nilai',
            'rombelId' => $rombelId,
            'mataPelajaranId' => $mataPelajaranId, // tambahkan ini
        ]);
    })->name('class.detail');

    // Finalization
    Route::prefix('finalization')->group(function () {
        Route::get('/preview', fn() => view('contents.wali.preview-rapor', ['title' => 'Preview Rapor']));
        Route::get('/pdf/generate', [PdfRaporWaliController::class, 'generatePdf'])->name('pdf.generate');
        Route::get('/ledger', fn() => view('contents.wali.preview-ledger', ['title' => 'Preview Leger']));
    });

    Route::get('/user', function () {
        return view('contents.wali.profil-pengguna', ['title' => 'Profil Pengguna']);
    })->name('user.profile');

    // Route wali kelas lainnya...
});

// Guru Routes
Route::middleware(['auth', 'role:guru'])->prefix('teacher')->name('guru.')->group(function () {
    Route::get('/dashboard', function () {
        return view('contents.guru.dashboard', ['title' => 'Dashboard Guru']);
    })->name('dashboard');

    Route::get('/class', function () {
        return view('contents.guru.rombel-ajar', ['title' => 'Rombongan Belajar']);
    })->name('kelasajar');

    Route::get('/class/detail/{rombelId}/{mataPelajaranId}', function ($rombelId, $mataPelajaranId) {
        return view('contents.guru.rombel-ajar-nilai', [
            'title' => 'Entri Data Nilai',
            'rombelId' => $rombelId,
            'mataPelajaranId' => $mataPelajaranId, // tambahkan ini
        ]);
    })->name('class.detail');

    // Route guru lainnya...
});

// Route untuk halaman preview
Route::get('/admin/rapor/preview', function () {
    return view('contents.admin.preview', [
        'title' => 'Preview Rapor'
    ]);
})->name('rapor.preview');

// Route untuk generate PDF
Route::get('/pdf/generate', [PdfController::class, 'generatePdf'])->name('pdf.generate');
