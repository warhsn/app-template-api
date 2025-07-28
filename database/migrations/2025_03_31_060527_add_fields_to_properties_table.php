<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->boolean('sub_dividable')->nullable()->default(false);
            $table->boolean('water_rights')->nullable()->default(false);
            $table->string('portion_number')->nullable();
            $table->string('original_portion_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn([
                'sub_dividable',
                'water_rights',
                'portion_number',
                'original_portion_number',
            ]);
        });
    }
};
