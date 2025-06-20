<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CheckUserInactivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-user-inactivity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if user is inactive and automatically logs him out of the website';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $inactiveUsers = Cache::get('user-activity');
        $inactivityLimit = 180; // 3 minutes

        foreach ($inactiveUsers as $userId => $timestamp) {
            $timeElapsed = Carbon::now()->timestamp - $timestamp;

            if ($timeElapsed >= $inactivityLimit * 60) {
                // Log out the user

                $user = User::find($userId);
                if ($user) {
                    // Invalidate the user's session
                    $user->tokens()->delete();

                    // Log out the user
                    Auth::guard()->logout();
                }


            }
        }

        $this->info('Funeral reminders sent.');

    }
}
