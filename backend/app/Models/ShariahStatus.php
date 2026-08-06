<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShariahStatus extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'security_id', 'status', 'source_publication_date', 'imported_at', 'imported_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'source_publication_date' => 'date',
            'imported_at' => 'datetime',
        ];
    }

    public function security(): BelongsTo
    {
        return $this->belongsTo(Security::class);
    }

    public function importedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by_user_id');
    }
}
