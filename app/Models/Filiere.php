<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Filiere extends Model
{
    use HasFactory;

    protected $fillable = [
        'niveau_id',
        'code',
        'nom',
        'description',
    ];

    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }

    public function candidats(): HasMany
    {
        return $this->hasMany(CandidatProfile::class);
    }
}
