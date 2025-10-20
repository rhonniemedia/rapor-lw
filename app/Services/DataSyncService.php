<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use App\Models\Rombel;
use App\Models\Jurusan;
use App\Models\Pelajar;
use App\Models\RoleUser;
use App\Models\TahunAjaran;
use App\Models\OrangTuaWali;
use App\Models\RombelPelajar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class DataSyncService
{
    public function syncAll(array $apiData): void
    {
        DB::beginTransaction();

        try {
            $this->syncTahunAjaran($apiData['tahun_ajaran'] ?? []); // ADDED
            $this->syncJurusan($apiData['jurusan'] ?? []); // ADDED
            $this->syncRombel($apiData['rombel'] ?? []);
            $this->syncPesertaDidik($apiData['peserta_didik'] ?? []);
            $this->syncRombelDetail($apiData['rombel_detail'] ?? []);
            $this->syncGuru($apiData['guru'] ?? []);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error syncing data: ' . $e->getMessage());
            throw $e;
        }
    }

    // ADDED
    private function syncTahunAjaran(array $data): void
    {
        // Assuming App\Models\TahunAjaran exists
        foreach ($data as $item) {
            TahunAjaran::updateOrCreate(
                ['id' => $item['id']], // gunakan UUID dari API
                [
                    'nama'         => $item['tahun_ajaran'],
                    'tgl_mulai'    => $item['tgl_mulai'] ?? null,
                    'tgl_selesai'  => $item['tgl_selesai'] ?? null,
                    'status'       => $item['status'] ?? 'nonaktif',
                    'next_id'      => $item['next_id'] ?? null,
                ]
            );
        }
    }

    // ADDED
    private function syncJurusan(array $data): void
    {
        // Assuming App\Models\Jurusan exists
        foreach ($data as $item) {

            // --- FIX: Check for the required 'jurusan' key and 'id' key ---
            if (!isset($item['id']) || !isset($item['jurusan']) || empty($item['jurusan'])) {
                Log::warning('Skipping Jurusan sync due to missing ID or missing/empty "jurusan" field.', ['data_item' => $item]);
                continue; // Skip this iteration if the required data is missing
            }
            // -------------------------------------------------------------

            Jurusan::updateOrCreate(
                ['id' => $item['id']],
                [
                    'nama'      => $item['jurusan'],
                    'alias'     => $item['alias'] ?? null,
                    'kode'      => $item['kode_jurusan'] ?? null,
                    'status'    => $item['status'] ?? 'aktif',
                ]
            );
        }
    }

    private function syncRombel(array $data): void
    {
        foreach ($data as $item) {
            Rombel::updateOrCreate(
                ['id' => $item['id']],
                [
                    'tahun_ajaran_id' => $item['tahun_ajaran_id'] ?? null,
                    'jurusan_id' => $item['jurusan_id'] ?? null,
                    'tahun_ajaran_kurikulum_id' => null, // dikosongkan saat sync dari API
                    'wali_kelas_slug' => $item['wali_kelas'] ?? null,
                    'tingkat' => $this->getTingkatFromNama($item['nama_rombel'] ?? ''),
                    'nama' => $item['nama_rombel'] ?? null,
                ]
            );
        }
    }

    /**
     * Fungsi bantu untuk menentukan tingkat kelas dari nama rombel
     * Contoh:
     *  - "X DPIB 1"  => 10
     *  - "XI TKR"    => 11
     *  - "XII RPL"   => 12
     */
    private function getTingkatFromNama(string $namaRombel): int
    {
        $namaRombel = strtoupper($namaRombel);

        if (str_contains($namaRombel, 'XII')) {
            return 12;
        } elseif (str_contains($namaRombel, 'XI')) {
            return 11;
        } elseif (str_contains($namaRombel, 'X')) {
            return 10;
        }

        return 10; // default jika tidak ditemukan
    }

    private function syncPesertaDidik(array $data): void
    {
        DB::transaction(function () use ($data) {
            foreach ($data as $item) {
                // Gabungkan alamat pelajar
                $alamat = '';
                if (isset($item['alamat'])) {
                    $alamatParts = [
                        $item['alamat']['jalan'] ?? '',
                        'RT ' . ($item['alamat']['rt'] ?? ''),
                        'RW ' . ($item['alamat']['rw'] ?? ''),
                        $item['alamat']['kelurahan'] ?? '',
                        $item['alamat']['kecamatan'] ?? '',
                        $item['alamat']['kode_pos'] ?? '',
                    ];

                    $alamat = collect($alamatParts)
                        ->filter(fn($v) => !empty($v))
                        ->implode(', ');
                }

                // Simpan atau perbarui data pelajar
                $pelajar = Pelajar::updateOrCreate(
                    ['id' => $item['id']],
                    [
                        'nama_lengkap'  => $item['nama'] ?? null,
                        'nomor_induk'   => $item['nis'] ?? null,
                        'nisn'          => $item['nisn'] ?? null,
                        'tempat_lahir'  => $item['tempat_lahir'] ?? null,
                        'tanggal_lahir' => !empty($item['tgl_lahir']) ? $item['tgl_lahir'] : null,
                        'jenis_kelamin' => $item['jk'] ?? null,
                        'agama'         => $item['agama'] ?? null,
                        'status_dalam_keluarga' => 'anak-kandung' ?? null,
                        'anak_ke'       => $item['anak_ke'] ?? null,
                        'alamat'        => $alamat,
                        'telepon'       => $item['alamat']['telepon'] ?? null,
                        'sekolah_asal'  => $item['sekolah_asal']['nama'] ?? null,
                        'diterima_di_kelas' => $item['penempatan']['kelas'] ?? null,
                        'pada_tanggal'  => !empty($item['penempatan']['tgl_masuk'])
                            ? $item['penempatan']['tgl_masuk']
                            : null,
                    ]
                );

                // Sinkronisasi data orang tua/wali
                if (!empty($item['orang_tua'])) {
                    foreach ($item['orang_tua'] as $ortu) {
                        OrangTuaWali::updateOrCreate(
                            [
                                'id' => $ortu['id'], // gunakan UUID dari API
                            ],
                            [
                                'pelajar_id' => $pelajar->id,
                                'nama'       => $ortu['nama_ortu'] ?? null,
                                'hubungan'   => $ortu['hubungan'] ?? 'wali',
                                'status'     => $ortu['status'] ?? null,
                                'pekerjaan'  => $ortu['pekerjaan'] ?? null,
                                'telepon'    => $ortu['telepon'] ?? null,
                                'alamat'     => $ortu['alamat'] ?? null,
                            ]
                        );
                    }
                }
            }
        });
    }

    private function syncRombelDetail(array $data): void
    {
        foreach ($data as $item) {
            RombelPelajar::updateOrCreate(
                ['id' => $item['id']],
                [
                    // 'tahun_ajaran_id' => $item['tahun_ajaran_id'] ?? null,
                    'rombel_id' => $item['rombel_id'] ?? null,
                    'pelajar_id' => $item['peserta_didik_id'] ?? null,
                    // 'status_kelas' => $item['status_kelas'] ?? 'aktif',
                    'updated_at' => $item['updated_at'] ?? now(),
                ]
            );
        }
    }

    private function syncGuru(array $data): void
    {
        // Log ringkasan awal data
        Log::info('START syncGuru', ['total_data' => count($data)]);

        $roleGuru = Role::where('nama_role', 'guru')->first();

        // Periksa apakah peran 'guru' ada
        if (!$roleGuru) {
            Log::error("Role 'guru' not found. Aborting sync.");
            return;
        }

        foreach ($data as $index => $item) {
            $ptkSlug = $item['ptk_slug'] ?? null;
            $nama = $item['nama'] ?? null;
            $email = $item['email'] ?? null;

            // SKIP JIKA SLUG ADALAH ANGKA MINUS (atau format angka lainnya yang tidak valid untuk slug)
            if ($ptkSlug && preg_match('/^-?\d+$/', $ptkSlug)) {
                Log::warning("Skipped: Invalid slug format (looks like number)", [
                    'slug' => $ptkSlug,
                    'nama' => $nama
                ]);
                continue;
            }

            // SKIP JIKA DATA WAJIB TIDAK LENGKAP
            if (empty($nama) || empty($email) || empty($ptkSlug)) {
                Log::warning('Skipped: Incomplete required data (name, email, or slug missing)', $item);
                continue;
            }

            try {
                DB::transaction(function () use ($item, $roleGuru, $ptkSlug, $email) {
                    $user = User::where('slug', $ptkSlug)->first();

                    $userData = [
                        'name' => $item['nama'],
                        'email' => $item['email'],
                        'slug' => $ptkSlug,
                        'nip' => !empty($item['nip']) ? $item['nip'] : null,
                        'telephone' => $item['telepon'] ?? null,
                        'status' => 'aktif',
                    ];

                    if ($user) {
                        $user->update($userData);
                        // Log::info('Updated user', ['slug' => $ptkSlug]); // Log detail ini dihilangkan
                    } else {
                        if (User::where('email', $email)->exists()) {
                            Log::warning('Skipped: Email already exists for a different user', ['email' => $email, 'slug' => $ptkSlug]);
                            return;
                        }

                        $userData['password'] = Hash::make('Pass' . ($item['telepon'] ?? '12345') . '*');
                        $user = User::create($userData);
                        // Log::info('Created new user', ['slug' => $ptkSlug]); // Log detail ini dihilangkan
                    }

                    RoleUser::firstOrCreate([
                        'user_id' => $user->id,
                        'role_id' => $roleGuru->id,
                    ]);
                });
            } catch (\Exception $e) {
                // Log kesalahan serius saat memproses item
                Log::error('FATAL Error processing guru data', [
                    'message' => $e->getMessage(),
                    'slug' => $ptkSlug,
                    'email' => $email,
                ]);
            }
        }

        Log::info('END syncGuru');
    }
}
