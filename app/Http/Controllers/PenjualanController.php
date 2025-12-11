<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Barang;

class PenjualanController extends Controller
{
    public function index()
    {
        $barang = Barang::select('id', 'nama_barang', 'harga_jual')->get();

        return Inertia::render('pages/MenuPenjualan', [
    'barang' => $barang
]);

    }
}