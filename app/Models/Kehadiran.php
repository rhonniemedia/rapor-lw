<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Kehadiran extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kehadirans';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'pelajar_id',
        'rombel_id',  // 👈 Ganti dari rombel_pengajar_id
        'tahun_ajaran_semester_id',
        'jumlah_sakit',
        'jumlah_izin',
        'jumlah_tanpa_keterangan',
    ];

    protected $casts = [
        'jumlah_sakit' => 'integer',
        'jumlah_izin' => 'integer',
        'jumlah_tanpa_keterangan' => 'integer',
    ];

    // Relasi ke Pelajar
    public function pelajar()
    {
        return $this->belongsTo(Pelajar::class, 'pelajar_id');
    }

    // Relasi ke Rombel
    public function rombel()
    {
        return $this->belongsTo(Rombel::class, 'rombel_id');
    }

    // Relasi ke Tahun Ajaran Semester
    public function tahunAjaranSemester()
    {
        return $this->belongsTo(TahunAjaranSemester::class, 'tahun_ajaran_semester_id');
    }
}
