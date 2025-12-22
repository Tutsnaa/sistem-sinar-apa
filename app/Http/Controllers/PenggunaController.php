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
                    'foto' => $user->foto,
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
            'foto' => 'nullable|image|max:2048',
            'role' => 'required|in:pemilik_toko,karyawan',
        ]);

         $namaFile = null;

    if ($request->hasFile('foto')) {
        $file = $request->file('foto');
        $namaFile = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('foto_pengguna'), $namaFile);
    }

        $pengguna = Pengguna::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'no_telepon' => $request->no_telepon,
            'nama_pengguna' => $request->nama_pengguna,
            'kata_sandi' => bcrypt($request->kata_sandi),
            'role' => $request->role,
            'foto' => $namaFile
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

    public function detail($id)
{
    $pengguna = Pengguna::find($id);

    if (!$pengguna) {
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
// READ KARYAWAN
// ============================
public function viewKaryawan(Request $request)
{
    $query = Pengguna::where('role', 'karyawan');

    if ($request->has('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('nama_lengkap', 'like', "%$search%")
              ->orWhere('email', 'like', "%$search%")
              ->orWhere('nama_pengguna', 'like', "%$search%");
        });
    }

    $karyawan = $query->orderBy('nama_lengkap', 'asc')->get();

    return response()->json([
        'success' => true,
        'data' => $karyawan
    ]);
}

public function show($id)
{
    $pengguna = Pengguna::find($id);

    if (!$pengguna) {
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

//     // ============================
//     // READ BY NAME
//     // ============================
//     public function show($namaLengkap)
//     {
//     $pengguna = Pengguna::where('nama_lengkap', 'like', "%$namaLengkap%")->get();

//     if ($pengguna->isEmpty()) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Pengguna tidak ditemukan'
//         ], 404);
//     }

//     return response()->json([
//         'success' => true,
//         'data' => $pengguna
//     ]);
// }

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

    $request->validate([
        'nama_lengkap' => 'required|max:100',
        'email' => 'required|email|unique:pengguna,email,' . $id,
        'no_telepon' => 'required|max:15',
        'nama_pengguna' => 'required|max:50|unique:pengguna,nama_pengguna,' . $id,
        'kata_sandi' => 'nullable|min:6',
        'foto' => 'nullable|image|max:2048',
    ]);

    // Upload foto jika ada
    if ($request->hasFile('foto')) {
        $file = $request->file('foto');
        $namaFile = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('foto_pengguna'), $namaFile);
        $pengguna->foto = $namaFile;
    }

    $pengguna->nama_lengkap = $request->nama_lengkap;
    $pengguna->email = $request->email;
    $pengguna->no_telepon = $request->no_telepon;
    $pengguna->nama_pengguna = $request->nama_pengguna;

    if ($request->kata_sandi) {
        $pengguna->kata_sandi = Hash::make($request->kata_sandi);
    }

    $pengguna->save();

    return response()->json([
        'success' => true,
        'message' => 'Profil berhasil diperbarui',
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