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
        Schema::table('cc_plant', function (Blueprint $table) {
            //
            $table->decimal('billet_size_inch', 10, 2)->nullable()->after('uncast_metal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cc_plant', function (Blueprint $table) {
            $table->dropColumn('billet_size_inch');
        });
    }
};
