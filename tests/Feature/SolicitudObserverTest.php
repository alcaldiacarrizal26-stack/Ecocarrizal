<?php

namespace Tests\Feature;

use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SolicitudObserverTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_asigna_fecha_inicio_inspeccion_al_cambiar_estado_a_en_inspeccion(): void
    {
        $solicitud = Solicitud::factory()->create([
            'user_id' => $this->user->id,
            'estado' => 'pendiente',
            'fecha_inicio_inspeccion' => null,
        ]);

        $solicitud->update(['estado' => 'en_inspeccion']);

        $this->assertNotNull($solicitud->fresh()->fecha_inicio_inspeccion);
    }

    #[DataProvider('estadosFinalesProvider')]
    public function test_asigna_fecha_cierre_al_cambiar_a_estado_final(string $estadoFinal): void
    {
        $solicitud = Solicitud::factory()->create([
            'user_id' => $this->user->id,
            'estado' => 'en_inspeccion',
            'fecha_cierre' => null,
        ]);

        $solicitud->update(['estado' => $estadoFinal]);

        $this->assertNotNull($solicitud->fresh()->fecha_cierre);
    }

    public static function estadosFinalesProvider(): array
    {
        return [
            ['aprobada'],
            ['rechazada'],
        ];
    }
}