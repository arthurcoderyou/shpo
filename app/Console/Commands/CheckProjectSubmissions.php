<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;
use App\Events\ProjectSubmitted;
use Illuminate\Support\Facades\Log;
use App\Mail\ProjectSubmissionReminder;
use Illuminate\Support\Facades\Mail;

class CheckProjectSubmissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-project-submissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks and auto-submits queued projects whose submission time has passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $projects = Project::where('status', 'on_que')
            ->where('created_at', '<=', now())
            ->get();

        foreach ($projects as $project) {
            // Run your submission logic here
             

            // Mail::to($project->creator->email)->send(new ProjectSubmissionReminder($project));


            // event(new ProjectSubmitted($project));


            // Log::info("Project {$project->id} has been auto-submitted.");

 
        }

        $this->info(count($projects) . ' project(s) were submitted.');
        // return Command::SUCCESS;
        return 0;
    }
}
