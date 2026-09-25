<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'telephone',
        'region_id',
        'etablissement_id',
        'internat_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function candidatProfile(): HasOne
    {
        return $this->hasOne(CandidatProfile::class, 'user_id');
    }

    public function etablissement(): BelongsTo
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function internat(): BelongsTo
    {
        return $this->belongsTo(Internat::class);
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function dossiers(): HasMany
    {
        return $this->hasMany(Dossier::class, 'candidat_id');
    }

    public function incidentReclamations(): HasMany
    {
        return $this->hasMany(IncidentReclamation::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isGestionnaire(): bool
    {
        return $this->role === 'gestionnaire';
    }

    public function isDirecteur(): bool
    {
        return $this->role === 'directeur';
    }

    public function isDirectionRegionale(): bool
    {
        return $this->role === 'direction_regionale';
    }

    public function isDirectionCentrale(): bool
    {
        return $this->role === 'direction_centrale';
    }

    public function isCandidat(): bool
    {
        return $this->role === 'candidat';
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Administrateur Système',
            'gestionnaire' => "Gestionnaire d'Internat",
            'directeur' => "Directeur d'Établissement / Pédagogique",
            'direction_regionale' => 'Direction Régionale',
            'direction_centrale' => 'Direction Centrale OFPPT',
            'candidat' => 'Candidat / Stagiaire',
            default => ucfirst($this->role),
        };
    }
}
