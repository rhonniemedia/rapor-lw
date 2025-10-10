<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = ['id'];

    // Relasi ke User
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_users', 'role_id', 'user_id')
            ->withTimestamps();
    }
}
