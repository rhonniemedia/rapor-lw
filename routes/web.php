<?php

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('contents.admin.dashboard', [
        'title' => 'Dashboard',
    ]);
});

Route::get('/home/rombongan-belajar', function () {
    return view('contents.admin.rombel', [
        'title' => 'Data Rombongan Belajar',
    ]);
});

Route::get('/home/rombongan-belajar/detail/{id}', function ($id) {
    return view('contents.admin.rombel-pelajar', [
        'title' => 'Detil Rombongan Belajar',
        'rombelId' => $id, // optional, bisa digunakan di view
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
