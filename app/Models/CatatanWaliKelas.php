<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CatatanWaliKelas extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'catatan_wali_kelas';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'pelajar_id',
        'guru_id',
        'tahun_ajaran_semester_id',
        'catatan',
        'tanggal_input',
    ];

    protected $casts = [
        'tanggal_input' => 'datetime',
    ];

    // Relasi ke Pelajar
    public function pelajar()
    {
        return $this->belongsTo(Pelajar::class, 'pelajar_id');
    }

    // Relasi ke Guru (Wali Kelas)
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    // Relasi ke Tahun Ajaran Semester
    public function tahunAjaranSemester()
    {
        return $this->belongsTo(TahunAjaranSemester::class, 'tahun_ajaran_semester_id');
    }
}
