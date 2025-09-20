<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectricityLog extends Model
{
    protected $table = 'electricity_log';

    protected $fillable = [
        'furnace_id',
        'heat_number',
        'start_time',
        'end_time',
        'starting_unit',
        'ending_unit',
        'unit_consumed',
        'unit_rate',
        'total_cost',
        'log_date',
    ];
}
