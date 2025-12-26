<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    use HasFactory;

    protected $table = 'detail_penjualan'; // karena bukan plural

    protected $fillable = [
        'id_penjualan',
        'id_barang',
        'harga',
        'jumlah',
        'total',
        'keuntungan'
    ];

    /**
     * Relasi ke Penjualan (detail_penjualan -> penjualan)
     */
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan');
    }

    /**
     * Relasi ke Barang (detail_penjualan -> barang)
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}