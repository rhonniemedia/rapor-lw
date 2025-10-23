<?php

use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard/admin', fn() => view('dashboard.admin'))->name('dashboard.admin');
    Route::get('/dashboard/guru', fn() => view('dashboard.guru'))->name('dashboard.guru');
    Route::get('/dashboard/siswa', fn() => view('dashboard.siswa'))->name('dashboard.siswa');
    Route::get('/dashboard', fn() => view('dashboard.default'))->name('dashboard.default');

    Route::post('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});

Route::get('/dashboard', function () {
    return view('contents.admin.dashboard', [
        'title' => 'Dashboard',
    ]);
});

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


Route::get('/sekolah/jurusan', function () {
    return view('contents.admin.jurusan', [
        'title' => 'Data Kompetensi Keahlian',
    ]);
});

Route::get('/sekolah/ekstrakurikuler', function () {
    return view('contents.admin.ekstrakurikuler', [
        'title' => 'Data Ekstrakurikuler',
    ]);
});

Route::get('/akademik/data-akademik', function () {
    return view('contents.admin.akademik', [
        'title' => 'Data Akademik',
    ]);
});

Route::get('/akademik/mata-pelajaran', function () {
    return view('contents.admin.mata-pelajaran', [
        'title' => 'Data Mata Pelajaran',
    ]);
});
