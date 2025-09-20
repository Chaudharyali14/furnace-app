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
        Schema::create('electricity_log', function (Blueprint $table) {
            $table->id();
            $table->integer('furnace_id');
            $table->string('heat_number');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->decimal('starting_unit', 10, 2);
            $table->decimal('ending_unit', 10, 2);
            $table->decimal('unit_consumed', 10, 2);
            $table->decimal('unit_rate', 10, 2);
            $table->decimal('total_cost', 10, 2);
            $table->date('log_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electricity_log');
    }
};
