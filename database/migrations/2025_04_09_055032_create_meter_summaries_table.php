<?php

use App\Models\Meter;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meter_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Meter::class)->constrained();
            $table->string('unit');
            $table->decimal('usage', 30, 5)->nullable()->default(0);
            $table->decimal('cumulative_usage', 30, 5)->nullable()->default(0);
            $table->unsignedBigInteger('cost')->nullable()->default(0);
            $table->date('month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meter_summaries');
    }
};
