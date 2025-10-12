<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JurusanMataPelajaran extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'jurusan_mata_pelajarans';

    protected $guarded = ['id'];

    // Relasi ke tabel jurusans
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    // Relasi ke tabel mata_pelajarans
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    // Relasi opsional ke tabel kurikulums
    public function kurikulum()
    {
        return $this->belongsTo(Kurikulum::class, 'kurikulum_id');
    }
}
