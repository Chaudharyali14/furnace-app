<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesExport implements FromCollection, WithHeadings
{
    protected $sales;

    public function __construct($sales)
    {
        $this->sales = $sales;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->sales->map(function ($sale) {
            return [
                'ID' => $sale->id,
                'Sale Date' => $sale->sale_date,
                'Customer' => $sale->customer->name ?? 'N/A',
                'Item' => $sale->item_name ?? 'N/A',
                'Billet Size' => $sale->billet_size ?? 'N/A',
                'Total Weight' => $sale->total_weight,
                'Rate' => $sale->rate,
                'Sub Total' => $sale->sub_total,
                'Paid Amount' => $sale->paid_amount,
                'Remaining Amount' => $sale->remaining_amount,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Sale Date',
            'Customer',
            'Item',
            'Billet Size',
            'Total Weight',
            'Rate',
            'Sub Total',
            'Paid Amount',
            'Remaining Amount',
        ];
    }
}
