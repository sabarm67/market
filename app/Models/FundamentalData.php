<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundamentalData extends Model
{
    public $timestamps = false;

    protected $table = 'fundamental_data';

    protected $fillable = [
        'company_id', 'period_type', 'period_end', 'revenue', 'net_profit', 'eps',
        'book_value_per_share', 'roe', 'roa', 'debt_equity', 'current_ratio',
        'dividend_per_share', 'ingested_at',
    ];

    protected function casts(): array
    {
        return [
            'period_end' => 'date',
            'ingested_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
