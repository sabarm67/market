<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sector extends Model
{
    protected $fillable = ['name', 'industry'];

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }
}
