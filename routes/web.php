<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Ciudadano\DashboardController;
use App\Http\Controllers\Ciudadano\TramiteController;
use Illuminate\Support\Facades\Route;

// Vista principal
Route::get('/', [AuthController::class, 'showHome'])->name('home');

// Rutas de autenticación
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:ciudadano'])->prefix('ciudadano')->name('ciudadano.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('tramites', TramiteController::class)->except(['destroy']);
});