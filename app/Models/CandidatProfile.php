<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidatProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cin',
        'is_mineur',
        'cin_tuteur',
        'nom_tuteur',
        'cef',
        'date_naissance',
        'genre',
        'ville_origine',
        'adresse',
        'telephone',
        'distance_km',
        'revenu_mensuel_parents',
        'nombre_freres_soeurs',
        'est_orphelin',
        'en_situation_handicap',
        'niveau_id',
        'filiere_id',
        'annee_precedente_heberge',
        'regime_alimentaire',
        'contact_urgence_nom',
        'contact_urgence_telephone',
    ];

    protected function casts(): array
    {
        return [
            'is_mineur' => 'boolean',
            'est_orphelin' => 'boolean',
            'en_situation_handicap' => 'boolean',
            'annee_precedente_heberge' => 'boolean',
            'date_naissance' => 'date',
            'distance_km' => 'integer',
            'revenu_mensuel_parents' => 'decimal:2',
            'nombre_freres_soeurs' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    public function getAgeAttribute(): int
    {
        return $this->date_naissance ? $this->date_naissance->age : 0;
    }
}
