<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class DataSekolah extends Model
{
    use HasFactory, HasUuids;

    // Nama tabel yang terkait dengan model.
    protected $table = 'data_sekolahs';

    // Menonaktifkan auto-increment karena kita menggunakan UUID.
    public $incrementing = false;

    // Mendefinisikan tipe primary key sebagai string (UUID).
    protected $keyType = 'string';

    // Kolom yang dapat diisi secara massal (mass assignable).
    protected $guarded = ['id'];
}
