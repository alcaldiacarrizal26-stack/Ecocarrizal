<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Vista principal
Route::get('/', [AuthController::class, 'showHome'])->name('home');

// Rutas de autenticación
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Ruta protegida de ejemplo
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});