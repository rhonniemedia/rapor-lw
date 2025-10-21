<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RombelPengajar extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'rombel_pengajars';

    protected $fillable = [
        'rombel_id',
        'mata_pelajaran_id',
        'guru_id',
        'jam_pelajaran',
    ];

    protected $casts = [
        'jam_pelajaran' => 'integer',
    ];

    // Relasi ke Rombel
    public function rombel()
    {
        return $this->belongsTo(Rombel::class, 'rombel_id');
    }

    // Relasi ke Mata Pelajaran
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    // Relasi ke User (Guru)
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }
}
