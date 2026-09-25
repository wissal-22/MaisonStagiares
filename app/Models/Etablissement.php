<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etablissement extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'code',
        'nom',
        'ville',
        'adresse',
        'telephone',
        'email',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function internats(): HasMany
    {
        return $this->hasMany(Internat::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
