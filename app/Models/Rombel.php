<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rombel extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'rombels';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'tahun_ajaran_id',
        'jurusan_id',
        'tahun_ajaran_kurikulum_id',
        'wali_kelas_slug',
        'tingkat',
        'nama',
    ];

    /**
     * Relasi ke Tahun Ajaran
     */
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id', 'id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id', 'id');
    }

    /**
     * Relasi ke Tahun Ajaran Kurikulum
     */
    public function tahunAjaranKurikulum()
    {
        return $this->belongsTo(TahunAjaranKurikulum::class, 'tahun_ajaran_kurikulum_id', 'id');
    }

    /**
     * Relasi ke Wali Kelas (users) via slug
     */
    public function waliKelas()
    {
        return $this->belongsTo(User::class, 'wali_kelas_slug', 'slug');
    }
}
