<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = [
        'internat_id',
        'dossier_id',
        'date_presence',
        'type_controle',
        'statut',
        'remarque',
        'pointe_par_user_id',
    ];

    protected function casts(): array
    {
        return [
            'date_presence' => 'date',
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

    public function pointePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pointe_par_user_id');
    }

    public function getStatutLabelAttribute(): string
    {
        return match ($this->statut) {
            'present' => 'Présent',
            'absent_justifie' => 'Absent justifié',
            'absent_injustifie' => 'Absent non-justifié',
            'permission' => 'En permission autorisée',
            default => ucfirst($this->statut),
        };
    }
}
