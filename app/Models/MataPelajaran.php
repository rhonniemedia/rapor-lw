<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MataPelajaran extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];

    public function kurikulumMataPelajarans()
    {
        return $this->hasMany(KurikulumMataPelajaran::class, 'mata_pelajaran_id');
    }
}
