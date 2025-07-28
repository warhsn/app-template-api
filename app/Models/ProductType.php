<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductType extends Model
{
    use HasFactory, SoftDeletes;

    public const ONCE_OFF = 'Once Off';

    public const RECURRING = 'Recurring Service';

    public const RECURRING_TIERED = 'Tiered Recurring Service';

    public const QUOTA = 'Quota Based Service';

    protected $fillable = [
        'name',
        'recurring',
        'tiered',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
