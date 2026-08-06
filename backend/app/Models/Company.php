<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    protected $fillable = [
        'market_id',
        'sector_id',
        'name',
        'stock_code',
        'overview',
        'business_segments',
        'listing_date',
        'management',
        'major_shareholders',
    ];

    protected function casts(): array
    {
        return [
            'listing_date' => 'date',
            'management' => 'array',
            'major_shareholders' => 'array',
        ];
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    public function security(): HasOne
    {
        return $this->hasOne(Security::class);
    }

    public function fundamentalData(): HasMany
    {
        return $this->hasMany(FundamentalData::class);
    }
}
