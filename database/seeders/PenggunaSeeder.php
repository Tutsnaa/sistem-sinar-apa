<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        Pengguna::create([
            'foto' => 'foto-profil.jpg',
            'nama_lengkap' => 'Pemilik Utama',
            'email' => 'pemilik@example.com',
            'no_telepon' => '081234567890',
            'nama_pengguna' => 'pemilik',
            'kata_sandi' => Hash::make('123456'),
            'role' => 'pemilik_toko'
        ]);

        Pengguna::create([
            'foto' => 'foto-profil.jpg',
            'nama_lengkap' => 'Karyawan 1',
            'email' => 'karyawan@example.com',
            'no_telepon' => '081212121212',
            'nama_pengguna' => 'karyawan',
            'kata_sandi' => Hash::make('123456'),
            'role' => 'karyawan'
        ]);

        Pengguna::create([
            'foto' => 'foto-profil.jpg',
            'nama_lengkap' => 'I Ketut Sujana',
            'email' => 'sujanaiketut@google.com',
            'no_telepon' => '081234567890',
            'nama_pengguna' => 'sujana',
            'kata_sandi' => Hash::make('123'),
            'role' => 'pemilik_toko'
        ]);
    }
}