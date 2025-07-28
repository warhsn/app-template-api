<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\Meter;
use App\Models\MeterReading;
use App\Models\MeterType;
use App\Models\Price;
use App\Models\Product;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Passport::enablePasswordGrant();
        DB::prohibitDestructiveCommands($this->app->isProduction());

        Relation::enforceMorphMap([
            'meter' => Meter::class,
            'meter_type' => MeterType::class,
            'user' => User::class,
            'property' => Property::class,
            'invoice' => Invoice::class,
            'price' => Price::class,
            'product' => Product::class,
            'document' => Document::class,
            'customer' => Customer::class,
            'product' => Product::class,
            'meter_reading' => MeterReading::class,
        ]);
    }
}
