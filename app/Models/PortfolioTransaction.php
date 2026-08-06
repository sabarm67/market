<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioTransaction extends Model
{
    public $timestamps = false;

    protected $fillable = ['portfolio_id', 'security_id', 'type', 'quantity', 'price', 'transaction_date', 'notes', 'created_at'];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'created_at' => 'datetime',
        ];
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function security(): BelongsTo
    {
        return $this->belongsTo(Security::class);
    }
}
