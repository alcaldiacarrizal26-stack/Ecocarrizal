<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Rutas temporales para evitar el RouteNotFoundException hasta definir los controladores finales
Route::post('/login', function () {
    return back();
})->name('login');

Route::post('/register', function () {
    return back();
})->name('register');