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
    protected $fillable = [
        'id',
        'name',
        'slug',
        'email',
        'password',
        'nip',
        'telephone',
        'is_teacher',
        'status',
    ];

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
            'is_teacher' => 'boolean',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            // ✅ HANYA generate slug jika belum diisi
            if (empty($user->slug)) {
                $user->slug = static::generateSlug($user->name); // ✅ Ubah 'nama' jadi 'name'
            }
        });

        static::updating(function ($user) {
            // ✅ HANYA generate slug jika name berubah DAN slug kosong
            if ($user->isDirty('name') && empty($user->slug)) {
                $user->slug = static::generateSlug($user->name, $user->id); // ✅ Ubah 'nama' jadi 'name'
            }
        });
    }

    protected static function generateSlug($name, $ignoreId = null)
    {
        // Buat slug dasar, hanya huruf, angka, strip
        $slug = Str::slug($name);

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
