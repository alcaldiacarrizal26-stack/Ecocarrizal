<?php

namespace App\Http\Controllers\Director\Ambiente;

use App\Http\Controllers\Controller;
use App\Models\Denuncia;
use App\Models\User;
use App\Models\Inspector;
use App\Models\Notificacion;
use Illuminate\Http\Request;

class DenunciaController extends Controller
{
    public function index(Request $request)
    {
        $filtroEstatus = $request->get('estatus', 'todos');

        $query = Denuncia::with(['user', 'inspector']);

        if ($filtroEstatus !== 'todos') {
            $query->where('estatus', $filtroEstatus);
        }

        $denuncias = $query->latest()->paginate(10);

        // Obtener funcionarios activos con sus relaciones (Inspectores y Directores)
        $funcionarios = User::whereHas('role', function ($q) {
                $q->whereIn('nombre', ['director', 'inspector']);
            })
            ->where('is_active', true)
            ->with(['role', 'direccion', 'inspector'])
            ->get();

        $stats = [
            'total_denuncias'  => Denuncia::count(),
            'atendidas'        => Denuncia::where('estatus', 'atendido')->count(),
            'pendientes'       => Denuncia::where('estatus', 'pendiente')->count(),
            'inspecciones_mes' => Denuncia::whereIn('estatus', ['inspector_asignado', 'en_revision'])
                                    ->whereMonth('created_at', now()->month)
                                    ->count(),
        ];

        return view('director.ambiente.denuncias', compact(
            'denuncias',
            'funcionarios',
            'stats',
            'filtroEstatus'
        ));
    }

    /**
     * Asignar un inspector o director a la denuncia.
     */
    public function asignar(Request $request, Denuncia $denuncia)
    {
        $request->validate([
            'user_id' => [
                'required', 
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $valido = User::where('id', $value)
                        ->where('is_active', true)
                        ->whereHas('role', function ($q) {
                            $q->whereIn('nombre', ['director', 'inspector']);
                        })->exists();

                    if (!$valido) {
                        $fail('El funcionario seleccionado debe ser obligatoriamente un Director o un Inspector activo.');
                    }
                },
            ],
        ]);

        $denuncia->inspector_id               = $request->user_id;
        $denuncia->estatus                    = 'inspector_asignado';
        $denuncia->fecha_asignacion_inspector = now();
        $denuncia->save();

        // Notificar al ciudadano que creó la denuncia
        if ($denuncia->user_id) {
            Notificacion::create([
                'user_id'  => $denuncia->user_id,
                'titulo'   => 'Denuncia en Proceso de Verificación',
                'mensaje'  => 'Tu denuncia ambiental ha sido asignada a un funcionario para su inspección y atención.',
                'tipo'     => 'info',
                'link_url' => '#',
            ]);
        }

        return redirect()->back()->with('success', 'Funcionario asignado correctamente a la denuncia.');
    }

    public function rechazar(Request $request, Denuncia $denuncia)
    {
        $request->validate([
            'motivo_rechazo' => 'required|string|max:500',
        ], [
            'motivo_rechazo.required' => 'El motivo del rechazo es obligatorio.',
        ]);

        $denuncia->estatus       = 'desestimado'; // O 'rechazada' según tu ENUM, el código actual usa desestimado
        $denuncia->observaciones = $request->motivo_rechazo;
        $denuncia->fecha_cierre  = now();
        $denuncia->save();

        // Notificar al ciudadano
        if ($denuncia->user_id) {
            Notificacion::create([
                'user_id'  => $denuncia->user_id,
                'titulo'   => 'Denuncia Ambiental Desestimada / Rechazada',
                'mensaje'  => 'Tu denuncia ambiental ha sido rechazada por la Dirección de Ambiente. Motivo: ' . $request->motivo_rechazo,
                'tipo'     => 'danger',
                'link_url' => '#',
            ]);
        }

        return redirect()->back()->with('success', 'La denuncia ha sido rechazada correctamente.');
    }
}