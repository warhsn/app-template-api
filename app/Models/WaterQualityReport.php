<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class WaterQualityReport extends Model
{
    protected $fillable = [
        'file',
        'reporting_date',
        'mime_type',
    ];

    protected $appends = [
        'file_url',
    ];

    protected function fileUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->file ? config('app.url').'/storage/'.$this->file : null
        );
    }
}
