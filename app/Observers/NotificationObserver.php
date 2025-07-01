<?php

namespace App\Observers;

use Illuminate\Notifications\DatabaseNotification;

class NotificationObserver
{
    /**
     * Handle the DatabaseNotification "created" event.
     */
    public function created(DatabaseNotification $databaseNotification): void
    {
        //
    }

    /**
     * Handle the DatabaseNotification "updated" event.
     */
    public function updated(DatabaseNotification $databaseNotification): void
    {
        //
    }

    /**
     * Handle the DatabaseNotification "deleted" event.
     */
    public function deleted(DatabaseNotification $databaseNotification): void
    {
       try {
            // Dispatch your custom event or log it
            event(new \App\Events\NotificationsDeleted($databaseNotification));
        } catch (\Throwable $e) {
            Log::error('Failed to dispatch NotificationDeleted event: ' . $e->getMessage(), [
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
