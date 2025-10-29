<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect root ke login
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/auth/login', function () {
    return view('contents.auth.login', [
        'title' => 'Rapor Digital | SMK Negeri 1 Rejang Lebong',
    ]);
})->name('login')->middleware('guest');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('contents.admin.dashboard', [
            'title' => 'Dashboard',
        ]);
    })->name('dashboard');

    Route::get('/home/master-data', function () {
        return view('contents.admin.master-data', [
            'title' => 'Master Data',
        ]);
    });

    Route::get('/home/rombongan-belajar', function () {
        return view('contents.admin.rombel', [
            'title' => 'Data Rombongan Belajar',
        ]);
    });

    Route::get('/home/pendidik', function () {
        return view('contents.admin.pendidik', [
            'title' => 'Data Pendidik',
        ]);
    });

    Route::get('/home/rombongan-belajar/detail/{id}', function ($id) {
        return view('contents.admin.rombel-pelajar', [
            'id' => $id,
            'title' => 'Rombongan Belajar',
            'rombelId' => $id, // opsional, bisa digunakan di Blade atau Livewire
        ]);
    })->name('rombel.detail');


    Route::get('/akademik/jurusan', function () {
        return view('contents.admin.jurusan', [
            'title' => 'Data Kompetensi Keahlian',
        ]);
    });

    Route::get('/akademik/ekstrakurikuler', function () {
        return view('contents.admin.ekstrakurikuler', [
            'title' => 'Data Ekstrakurikuler',
        ]);
    });

    Route::get('/akademik/tahun-ajaran', function () {
        return view('contents.admin.akademik', [
            'title' => 'Data Akademik',
        ]);
    });

    Route::get('/akademik/mata-pelajaran', function () {
        return view('contents.admin.mata-pelajaran', [
            'title' => 'Data Mata Pelajaran',
        ]);
    });

    Route::get('/pembelajaran/nilai-akhir', function () {
        return view('contents.admin.nilai-akhir', [
            'title' => 'Data Nilai Akhir',
        ]);
    });

    Route::get('/pembelajaran/kehadiran', function () {
        return view('contents.admin.kehadiran', [
            'title' => 'Data Kehadiran',
        ]);
    });

    Route::get('/pembelajaran/catatan-wali-kelas', function () {
        return view('contents.admin.catatan-walas', [
            'title' => 'Catatan Wali Kelas',
        ]);
    });

    Route::get('/pembelajaran/ekstrakurikuler', function () {
        return view('contents.admin.ekstrakurikuler', [
            'title' => 'Data Ekstrakurikuler',
        ]);
    });

    // // Tidak perlu parameter rombelId lagi
    // Route::get('/input-nilai', App\Livewire\InputNilaiRombel::class)
    //     ->name('input-nilai.index');

    Route::post('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});
