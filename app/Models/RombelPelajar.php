<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RombelPelajar extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'rombel_pelajars';
    protected $guarded = ['id'];
}
