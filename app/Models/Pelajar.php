<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pelajar extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pelajars';
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'nama_lengkap',
        'nomor_induk',
        'nisn',
        'nisn_hash',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'agama_hash',
        'status_dalam_keluarga',
        'anak_ke',
        'alamat',
        'telepon',
        'sekolah_asal',
        'diterima_di_kelas',
        'pada_tanggal',
    ];

    protected $casts = [
        'nisn'          => 'encrypted',
        'alamat'        => 'encrypted',
        'telepon'       => 'encrypted',
    ];

    protected function agama(): Attribute
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
            set: function ($value) {
                if (empty($value)) {
                    return null;
                }
                return encrypt($value);
            }
        );
    }

    protected function tanggalLahir(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) {
                    return null;
                }

                try {
                    $decrypted = decrypt($value);
                    return Carbon::parse($decrypted);
                } catch (\Exception $e) {
                    return null;
                }
            },
            set: function ($value) {
                if (empty($value)) {
                    return null;
                }

                $date = $value instanceof Carbon ? $value->format('Y-m-d') : $value;
                return encrypt($date);
            }
        );
    }

    public function getTanggalLahirFormattedAttribute()
    {
        return $this->tanggal_lahir
            ? Carbon::parse($this->tanggal_lahir)->translatedFormat('d F Y')
            : null;
    }

    public function getPadaTanggalFormattedAttribute()
    {
        return $this->pada_tanggal
            ? Carbon::parse($this->pada_tanggal)->translatedFormat('d F Y')
            : null;
    }

    // GABUNGKAN KEDUA METHOD BOOTED() MENJADI SATU
    protected static function booted()
    {
        static::saving(function ($model) {
            // Hash NISN
            $model->nisn_hash = $model->nisn
                ? hash('sha256', $model->nisn)
                : null;

            // Hash Agama
            if (!empty($model->agama)) {
                $agamaValue = $model->agama;
                $model->agama_hash = hash('sha256', Str::lower($agamaValue));
            } else {
                $model->agama_hash = null;
            }
        });
    }

    public function orangTuaWalis()
    {
        return $this->hasMany(OrangTuaWali::class);
    }

    public function rombels()
    {
        return $this->belongsToMany(
            Rombel::class,
            'rombel_pelajars',
            'pelajar_id',
            'rombel_id'
        )->withTimestamps();
    }

    public function kokurikuler()
    {
        return $this->hasMany(Kokurikuler::class);
    }

    public function getIconAttribute()
    {
        return match ($this->jenis_kelamin) {
            'L' => asset('assets/images/icons/male.png'),
            'P' => asset('assets/images/icons/female.png'),
            default => asset('assets/images/icons/unknown.png'),
        };
    }

    public function getJenisKelaminLabelAttribute()
    {
        return match ($this->jenis_kelamin) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => 'Tidak diketahui',
        };
    }

    // Format untuk INPUT (Y-m-d)
    public function getTanggalLahirInputAttribute()
    {
        return $this->tanggal_lahir?->format('Y-m-d');
    }

    /**
     * Relasi ke model Nilai (Asumsi Pelajar memiliki banyak Nilai)
     */
    public function nilai()
    {
        // Ganti 'pelajar_id' jika nama foreign key Anda berbeda
        return $this->hasMany(Nilai::class, 'pelajar_id');
    }

    /**
     * Relasi ke model Kehadiran (Untuk Eager Loading)
     */
    public function kehadiran()
    {
        return $this->hasMany(Kehadiran::class, 'pelajar_id');
    }
}
