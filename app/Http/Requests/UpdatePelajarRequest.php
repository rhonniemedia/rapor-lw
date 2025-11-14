<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePelajarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Data Pelajar
            'nama_lengkap' => 'required|string|max:255',
            'nomor_induk' => 'nullable|string|max:50',
            'nisn' => 'nullable|string|max:10|regex:/^[0-9]{10}$/',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date|before:today',
            'jenis_kelamin' => 'nullable|in:L,P',
            'agama' => 'nullable|in:islam,kristen,katolik,hindu,buddha,konghucu',
            'status_dalam_keluarga' => 'nullable|string|max:100',
            'anak_ke' => 'nullable|integer|min:1',
            'alamat' => 'nullable|string|max:500',
            'telepon' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'sekolah_asal' => 'nullable|string|max:255',
            'diterima_di_kelas' => 'nullable|string|max:100',
            'pada_tanggal' => 'nullable|date',

            // Data Ayah
            'ayah.nama' => 'nullable|string|max:255',
            'ayah.pekerjaan' => 'nullable|string|max:255',
            'ayah.telepon' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'ayah.alamat' => 'nullable|string|max:500',
            'ayah.status' => 'nullable|in:masih-hidup,sudah-meninggal',

            // Data Ibu
            'ibu.nama' => 'nullable|string|max:255',
            'ibu.pekerjaan' => 'nullable|string|max:255',
            'ibu.telepon' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'ibu.alamat' => 'nullable|string|max:500',
            'ibu.status' => 'nullable|in:masih-hidup,sudah-meninggal',

            // Data Wali
            'wali.nama' => 'nullable|string|max:255',
            'wali.pekerjaan' => 'nullable|string|max:255',
            'wali.telepon' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'wali.alamat' => 'nullable|string|max:500',
            'wali.status' => 'nullable|in:masih-hidup,sudah-meninggal',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.max' => 'Nama lengkap maksimal 255 karakter.',
            
            'nisn.regex' => 'NISN harus terdiri dari 10 digit angka.',
            'nisn.max' => 'NISN maksimal 10 karakter.',
            
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            
            'jenis_kelamin.in' => 'Jenis kelamin harus L (Laki-laki) atau P (Perempuan).',
            
            'agama.in' => 'Pilih agama yang valid.',
            
            'anak_ke.integer' => 'Anak ke harus berupa angka.',
            'anak_ke.min' => 'Anak ke minimal 1.',
            
            'telepon.regex' => 'Format nomor telepon tidak valid.',
            'ayah.telepon.regex' => 'Format nomor telepon ayah tidak valid.',
            'ibu.telepon.regex' => 'Format nomor telepon ibu tidak valid.',
            'wali.telepon.regex' => 'Format nomor telepon wali tidak valid.',
            
            'pada_tanggal.date' => 'Format tanggal tidak valid.',
            
            'ayah.status.in' => 'Status ayah harus "masih-hidup" atau "sudah-meninggal".',
            'ibu.status.in' => 'Status ibu harus "masih-hidup" atau "sudah-meninggal".',
            'wali.status.in' => 'Status wali harus "masih-hidup" atau "sudah-meninggal".',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama_lengkap' => 'nama lengkap',
            'nomor_induk' => 'nomor induk',
            'nisn' => 'NISN',
            'tempat_lahir' => 'tempat lahir',
            'tanggal_lahir' => 'tanggal lahir',
            'jenis_kelamin' => 'jenis kelamin',
            'agama' => 'agama',
            'status_dalam_keluarga' => 'status dalam keluarga',
            'anak_ke' => 'anak ke',
            'alamat' => 'alamat',
            'telepon' => 'telepon',
            'sekolah_asal' => 'sekolah asal',
            'diterima_di_kelas' => 'diterima di kelas',
            'pada_tanggal' => 'pada tanggal',
            
            'ayah.nama' => 'nama ayah',
            'ayah.pekerjaan' => 'pekerjaan ayah',
            'ayah.telepon' => 'telepon ayah',
            'ayah.alamat' => 'alamat ayah',
            'ayah.status' => 'status ayah',
            
            'ibu.nama' => 'nama ibu',
            'ibu.pekerjaan' => 'pekerjaan ibu',
            'ibu.telepon' => 'telepon ibu',
            'ibu.alamat' => 'alamat ibu',
            'ibu.status' => 'status ibu',
            
            'wali.nama' => 'nama wali',
            'wali.pekerjaan' => 'pekerjaan wali',
            'wali.telepon' => 'telepon wali',
            'wali.alamat' => 'alamat wali',
            'wali.status' => 'status wali',
        ];
    }
}
