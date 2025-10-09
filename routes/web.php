<?php

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('contents.admin.dashboard', [
        'title' => 'Dashboard',
    ]);
});

Route::get('/rombongan-belajar', function () {
    return view('contents.admin.rombel', [
        'title' => 'Data Rombongan Belajar',
    ]);
});

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

Route::get('/akademik/mata-pelajaran', function () {
    return view('contents.admin.mata-pelajaran', [
        'title' => 'Data Mata Pelajaran',
    ]);
});
