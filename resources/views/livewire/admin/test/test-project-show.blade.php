<!--
  Project Detail UI (Tailwind + Alpine, Blade-ready)
  - Mobile-first, clean, compact
  - Works in Laravel Blade; sprinkle of Alpine for menus
  - Tailwind CSS required

  DATA FIELDS expected (Blade/Livewire):
    $project = [
      'name','description','federal_agency','type','status','allow_project_submission',
      'created_by','updated_by','created_at','updated_at','project_number','rc_number',
      'street','area','lot_number','submitter_response_duration_type','submitter_response_duration',
      'submitter_due_date','reviewer_response_duration','reviewer_response_duration_type','reviewer_due_date',
      'latitude','longitude','location','last_submitted_at','last_submitted_by','last_reviewed_at','last_reviewed_by'
    ]
-->

<div class="min-h-screen bg-slate-50 p-4 sm:p-6">
  <!-- Toolbar / Breadcrumbs -->
  <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <nav class="text-sm text-slate-600" aria-label="Breadcrumb">
      <ol class="flex flex-wrap items-center gap-1">
        <li><a href="#" class="hover:text-slate-900">Projects</a></li>
        <li class="mx-1">/</li>
        <li aria-current="page" class="text-slate-900 font-medium">Project Detail</li>
      </ol>
    </nav>

    <!-- Primary Actions -->
    <div class="flex items-center gap-2" x-data="{ open:false }">
      <button class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Back</button>
      <button class="rounded-xl bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-800">Edit</button>
      <div class="relative" @keydown.escape="open=false" @click.away="open=false">
        <button @click="open=!open" class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
          More
          <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z"/></svg>
        </button>
        <div x-show="open" x-transition class="absolute right-0 z-50 mt-2 w-48 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
          <div class="py-1 text-sm">
            <a href="#" class="block px-3 py-2 hover:bg-slate-50">Force Submit</a>
            <a href="#" class="block px-3 py-2 hover:bg-slate-50">Approve</a>
            <a href="#" class="block px-3 py-2 hover:bg-slate-50">Reject</a>
            <div class="my-1 border-t border-slate-200"></div>
            <a href="#" class="block px-3 py-2 hover:bg-slate-50">Reviewer Link</a>
            <a href="#" class="block px-3 py-2 hover:bg-slate-50">Project Documents</a>
            <div class="my-1 border-t border-slate-200"></div>
            <button class="block w-full px-3 py-2 text-left text-rose-600 hover:bg-rose-50">Delete</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Header Card -->
  <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 sm:p-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
      <div>
        <div class="flex items-center gap-2">
          <h1 class="text-xl font-semibold text-slate-900">{{ $project->name ?? 'Barrigada Food Court' }}</h1>
          <!-- Type chip -->
          <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
            {{ ucfirst($project->type ?? 'federal project') }}
          </span>
        </div>
        <p class="mt-1 max-w-prose text-slate-600">{{ $project->description ?? 'This is a project' }}</p>
        <div class="mt-3 flex flex-wrap items-center gap-2 text-sm">
          <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-slate-700">Project #: <span class="font-medium text-slate-900">{{ $project->project_number ?? 'PRJ-000000186' }}</span></span>
          <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-slate-700">RC #: <span class="font-medium text-slate-900">{{ $project->rc_number ?? 'RC-000000001' }}</span></span>
        </div>
      </div>

      <!-- Status / Submission lock -->
      <div class="flex flex-col items-start sm:items-end gap-2">
        @php
          $status = $project->status ?? 'in_review';
          $map = [
            'draft' => 'bg-slate-100 text-slate-700',
            'submitted' => 'bg-sky-100 text-sky-700',
            'in_review' => 'bg-amber-100 text-amber-800',
            'approved' => 'bg-emerald-100 text-emerald-700',
            'rejected' => 'bg-rose-100 text-rose-700',
            'completed' => 'bg-emerald-100 text-emerald-700',
            'cancelled' => 'bg-slate-200 text-slate-700',
            'on_que' => 'bg-violet-100 text-violet-700',
          ];
        @endphp
        <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium {{ $map[$status] ?? 'bg-slate-100 text-slate-700' }}">
          <span class="size-1.5 rounded-full bg-current"></span>
          {{ str_replace('_',' ', ucfirst($status)) }}
        </span>
        <div class="text-xs text-slate-600">
          <span class="mr-1">Submission:</span>
          @if(!($project->allow_project_submission ?? true))
            <span class="rounded-md bg-slate-100 px-2 py-1 text-slate-700" title="Locked until review is done">Locked</span>
          @else
            <span class="rounded-md bg-emerald-100 px-2 py-1 text-emerald-700">Allowed</span>
          @endif
        </div>
      </div>
    </div>
  </section>

  <!-- Meta Grid -->
  <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
    <!-- Left: Core fields -->
    <div class="md:col-span-2 space-y-4">
      <!-- Company & Location -->
      <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6">
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">Company & Location</h2>
        <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <div>
            <dt class="text-xs font-medium text-slate-500">Company</dt>
            <dd class="text-sm text-slate-900">{{ $project->federal_agency ?? 'DSI' }}</dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-slate-500">Type</dt>
            <dd class="text-sm text-slate-900">{{ ucfirst($project->type ?? 'federal project') }}</dd>
          </div>
          <div class="sm:col-span-2">
            <dt class="text-xs font-medium text-slate-500">Location</dt>
            <dd class="text-sm text-slate-900">{{ $project->location ?? 'CQ55+FV Yona, Guam' }}</dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-slate-500">Street</dt>
            <dd class="text-sm text-slate-900">{{ $project->street ?? '123 Marine Dr' }}</dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-slate-500">Area</dt>
            <dd class="text-sm text-slate-900">{{ $project->area ?? 'Barrigada' }}</dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-slate-500">Lot #</dt>
            <dd class="text-sm text-slate-900">{{ $project->lot_number ?? 'L-001' }}</dd>
          </div>
        </dl>

        <div class="mt-4 grid grid-cols-2 gap-3">
          <div>
            <dt class="text-xs font-medium text-slate-500">Latitude</dt>
            <dd class="text-sm text-slate-900">{{ $project->latitude ?? '13.4087355' }}</dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-slate-500">Longitude</dt>
            <dd class="text-sm text-slate-900">{{ $project->longitude ?? '144.7597110' }}</dd>
          </div>
        </div>

        <!-- Map placeholder -->
        <div class="mt-4 aspect-[16/9] w-full overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
          <div class="grid h-full place-content-center text-slate-500">Map preview</div>
        </div>
      </div>

      <!-- SLA / Deadlines -->
      <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6">
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">Response Windows</h2>
        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
          <!-- Submitter -->
          <div class="rounded-xl border border-slate-200 p-3">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-semibold text-slate-900">Submitter</h3>
              <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{ strtoupper($project->submitter_response_duration_type ?? 'day') }}</span>
            </div>
            <div class="mt-2 flex items-center gap-3 text-sm">
              <div class="rounded-lg bg-slate-50 px-2.5 py-1">Duration: <span class="font-medium text-slate-900">{{ $project->submitter_response_duration ?? 2 }}</span></div>
              <div class="rounded-lg bg-slate-50 px-2.5 py-1">Due: <span class="font-medium text-slate-900">{{ $project->submitter_due_date ?? '2025-08-06 17:16:15' }}</span></div>
            </div>
          </div>
          <!-- Reviewer -->
          <div class="rounded-xl border border-slate-200 p-3">
            <div class="flex items-center justify-between">
              <h3 class="text-sm font-semibold text-slate-900">Reviewer</h3>
              <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{ strtoupper($project->reviewer_response_duration_type ?? 'day') }}</span>
            </div>
            <div class="mt-2 flex items-center gap-3 text-sm">
              <div class="rounded-lg bg-slate-50 px-2.5 py-1">Duration: <span class="font-medium text-slate-900">{{ $project->reviewer_response_duration ?? 2 }}</span></div>
              <div class="rounded-lg bg-slate-50 px-2.5 py-1">Due: <span class="font-medium text-slate-900">{{ $project->reviewer_due_date ?? '2025-08-06 17:16:15' }}</span></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Audit -->
      <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6">
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">Audit</h2>
        <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
          <div>
            <dt class="text-xs font-medium text-slate-500">Created by</dt>
            <dd class="text-sm text-slate-900">ID {{ $project->created_by ?? 9 }} — <span class="text-slate-600">{{ $project->created_at ?? '2025-08-04 07:01:06' }}</span></dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-slate-500">Updated by</dt>
            <dd class="text-sm text-slate-900">ID {{ $project->updated_by ?? 9 }} — <span class="text-slate-600">{{ $project->updated_at ?? '2025-09-22 05:18:14' }}</span></dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-slate-500">Last submitted</dt>
            <dd class="text-sm text-slate-900">{{ $project->last_submitted_at ?? '—' }} <span class="text-slate-600">by {{ $project->last_submitted_by ?? '—' }}</span></dd>
          </div>
          <div>
            <dt class="text-xs font-medium text-slate-500">Last reviewed</dt>
            <dd class="text-sm text-slate-900">{{ $project->last_reviewed_at ?? '—' }} <span class="text-slate-600">by {{ $project->last_reviewed_by ?? '—' }}</span></dd>
          </div>
        </dl>
      </div>
    </div>

    <!-- Right: Quick glance / sticky on desktop -->
    <aside class="sticky top-4 h-max space-y-4">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6">
        <h2 class="mb-3 text-sm font-semibold uppercase tracking-wide text-slate-500">Quick Glance</h2>
        <ul class="space-y-2 text-sm text-slate-700">
          <li class="flex items-center justify-between"><span>Status</span><span class="font-medium text-slate-900">{{ str_replace('_',' ', ucfirst($status)) }}</span></li>
          <li class="flex items-center justify-between"><span>Company</span><span class="font-medium text-slate-900">{{ $project->federal_agency ?? 'DSI' }}</span></li>
          <li class="flex items-center justify-between"><span>Project #</span><span class="font-medium text-slate-900">{{ $project->project_number ?? 'PRJ-000000186' }}</span></li>
          <li class="flex items-center justify-between"><span>RC #</span><span class="font-medium text-slate-900">{{ $project->rc_number ?? 'RC-000000001' }}</span></li>
        </ul>
        <div class="mt-3 rounded-xl bg-slate-50 p-3 text-xs text-slate-600">
          Tip: toggle submission in admin once review completes.
        </div>
      </div>

      <!-- Compact actions list -->
      <div class="rounded-2xl border border-slate-200 bg-white p-4">
        <h3 class="mb-2 text-sm font-semibold text-slate-900">Actions</h3>
        <div class="flex flex-wrap gap-2">
          <button class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm hover:bg-slate-50">Edit</button>
          <button class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm hover:bg-slate-50">Reviewer Link</button>
          <button class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm hover:bg-slate-50">Documents</button>
          <button class="rounded-lg bg-slate-900 px-3 py-1.5 text-sm text-white hover:bg-slate-800">Submit</button>
        </div>
      </div>
    </aside>
  </section>
</div>
