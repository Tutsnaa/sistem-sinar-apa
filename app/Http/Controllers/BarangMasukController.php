<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarangMasukController extends Controller
{
    // GET: tampilkan semua barang masuk
    public function view()
    {
        $data = BarangMasuk::with('barang')
        ->orderByRaw("FIELD(status, 'Ditolak','Menunggu', 'Diterima')")
        ->orderBy('created_at', 'desc')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
    }

    // POST: simpan barang masuk
    public function create(Request $request)
{
    $validator = Validator::make($request->all(), [
        'id_barang'   => 'required|exists:barang,id',
        'id_pengguna' => 'required|exists:pengguna,id',
        'jumlah'      => 'required|integer|min:1',
        'harga_beli'  => 'required|numeric',
        'harga_jual'  => 'required|numeric',
        'status'      => 'required|in:Diterima,Menunggu,Ditolak',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors'  => $validator->errors()
        ], 422);
    }

    $barangMasuk = BarangMasuk::create($request->all());

    // Ambil ulang dengan relasi barang
    $barangMasuk = BarangMasuk::with('barang')->find($barangMasuk->id);

    return response()->json([
        'success' => true,
        'message' => 'Barang masuk berhasil disimpan',
        'data'    => $barangMasuk
    ], 201);
}


    // GET: detail barang masuk
    public function show($id)
    {
        $data = BarangMasuk::with(['barang', 'pengguna'])->find($id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]); 
    }

    // PUT: update barang masuk
    public function update(Request $request, $id)
{
    $barangMasuk = BarangMasuk::findOrFail($id);
    if ($barangMasuk->status === 'Diterima') {
        return response()->json([
            'message' => 'Barang masuk yang sudah diterima tidak dapat diubah'
        ], 403);
    }


    $validated = $request->validate([
        'id_barang' => 'required|exists:barang,id',
        'jumlah' => 'required|numeric|min:1',
        'harga_beli' => 'required|numeric',
        'harga_jual' => 'required|numeric',
        'status' => 'required|string',
    ]);

    $previousStatus = $barangMasuk->status;
    $barangMasuk->update($validated);

     // Jika status berubah menjadi 'Diterima' dan sebelumnya bukan 'Diterima'
    if ($validated['status'] === 'Diterima' && $previousStatus !== 'Diterima') {
        $barang = $barangMasuk->barang; // ambil relasi barang
        $barang->jumlah += $barangMasuk->jumlah;
        $barang->save();
    }
    

    // Ambil ulang dengan relasi barang
    $barangMasuk = BarangMasuk::with('barang')->find($barangMasuk->id);

    return response()->json([
        'message' => 'Barang masuk berhasil diperbarui',
        'data' => $barangMasuk
    ]);
}



    // DELETE: hapus barang masuk
    public function delete($id)
    {
        // $barangMasuk = BarangMasuk::find($id);
        $barangMasuk = BarangMasuk::findOrFail($id);
        if (!$barangMasuk->status === 'Diterima') {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $barangMasuk->delete();

        return response()->json([
            'success' => true,
            'message' => 'Barang masuk berhasil dihapus'
        ]);
    }
}