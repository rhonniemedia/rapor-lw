<?php

/**
 * HELPER FUNCTIONS UNTUK ENUM
 * 
 * Letakkan di app/Helpers/enum_helpers.php
 * atau tambahkan di composer.json → autoload → files
 */

if (!function_exists('enum')) {
    /**
     * Get enum value or all values
     * 
     * @param string $type Tipe enum (agama, pekerjaan, dll)
     * @param string|null $key Key spesifik (opsional)
     * @return mixed
     */
    function enum($type, $key = null)
    {
        $enums = config("enums.{$type}", []);

        if (is_null($key)) {
            return $enums;
        }

        return $enums[$key] ?? null;
    }
}

if (!function_exists('enum_options')) {
    /**
     * Get enum sebagai option array untuk select/dropdown
     * Format: [key => label]
     * 
     * @param string $type
     * @param bool $withEmptyOption Tambahkan option kosong di awal
     * @param string $emptyLabel Label untuk option kosong
     * @return array
     */
    function enum_options($type, $withEmptyOption = false, $emptyLabel = '-- Pilih --')
    {
        $options = config("enums.{$type}", []);

        if ($withEmptyOption) {
            return ['' => $emptyLabel] + $options;
        }

        return $options;
    }
}

if (!function_exists('enum_label')) {
    /**
     * Get label dari enum key
     * 
     * @param string $type
     * @param string $key
     * @param string $default Default jika tidak ditemukan
     * @return string
     */
    function enum_label($type, $key, $default = '-')
    {
        return config("enums.{$type}.{$key}", $default);
    }
}

if (!function_exists('enum_keys')) {
    /**
     * Get semua keys dari enum (untuk validasi)
     * 
     * @param string $type
     * @return array
     */
    function enum_keys($type)
    {
        return array_keys(config("enums.{$type}", []));
    }
}

if (!function_exists('enum_validation_rule')) {
    /**
     * Generate validation rule untuk enum
     * 
     * @param string $type
     * @param bool $nullable
     * @return string
     */
    function enum_validation_rule($type, $nullable = false)
    {
        $keys = enum_keys($type);
        $rule = 'in:' . implode(',', $keys);

        return $nullable ? "nullable|{$rule}" : "required|{$rule}";
    }
}
