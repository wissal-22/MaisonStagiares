<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurationRepas extends Model
{
    use HasFactory;

    protected $table = 'restauration_repas';

    protected $fillable = [
        'internat_id',
        'date_repas',
        'type_repas',
        'effectif_prevu',
        'effectif_reel',
        'regimes_speciaux_count',
        'cout_unitaire_dh',
        'cout_total_dh',
        'menu_description',
        'bon_commande_ref',
        'statut',
        'enregistre_par_user_id',
    ];

    protected function casts(): array
    {
        return [
            'date_repas' => 'date',
            'effectif_prevu' => 'integer',
            'effectif_reel' => 'integer',
            'regimes_speciaux_count' => 'integer',
            'cout_unitaire_dh' => 'decimal:2',
            'cout_total_dh' => 'decimal:2',
        ];
    }

    public function internat(): BelongsTo
    {
        return $this->belongsTo(Internat::class);
    }

    public function enregistrePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enregistre_par_user_id');
    }

    public function getTypeRepasLabelAttribute(): string
    {
        return match ($this->type_repas) {
            'petit_dejeuner' => 'Petit-déjeuner',
            'dejeuner' => 'Déjeuner',
            'diner' => 'Dîner',
            default => ucfirst($this->type_repas),
        };
    }

    public function getEcartAttribute(): int
    {
        return $this->effectif_reel - $this->effectif_prevu;
    }
}
