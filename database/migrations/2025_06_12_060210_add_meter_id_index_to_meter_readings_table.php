<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Create a covering index that includes all needed columns
        DB::statement('
            CREATE INDEX idx_meter_readings_covering 
            ON meter_readings (meter_id, read_at DESC) 
            WHERE deleted_at IS NULL
        ');
    }

    public function down()
    {
        DB::statement('DROP INDEX IF EXISTS idx_meter_readings_covering');
    }
};
