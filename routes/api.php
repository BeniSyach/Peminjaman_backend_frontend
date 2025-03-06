<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('post.login');

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile']);

    // buku
    Route::apiResource('buku', BukuController::class);

    // Peminjaman
    Route::apiResource('peminjaman', PeminjamanController::class);
    Route::get('peminjaman/pengembalian/{id}', [PeminjamanController::class, 'returnBook']);

    // User
    Route::apiResource('users', UserController::class);
});
