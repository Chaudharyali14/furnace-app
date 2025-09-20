<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RawMaterialStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('raw_materials_stock')->insert([
            [
                'raw_material_name' => 'Dean Howe',
                'total_purchased_qty' => 84.00,
                'total_issued_qty' => 50.00,
                'remaining_stock_qty' => 34.00,
            ],
            [
                'raw_material_name' => 'Melinda Barron',
                'total_purchased_qty' => 6200.00,
                'total_issued_qty' => 6000.00,
                'remaining_stock_qty' => 200.00,
            ],
            [
                'raw_material_name' => 'tapa',
                'total_purchased_qty' => 6000.00,
                'total_issued_qty' => 5000.00,
                'remaining_stock_qty' => 1000.00,
            ],
            [
                'raw_material_name' => 'dom',
                'total_purchased_qty' => 11780.00,
                'total_issued_qty' => 1500.00,
                'remaining_stock_qty' => 10280.00,
            ],
            [
                'raw_material_name' => 'Uncast Metal',
                'total_purchased_qty' => 1000.00,
                'total_issued_qty' => 0.00,
                'remaining_stock_qty' => 4000.00,
            ],
            [
                'raw_material_name' => 'arrat chantt',
                'total_purchased_qty' => 4252.50,
                'total_issued_qty' => 0.00,
                'remaining_stock_qty' => 4252.50,
            ],
            [
                'raw_material_name' => 'nigar  mall',
                'total_purchased_qty' => 4825.00,
                'total_issued_qty' => 0.00,
                'remaining_stock_qty' => 4825.00,
            ],
        ]);
    }
}
