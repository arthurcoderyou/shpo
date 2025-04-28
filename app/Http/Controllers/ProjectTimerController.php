<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActiveDays;
use Illuminate\Http\Request;

class ProjectTimerController extends Controller
{
    public function index()
    {
        // Check if any days exist in the table
        if (ActiveDays::count() === 0) {
            $adminUser = User::role('Admin')->first();

            if (!$adminUser) {
                throw new \Exception('No admin user found');
            }

            $days = [
                'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'
            ];

            foreach ($days as $index => $day) {
                $id = $index + 1;
                ActiveDays::create([
                    'id' => $id,
                    'day' => $day,
                    'is_active' => $day !== 'Saturday' && $day !== 'Sunday', // Active only Mon–Fri
                    'created_by' => $adminUser->id,
                    'updated_by' => $adminUser->id,
                ]);
            }


            event(new \App\Events\ProjectTimerUpdated());

        }

        return view('admin.project_timer.index');
    }



}
