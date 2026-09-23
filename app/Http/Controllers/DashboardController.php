<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Despachador principal (/dashboard)
     */
    public function index()
    {
        $user = Auth::user();

        $rolNombre = strtolower($user->role->nombre ?? '');
        $roleId = (int) $user->role_id;
        $direccionId = (int) $user->direccion_id;

        // 1. Rol: Director (role_id = 2 o equivalente)
        if ($roleId === 2 || in_array($rolNombre, ['director', 'director_ambiente', 'director_residuos'])) {
            
            // Dirección de Residuos
            if ($direccionId === 2 || $rolNombre === 'director_residuos') {
                return redirect()->route('director.residuos.dashboard');
            }

            // Dirección de Ambiente
            return redirect()->route('director.ambiente.dashboard');
        }

        // 2. Rol: Inspector Unificado (role_id = 3)
        if ($roleId === 3 || $rolNombre === 'inspector') {
            return redirect()->route('inspector.dashboard');
        }

        // 3. Rol: Ciudadano (role_id = 4)
        if ($roleId === 4 || $rolNombre === 'ciudadano') {
            return redirect()->route('ciudadano.dashboard');
        }

        // 4. Admin o fallback general
        return redirect()->route('admin.dashboard');
    }

    /**
     * Método final para renderizar el Dashboard de Residuos
     */
    public function dashboardResiduos()
    {
        $user = Auth::user();

        $stats = [
            'pendientes' => Empresa::where('estatus', 'PENDIENTE_APROBACION')->count(),
            'aprobadas'  => Empresa::where('estatus', 'APROBADO')->count(),
            'rechazadas' => Empresa::where('estatus', 'RECHAZADO')->count(),
            'total'      => Empresa::count(),
        ];

        $empresasPendientes = Empresa::with(['user', 'perfilAmbiental'])
            ->where('estatus', 'PENDIENTE_APROBACION')
            ->latest()
            ->get();

        return view('director.residuos.dashboard', compact('stats', 'empresasPendientes', 'user'));
    }
}