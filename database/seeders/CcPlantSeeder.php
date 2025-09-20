<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CcPlantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cc_plant')->insert([
            [
                'heat_no' => '1',
                'total_metal' => 6500.50,
                'casted_metal' => 5500.50,
                'uncast_metal' => 1000.00,
            ],
            [
                'heat_no' => '1',
                'total_metal' => 6500.00,
                'casted_metal' => 5500.00,
                'uncast_metal' => 1000.00,
            ],
            [
                'heat_no' => '2',
                'total_metal' => 6500.00,
                'casted_metal' => 6450.00,
                'uncast_metal' => 50.00,
            ],
            [
                'heat_no' => '3',
                'total_metal' => 6500.00,
                'casted_metal' => 5500.00,
                'uncast_metal' => 1000.00,
            ],
            [
                'heat_no' => '5',
                'total_metal' => 10000.00,
                'casted_metal' => 9000.00,
                'uncast_metal' => 1000.00,
            ],
        ]);
    }
}
