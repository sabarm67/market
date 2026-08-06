<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Market extends Model
{
    protected $fillable = ['name', 'sub_market'];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }
}
