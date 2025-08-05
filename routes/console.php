<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\CheckUserInactivity;
use App\Console\Commands\SubmitQueuedProjectsCommand;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Schedule::command(CheckUserInactivity::class)->everyMinute();
Schedule::command(SubmitQueuedProjectsCommand::class)->everyMinute();
