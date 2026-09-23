<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Denuncia;
use App\Models\Solicitud;
use App\Observers\DenunciaObserver;
use App\Observers\SolicitudObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Denuncia::observe(DenunciaObserver::class);
        Solicitud::observe(SolicitudObserver::class);
    }
}
