<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Territorio extends Model
{
    protected $table = 'territorios';
    
    protected $fillable = ['name'];

    public function sectores(): HasMany
    {
        return $this->hasMany(Sector::class, 'territorio_id');
    }
}