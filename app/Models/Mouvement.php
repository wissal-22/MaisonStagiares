<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mouvement extends Model
{
    use HasFactory;

    protected $fillable = [
        'dossier_id',
        'internat_id',
        'type',
        'date_mouvement',
        'ancien_lit_id',
        'nouveau_lit_id',
        'motif',
        'enregistre_par_user_id',
    ];

    protected function casts(): array
    {
        return [
            'date_mouvement' => 'datetime',
        ];
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function internat(): BelongsTo
    {
        return $this->belongsTo(Internat::class);
    }

    public function ancienLit(): BelongsTo
    {
        return $this->belongsTo(Lit::class, 'ancien_lit_id');
    }

    public function nouveauLit(): BelongsTo
    {
        return $this->belongsTo(Lit::class, 'nouveau_lit_id');
    }

    public function enregistrePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enregistre_par_user_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'arrivee' => 'Arrivée (Check-in)',
            'depart' => 'Départ définitif (Check-out)',
            'changement_chambre' => 'Changement de chambre / lit',
            'absence_prolongee' => 'Absence prolongée signalée',
            'exclusion' => 'Exclusion disciplinaire',
            default => ucfirst($this->type),
        };
    }
}
