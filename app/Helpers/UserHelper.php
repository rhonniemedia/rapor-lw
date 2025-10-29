<?php

namespace App\Helpers;

class UserHelper
{
    /**
     * Membersihkan gelar/title dan mengambil nama depan.
     * @param string $fullName Nama lengkap user.
     * @return string Nama depan yang sudah bersih.
     */
    public static function getFirstName(string $fullName): string
    {
        // Daftar gelar/title yang umum
        $titles = [
            'Dr.',
            'Drs.',
            'Dra.',
            'H.',
            'Hj.',
            'Ir.',
            'KH.',
            'K.H.',
            'S.E.',
            'S.H.',
            'S.Ked.',
            'S.Pd.',
            'M.Pd.',
            'M.M.',
            'B.Sc.',
            'Amd.'
        ];

        // Hapus semua gelar dari string nama
        $cleanedName = $fullName;
        foreach ($titles as $title) {
            $cleanedName = str_ireplace($title, '', $cleanedName);
        }

        // Hapus spasi berlebih setelah penghapusan gelar
        $cleanedName = trim(preg_replace('/\s+/', ' ', $cleanedName));

        // Ambil kata pertama (Nama depan)
        $parts = explode(' ', $cleanedName);
        return $parts[0] ?? '';
    }
}
