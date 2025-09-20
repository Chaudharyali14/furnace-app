<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScrapPurchase extends Model
{
    protected $table = 'scrap_purchase';

    protected $fillable = [
        'supplier_id',
        'scrap_name',
        'quantity',
        'weight',
        'amount_per_kg',
        'waste_percentage',
        'waste_in_kg',
        'weight_without_waste',
        'total_amount',
        'discount',
        'waste_amount',
        'grand_total',
        'paid_amount',
        'remaining_amount',
        'purchase_date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
