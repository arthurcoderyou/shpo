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
        Schema::table('projects', function (Blueprint $table) {
            $table->longText('street')->nullable()->after('location');
             $table->longText('area')->nullable()->after('street');
              $table->longText('lot_number')->nullable()->after('area'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('street');
            $table->dropColumn('area');
            $table->dropColumn('lot_number');

        });
    }
};
