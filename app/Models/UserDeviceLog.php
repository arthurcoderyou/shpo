<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Agent\Agent;

class UserDeviceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'browser',
        'device',
        'platform',
        'user_agent',
        'location',
        'trusted',
    ];

    static public function getUserDeviceLog(){
 

        if (Auth::check()) {
            $user = Auth::user();
            $agent = new Agent();
        
            $ip = request()->ip();
            $browser = $agent->browser();
            $device = $agent->device();
            $platform = $agent->platform();
            $userAgent = request()->header('User-Agent');
            $location = request()->header('X-Forwarded-For') ?? $ip;
        
            // Check if a record with the same details already exists
            $existing_user_device_log = UserDeviceLog::where('user_id', $user->id)
                ->where('ip_address', $ip)
                ->where('browser', $browser)
                ->where('device', $device)
                ->where('platform', $platform)
                ->where('user_agent', $userAgent)
                ->first();
        
            if (!$existing_user_device_log) {
                $user_device_log = UserDeviceLog::create([
                    'user_id' => $user->id,
                    'ip_address' => $ip,
                    'browser' => $browser,
                    'device' => $device,
                    'platform' => $platform,
                    'user_agent' => $userAgent,
                    'location' => $location,
                ]);

                return $user_device_log;
            }else{
                return $existing_user_device_log;
            }
        }

    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
