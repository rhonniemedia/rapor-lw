<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jurusan extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'jurusans';
    protected $keyType = 'string';
    protected $fillable = ['id', 'nama', 'alias', 'kode', 'status'];
}
