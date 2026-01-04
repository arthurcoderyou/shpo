<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\CheckUserInactivity;
use App\Console\Commands\SubmitQueuedProjectsCommand;
use App\Console\Commands\SubmitQueuedProjectDocumentsCommand;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Schedule::command(CheckUserInactivity::class)->everyMinute();
Schedule::command(SubmitQueuedProjectsCommand::class)->everyMinute();

Schedule::command('app:auto-update-project-status')->everyMinute();



// Schedule::command(SubmitQueuedProjectDocumentsCommand::class)->everyMinute();
