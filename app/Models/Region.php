<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'nom',
        'chef_lieu',
    ];

    public function etablissements(): HasMany
    {
        return $this->hasMany(Etablissement::class);
    }
}
