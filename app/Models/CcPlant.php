<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CcPlant extends Model
{
    protected $table = 'cc_plant';

    protected $fillable = [
        'date',
        'heat_no',
        'total_metal',
        'casted_metal',
        'uncast_metal',
        'billet_size_inch',
        'cast_item_name',
    ];
}
