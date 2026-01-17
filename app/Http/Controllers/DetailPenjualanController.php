<?php

namespace App\Http\Controllers;

use App\Models\DetailPenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DetailPenjualanController extends Controller
{
    // Tampilkan semua detail penjualan
    public function view()
{
    $data = DetailPenjualan::with('barang')->get();

    return response()->json([
        'message' => 'Data detail penjualan',
        'data' => $data
    ]);
}

public function barangTerlaris(Request $request)
{
    $bulan = (int) $request->bulan;
    $tahun = (int) $request->tahun;

    $data = DetailPenjualan::join('barang', 'barang.id', '=', 'detail_penjualan.id_barang')
        ->join('penjualan', 'penjualan.id', '=', 'detail_penjualan.id_penjualan')
        ->select(
            'detail_penjualan.id_barang',
            'barang.nama_barang',
            'barang.harga_beli',
            'barang.harga_jual',
            DB::raw('SUM(detail_penjualan.jumlah) as total_terjual'),
            DB::raw('SUM(detail_penjualan.total) as total_pendapatan'),
            DB::raw('(barang.harga_jual - barang.harga_beli) * SUM(detail_penjualan.jumlah) as total_keuntungan')
        )
        ->whereMonth('penjualan.created_at', $bulan)
        ->whereYear('penjualan.created_at', $tahun)
        ->groupBy(
            'detail_penjualan.id_barang',
            'barang.nama_barang',
            'barang.harga_beli',
            'barang.harga_jual'
        )
        ->orderByDesc('total_terjual')
        ->get();

    return response()->json([
        'message' => 'Barang terlaris',
        'data' => $data
    ]);
}




    // Simpan detail penjualan baru
    // public function create(Request $request)
    // {
    //     $request->validate([
    //         'id_penjualan' => 'required|integer',
    //         'id_barang'    => 'required|integer',
    //         'harga'        => 'required|numeric',
    //         'jumlah'       => 'required|integer',
    //     ]);

    //     $total = $request->harga * $request->jumlah;

    //     $detail = DetailPenjualan::create([
    //         'id_penjualan' => $request->id_penjualan,
    //         'id_barang'    => $request->id_barang,
    //         'harga'        => $request->harga,
    //         'jumlah'       => $request->jumlah,
    //         'total'        => $total,
    //     ]);

    //     return response()->json([
    //         'message' => 'Detail penjualan berhasil ditambahkan',
    //         'data' => $detail
    //     ], 201);
    // }

    public function create(Request $request)
{
    $request->validate([
        'id_penjualan' => 'required|integer',
        'id_barang'    => 'required|integer',
        'harga'        => 'required|numeric', // harga jual
        'jumlah'       => 'required|integer',
    ]);

    // ambil harga beli dari tabel barang
    $barang = DB::table('barang')->where('id', $request->id_barang)->first();

    if (!$barang) {
        return response()->json(['message' => 'Barang tidak ditemukan'], 404);
    }

    $total = $request->harga * $request->jumlah;
    $keuntungan = ($request->harga - $barang->harga_beli) * $request->jumlah;

    $detail = DetailPenjualan::create([
        'id_penjualan' => $request->id_penjualan,
        'id_barang'    => $request->id_barang,
        'harga'        => $request->harga,
        'jumlah'       => $request->jumlah,
        'total'        => $total,
        'keuntungan'   => $keuntungan,
    ]);

    return response()->json([
        'message' => 'Detail penjualan berhasil ditambahkan',
        'data' => $detail
    ], 201);
}


    // Tampilkan detail berdasarkan ID
    public function show($id)
    {
        $detail = DetailPenjualan::find($id);

        if (!$detail) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json($detail);
    }

    // Update detail penjualan
    // public function update(Request $request, $id)
    // {
    //     $detail = DetailPenjualan::find($id);

    //     if (!$detail) {
    //         return response()->json([
    //             'message' => 'Data tidak ditemukan'
    //         ], 404);
    //     }

    //     $request->validate([
    //         'harga'  => 'required|numeric',
    //         'jumlah' => 'required|integer',
    //     ]);

    //     $detail->update([
    //         'harga'  => $request->harga,
    //         'jumlah' => $request->jumlah,
    //         'total'  => $request->harga * $request->jumlah,
    //     ]);

    //     return response()->json([
    //         'message' => 'Detail penjualan berhasil diupdate',
    //         'data' => $detail
    //     ]);
    // }
    public function update(Request $request, $id)
{
    $detail = DetailPenjualan::find($id);

    if (!$detail) {
        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    $request->validate([
        'harga'  => 'required|numeric',
        'jumlah' => 'required|integer',
    ]);

    $barang = DB::table('barang')->where('id', $detail->id_barang)->first();

    $total = $request->harga * $request->jumlah;
    $keuntungan = ($request->harga - $barang->harga_beli) * $request->jumlah;

    $detail->update([
        'harga'      => $request->harga,
        'jumlah'     => $request->jumlah,
        'total'      => $total,
        'keuntungan' => $keuntungan,
    ]);

    return response()->json([
        'message' => 'Detail penjualan berhasil diupdate',
        'data' => $detail
    ]);
}


    // Hapus detail penjualan
    public function delete($id)
    {
        $detail = DetailPenjualan::find($id);

        if (!$detail) {
            return response()->json([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $detail->delete();

        return response()->json([
            'message' => 'Detail penjualan berhasil dihapus'
        ]);
    }
}