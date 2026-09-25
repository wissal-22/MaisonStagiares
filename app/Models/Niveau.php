<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Niveau extends Model
{
    use HasFactory;

    protected $table = 'niveaux';

    protected $fillable = [
        'code',
        'nom',
        'description',
    ];

    public function filieres(): HasMany
    {
        return $this->hasMany(Filiere::class);
    }

    public function candidats(): HasMany
    {
        return $this->hasMany(CandidatProfile::class);
    }
}
