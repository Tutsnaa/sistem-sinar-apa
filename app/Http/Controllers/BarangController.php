<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    /**
     * Tampilkan semua barang
     */
    public function view()
    {
        return response()->json(
            Barang::with('kategori')->get()
        );
    }

    /**
     * Simpan barang baru
     */
    public function create(Request $request)
    {
        $request->validate([
            'id_kategori' => 'required|exists:kategori,id',
            'nama_barang' => 'required|string|max:100',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'jumlah' => 'required|integer|min:0',
        ]);

        $barang = Barang::create([
            'id_kategori' => $request->id_kategori,
            'nama_barang' => $request->nama_barang,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'jumlah' => $request->jumlah,
            // STATUS TIDAK PERLU DIISI
        ]);

        return response()->json([
            'message' => 'Barang berhasil ditambahkan',
            'data' => $barang
        ], 201);
    }

    /**
     * Detail barang
     */
    public function show($id)
    {
        return response()->json(
            Barang::with('kategori')->findOrFail($id)
        );
    }

    /**
     * Update barang
     */
    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $request->validate([
            'id_kategori' => 'required|exists:kategori,id',
            'nama_barang' => 'required|string|max:100',
            'harga_beli' => 'required|numeric',
            'harga_jual' => 'required|numeric',
            'jumlah' => 'required|integer|min:0',
        ]);

        $barang->update([
            'id_kategori' => $request->id_kategori,
            'nama_barang' => $request->nama_barang,
            'harga_beli' => $request->harga_beli,
            'harga_jual' => $request->harga_jual,
            'jumlah' => $request->jumlah,
        ]);

        return response()->json([
            'message' => 'Barang berhasil diperbarui',
            'data' => $barang
        ]);
    }

    /**
     * Hapus barang
     */
    public function delete($id)
    {
        Barang::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Barang berhasil dihapus'
        ]);
    }
}