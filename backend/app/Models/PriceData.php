<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceData extends Model
{
    public $timestamps = false;

    protected $table = 'price_data';

    protected $fillable = [
        'security_id', 'trade_date', 'open', 'high', 'low', 'close', 'volume', 'ingested_at',
    ];

    protected function casts(): array
    {
        return [
            'trade_date' => 'date',
            'ingested_at' => 'datetime',
        ];
    }

    public function security(): BelongsTo
    {
        return $this->belongsTo(Security::class);
    }
}
