<?php

namespace Tests\Feature;

use App\Models\Denuncia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DenunciaObserverTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_asigna_fecha_inicio_inspeccion_al_cambiar_estatus_a_en_inspeccion(): void
    {
        $denuncia = Denuncia::factory()->create([
            'user_id' => $this->user->id,
            'estatus' => 'pendiente',
            'fecha_inicio_inspeccion' => null,
        ]);

        $denuncia->update(['estatus' => 'en_inspeccion']);

        $this->assertNotNull($denuncia->fresh()->fecha_inicio_inspeccion);
    }

    public function test_no_sobrescribe_fecha_inicio_inspeccion_si_ya_estaba_asignada(): void
    {
        $fechaOriginal = now()->subDays(2);

        $denuncia = Denuncia::factory()->create([
            'user_id' => $this->user->id,
            'estatus' => 'en_inspeccion',
            'fecha_inicio_inspeccion' => $fechaOriginal,
        ]);

        $denuncia->update(['estatus' => 'en_revision']);
        $denuncia->update(['estatus' => 'en_inspeccion']);

        $this->assertEquals(
            $fechaOriginal->toDateTimeString(),
            $denuncia->fresh()->fecha_inicio_inspeccion->toDateTimeString()
        );
    }

    #[DataProvider('estatusFinalesProvider')]
    public function test_asigna_fecha_cierre_al_cambiar_a_estatus_final(string $estatusFinal): void
    {
        $denuncia = Denuncia::factory()->create([
            'user_id' => $this->user->id,
            'estatus' => 'en_inspeccion',
            'fecha_cierre' => null,
        ]);

        $denuncia->update(['estatus' => $estatusFinal]);

        $this->assertNotNull($denuncia->fresh()->fecha_cierre);
    }

    public static function estatusFinalesProvider(): array
    {
        return [
            ['atendido'],
            ['desestimado'],
        ];
    }
}