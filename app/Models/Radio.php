<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Radio extends Model
{
    protected $fillable = [
        'serial_number',
    ];

    public function meters(): HasMany
    {
        return $this->hasMany(Meter::class);
    }
}
