<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'nom',
        'description',
        'est_obligatoire',
        'formats_acceptes',
        'taille_max_ko',
    ];

    protected function casts(): array
    {
        return [
            'est_obligatoire' => 'boolean',
            'taille_max_ko' => 'integer',
        ];
    }

    public function dossierDocs(): HasMany
    {
        return $this->hasMany(DossierDoc::class);
    }
}
