<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function barangTerlaris(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

        $data = DB::table('detail_penjualan')
            ->join('barang', 'barang.id', '=', 'detail_penjualan.id_barang')
            ->join('penjualan', 'penjualan.id', '=', 'detail_penjualan.id_penjualan')
            ->select(
                'barang.nama_barang',
                DB::raw('SUM(detail_penjualan.jumlah) as total_terjual'),
                DB::raw('SUM(detail_penjualan.total) as total_pendapatan')
            )
            ->whereMonth('penjualan.tanggal', $bulan)
            ->whereYear('penjualan.tanggal', $tahun)
            ->groupBy('barang.nama_barang')
            ->orderByDesc('total_terjual') // 🔥 PALING PENTING
            ->get();

        return response()->json([
            'message' => 'Barang terlaris',
            'data' => $data
        ]);
    }
}