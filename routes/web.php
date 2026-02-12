<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StokController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [StokController::class, 'index']);
Route::get('/maintenance-stok', [StokController::class, 'index']);


Route::post('/store', [StokController::class, 'store'])->name('stok.store');


Route::get('/get-nama-barang/{kode}', [StokController::class, 'getNamaBarang']);


Route::get('/report-saldo', [StokController::class, 'reportSaldo']);
// Route untuk menampilkan halaman tabelnya
Route::get('/report-history', [StokController::class, 'reportHistory']);

// Route khusus untuk mengambil data JSON (API) yang dipanggil AJAX
Route::get('/api/report-history', [StokController::class, 'apiHistory']);