<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'supplier_id',
        'customer_id',
        'employee_id',
        'amount',
        'payment_date',
        'description',
        'type',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}