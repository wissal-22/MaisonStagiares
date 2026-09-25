<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CritereSelection extends Model
{
    use HasFactory;

    protected $fillable = [
        'internat_id',
        'code',
        'nom',
        'type_critere',
        'points_base',
        'poids',
        'description',
        'est_actif',
    ];

    protected function casts(): array
    {
        return [
            'points_base' => 'integer',
            'poids' => 'decimal:2',
            'est_actif' => 'boolean',
        ];
    }

    public function internat(): BelongsTo
    {
        return $this->belongsTo(Internat::class);
    }
}
