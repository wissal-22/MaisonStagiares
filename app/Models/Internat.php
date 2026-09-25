<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Internat extends Model
{
    use HasFactory;

    protected $fillable = [
        'etablissement_id',
        'nom',
        'capacite_totale',
        'capacite_garcons',
        'capacite_filles',
        'description',
        'reglement_interieur',
        'date_arrivee_prevue',
        'telephone',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'date_arrivee_prevue' => 'date',
        ];
    }

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function batiments(): HasMany
    {
        return $this->hasMany(Batiment::class);
    }

    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class);
    }

    public function mouvements(): HasMany
    {
        return $this->hasMany(Mouvement::class);
    }

    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    public function repas(): HasMany
    {
        return $this->hasMany(RestaurationRepas::class);
    }

    public function critereSelections(): HasMany
    {
        return $this->hasMany(CritereSelection::class);
    }

    public function incidentReclamations(): HasMany
    {
        return $this->hasMany(IncidentReclamation::class);
    }

    /**
     * Get all rooms in this internat through batiments.
     */
    public function chambres(): HasManyThrough
    {
        return $this->hasManyThrough(Chambre::class, Batiment::class);
    }

    public function getPlacesOccupeesAttribute(): int
    {
        return $this->dossiers()->where('statut', 'admis')->whereNotNull('lit_id')->count();
    }

    public function getPlacesDisponiblesAttribute(): int
    {
        return max(0, $this->capacite_totale - $this->places_occupees);
    }

    public function getTauxOccupationAttribute(): float
    {
        if ($this->capacite_totale <= 0) {
            return 0.0;
        }

        return round(($this->places_occupees / $this->capacite_totale) * 100, 1);
    }
}
