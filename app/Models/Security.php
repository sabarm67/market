<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Security extends Model
{
    protected $fillable = ['company_id', 'ticker', 'type'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function priceData(): HasMany
    {
        return $this->hasMany(PriceData::class);
    }

    public function shariahStatuses(): HasMany
    {
        return $this->hasMany(ShariahStatus::class)->orderByDesc('source_publication_date');
    }

    /** Current status = the row with the latest source_publication_date, per erd.md notes. */
    public function currentShariahStatus(): HasOne
    {
        return $this->hasOne(ShariahStatus::class)->ofMany('source_publication_date', 'max');
    }

    /** Latest trading day's price row, per security. */
    public function latestPrice(): HasOne
    {
        return $this->hasOne(PriceData::class)->ofMany('trade_date', 'max');
    }

    public function watchlistItems(): HasMany
    {
        return $this->hasMany(WatchlistItem::class);
    }
}
