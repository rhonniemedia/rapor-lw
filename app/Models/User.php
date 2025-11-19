<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
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
        'is_teacher' => 'boolean',
        'is_guru_agama' => 'boolean',
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
                    Log::error('Error decrypting email: ' . $e->getMessage());
                    return null;
                }
            },
            set: function (?string $value) {
                if (empty($value)) {
                    return [
                        'email' => null,
                        'email_hash' => null,
                    ];
                }
                return [
                    'email' => encrypt($value),
                    'email_hash' => hash('sha256', Str::lower($value)),
                ];
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
                    Log::error('Error decrypting nip: ' . $e->getMessage());
                    return null;
                }
            },
            set: function (?string $value) {
                if (empty($value)) {
                    return [
                        'nip' => null,
                        'nip_hash' => null,
                    ];
                }
                return [
                    'nip' => encrypt($value),
                    'nip_hash' => hash('sha256', $value),
                ];
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
                    Log::error('Error decrypting telephone: ' . $e->getMessage());
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
     * PERBAIKAN: Enkripsi spesialisasi agama dengan nama method yang benar (snake_case)
     * Nama method harus sesuai dengan nama kolom database
     */
    protected function spesialisasiAgama(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) return null;
                try {
                    return decrypt($value);
                } catch (\Exception $e) {
                    Log::error('Error decrypting spesialisasi_agama: ' . $e->getMessage());
                    return null;
                }
            },
            set: function (?string $value) {
                if (empty($value)) {
                    return [
                        'spesialisasi_agama' => null,
                        'spesialisasi_agama_hash' => null,
                    ];
                }
                return [
                    'spesialisasi_agama' => encrypt($value),
                    'spesialisasi_agama_hash' => hash('sha256', Str::lower($value)),
                ];
            }
        );
    }

    protected static function booted()
    {
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

    public function rombelPengajars()
    {
        return $this->hasMany(RombelPengajar::class, 'guru_id');
    }
}
