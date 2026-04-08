<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    
    // Admin Routes
    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function() {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::resource('areas', \App\Http\Controllers\Admin\AreaController::class);
        Route::resource('tarifs', \App\Http\Controllers\Admin\TarifController::class);
        Route::get('logs', [\App\Http\Controllers\Admin\LogController::class, 'index'])->name('logs.index');
        Route::get('pengaturan', [\App\Http\Controllers\Admin\PengaturanController::class, 'index'])->name('pengaturan.index');
        Route::put('pengaturan', [\App\Http\Controllers\Admin\PengaturanController::class, 'update'])->name('pengaturan.update');
    });

    // Kendaraan (Admin & Petugas)
    Route::middleware('can:manage-kendaraan')->prefix('admin')->name('admin.')->group(function() {
        Route::resource('kendaraan', \App\Http\Controllers\Admin\KendaraanController::class);
    });

    // Admin & Petugas (Reports)
    Route::middleware('can:view-reports')->group(function() {
        Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    });

    // Admin Only (Logs)
    Route::middleware('can:admin')->group(function() {
        Route::get('/logs', [\App\Http\Controllers\Admin\LogController::class, 'index'])->name('admin.logs.index');
    });

    // Transaksi Petugas & Admin
    Route::middleware('can:manage-transaksi')->prefix('transaksi')->name('transaksi.')->group(function() {
        Route::get('/masuk', [\App\Http\Controllers\TransaksiController::class, 'masuk'])->name('masuk');
        Route::post('/masuk', [\App\Http\Controllers\TransaksiController::class, 'storeMasuk'])->name('storeMasuk');
        Route::get('/keluar', [\App\Http\Controllers\TransaksiController::class, 'keluar'])->name('keluar');
        Route::post('/keluar/{id_parkir}', [\App\Http\Controllers\TransaksiController::class, 'prosesKeluar'])->name('prosesKeluar');
        Route::get('/struk/{id_transaksi}', [\App\Http\Controllers\TransaksiController::class, 'cetakStruk'])->name('cetakStruk');
    });

    // Midtrans Snap Token
    Route::post('/midtrans/token/{id_parkir}', [\App\Http\Controllers\MidtransController::class, 'getToken'])->name('midtrans.token');
    Route::post('/midtrans/finish/{id_parkir}', [\App\Http\Controllers\MidtransController::class, 'finishTransaction'])->name('midtrans.finish');
});

// Midtrans Callback (External)
Route::post('/midtrans/callback', [\App\Http\Controllers\MidtransController::class, 'callback'])->name('midtrans.callback');
