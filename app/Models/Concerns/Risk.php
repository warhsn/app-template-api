<?php

namespace App\Models\Concerns;

enum Risk: string
{
    case OPERATIONAL = 'operational';
    case AESTHETIC = 'Aesthetic';
    case CHRONIC_HEALTH = 'Chronic health';
    case ACUTE_HEALTH = 'Acute health';
    case NS = 'ns';
}
