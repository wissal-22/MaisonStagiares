<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chambre extends Model
{
    use HasFactory;

    protected $fillable = [
        'batiment_id',
        'numero',
        'etage',
        'capacite',
        'type',
        'statut',
        'equipements',
    ];

    public function batiment(): BelongsTo
    {
        return $this->belongsTo(Batiment::class);
    }

    public function lits(): HasMany
    {
        return $this->hasMany(Lit::class);
    }

    public function incidentReclamations(): HasMany
    {
        return $this->hasMany(IncidentReclamation::class);
    }

    public function getLitsOccupesCountAttribute(): int
    {
        return $this->lits()->where('statut', 'occupe')->count();
    }

    public function getLitsLibresCountAttribute(): int
    {
        return $this->lits()->where('statut', 'libre')->count();
    }
}
