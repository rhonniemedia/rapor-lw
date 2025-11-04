<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TemplateNilaiCapaian extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'tahun_ajaran_semester_id',
        'mata_pelajaran_id',
        'tingkat',
        'rentang_min',
        'rentang_max',
        'predikat',
        'deskripsi',
        'aktif',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function tahunAjaranSemester()
    {
        return $this->belongsTo(TahunAjaranSemester::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
