<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;

Route::get('/', [AuthController::class, 'index'])->name('login');

Route::get('/home', [DashboardController::class, 'index'])->name('/');
Route::get('/users', [DashboardController::class, 'users'])->name('users.index');
Route::get('/buku', [DashboardController::class, 'buku'])->name('buku.index');
Route::get('/peminjaman', [DashboardController::class, 'peminjaman'])->name('peminjaman.index');
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/check-auth', function () {
        return auth()->guard('sanctum')->user();
    });
});


