namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Empresa extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'rif',
        'razon_social',
        'nombre_comercial',
        'numero_cuenta_patente',
        'correo_comercial',
        'telefono_comercial',
        'sector',
        'calle_avenida',
        'edificio_galpon_local',
        'punto_referencia',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function perfilAmbiental(): HasOne
    {
        return $this->hasOne(PerfilAmbiental::class);
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(DocumentoExpediente::class);
    }
}