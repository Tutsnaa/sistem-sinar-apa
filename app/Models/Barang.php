<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';

    protected $fillable = [
        'id_kategori',
        'nama_barang',
        'harga_beli',
        'harga_jual', 
        'jumlah',
        'status'
    ];

    /**
     * Relasi ke Penjualan (detail_penjualan -> penjualan)
     */
    public function Kategori()
    {
        return $this->belongsTo(Penjualan::class, 'id_kategori');
    }


}