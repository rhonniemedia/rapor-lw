<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TahunAjaranSemester extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];

    // Scope untuk filter status aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // Relasi ke TahunAjaran
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }

    // Relasi ke Semester
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function kokurikuler()
    {
        return $this->hasMany(Kokurikuler::class);
    }

    // Accessor untuk memudahkan tampilan di dropdown
    public function getNamaLengkapAttribute()
    {
        return $this->tahunAjaran->nama . ' - ' . $this->semester->nama;
    }
}
