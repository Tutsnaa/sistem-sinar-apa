<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id',
            'nama_pelanggan' => 'required|string',
            'bayar' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.id_barang' => 'required|exists:barang,id',
            'items.*.harga' => 'required|numeric',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            // 🔢 hitung total
            $total = collect($request->items)
                ->sum(fn ($i) => $i['harga'] * $i['jumlah']);

            $kembalian = $request->bayar - $total;

            if ($kembalian < 0) {
                return response()->json([
                    'message' => 'Uang bayar kurang'
                ], 422);
            }

            // 🧾 simpan penjualan
            $penjualan = Penjualan::create([
                'id_pengguna' => $request->id_pengguna,
                'nama_pelanggan' => $request->nama_pelanggan,
                'total' => $total,
                'bayar' => $request->bayar,
                'kembalian' => $kembalian,
            ]);

            // 📦 simpan detail penjualan
            foreach ($request->items as $item) {
                DetailPenjualan::create([
                    'id_penjualan' => $penjualan->id,
                    'id_barang' => $item['id_barang'],
                    'harga' => $item['harga'],
                    'jumlah' => $item['jumlah'],
                    'total' => $item['harga'] * $item['jumlah'],
                ]);

                // 🔽 kurangi stok barang
                Barang::where('id', $item['id_barang'])
                    ->decrement('jumlah', $item['jumlah']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Penjualan berhasil',
                'id_penjualan' => $penjualan->id
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}