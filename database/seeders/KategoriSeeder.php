<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kategori::create([
            'nama_kategori' => 'Kabel Listrik'
        ]);

        Kategori::create([
            'nama_kategori' => 'Lampu'
        ]); 
        
        Kategori::create([
            'nama_kategori' => 'Stop Kontak'
        ]); 
    }
}