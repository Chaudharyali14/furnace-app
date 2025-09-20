<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScrapPurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('scrap_purchase')->insert([
            [
                'supplier_id' => 1,
                'scrap_name' => 'Vance Weaver',
                'weight' => 48.00,
                'amount_per_kg' => 81.00,
                'weight_without_waste' => 39.84,
                'total_amount' => 3888.00,
                'waste_amount' => 660.96,
                'waste_percentage' => 17.00,
                'grand_total' => 4548.96,
                'purchase_date' => NULL,
            ],
            [
                'supplier_id' => 2,
                'scrap_name' => 'Jamal Snyder',
                'weight' => 21.00,
                'amount_per_kg' => 57.00,
                'weight_without_waste' => 19.95,
                'total_amount' => 1197.00,
                'waste_amount' => 59.85,
                'waste_percentage' => 5.00,
                'grand_total' => 1256.85,
                'purchase_date' => NULL,
            ],
            [
                'supplier_id' => 3,
                'scrap_name' => 'Shannon Travis',
                'weight' => 80.00,
                'amount_per_kg' => 90.00,
                'weight_without_waste' => 14.40,
                'total_amount' => 7200.00,
                'waste_amount' => 5904.00,
                'waste_percentage' => 82.00,
                'grand_total' => 13104.00,
                'purchase_date' => NULL,
            ],
        ]);
    }
}
