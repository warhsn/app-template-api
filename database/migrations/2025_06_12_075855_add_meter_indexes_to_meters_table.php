<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('meters', function (Blueprint $table) {
            $table->index(['serial_number'], 'idx_meters_serial_number_asc');
        });
    }

    public function down()
    {
        Schema::table('meters', function (Blueprint $table) {
            $table->dropIndex('idx_meters_serial_number_asc');
        });
    }
};
