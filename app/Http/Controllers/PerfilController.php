<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PerfilController extends Controller
{
    /**
     * Muestra la vista de perfil en modo solo lectura para cualquier usuario autenticado.
     */
    public function show()
    {
        $user = auth()->user()->load(['role', 'direccion', 'inspector']);

        return view('perfil.show', compact('user'));
    }
}