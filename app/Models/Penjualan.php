<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';

    protected $fillable = [
        'id_pengguna',
        'nama_pelanggan',
        'total',
        'bayar',
        'kembalian',
    ];

    // Relasi: 1 penjualan punya banyak detail
    public function detail()
    {
        return $this->hasMany(DetailPenjualan::class, 'id_penjualan');
    }

    // Relasi ke pengguna
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }
}