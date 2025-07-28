<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meter_readings', function (Blueprint $table) {
            $table->id();
            $table->string('obis_code');
            $table->string('unit');
            $table->foreignId('meter_id')->constrained();
            $table->decimal('value', 30, 5);
            $table->timestamp('read_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meter_readings');
    }
};
