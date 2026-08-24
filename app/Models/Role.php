namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $primaryKey = 'id_roles';
    public $timestamps = false;

    protected $fillable = ['id_roles', 'nombre', 'descripcion', 'created_at'];

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class, 'role_id', 'id_roles');
    }
}