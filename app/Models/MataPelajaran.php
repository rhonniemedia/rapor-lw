<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MataPelajaran extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'mata_pelajarans';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nama',
        'kode',
        'is_mapel_agama',
        'agama_terkait',
        'agama_terkait_hash',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * Boot method untuk handle hashing otomatis
     */
    protected static function booted()
    {
        static::saving(function ($model) {
            // Hash agama_terkait jika ada nilainya
            if (!empty($model->agama_terkait)) {
                $model->agama_terkait_hash = hash('sha256', Str::lower($model->agama_terkait));
            } else {
                $model->agama_terkait_hash = null;
            }
        });
    }

    // --- RELATIONS ---

    public function kurikulumMataPelajarans()
    {
        return $this->hasMany(KurikulumMataPelajaran::class, 'mata_pelajaran_id');
    }

    public function jurusanMataPelajarans()
    {
        return $this->hasMany(JurusanMataPelajaran::class, 'mata_pelajaran_id');
    }

    public function rombelPengajars()
    {
        return $this->hasMany(RombelPengajar::class, 'mata_pelajaran_id');
    }

    public function kelompok()
    {
        // Coba salah satu dari opsi ini sesuai struktur database Anda:

        // OPSI 1: Jika foreign key bernama 'mata_pelajaran_kelompok_id'
        return $this->belongsTo(MataPelajaranKelompok::class, 'mata_pelajaran_kelompok_id');

        // OPSI 2: Jika foreign key bernama 'kelompok_id'
        // return $this->belongsTo(MataPelajaranKelompok::class, 'kelompok_id');
    }

    // Relasi lainnya yang mungkin sudah ada
    public function nilais()
    {
        return $this->hasMany(Nilai::class);
    }
}
