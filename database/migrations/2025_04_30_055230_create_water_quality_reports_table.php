<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('water_quality_reports', function (Blueprint $table) {
            $table->id();
            $table->string('file');
            $table->date('reporting_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('water_quality_reports');
    }
};
