<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PenjualanController extends Controller
{
    public function show($id)
{
    try {
        $penjualan = DB::table('penjualan')
            ->join('pengguna', 'pengguna.id', '=', 'penjualan.id_pengguna')
            ->where('penjualan.id', $id)
            ->select(
                'penjualan.id as id_penjualan',
                'penjualan.nama_pelanggan',
                'penjualan.total',
                'penjualan.bayar',
                'penjualan.kembalian',
                'pengguna.nama_lengkap as kasir',
                'penjualan.created_at'
            )
            ->first();

        if (!$penjualan) {
            return response()->json(['message' => 'Penjualan tidak ditemukan'], 404);
        }

        $items = DB::table('detail_penjualan')
            ->join('barang', 'barang.id', '=', 'detail_penjualan.id_barang')
            ->where('detail_penjualan.id_penjualan', $id)
            
            ->select(
                'barang.id as id_barang',
                'barang.nama_barang as nama',
                'detail_penjualan.jumlah',
                'detail_penjualan.harga',
                'detail_penjualan.keuntungan'
            )
            ->get();
            

        return response()->json([
    'data' => [
        'id_penjualan' => $penjualan->id_penjualan,
        'tanggal'      => date('d-m-Y', strtotime($penjualan->created_at)),
        'kasir'        => $penjualan->kasir,
        'pelanggan'    => $penjualan->nama_pelanggan ?? 'Umum',
        'items'        => $items,
        'total'        => $penjualan->total,
        'bayar'        => $penjualan->bayar,
        'kembalian'    => $penjualan->kembalian,
    ]
]);

    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Server error',
            'error'   => $e->getMessage()
        ], 500);
    }
}

    /**
     * 🔹 RIWAYAT PENJUALAN
     */
    // public function view(Request $request)
    // {
    //     $query = Penjualan::with([
    //         'pengguna:id,nama',
    //         'detailPenjualan:id_penjualan,harga,jumlah'
    //     ])->orderBy('created_at', 'desc');

    //     if ($request->periode_awal) {
    //         $query->whereDate('created_at', '>=', $request->periode_awal);
    //     }

    //     if ($request->periode_akhir) {
    //         $query->whereDate('created_at', '<=', $request->periode_akhir);
    //     }

    //     return response()->json([
    //         'data' => $query->get()
    //     ]);
    // }
public function view(Request $request)
{
    $query = Penjualan::with('pengguna')
        ->withSum('detailPenjualan as keuntungan', 'keuntungan')
        ->orderBy('created_at', 'desc');

    if ($request->periode_awal && $request->periode_akhir) {
        $query->whereBetween('created_at', [
            $request->periode_awal . ' 00:00:00',
            $request->periode_akhir . ' 23:59:59',
        ]);
    }

    return response()->json([
        'data' => $query->get()
    ]);
}



     // SIMPAN PENJUALAN

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
        $total = collect($request->items)
            ->sum(fn ($i) => $i['harga'] * $i['jumlah']);

        if ($request->bayar < $total) {
            return response()->json([
                'message' => 'Uang bayar kurang'
            ], 422);
        }

        $penjualan = Penjualan::create([
            'id_pengguna' => $request->id_pengguna,
            'nama_pelanggan' => $request->nama_pelanggan,
            'total' => $total,
            'bayar' => $request->bayar,
            'kembalian' => $request->bayar - $total,
        ]);

        foreach ($request->items as $item) {
            $barang = Barang::lockForUpdate()->find($item['id_barang']);

            if ($barang->jumlah < $item['jumlah']) {
                throw new \Exception(
                    "Stok {$barang->nama_barang} tidak mencukupi"
                );
            }

            DetailPenjualan::create([
                'id_penjualan' => $penjualan->id,
                'id_barang' => $item['id_barang'],
                'harga' => $item['harga'],
                'jumlah' => $item['jumlah'],
                'total' => $item['harga'] * $item['jumlah'],
                'keuntungan' => ($item['harga'] - $barang->harga_beli) * $item['jumlah'],
            ]);

            $barang->decrement('jumlah', $item['jumlah']);
        }

        // ✅ COMMIT DULU
        DB::commit();

        // ✅ BARU RETURN
        return response()->json([
            'message' => 'Penjualan berhasil',
            'id_penjualan' => $penjualan->id
        ], 201);

    } catch (\Throwable $e) {
        DB::rollBack();
        return response()->json([
            'message' => $e->getMessage()
        ], 500);
    }
}


    /**
     * 🔹 HAPUS PENJUALAN
     */
    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $penjualan = Penjualan::with('detailPenjualan')->findOrFail($id);

            // kembalikan stok
            foreach ($penjualan->detailPenjualan as $detail) {
                Barang::where('id', $detail->id_barang)
                    ->increment('jumlah', $detail->jumlah);
            }

            $penjualan->detailPenjualan()->delete();
            $penjualan->delete();

            DB::commit();

            return response()->json([
                'message' => 'Penjualan berhasil dihapus'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}