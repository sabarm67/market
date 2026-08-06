<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlertRule extends Model
{
    protected $fillable = ['watchlist_item_id', 'type', 'direction', 'threshold', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function watchlistItem(): BelongsTo
    {
        return $this->belongsTo(WatchlistItem::class);
    }

    public function triggers(): HasMany
    {
        return $this->hasMany(AlertTrigger::class);
    }
}
