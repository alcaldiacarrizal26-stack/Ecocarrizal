namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Direccion extends Model
{
    protected $table = 'direcciones';
    protected $primaryKey = 'id_direccion';
    public $timestamps = false;

    protected $fillable = ['id_direccion', 'nombre_direc', 'descripcion_direc', 'created_at'];

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class, 'direction_id', 'id_direccion');
    }
}