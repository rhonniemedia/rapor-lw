<?php

namespace App\Models;

use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasUuids;

    protected $table = 'users';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'email_hash',
        'password',
        'nip',
        'nip_hash',
        'telephone',
        'is_teacher',
        'is_guru_agama',
        'spesialisasi_agama',
        'spesialisasi_agama_hash',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // --- ACCESSOR/MUTATOR UNTUK ENKRIPSI DAN HASHING ---

    /**
     * Enkripsi email dan generate hash untuk login/pencarian
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) return null;
                try {
                    return decrypt($value);
                } catch (\Exception $e) {
                    return null;
                }
            },
            set: function (?string $value) {
                if (empty($value)) {
                    return null;
                }
                return encrypt($value);
            }
        );
    }

    /**
     * Enkripsi NIP dan generate hash untuk login/pencarian
     */
    protected function nip(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) return null;
                try {
                    return decrypt($value);
                } catch (\Exception $e) {
                    return null;
                }
            },
            set: function (?string $value) {
                if (empty($value)) {
                    return null;
                }
                return encrypt($value);
            }
        );
    }

    /**
     * Enkripsi telephone
     */
    protected function telephone(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) return null;
                try {
                    return decrypt($value);
                } catch (\Exception $e) {
                    return null;
                }
            },
            set: function (?string $value) {
                if (empty($value)) {
                    return null;
                }
                return encrypt($value);
            }
        );
    }

    /**
     * Enkripsi spesialisasi agama
     */
    protected function spesialisasiAgama(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) return null;
                try {
                    return decrypt($value);
                } catch (\Exception $e) {
                    return null;
                }
            },
            set: function (?string $value) {
                if (empty($value)) {
                    return null;
                }
                return encrypt($value);
            }
        );
    }

    protected static function booted()
    {
        static::saving(function ($user) {
            // Hash email untuk login/pencarian
            if (!empty($user->email)) {
                $user->email_hash = hash('sha256', Str::lower($user->email));
            } else {
                $user->email_hash = null;
            }

            // Hash NIP untuk login/pencarian
            if (!empty($user->nip)) {
                $user->nip_hash = hash('sha256', $user->nip);
            } else {
                $user->nip_hash = null;
            }

            // Hash spesialisasi agama
            if (!empty($user->spesialisasi_agama)) {
                $user->spesialisasi_agama_hash = hash('sha256', Str::lower($user->spesialisasi_agama));
            } else {
                $user->spesialisasi_agama_hash = null;
            }
        });

        static::creating(function ($user) {
            // Generate slug jika belum diisi
            if (empty($user->slug)) {
                $user->slug = static::generateSlug($user->name);
            }
        });

        static::updating(function ($user) {
            // Generate slug jika name berubah DAN slug kosong
            if ($user->isDirty('name') && empty($user->slug)) {
                $user->slug = static::generateSlug($user->name, $user->id);
            }
        });
    }

    protected static function generateSlug($name, $ignoreId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 2;

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

    /**
     * Method untuk mencari user berdasarkan email (menggunakan hash)
     */
    public static function findByEmail($email)
    {
        $emailHash = hash('sha256', Str::lower($email));
        return static::where('email_hash', $emailHash)->first();
    }

    /**
     * Method untuk mencari user berdasarkan NIP (menggunakan hash)
     */
    public static function findByNip($nip)
    {
        $nipHash = hash('sha256', $nip);
        return static::where('nip_hash', $nipHash)->first();
    }

    public function kokurikulerDibimbing()
    {
        return $this->hasMany(Kokurikuler::class, 'guru_id');
    }
}
