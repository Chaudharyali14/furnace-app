<?php

namespace App\Exports;

use App\Models\ScrapPurchase;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchasesExport implements FromCollection, WithHeadings
{
    protected $purchases;

    public function __construct($purchases)
    {
        $this->purchases = $purchases;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->purchases->map(function ($purchase) {
            return [
                'Supplier' => $purchase->supplier->name,
                'Scrap' => $purchase->scrap_name,
                'Weight (kg)' => $purchase->weight,
                'Amount/Kg' => $purchase->amount_per_kg,
                'Waste %' => $purchase->waste_percentage,
                'Weight with waste' => $purchase->weight_without_waste,
                'Total Amount' => $purchase->total_amount,
                'Waste Amount' => $purchase->total_amount_with_waste,
                'Grand Total' => $purchase->grand_total,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Supplier',
            'Scrap',
            'Weight (kg)',
            'Amount/Kg',
            'Waste %',
            'Weight with waste',
            'Total Amount',
            'Waste Amount',
            'Grand Total',
        ];
    }
}
