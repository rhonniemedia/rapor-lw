<?php

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('contents.admin.dashboard');
});

Route::get('/rombongan-belajar', function () {
    return view('contents.admin.rombel');
});

Route::get('/sekolah/jurusan', function () {
    return view('contents.admin.jurusan');
});
