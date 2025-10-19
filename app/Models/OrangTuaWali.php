<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrangTuaWali extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'orang_tua_walis';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'pelajar_id',
        'nama',
        'hubungan',
        'status',
        'pekerjaan',
        'telepon',
        'alamat',
    ];
}
