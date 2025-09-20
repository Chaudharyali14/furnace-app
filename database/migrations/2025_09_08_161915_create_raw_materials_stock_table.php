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
        Schema::create('raw_materials_stock', function (Blueprint $table) {
            $table->id();
            $table->string('raw_material_name')->unique();
            $table->decimal('total_purchased_qty', 10, 2)->default(0.00);
            $table->decimal('total_issued_qty', 10, 2)->default(0.00);
            $table->decimal('remaining_stock_qty', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_materials_stock');
    }
};
