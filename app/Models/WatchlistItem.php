<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchlistItem extends Model
{
    public $timestamps = false;

    protected $fillable = ['watchlist_id', 'security_id', 'note', 'added_at'];

    protected function casts(): array
    {
        return [
            'added_at' => 'datetime',
        ];
    }

    public function watchlist(): BelongsTo
    {
        return $this->belongsTo(Watchlist::class);
    }

    public function security(): BelongsTo
    {
        return $this->belongsTo(Security::class);
    }
}
