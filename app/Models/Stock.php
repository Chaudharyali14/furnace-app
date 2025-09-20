<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'cc_plant';

    protected $fillable = [
        'billet_size_inch',
        'cast_item_name',
        'casted_metal',
        'quantity',
        'date',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
