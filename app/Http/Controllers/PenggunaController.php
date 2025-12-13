<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    // ============================
    // LOGIN API UNTUK VUE
    // ============================
    public function login(Request $request)
    {
        $request->validate([
            'nama_pengguna' => 'required',
            'kata_sandi' => 'required'
        ]);

        $user = Pengguna::where('nama_pengguna', $request->nama_pengguna)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Nama pengguna tidak ditemukan'
            ], 404);
        }

        if (!Hash::check($request->kata_sandi, $user->kata_sandi)) {
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi salah'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'id' => $user->id,
                'nama' => $user->nama_lengkap,
                'role' => $user->role,
                'foto' => $user->foto ?? null,
            ]
        ]);
    }

    // ============================
    // CREATE
    // ============================
    public function create(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|max:100',
            'email' => 'required|email|unique:pengguna,email',
            'no_telepon' => 'required|max:15',
            'nama_pengguna' => 'required|max:50|unique:pengguna,nama_pengguna',
            'kata_sandi' => 'required|min:6',
            'role' => 'required|in:pemilik_toko,karyawan',
        ]);

        $pengguna = Pengguna::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'no_telepon' => $request->no_telepon,
            'nama_pengguna' => $request->nama_pengguna,
            'kata_sandi' => Hash::make($request->kata_sandi),
            'role' => $request->role,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil ditambahkan',
            'data' => $pengguna
        ]);
    }

    // ============================
    // READ ALL
    // ============================
    public function view()
    {
        return response()->json(Pengguna::all());
    }

    // ============================
    // READ BY NAME
    // ============================
    public function show($namaLengkap)
    {
    $pengguna = Pengguna::where('nama_lengkap', 'like', "%$namaLengkap%")->get();

    if ($pengguna->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Pengguna tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => $pengguna
    ]);
}

    // ============================
    // UPDATE
    // ============================
    public function update(Request $request, $id)
    {
        $pengguna = Pengguna::find($id);

        if (!$pengguna) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }

        $pengguna->update($request->except('kata_sandi'));

        if ($request->kata_sandi) {
            $pengguna->kata_sandi = Hash::make($request->kata_sandi);
            $pengguna->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil diperbarui',
            'data' => $pengguna
        ]);
    }

    // ============================
    // DELETE
    // ============================
    public function delete($id)
    {
        $pengguna = Pengguna::find($id);

        if (!$pengguna) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna tidak ditemukan'
            ], 404);
        }

        $pengguna->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil dihapus'
        ]);
    }
}