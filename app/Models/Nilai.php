<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Nilai extends Model
{
    // use HasFactory, SoftDeletes, HasUuids;
    use HasFactory, HasUuids;

    protected $table = 'nilais';

    public $incrementing = false;
    protected $keyType = 'string';

    // ✅ PERBAIKAN: Tambahkan created_by dan updated_by ke fillable
    protected $guarded = ['id'];

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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
