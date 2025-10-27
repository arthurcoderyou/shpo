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
        Schema::table('project_reviewers', function (Blueprint $table) {
            $table->enum('slot_type',['person','open'])->default('person');
            $table->enum('slot_role',['admin','reviewer'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_reviewers', function (Blueprint $table) {
            $table->dropColumn('slot_type');
            $table->dropColumn('slot_role');
        });
    }
};
