<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentReclamation extends Model
{
    use HasFactory;

    protected $fillable = [
        'internat_id',
        'dossier_id',
        'user_id',
        'type',
        'priorite',
        'titre',
        'description',
        'chambre_id',
        'statut',
        'reponse_gestionnaire',
        'traite_par_user_id',
        'date_resolution',
    ];

    protected function casts(): array
    {
        return [
            'date_resolution' => 'datetime',
        ];
    }

    public function internat(): BelongsTo
    {
        return $this->belongsTo(Internat::class);
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chambre(): BelongsTo
    {
        return $this->belongsTo(Chambre::class);
    }

    public function traitePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'traite_par_user_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'reclamation' => 'Réclamation / Recours',
            'incident_discipline' => 'Incident disciplinaire',
            'maintenance_chambre' => 'Demande de maintenance',
            default => ucfirst($this->type),
        };
    }
}
