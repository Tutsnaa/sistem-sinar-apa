<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class PenggunaController extends Controller
{
    public function showLogin()
    {
        return view('app'); // atau 'login' jika pakai login.blade
    }

    public function login(Request $request)
    {
        $request->validate([
            'nama_pengguna' => 'required',
            'kata_sandi' => 'required'
        ]);

        $user = Pengguna::where('nama_pengguna', $request->nama_pengguna)->first();

        if (!$user) {
            return back()->with('error', 'Nama pengguna tidak ditemukan');
        }

        if (!Hash::check($request->kata_sandi, $user->kata_sandi)) {
            return back()->with('error', 'Kata sandi salah');
        }

        Session::put('loginId', $user->id);
        Session::put('nama', $user->nama_lengkap);
        Session::put('role', $user->role);

        return response()->json([
            'success' => true,
            'role' => $user->role,
        ]);
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('login');
    }
}