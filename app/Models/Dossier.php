<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dossier extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_dossier',
        'candidat_id',
        'campagne_id',
        'internat_id',
        'date_depot',
        'statut',
        'score_total',
        'details_score',
        'motif_decision',
        'is_valide',
        'valide_par_admin',
        'valide_par_user_id',
        'date_validation',
        'lit_id',
        'date_arrivee_effective',
        'date_depart_effective',
    ];

    protected function casts(): array
    {
        return [
            'date_depot' => 'datetime',
            'date_validation' => 'datetime',
            'date_arrivee_effective' => 'datetime',
            'date_depart_effective' => 'datetime',
            'score_total' => 'decimal:2',
            'details_score' => 'array',
            'is_valide' => 'boolean',
            'valide_par_admin' => 'boolean',
        ];
    }

    public function candidat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'candidat_id');
    }

    public function campagne(): BelongsTo
    {
        return $this->belongsTo(Campagne::class);
    }

    public function internat(): BelongsTo
    {
        return $this->belongsTo(Internat::class);
    }

    public function lit(): BelongsTo
    {
        return $this->belongsTo(Lit::class);
    }

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par_user_id');
    }

    public function docs(): HasMany
    {
        return $this->hasMany(DossierDoc::class);
    }

    public function mouvements(): HasMany
    {
        return $this->hasMany(Mouvement::class);
    }

    public function presences(): HasMany
    {
        return $this->hasMany(Presence::class);
    }

    public function incidentReclamations(): HasMany
    {
        return $this->hasMany(IncidentReclamation::class);
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'brouillon' => 'Brouillon',
            'recu' => 'Reçu',
            'soumis' => 'Soumis',
            'complet' => 'Complet (Vérifié)',
            'incomplet' => 'Pièces Incomplètes',
            'en_commission' => 'En Commission de Sélection',
            'admis' => 'Admis en Internat',
            'liste_attente' => "Sur Liste d'Attente",
            'refuse' => 'Refusé',
            'desiste' => 'Désisté',
            'exclu' => 'Exclu',
            default => ucfirst($this->statut),
        };
    }

    public function getStatutBadgeClassAttribute(): string
    {
        return match ($this->statut) {
            'admis' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800',
            'liste_attente' => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800',
            'refuse', 'exclu' => 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800',
            'complet' => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800',
            'incomplet' => 'bg-orange-100 text-orange-800 border-orange-300 dark:bg-orange-950/40 dark:text-orange-300 dark:border-orange-800',
            'en_commission' => 'bg-purple-100 text-purple-800 border-purple-300 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800',
            default => 'bg-slate-100 text-slate-800 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        };
    }
}
