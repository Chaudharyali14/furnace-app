<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('scrap_purchase', function (Blueprint $table) {
            $table->renameColumn('total_amount_with_waste', 'waste_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scrap_purchase', function (Blueprint $table) {
            $table->renameColumn('waste_amount', 'total_amount_with_waste');
        });
    }
};