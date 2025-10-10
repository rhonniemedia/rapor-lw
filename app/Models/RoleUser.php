<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoleUser extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'role_users';
    public $incrementing = false;   // karena pakai UUID
    protected $keyType = 'string';

    protected $guarded = ['id'];

    // Relasi ke Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // Relasi ke User (opsional, untuk mempermudah)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
