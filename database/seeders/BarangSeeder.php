<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Barang::create([
            'id_kategori' => 1,
            'nama_barang' => 'Kabel Listrik 10 Meter',
            'harga_beli' => 25000,
            'harga_jual' => 35000,
            'jumlah' => 100,
            'status' => 'tersedia'
        ]);

        Barang::create([
            'id_kategori' => 2,
            'nama_barang' => 'Lampu LED 15 Watt',
            'harga_beli' => 15000,
            'harga_jual' => 25000,
            'jumlah' => 50,
            'status' => 'tersedia'
        ]);

        Barang::create([
            'id_kategori' => 3,
            'nama_barang' => 'Stop Kontak 3 Lubang',
            'harga_beli' => 18000,
            'harga_jual' => 27000,
            'jumlah' => 0,
            'status' => 'habis'
        ]);
    }
}