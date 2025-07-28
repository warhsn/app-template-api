<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement('DROP VIEW IF EXISTS daily_meter_readings');

        DB::statement("
            CREATE VIEW daily_meter_readings AS
            WITH first_readings AS (
                SELECT DISTINCT ON (meter_id, obis_code, DATE(read_at))
                    meter_id,
                    obis_code,
                    DATE(read_at) as read_date,
                    read_at as first_reading_at,
                    value as opening,
                    photo as opening_photo
                FROM meter_readings
                WHERE obis_code IN ('1.1.1.8.0.255', '1.1.2.8.0.255', '8.1.1.0.0.255')
                ORDER BY meter_id, obis_code, DATE(read_at), read_at ASC
            ),
            last_readings AS (
                SELECT DISTINCT ON (meter_id, obis_code, DATE(read_at))
                    meter_id,
                    obis_code,
                    DATE(read_at) as read_date,
                    read_at as last_reading_at,
                    value as closing,
                    photo as closing_photo
                FROM meter_readings
                WHERE obis_code IN ('1.1.1.8.0.255', '1.1.2.8.0.255', '8.1.1.0.0.255')
                ORDER BY meter_id, obis_code, DATE(read_at), read_at DESC
            ),
            counts AS (
                SELECT 
                    meter_id,
                    obis_code,
                    DATE(read_at) as read_date,
                    COUNT(*) as readings_count
                FROM meter_readings
                WHERE obis_code IN ('1.1.1.8.0.255', '1.1.2.8.0.255', '8.1.1.0.0.255')
                GROUP BY meter_id, obis_code, DATE(read_at)
            )
            SELECT 
                f.meter_id,
                f.obis_code,
                f.read_date,
                f.first_reading_at,
                l.last_reading_at,
                c.readings_count,
                f.opening,
                l.closing,
                f.opening_photo,
                l.closing_photo
            FROM first_readings f
            JOIN last_readings l USING (meter_id, obis_code, read_date)
            JOIN counts c USING (meter_id, obis_code, read_date)
        ");
    }

    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS daily_meter_readings');
    }
};
