<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Lit extends Model
{
    use HasFactory;

    protected $fillable = [
        'chambre_id',
        'code',
        'position',
        'statut',
    ];

    public function chambre(): BelongsTo
    {
        return $this->belongsTo(Chambre::class);
    }

    public function dossierActif(): HasOne
    {
        return $this->hasOne(Dossier::class, 'lit_id')->where('statut', 'admis');
    }
}
