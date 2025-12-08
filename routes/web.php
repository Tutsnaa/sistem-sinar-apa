<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenggunaController;

// POST login menuju Laravel
Route::post('/login', [PenggunaController::class, 'login'])->name('login.process');

// Logout
Route::get('/logout', [PenggunaController::class, 'logout'])->name('logout');

// Semua halaman (GET) dikirim ke Vue
Route::get('/{any}', function () {
    return view('app'); // app.blade.php memuat Vue
})->where('any', '.*');