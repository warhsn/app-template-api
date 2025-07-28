<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Billow\Utilities\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Passport\HasApiTokens;
use Laravel\Scout\Searchable;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class User extends Authenticatable
{
    use Filterable, HasApiTokens, HasRelationships, Searchable;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class);
    }

    public function meters(): HasManyDeep
    {
        return $this->hasManyDeep(Meter::class, ['property_user', Property::class]);
    }

    public function invoices(): HasManyDeep
    {
        return $this->hasManyDeep(Invoice::class, ['property_user', Property::class]);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Customer::class);
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path ? Storage::url($this->profile_photo_path) : null;
    }
}
