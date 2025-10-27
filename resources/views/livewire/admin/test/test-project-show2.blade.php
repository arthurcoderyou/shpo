<!--
  Project Detail UI (Tailwind + Alpine, Blade-ready)
  - Mobile-first, clean, compact
  - Drops big top button cluster; moves contextual actions beside description
  - Adds Documents section (with attachment counts + empty state)
  - Adds Subscribers section
  - Works in Laravel Blade; sprinkle of Alpine for menus
  - Tailwind CSS required

  EXPECTED DATA (Blade/Livewire):
    $project
    $projectDocuments  // Illuminate\Support\Collection of docs with: id,name,status,attachments_count,updated_at
    $subscribers       // Collection of users with: id,name,email,role

  Optional named routes used (adjust as needed):
    route('projects.index'), route('projects.show', $project), route('projects.edit', $project)
    route('projects.documents.index', $project)
    route('projects.documents.create', $project)
    route('projects.reviewer.link', $project)
    route('projects.force-submit', $project)
    route('projects.approve', $project)
    route('projects.reject', $project)
    route('projects.subscribers.index', $project)
-->

<div class="min-h-screen bg-slate-50 p-4 sm:p-6" x-data="{ moreOpen:false }">
  <!-- Toolbar / Breadcrumbs -->
  <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <nav class="text-sm text-slate-600" aria-label="Breadcrumb">
      <ol class="flex flex-wrap items-center gap-1">
        <li><a href="{{ route('projects.index') }}" class="hover:text-slate-900">Projects</a></li>
        <li class="mx-1">/</li>
        <li aria-current="page" class="text-slate-900 font-medium">Project Detail</li>
      </ol>
    </nav>

    <!-- Minimal top actions -->
    <div class="flex items-center gap-2">
      <a href="{{ route('projects.index') }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Back</a>
      <a href="{{ route('projects.edit', $project) }}" class="rounded-xl bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-800">Edit</a>
      <div class="relative" @keydown.escape="moreOpen=false" @click.outside="moreOpen=false">
        <button @click="moreOpen=!moreOpen" class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
          More
          <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z"/></svg>
        </button>
        <div x-cloak x-show="moreOpen" x-transition class="absolute right-0 z-50 mt-2 w-56 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
          <div class="py-1 text-sm">
            <a href="{{ route('projects.force-submit', $project) }}" class="block px-3 py-2 hover:bg-slate-50">Force Submit</a>
            <a href="{{ route('projects.approve', $project) }}" class="block px-3 py-2 hover:bg-slate-50">Approve</a>
            <a href="{{ route('projects.reject', $project) }}" class="block px-3 py-2 hover:bg-slate-50">Reject</a>
            <div class="my-1 border-t border-slate-200"></div>
            <a href="{{ route('projects.reviewer.link', $project) }}" class="block px-3 py-2 hover:bg-slate-50">Reviewer Link</a>
            <a href="{{ route('projects.documents.index', $project) }}" class="block px-3 py-2 hover:bg-slate-50">Project Documents</a>
            <div class="my-1 border-t border-slate-200"></div>
            <form method="POST" action="#" onsubmit="return confirm('Delete this project?');">
              @csrf @method('DELETE')
              <button class="block w-full px-3 py-2 text-left text-rose-600 hover:bg-rose-50">Delete</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Header Card -->
  <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 sm:p-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
      <div class="min-w-0">
        <div class="flex flex-wrap items-center gap-2">
          <h1 class="text-xl font-semibold text-slate-900 truncate">{{ $project->name ?? 'Barrigada Food Court' }}</h1>
          <!-- Type chip -->
          <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">{{ ucfirst($project->type ?? 'federal project') }}</span>
          <!-- Counts chip -->
          @php $docCount = $projectDocuments->count() ?? 0; $attCount = ($projectDocuments->sum('attachments_count') ?? 0); @endphp
          <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-700">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4"><path d="M8.25 7.5a3.75 3.75 0 017.5 0v6a5.25 5.25 0 11-10.5 0V9a.75.75 0 011.5 0v4.5a3.75 3.75 0 107.5 0v-6a2.25 2.25 0 10-4.5 0v6a.75.75 0 01-1.5 0v-6z"/></svg>
            {{ $docCount }} docs · {{ $attCount }} files
          </span>
        </div>
        <p class="mt-1 max-w-prose text-slate-600">{{ $project->description ?? 'This is a project' }}</p>

        <!-- Contextual quick actions (inside details, compact) -->
        <div class="mt-3 flex flex-wrap items-center gap-2">
          <a href="{{ route('projects.edit', $project) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">
            <svg class="size-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M21.731 2.269a2.625 2.625 0 00-3.713 0l-1.157 1.157 3.713 3.713 1.157-1.157a2.625 2.625 0 000-3.713z"/><path d="M19.513 8.199l-3.712-3.712-11.4 11.4a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.924.924l2.685-.8a5.25 5.25 0 002.214-1.32l11.409-11.39z"/></svg>
            Edit details
          </a>
          <a href="{{ route('projects.documents.create', $project) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">
            <svg class="size-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
            New document
          </a>
          <a href="{{ route('projects.reviewer.link', $project) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">
            <svg class="size-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M13.19 8.688l-3.879 3.879a2.25 2.25 0 003.182 3.183l2.121-2.122m-4.242-4.94l-2.12 2.121a4.5 4.5 0 106.364 6.364l3.879-3.879a4.5 4.5 0 10-6.364-6.364z"/></svg>
            Reviewer link
          </a>
          <a href="{{ route('projects.documents.index', $project) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">
            <svg class="size-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M19.5 14.25v2.25A2.25 2.25 0 0117.25 18.75H6.75A2.25 2.25 0 014.5 16.5v-9A2.25 2.25 0 016.75 5.25h6.75L19.5 11.25v.75"/></svg>
            All documents
          </a>
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

  <!-- Meta & Content Grid -->
  <section class="grid grid-cols-1 gap-4 lg:grid-cols-3">
    <!-- Left / Center: Details -->
    <div class="lg:col-span-2 space-y-4">
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

      <!-- Documents (supports none / one / many) -->
      <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6">
        <div class="mb-3 flex items-center justify-between gap-2">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Project Documents</h2>
          <a href="{{ route('projects.documents.create', $project) }}" class="inline-flex items-center gap-1 rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-800">New document</a>
        </div>

        @if(($projectDocuments->count() ?? 0) === 0)
          <!-- Empty state -->
          <div class="rounded-xl border border-dashed border-slate-300 p-6 text-center">
            <div class="mx-auto mb-2 grid size-10 place-content-center rounded-full bg-slate-100">
              <svg class="size-5 text-slate-600" viewBox="0 0 24 24" fill="currentColor"><path d="M19.5 14.25v2.25A2.25 2.25 0 0117.25 18.75H6.75A2.25 2.25 0 014.5 16.5v-9A2.25 2.25 0 016.75 5.25h6.75L19.5 11.25v.75"/></svg>
            </div>
            <p class="text-sm text-slate-600">No documents yet. Create the first project document to begin the review.</p>
            <div class="mt-3">
              <a href="{{ route('projects.documents.create', $project) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Add document</a>
            </div>
          </div>
        @else
          <ul role="list" class="divide-y divide-slate-100">
            @foreach($projectDocuments as $doc)
              <li class="group flex items-start justify-between gap-3 py-3">
                <div class="min-w-0">
                  <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('projects.documents.index', $project) }}#doc-{{ $doc->id }}" class="font-medium text-slate-900 hover:underline">{{ $doc->name }}</a>
                    <!-- status chip -->
                    @php
                      $s = $doc->status ?? 'in_review';
                      $docMap = [
                        'draft' => 'bg-slate-100 text-slate-700',
                        'submitted' => 'bg-sky-100 text-sky-700',
                        'in_review' => 'bg-amber-100 text-amber-800',
                        'approved' => 'bg-emerald-100 text-emerald-700',
                        'rejected' => 'bg-rose-100 text-rose-700',
                        'completed' => 'bg-emerald-100 text-emerald-700',
                      ];
                    @endphp
                    <span class="rounded-full px-2.5 py-0.5 text-xs font-medium {{ $docMap[$s] ?? 'bg-slate-100 text-slate-700' }}">{{ str_replace('_',' ', ucfirst($s)) }}</span>
                    <!-- attachments badge -->
                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-700">
                      <svg class="size-3" viewBox="0 0 24 24" fill="currentColor"><path d="M18.364 5.636a4.5 4.5 0 00-6.364 0L5.636 12a3 3 0 104.243 4.243l6.01-6.01a1.5 1.5 0 00-2.122-2.122L8.5 13.379"/></svg>
                      {{ $doc->attachments_count ?? 0 }} attachments
                    </span>
                  </div>
                  <p class="mt-0.5 line-clamp-1 text-sm text-slate-600">Updated {{ optional($doc->updated_at)->diffForHumans() ?? '—' }}</p>
                </div>
                <div class="flex shrink-0 items-center gap-1">
                  <a href="{{ route('projects.documents.index', $project) }}#doc-{{ $doc->id }}" class="rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50">Open</a>
                  <div class="relative" x-data="{o:false}">
                    <button @click="o=!o" class="rounded-md border border-slate-200 bg-white px-2.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50">Actions</button>
                    <div x-cloak x-show="o" x-transition class="absolute right-0 z-50 mt-1 w-44 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-md">
                      <a href="{{ route('projects.documents.index', $project) }}#doc-{{ $doc->id }}" class="block px-3 py-2 text-sm hover:bg-slate-50">View</a>
                      <a href="#" class="block px-3 py-2 text-sm hover:bg-slate-50">Reviewer link</a>
                      <a href="#" class="block px-3 py-2 text-sm hover:bg-slate-50">Approve</a>
                      <a href="#" class="block px-3 py-2 text-sm hover:bg-slate-50">Reject</a>
                    </div>
                  </div>
                </div>
              </li>
            @endforeach
          </ul>
          <div class="mt-3 text-right">
            <a href="{{ route('projects.documents.index', $project) }}" class="text-sm font-medium text-slate-700 hover:underline">View all documents →</a>
          </div>
        @endif
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

      <!-- Subscribers -->
      <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6">
        <div class="mb-3 flex items-center justify-between">
          <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Subscribers</h2>
          <a href="{{ route('projects.subscribers.index', $project) }}" class="text-xs font-medium text-slate-700 hover:underline">Manage</a>
        </div>
        @if(($subscribers->count() ?? 0) === 0)
          <p class="text-sm text-slate-600">No one is subscribed yet.</p>
          <div class="mt-3">
            <a href="{{ route('projects.subscribers.index', $project) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">Add subscribers</a>
          </div>
        @else
          <ul role="list" class="space-y-2">
            @foreach($subscribers as $u)
              <li class="flex items-center justify-between rounded-lg border border-slate-200 p-2">
                <div class="flex items-center gap-3 min-w-0">
                  <div class="grid size-8 place-content-center rounded-full bg-slate-100 text-xs font-semibold text-slate-700">
                    {{ strtoupper(Str::of($u->name)->explode(' ')->map(fn($p)=>Str::substr($p,0,1))->take(2)->implode('')) }}
                  </div>
                  <div class="min-w-0">
                    <p class="truncate text-sm font-medium text-slate-900">{{ $u->name }}</p>
                    <p class="truncate text-xs text-slate-600">{{ $u->email }}</p>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-700">{{ $u->role ?? 'Member' }}</span>
                  <form method="POST" action="#" onsubmit="return confirm('Unsubscribe {{ $u->name }}?');">
                    @csrf @method('DELETE')
                    <button class="rounded-md border border-slate-200 bg-white px-2.5 py-1 text-xs text-slate-700 hover:bg-rose-50">Unsubscribe</button>
                  </form>
                </div>
              </li>
            @endforeach
          </ul>
        @endif
      </div>

      <!-- Compact actions list -->
      <div class="rounded-2xl border border-slate-200 bg-white p-4">
        <h3 class="mb-2 text-sm font-semibold text-slate-900">Actions</h3>
        <div class="flex flex-wrap gap-2">
          <a href="{{ route('projects.edit', $project) }}" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm hover:bg-slate-50">Edit</a>
          <a href="{{ route('projects.reviewer.link', $project) }}" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm hover:bg-slate-50">Reviewer Link</a>
          <a href="{{ route('projects.documents.index', $project) }}" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm hover:bg-slate-50">Documents</a>
          <button class="rounded-lg bg-slate-900 px-3 py-1.5 text-sm text-white hover:bg-slate-800">Submit</button>
        </div>
      </div>
    </aside>
  </section>
</div>
