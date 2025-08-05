<?php

namespace App\Http\Controllers;

use App\Events\NotificationsUpdated;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsReadandOpen($notificationId)
    {
        $notification = auth()->user()->notifications()->where('id', $notificationId)->first();

        if (!$notification || empty($notification->data['url'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid notification or URL not found',
            ], 404);
        }

        if (empty($notification->read_at)) {
            $notification->read_at = now();
            $notification->save();

            

        }

        



        return response()->json([
            'status' => 'success',
            'url' => $notification->data['url'],
        ]);
    }

}
