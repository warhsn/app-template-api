<?php

namespace App\Http\Filters;

use App\Models\Meter;
use Billow\Utilities\QueryFilter;
use Carbon\Carbon;

class MeterReadingFilters extends QueryFilter
{
    public function start_date($date)
    {
        return $this->builder
            ->when(
                filled($date),
                fn ($query) => $query->where('read_at', '>=', (string) Carbon::parse($date)->startOfDay())
            );
    }

    public function end_date($date)
    {
        return $this->builder
            ->when(
                filled($date),
                fn ($query) => $query->where('read_at', '<=', (string) Carbon::parse($date)->endOfDay())
            );
    }

    public function reading_type($type)
    {
        return $this->builder
            ->when(
                filled($type),
                fn ($query) => $query->where(
                    'obis_code',
                    $type === 'feedback'
                        ? Meter::CUMULATIVE_ELECTRICAL_FEEDBACK_OBIS
                        : Meter::CUMULATIVE_ELECTRICAL_OBIS
                )
            );
    }
}
