<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DossierDoc extends Model
{
    use HasFactory;

    protected $fillable = [
        'dossier_id',
        'doc_type_id',
        'file_path',
        'original_name',
        'is_valid',
        'admin_message',
        'verified_by_user_id',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'is_valid' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(Dossier::class);
    }

    public function docType(): BelongsTo
    {
        return $this->belongsTo(DocType::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }
}
