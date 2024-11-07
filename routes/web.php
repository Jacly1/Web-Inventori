<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasukBahanController;
use App\Http\Controllers\MasukKemasanController;
use App\Http\Controllers\MasukProdukController;
use App\Http\Controllers\KeluarProdukController;
use App\Http\Controllers\KeluarBahanController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StokBahanController;
use App\Http\Controllers\StokKemasanController;
use App\Http\Controllers\StokProdukController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LaporanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendefinisikan route untuk aplikasi Anda. Route ini
| di-load oleh RouteServiceProvider dan semua route masuk ke group "web".
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route untuk autentikasi
Auth::routes();

// Route dengan middleware "auth" agar hanya pengguna yang login dapat mengaksesnya
Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::resources([
        'dashboard' => DashboardController::class,
        'roles' => RoleController::class,
        'users' => UserController::class,
        'stok' => StokController::class,
        'stokbahan' => StokBahanController::class,
        'stokkemasan' => StokKemasanController::class,
        'stokproduk' => StokProdukController::class,
        'masukbahan' => MasukBahanController::class,
        'masukkemasan' => MasukKemasanController::class,
        'masukproduk' => MasukProdukController::class,
        'keluarproduk' => KeluarProdukController::class,
        'keluarbahan' => KeluarBahanController::class,
        // 'laporan' => LaporanController::class
    ]);

    Route::prefix('laporan')->group(function () {
        Route::get('penggunaan', [LaporanController::class, 'laporanPenggunaan'])->name('laporan.penggunaan');
        Route::get('penggunaan/pdf', [LaporanController::class, 'laporanPenggunaanPdf'])->name('laporan.penggunaan.pdf');
        Route::get('penggunaan/excel', [LaporanController::class, 'laporanPenggunaanExcel'])->name('laporan.penggunaan.excel');
        Route::get('laporan/stok', [LaporanController::class, 'laporanStok'])->name('laporan.stok');
        Route::get('laporan/stok/pdf', [LaporanController::class, 'laporanStokPdf'])->name('laporan.stok.pdf');
        Route::get('laporan/stok/excel', [LaporanController::class, 'laporanStokExcel'])->name('laporan.stok.excel');
        Route::get('kartu_stok', [LaporanController::class, 'laporanKartuStok'])->name('laporan.kartu_stok');
        Route::get('kartu_stok/pdf', [LaporanController::class, 'laporanKartuStokPdf'])->name('laporan.kartu_stok.pdf');
    });




});
