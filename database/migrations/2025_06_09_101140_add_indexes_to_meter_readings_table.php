<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Remove CONCURRENTLY for migration - it will still be fast enough
        DB::statement('CREATE INDEX IF NOT EXISTS idx_meter_readings_daily_lookup 
                      ON meter_readings (meter_id, obis_code, read_at) 
                      WHERE obis_code IN (\'1.1.1.8.0.255\', \'1.1.2.8.0.255\', \'8.1.1.0.0.255\')');

        // Date-based index for filtering
        DB::statement('CREATE INDEX IF NOT EXISTS idx_meter_readings_date 
                      ON meter_readings (DATE(read_at), meter_id, obis_code) 
                      WHERE obis_code IN (\'1.1.1.8.0.255\', \'1.1.2.8.0.255\', \'8.1.1.0.0.255\')');

        // Index for reverse chronological ordering
        DB::statement('CREATE INDEX IF NOT EXISTS idx_meter_readings_reverse_chrono 
                      ON meter_readings (meter_id, obis_code, read_at DESC) 
                      WHERE obis_code IN (\'1.1.1.8.0.255\', \'1.1.2.8.0.255\', \'8.1.1.0.0.255\')');
    }

    public function down()
    {
        DB::statement('DROP INDEX IF EXISTS idx_meter_readings_daily_lookup');
        DB::statement('DROP INDEX IF EXISTS idx_meter_readings_date');
        DB::statement('DROP INDEX IF EXISTS idx_meter_readings_reverse_chrono');
    }
};
