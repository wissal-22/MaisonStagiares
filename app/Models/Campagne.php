<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campagne extends Model
{
    use HasFactory;

    protected $fillable = [
        'annee_scolaire_id',
        'internat_id',
        'titre',
        'date_debut',
        'date_fin',
        'statut',
        'instructions',
        'quota_places',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'datetime',
            'date_fin' => 'datetime',
            'quota_places' => 'integer',
        ];
    }

    public function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class);
    }

    public function internat(): BelongsTo
    {
        return $this->belongsTo(Internat::class);
    }

    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class);
    }

    public function isOuverte(): bool
    {
        $now = now();

        return $this->statut === 'ouverte'
            && $this->date_debut <= $now
            && $this->date_fin >= $now;
    }
}
