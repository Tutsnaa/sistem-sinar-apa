<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    // READ ALL
    public function view()
    {
        return response()->json([
            'success' => true,
            'data' => Kategori::all(),
            'message' => 'Daftar kategori berhasil diambil'
        ]);
    }

    // CREATE
    public function create(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:50|unique:kategori,nama_kategori',
        ]);

        $kategori = Kategori::create($validated);

        return response()->json([
            'success' => true,
            'data' => $kategori,
            'message' => 'Kategori berhasil ditambahkan'
        ], 201);
    }

    // READ SINGLE
    public function show($param)
    {
        if (is_numeric($param)) {
            $kategori = Kategori::find($param);
        } else {
            $kategori = Kategori::where('nama_kategori', $param)->first();
        }

        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $kategori,
            'message' => 'Kategori berhasil ditemukan'
        ]);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $kategori = Kategori::find($id);

        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:50|unique:kategori,nama_kategori,' . $id,
        ]);

        $kategori->update($validated);

        return response()->json([
            'success' => true,
            'data' => $kategori,
            'message' => 'Kategori berhasil diperbarui'
        ]);
    }

    // DELETE
    public function delete($id)
    {
        $kategori = Kategori::find($id);

        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan'
            ], 404);
        }

        $kategori->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus'
        ]);
    }
}