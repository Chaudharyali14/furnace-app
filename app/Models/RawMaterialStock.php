<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialStock extends Model
{
    use HasFactory;

    protected $table = 'raw_materials_stock';

    // Assuming primary key is 'id' and it's auto-incrementing
    // If not, you might need to set protected $primaryKey and public $incrementing

    // Assuming timestamps are handled by Laravel (created_at, updated_at)
    // If not, set public $timestamps = false;

    protected $fillable = [
        'raw_material_name',
        'total_purchased_qty',
        'total_issued_qty',
        'remaining_stock_qty',
    ];
}
