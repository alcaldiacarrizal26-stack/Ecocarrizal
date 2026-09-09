<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $rolNombre = strtolower($user->role->nombre ?? '');
        $roleId = (int) $user->role_id;
        $direccionId = (int) $user->direccion_id;

        // 1. Rol: Director (role_id = 2 o nombres equivalentes)
        if ($roleId === 2 || in_array($rolNombre, ['director', 'director_ambiente', 'director_residuos', 'administrador'])) {
            
            // Sub-caso: Dirección de Residuos (direccion_id = 2)
            if ($direccionId === 2 || $rolNombre === 'director_residuos') {
                return redirect()->route('director.residuos.dashboard');
            }

            // Sub-caso: Dirección de Ambiente (direccion_id = 1 o por defecto)
            return redirect()->route('director.ambiente.dashboard');
        }

        // 2. Rol: Inspector (role_id = 3 o nombre 'inspector')
        if ($roleId === 3 || $rolNombre === 'inspector') {
            if ($direccionId === 2) {
                return redirect()->route('inspector.residuos.dashboard');
            }
            return redirect()->route('inspector.ambiente.dashboard');
        }

        // 3. Rol: Ciudadano (role_id = 4 o nombre 'ciudadano')
        if ($roleId === 4 || $rolNombre === 'ciudadano') {
            return redirect()->route('ciudadano.dashboard');
        }

        // 4. Admin o fallback general
        return view('dashboard', compact('user'));
    }
}