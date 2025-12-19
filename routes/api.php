<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BarangMasukController;

// Login API
Route::post('/login', [PenggunaController::class, 'login']);

// CRUD Pengguna

Route::get('/pengguna', [PenggunaController::class, 'view']);
Route::get('/pengguna/{id}', [PenggunaController::class, 'show']);
Route::post('/pengguna', [PenggunaController::class, 'create']);
Route::put('/pengguna/{id}', [PenggunaController::class, 'update']);
Route::delete('/pengguna/{id}', [PenggunaController::class, 'delete']);

// Karyawan
Route::get('/karyawan', [PenggunaController::class, 'viewKaryawan']);
Route::post('/karyawan', [PenggunaController::class, 'create']);
Route::put('/karyawan/{id}', [PenggunaController::class, 'update']); // <-- ini ditambahkan
Route::delete('/karyawan/{id}', [PenggunaController::class, 'delete']);


//CRUD Barang
Route::get('/barang', [BarangController::class, 'view']);
Route::post('/barang', [BarangController::class, 'create']);
Route::get('/barang/{id}', [BarangController::class, 'show']);
Route::put('/barang/{id}', [BarangController::class, 'update']);
Route::patch('/barang/{id}', [BarangController::class, 'update']);
Route::delete('/barang/{id}', [BarangController::class, 'delete']);

//CRUD Penjualan
Route::get('/penjualan', [PenjualanController::class, 'view']);
Route::post('/penjualan', [PenjualanController::class, 'create']);
Route::get('/penjualan/{id}', [PenjualanController::class, 'show']);
Route::put('/penjualan/{id}', [PenjualanController::class, 'update']);
Route::patch('/penjualan/{id}', [PenjualanController::class, 'update']);
Route::delete('/penjualan/{id}', [PenjualanController::class, 'delete']);

//CRUD Kategori
Route::get('/kategori', [KategoriController::class, 'view']);        
Route::post('/kategori', [KategoriController::class, 'create']);     
Route::get('/kategori/{param}', [KategoriController::class, 'show']);
Route::put('/kategori/{id}', [KategoriController::class, 'update']); 
Route::delete('/kategori/{id}', [KategoriController::class, 'delete']);

//CRUD Barang Masuk
Route::get('/barang-masuk', [BarangMasukController::class, 'view']);
Route::post('/barang-masuk', [BarangMasukController::class, 'create']);
Route::get('/barang-masuk/{id}', [BarangMasukController::class, 'show']);
Route::put('/barang-masuk/{id}', [BarangMasukController::class, 'update']);
Route::delete('/barang-masuk/{id}', [BarangMasukController::class, 'delete']);