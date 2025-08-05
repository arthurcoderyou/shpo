<?php

namespace App\Observers;

use App\Events\NotificationsCreated;
use App\Events\NotificationsDeleted;
use App\Events\NotificationsUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationObserver
{
    /**
     * Handle the DatabaseNotification "created" event.
     */
    public function created(DatabaseNotification $databaseNotification): void
    {
        try {
            // Dispatch your custom event or log it
            event(new NotificationsCreated($databaseNotification,Auth::user()->id));
        } catch (\Throwable $e) {
            Log::error('Failed to dispatch NotificationsCreated event: ' . $e->getMessage(), [
                'notification_id' => $databaseNotification->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle the DatabaseNotification "updated" event.
     */
    public function updated(DatabaseNotification $databaseNotification): void
    {
        try {
            // Dispatch your custom event or log it
            event(new NotificationsUpdated($databaseNotification,Auth::user()->id));
        } catch (\Throwable $e) {
            Log::error('Failed to dispatch NotificationsUpdated event: ' . $e->getMessage(), [
                'notification_id' => $databaseNotification->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle the DatabaseNotification "deleted" event.
     */
    public function deleted(DatabaseNotification $databaseNotification): void
    {
       try {
            // Dispatch your custom event or log it
            event(new NotificationsDeleted($databaseNotification,Auth::user()->id));
        } catch (\Throwable $e) {
            Log::error('Failed to dispatch NotificationsDeleted event: ' . $e->getMessage(), [
                'notification_id' => $databaseNotification->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle the DatabaseNotification "restored" event.
     */
    public function restored(DatabaseNotification $databaseNotification): void
    {
        //
    }

    /**
     * Handle the DatabaseNotification "force deleted" event.
     */
    public function forceDeleted(DatabaseNotification $databaseNotification): void
    {
        //
    }
}
