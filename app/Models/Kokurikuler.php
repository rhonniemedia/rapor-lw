<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Kokurikuler extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'pelajar_id',
        'guru_id',
        'tahun_ajaran_semester_id',
        'predikat',
        'capaian',
        'tanggal_input',
    ];

    // =======================
    // RELASI MODEL
    // =======================

    // Pelajar (satu pelajar punya banyak kokurikuler)
    public function pelajar()
    {
        return $this->belongsTo(Pelajar::class);
    }

    // Guru (user)
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    // Tahun ajaran semester
    public function tahunAjaranSemester()
    {
        return $this->belongsTo(TahunAjaranSemester::class);
    }
}
