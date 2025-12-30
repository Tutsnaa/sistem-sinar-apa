<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';

    protected $fillable = [
        'id_kategori',
        'nama_barang',
        'satuan',
        'harga_beli',
        'harga_jual', 
        'jumlah',
        'status'
    ];

    /**
     * AUTO SET STATUS BERDASARKAN JUMLAH
     */
    protected static function booted()
    {
        static::saving(function ($barang) {
            $barang->status = $barang->jumlah == 0 ? 'habis' : 'tersedia';
        });
    }

    /**
     * Relasi ke kategori
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }


}