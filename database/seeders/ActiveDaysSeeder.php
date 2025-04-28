<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ActiveDaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $adminUser = User::role('Admin')->first();

        if (!$adminUser) {
            throw new \Exception('No admin user found');
        }

        $days = [
            ['id' => 1, 'day' => 'Monday', 'is_active' => true, 'created_by' => $adminUser->id, 'updated_by' => $adminUser->id],
            ['id' => 2, 'day' => 'Tuesday', 'is_active' => true, 'created_by' => $adminUser->id, 'updated_by' => $adminUser->id],
            ['id' => 3, 'day' => 'Wednesday', 'is_active' => true, 'created_by' => $adminUser->id, 'updated_by' => $adminUser->id],
            ['id' => 4, 'day' => 'Thursday', 'is_active' => true, 'created_by' => $adminUser->id, 'updated_by' => $adminUser->id],
            ['id' => 5, 'day' => 'Friday', 'is_active' => true, 'created_by' => $adminUser->id, 'updated_by' => $adminUser->id],
            ['id' => 6, 'day' => 'Saturday', 'is_active' => false, 'created_by' => $adminUser->id, 'updated_by' => $adminUser->id],
            ['id' => 7, 'day' => 'Sunday', 'is_active' => false, 'created_by' => $adminUser->id, 'updated_by' => $adminUser->id],
        ];

        DB::table('active_days')->insert($days);

    }
}
