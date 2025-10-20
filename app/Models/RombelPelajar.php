<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RombelPelajar extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'rombel_pelajars';
    protected $keyType = 'string';
    protected $fillable = ['id', 'pelajar_id', 'rombel_id'];

    // Relasi ke Rombel
    public function rombel()
    {
        return $this->belongsTo(Rombel::class, 'rombel_id');
    }

    public function pelajar()
    {
        return $this->belongsTo(Pelajar::class, 'pelajar_id');
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
}
