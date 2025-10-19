<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TahunAjaran extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $table = 'tahun_ajarans';
    protected $keyType = 'string';
    protected $fillable = ['id', 'nama', 'tgl_mulai', 'tgl_selesai', 'status'];

    public function tahunAjaranKurikulum()
    {
        return $this->hasMany(TahunAjaranKurikulum::class);
    }

    public function tahunAjaranSemesters()
    {
        return $this->hasMany(TahunAjaranSemester::class, 'tahun_ajaran_id');
    }
}
