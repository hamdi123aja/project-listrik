<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    protected $fillable = [
        'device_id',
        'voltage',
        'current',
        'power',
        'energy',
        'frequency',
        'power_factor',
        'status',
        'recorded_at',
        'raw_payload',
    ];

    protected function casts(): array
    {
        return [
            'voltage' => 'float',
            'current' => 'float',
            'power' => 'float',
            'energy' => 'float',
            'frequency' => 'float',
            'power_factor' => 'float',
            'recorded_at' => 'datetime',
            'raw_payload' => 'array',
        ];
    }
}
