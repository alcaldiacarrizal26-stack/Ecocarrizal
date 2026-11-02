<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\DeclaracionResiduo;
use App\Models\Solicitud;
use App\Models\Denuncia;
use App\Models\EntregaCompensacion;
use App\Models\Empresa;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Compartir contadores automáticamente en todas las vistas que comiencen con el prefijo director.*
        View::composer('director.*', function ($view) {
            $view->with([
                'countDeclaraciones'  => DeclaracionResiduo::where('estatus', 'PRESENTADA')->count(),
                'countSolicitudes'    => Solicitud::where('estado', 'pendiente')->count(),
                'countDenuncias'      => Denuncia::where('estatus', 'pendiente')->count(),
                'countCompensaciones' => EntregaCompensacion::where('estatus', 'PENDIENTE')->count(),
                'countEmpresas'       => Empresa::where('estatus', 'PENDIENTE_APROBACION')->count(),
            ]);
        });
    }
}