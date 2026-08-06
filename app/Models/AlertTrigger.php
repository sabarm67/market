<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertTrigger extends Model
{
    public $timestamps = false;

    protected $fillable = ['alert_rule_id', 'trigger_date', 'message', 'notified_at', 'read_at'];

    protected function casts(): array
    {
        return [
            'trigger_date' => 'date',
            'notified_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }

    public function alertRule(): BelongsTo
    {
        return $this->belongsTo(AlertRule::class);
    }
}
