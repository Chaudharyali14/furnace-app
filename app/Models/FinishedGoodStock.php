<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinishedGoodStock extends Model
{
    use HasFactory;

    protected $table = 'finished_good_stocks';

    protected $fillable = [
        'cc_plant_id',
        'item_name',
        'weight',
    ];

    public function ccPlant()
    {
        return $this->belongsTo(CcPlant::class);
    }
}