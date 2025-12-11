<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Inertia\Inertia; // <-- WAJIB ADA, JANGAN SALAH

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::all();

        return Inertia::render('Barang/Index', [
            'barang' => $barang
        ]);
    }

    public function create()
    {
        return Inertia::render('Barang/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string',
            'stok' => 'required|integer',
            'harga' => 'required|integer',
        ]);

        Barang::create($request->all());

        return redirect()->route('barang.index')->with('success', 'Barang berhasil ditambahkan');
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);

        return Inertia::render('Barang/Edit', [
            'barang' => $barang
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_barang' => 'required|string',
            'stok' => 'required|integer',
            'harga' => 'required|integer',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update($request->all());

        return redirect()->route('barang.index')->with('success', 'Barang berhasil diperbarui');
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect()->route('barang.index')->with('success', 'Barang berhasil dihapus');
    }

    public function penjualan()
{
    $barang = Barang::select('id', 'nama_barang', 'harga_jual')->get();

    return Inertia::render('Penjualan/Index', [
        'barang' => $barang
    ]);
}

}