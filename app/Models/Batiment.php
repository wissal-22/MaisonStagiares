<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batiment extends Model
{
    use HasFactory;

    protected $fillable = [
        'internat_id',
        'nom',
        'code',
        'genre',
        'nombre_etages',
        'description',
    ];

    public function internat(): BelongsTo
    {
        return $this->belongsTo(Internat::class);
    }

    public function chambres(): HasMany
    {
        return $this->hasMany(Chambre::class);
    }

    public function getCapaciteTotaleAttribute(): int
    {
        return $this->chambres()->sum('capacite');
    }
}
