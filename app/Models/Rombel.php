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

    public function pelajars()
    {
        return $this->belongsToMany(
            Pelajar::class,
            'rombel_pelajars',  // nama tabel pivot
            'rombel_id',       // foreign key di tabel pivot untuk rombel
            'pelajar_id'       // foreign key di tabel pivot untuk pelajar
        )->withTimestamps(); // opsional, jika tabel pivot punya created_at & updated_at
    }

    public function rombelPelajars()
    {
        return $this->hasMany(RombelPelajar::class, 'rombel_id');
    }

    // Total semua pelajar
    public function getTotalPelajarAttribute()
    {
        return $this->rombelPelajars()->count();
    }

    // Total laki-laki
    public function getTotalLakiAttribute()
    {
        return $this->rombelPelajars()
            ->whereHas('pelajar', function ($q) {
                $q->where('jenis_kelamin', 'L');
            })->count();
    }

    // Total perempuan
    public function getTotalPerempuanAttribute()
    {
        return $this->rombelPelajars()
            ->whereHas('pelajar', function ($q) {
                $q->where('jenis_kelamin', 'P');
            })->count();
    }
}
