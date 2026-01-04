<?php

namespace App\Console\Commands;

use App\Models\Project;
use Illuminate\Console\Command;

class AutoUpdateProjectStatus extends Command
{
    protected $signature = 'app:auto-update-project-status
                            {--dry-run : Do not write changes, only report}
                            {--chunk=500 : Chunk size}';

    protected $description = 'Auto-updates project status: approved if all docs approved; in_review otherwise';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $chunk  = (int) $this->option('chunk');

        $approvedStatus = 'approved';

        $updated = 0;
        $scanned = 0;

        Project::query()
            ->whereHas('project_documents') // only projects that have docs
            ->select(['id', 'status'])
            ->orderBy('id')
            ->chunkById($chunk, function ($projects) use ($dryRun, $approvedStatus, &$updated, &$scanned) {
                foreach ($projects as $project) {
                    $scanned++;

                    $hasNotApprovedDoc = $project->project_documents()
                        ->where(function ($q) use ($approvedStatus) {
                            $q->where('status', '!=', $approvedStatus)
                              ->orWhereNull('status'); // optional but recommended
                        })
                        ->exists();

                    $desiredStatus = $hasNotApprovedDoc ? 'in_review' : 'approved';

                    if ($project->status === $desiredStatus) {
                        continue;
                    }

                    if ($dryRun) {
                        $this->line("DRY RUN: Would update project #{$project->id} '{$project->status}' -> '{$desiredStatus}'");
                        $updated++;
                        continue;
                    }

                    $old = $project->status;
                    $project->forceFill(['status' => $desiredStatus])->save();

                    $this->line("Updated project #{$project->id} '{$old}' -> '{$desiredStatus}'");
                    $updated++;
                }
            });

        $this->info("Done. Scanned: {$scanned}. Updated: {$updated}." . ($dryRun ? ' (dry-run)' : ''));

        return self::SUCCESS;
    }
}
