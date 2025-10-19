<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pelajar extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pelajars';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'nama_lengkap',
        'nomor_induk',
        'nisn',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_dalam_keluarga',
        'anak_ke',
        'alamat',
        'telepon',
        'sekolah_asal',
        'diterima_di_kelas',
        'pada_tanggal',
    ];

    public function orangTuaWalis()
    {
        return $this->hasMany(OrangTuaWali::class);
    }
}
