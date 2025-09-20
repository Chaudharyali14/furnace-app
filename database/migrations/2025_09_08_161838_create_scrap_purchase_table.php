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
        Schema::create('scrap_purchase', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->string('scrap_name');
            $table->decimal('weight', 10, 2);
            $table->decimal('amount_per_kg', 10, 2);
            $table->decimal('weight_without_waste', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('total_amount_with_waste', 10, 2);
            $table->decimal('waste_percentage', 5, 2);
            $table->decimal('grand_total', 10, 2);
            $table->dateTime('purchase_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scrap_purchase');
    }
};
