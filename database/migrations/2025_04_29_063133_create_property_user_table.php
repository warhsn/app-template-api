<?php

use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_user', function (Blueprint $table) {
            $table->foreignIdFor(Property::class)->constrained();
            $table->foreignIdFor(User::class)->constrained();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_user');
    }
};
