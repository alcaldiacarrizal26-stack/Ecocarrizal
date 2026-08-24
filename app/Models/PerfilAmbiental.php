namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerfilAmbiental extends Model
{
    use HasUuids;

    protected $table = 'perfiles_ambientales';

    protected $fillable = [
        'empresa_id',
        'tiene_renma',
        'tipo_racda',
        'numero_racda',
        'genera_efluentes',
        'genera_emisiones',
        'genera_ruidos',
        'usa_pozo_agua',
        'residuos_comunes',
        'residuos_reciclables',
        'residuos_peligrosos',
    ];

    protected $casts = [
        'tiene_renma' => 'boolean',
        'genera_efluentes' => 'boolean',
        'genera_emisiones' => 'boolean',
        'genera_ruidos' => 'boolean',
        'usa_pozo_agua' => 'boolean',
        'residuos_comunes' => 'array',
        'residuos_reciclables' => 'array',
        'residuos_peligrosos' => 'array',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}