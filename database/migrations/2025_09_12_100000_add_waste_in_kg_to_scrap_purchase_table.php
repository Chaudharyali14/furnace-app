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
            $table->decimal('waste_in_kg', 10, 2)->nullable()->after('waste_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scrap_purchase', function (Blueprint $table) {
            $table->dropColumn('waste_in_kg');
        });
    }
};
