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

        // 1. Rol: Director (role_id = 2)
        if ($user->role_id === 2) {
            
            // Sub-caso: Dirección de Residuos (direccion_id = 2)
            if ($user->direccion_id === 2) {
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

            // Sub-caso: Dirección de Ambiente (direccion_id = 1)
            if ($user->direccion_id === 1) {
                return view('director.ambiente.dashboard', compact('user'));
            }
        }

        // 2. Rol: Inspector (role_id = 3)
        if ($user->role_id === 3) {
            if ($user->direccion_id === 2) {
                return view('inspector.residuos.dashboard', compact('user'));
            }
            return view('inspector.ambiente.dashboard', compact('user'));
        }

        // 3. Rol: Ciudadano (role_id = 4)
        if ($user->role_id === 4) {
            return view('ciudadano.dashboard', compact('user'));
        }

        // 4. Admin o fallback
        return view('dashboard', compact('user'));
    }
}