<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class EkskulPelajar extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ekskul_pelajars';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tahun_ajaran_semester_id',
        'ekstrakurikuler_id',
        'pelajar_id',
        'nilai',
        'deskripsi',

        // 🚨 PERBAIKAN: Tambahkan kolom audit untuk mass assignment
        'created_by',
        'updated_by',
    ];

    // Default nilai untuk kolom yang mungkin tidak selalu ada di input
    protected $attributes = [
        'deskripsi' => '',
    ];

    // Relasi ke Ekstrakurikuler
    public function ekstrakurikuler()
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'ekstrakurikuler_id');
    }

    // Relasi ke Pelajar
    public function pelajar()
    {
        return $this->belongsTo(Pelajar::class, 'pelajar_id');
    }

    // Relasi ke User (opsional, jika Anda perlu melacak siapa yang membuat/memperbarui)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
