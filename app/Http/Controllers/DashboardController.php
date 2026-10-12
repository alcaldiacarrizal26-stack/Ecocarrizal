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
     * Método seguro para renderizar el Dashboard de Residuos
     */
    public function dashboardResiduos()
    {
        $user = Auth::user();
        $rolNombre = strtolower($user->role->nombre ?? '');
        $esAdmin = in_array($rolNombre, ['admin', 'administrador']);
        $esDirectorResiduos = ($user->direccion_id === 2 || $rolNombre === 'director_residuos');

        // Blindaje de seguridad: Solo el admin o el director de residuos pueden ver este panel
        if (!$esAdmin && !$esDirectorResiduos) {
            abort(403, 'No tienes autorización para acceder al panel de la Dirección de Manejo de Residuos.');
        }

        $stats = [
            'pendientes' => Empresa::whereIn('estatus', ['PENDIENTE', 'PENDIENTE_APROBACION'])->count(),
            'aprobadas'  => Empresa::where('estatus', 'APROBADO')->count(),
            'rechazadas' => Empresa::where('estatus', 'RECHAZADO')->count(),
            'total'      => Empresa::count(),
        ];

        $empresasPendientes = Empresa::with(['user', 'perfilAmbiental'])
            ->whereIn('estatus', ['PENDIENTE', 'PENDIENTE_APROBACION'])
            ->latest()
            ->get();

        return view('director.residuos.dashboard', compact('stats', 'empresasPendientes', 'user'));
    }
}