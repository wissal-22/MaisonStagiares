<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnneeScolaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'date_debut',
        'date_fin',
        'est_active',
    ];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'est_active' => 'boolean',
        ];
    }

    public function campagnes(): HasMany
    {
        return $this->hasMany(Campagne::class);
    }
}
