<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasUuids, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = ['id'];

    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            $user->slug = static::generateSlug($user->nama);
        });

        static::updating(function ($user) {
            if ($user->isDirty('nama')) {
                $user->slug = static::generateSlug($user->nama, $user->id);
            }
        });
    }

    protected static function generateSlug($nama, $ignoreId = null)
    {
        // Buat slug dasar, hanya huruf, angka, strip
        $slug = Str::slug($nama);

        $originalSlug = $slug;
        $counter = 2;

        // Cek apakah slug sudah ada di database
        while (
            static::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    // Relasi ke Role
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users', 'user_id', 'role_id')
            ->withTimestamps();
    }

    // Cek apakah user punya role tertentu
    public function hasRole($role)
    {
        return $this->roles()->where('nama_role', $role)->exists();
    }

    public function assignRole($roleId)
    {
        return RoleUser::create([
            'user_id' => $this->id,
            'role_id' => $roleId,
        ]);
    }
}
