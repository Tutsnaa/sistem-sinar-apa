<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\pengguna;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        pengguna::create([
            'nama_lengkap' => 'Pemilik Utama',
            'email' => 'pemilik@example.com',
            'no_telepon' => '081234567890',
            'nama_pengguna' => 'pemilik',
            'kata_sandi' => Hash::make('123456'),
            'role' => 'pemilik_toko'
        ]);

        pengguna::create([
            'nama_lengkap' => 'Karyawan 1',
            'email' => 'karyawan@example.com',
            'no_telepon' => '081212121212',
            'nama_pengguna' => 'karyawan',
            'kata_sandi' => Hash::make('123456'),
            'role' => 'karyawan'
        ]);
    }
}