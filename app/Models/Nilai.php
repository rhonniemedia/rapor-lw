<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Nilai extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'nilais';
    protected $primaryKey = 'id';
    public $incrementing = false; // karena pakai UUID
    protected $keyType = 'string';

    protected $fillable = [
        'pelajar_id',
        'mata_pelajaran_id',
        'rombel_pengajar_id',
        'tahun_ajaran_semester_id',
        'guru_id',
        'nilai_angka',
        'predikat',
        'capaian_kompetensi',
        'created_by',
        'updated_by',
    ];

    // 🔹 Relasi ke Pelajar
    public function pelajar()
    {
        return $this->belongsTo(Pelajar::class);
    }

    // 🔹 Relasi ke Mata Pelajaran
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    // 🔹 Relasi ke Rombel Pengajar
    public function rombelPengajar()
    {
        return $this->belongsTo(RombelPengajar::class);
    }

    // 🔹 Relasi ke Tahun Ajaran Semester
    public function tahunAjaranSemester()
    {
        return $this->belongsTo(TahunAjaranSemester::class);
    }

    // 🔹 Relasi ke Guru (User)
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}
