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
        Schema::create('finished_good_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cc_plant_id')->constrained('cc_plant')->onDelete('cascade');
            $table->string('item_name');
            $table->decimal('weight', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finished_good_stocks');
    }
};
