<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TemplateEkstrakurikulerDeskripsi extends Model
{
    // Jika Anda menggunakan UUID
    use HasFactory, HasUuids;

    // ✅ Tambahkan semua kolom ini ke $fillable
    protected $guarded = ['id'];

    // Kolom-kolom yang tidak boleh diisi secara massal
    // protected $guarded = ['id']; // Alternatif jika $fillable terlalu panjang

    // ... (Definisi relasi, dll.)

    public function ekstrakurikuler()
    {
        // Relasi ke model Ekstrakurikuler menggunakan kolom 'ekstrakurikuler_id'
        // Ini sesuai dengan skema migrasi Anda
        return $this->belongsTo(Ekstrakurikuler::class, 'ekstrakurikuler_id');
    }

    // Asumsi relasi ke TahunAjaranSemester
    public function tahunAjaranSemester()
    {
        return $this->belongsTo(TahunAjaranSemester::class);
    }
}
