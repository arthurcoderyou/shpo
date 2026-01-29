<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProjectOwnerAndSyncApproval
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // If the route doesn't have a project parameter, do nothing.
        if (!$request->route() || !$request->route()->hasParameter('project')) {
            return $next($request);
        }

        $routeProject = $request->route('project');

        // Support both route-model binding (Project instance) and ID.
        $project = $routeProject instanceof Project
            ? $routeProject->loadMissing('project_documents')
            : Project::with('project_documents')->find($routeProject);

        // // If project not found, let Laravel handle it (or abort 404).
        // if (!$project) {
        //     abort(404, 'Project not found.');
        // }

        // Must be authenticated (assume you also use auth middleware, but safe check)
        $user = $request->user();
        // if (!$user) {
        //     abort(401);
        // }

        // Owner check: project.created_by must match auth user id
        // if ((int) $project->created_by !== (int) $user->id) {
        //     abort(403, 'You are not authorized to access this project.');
        // }

        // ---- Auto approval logic ----
        $docs = $project->project_documents ?? collect();

        // If there are no docs, we won't auto-update anything.
        if ($docs->isEmpty()) {
            return $next($request);
        }

        // Adjust these statuses to match your system.
        $APPROVED = 'approved';
        $DRAFT    = 'draft';

        $approvedCount = $docs->where('status', $APPROVED)->count();
        $allApproved   = $docs->every(fn ($d) => $d->status === $APPROVED);

        /**
         * RULE 1:
         * If all project documents are approved -> project is approved.
         */
        if ($allApproved) {
            // Only write if needed to avoid noisy updated_at changes.
            if ($project->status !== $APPROVED) {
                $project->forceFill([
                    'status' => $APPROVED,
                ])->save();
            }

            return $next($request);
        }

        /**
         * RULE 2:
         * If nothing is approved AND project has no rc_number AND there are docs
         * -> do not update (skip auto status changes).
         *
         * We simply "return next" without touching project.
         */
        $hasNoRcNumber = empty($project->rc_number);

        if ($approvedCount === 0 && $hasNoRcNumber) {
            return $next($request);
        }

        /**
         * OPTIONAL:
         * You said: "check docs and check if all are either approved or draft"
         * If you want to enforce that only those statuses are allowed here:
         */
        $allowed = [$APPROVED, $DRAFT];
        $allApprovedOrDraft = $docs->every(fn ($d) => in_array($d->status, $allowed, true));

        if (!$allApprovedOrDraft) {
            // Either ignore, or block, depending on your preference:
            // abort(422, 'Project documents contain invalid statuses for this operation.');
            // For now, do nothing.
        }


        return $next($request);
    }
}
