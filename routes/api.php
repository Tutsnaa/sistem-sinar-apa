<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenggunaController;

// Login API
Route::post('/login', [PenggunaController::class, 'login']);

// CRUD Pengguna
Route::get('/pengguna', [PenggunaController::class, 'index']);
Route::get('/pengguna/{id}', [PenggunaController::class, 'show']);
Route::post('/pengguna', [PenggunaController::class, 'store']);
Route::put('/pengguna/{id}', [PenggunaController::class, 'update']);
Route::delete('/pengguna/{id}', [PenggunaController::class, 'destroy']);