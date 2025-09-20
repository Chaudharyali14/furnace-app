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
            $table->decimal('paid_amount', 10, 2)->nullable()->after('total_amount');
            $table->decimal('remaining_amount', 10, 2)->nullable()->after('paid_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scrap_purchase', function (Blueprint $table) {
            $table->dropColumn('paid_amount');
            $table->dropColumn('remaining_amount');
        });
    }
};
