<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Pengaturan extends Model
{
    use HasUuids;

    protected $fillable = [
        'tahun_ajaran_semester_id',
        'kepala_sekolah_id',
        'tanggal_rapor',
        'konfigurasi_tampilan',
    ];

    protected $casts = [
        'tanggal_rapor' => 'date',
        'konfigurasi_tampilan' => 'array',
    ];

    // ========================================
    // RELASI
    // ========================================

    public function tahunAjaranSemester()
    {
        return $this->belongsTo(TahunAjaranSemester::class);
    }

    public function kepalaSekolah()
    {
        return $this->belongsTo(User::class, 'kepala_sekolah_id');
    }
}
