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
            $table->longText('staff_engineering_data')->nullable();
            $table->longText('staff_initials')->nullable();

            

            $table->double('lot_size')->nullable();
            $table->longText('unit_of_size')->nullable();

            $table->boolean('site_area_inspection')->default(false);
            $table->boolean('burials_discovered_onsite')->default(false);
            $table->boolean('certificate_of_approval')->default(false);
            $table->boolean('notice_of_violation')->default(false);


        });


        Schema::table('project_documents', function (Blueprint $table) {
            $table->integer('permit_number')->nullable();
            $table->longText('application_type')->nullable(); 
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'staff_engineering_data',
                'staff_initials',
                'lot_size',
                'unit_of_size',
                'site_area_inspection',
                'burials_discovered_onsite',
                'certificate_of_approval',
                'notice_of_violation',

            ]);
        });

        Schema::table('project_documents', function (Blueprint $table) {
            $table->dropColumn([
                'permit_number',
                'application_type', 

            ]);
        });
    }
};
