<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('scrap_purchase', function (Blueprint $table) {
            DB::statement('ALTER TABLE scrap_purchase CHANGE total_amount_with_waste waste_amount DECIMAL(10, 2) NOT NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scrap_purchase', function (Blueprint $table) {
            DB::statement('ALTER TABLE scrap_purchase CHANGE waste_amount total_amount_with_waste DECIMAL(10, 2) NOT NULL');
        });
    }
};