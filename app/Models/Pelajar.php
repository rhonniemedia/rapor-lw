<?php

namespace App\Models;

use Carbon\Carbon;
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
        'agama'         => 'encrypted',
        'alamat'        => 'encrypted',
        'telepon'       => 'encrypted',
    ];

    // Accessor untuk tanggal_lahir (saat dibaca)
    protected function tanggalLahir(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (empty($value)) {
                    return null;
                }

                try {
                    // Decrypt dulu, lalu parse ke Carbon
                    $decrypted = decrypt($value);
                    return Carbon::parse($decrypted);
                } catch (\Exception $e) {
                    // Jika gagal decrypt, kembalikan null atau value asli
                    return null;
                }
            },
            set: function ($value) {
                if (empty($value)) {
                    return null;
                }

                // Convert ke string date format, lalu encrypt
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

    protected static function booted()
    {
        static::saving(function ($model) {
            $model->nisn_hash = $model->nisn
                ? hash('sha256', $model->nisn)
                : null;
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
            'rombel_pelajar',
            'pelajar_id',
            'rombel_id'
        )->withTimestamps();
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
}
