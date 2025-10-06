<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pelajar extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'data_pelajars';
    protected $guarded = ['id'];
}
