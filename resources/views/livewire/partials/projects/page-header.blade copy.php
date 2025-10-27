{{--
  resources/views/livewire/project/header-card.blade.php (Volt)

  Usage example in a Blade/Volt view:

  <livewire:project.header-card
      :title="'Barrigada Food Court'"
      :project-id="$project->id"
      :page-type="'project'"        
      {{-- 'project' | 'project_document' | 'project_document_attachment' | 'project_reviewer' --}}
      :mode="'show'"                
      {{-- 'show' | 'create' | 'edit' --}}
      :links="[
          // Buttons (always visible)
          ['label' => 'Back',
           'type' => 'button',        // 'button' or 'dropdown'
           'route' => 'project.index',
           'params' => [],
           'show_when' => ['any' => ['show','create','edit']],
           'can' => ['system access global admin','project edit']
          ],
          ['label' => 'Edit',
           'type' => 'button',
           'route' => 'project.edit',
           'params' => ['project' => '{project_id}'],
           'show_when' => ['only' => ['show']],
           'can' => ['system access global admin','project edit']
          ],

          // Dropdown items
          ['label' => 'View',
           'type' => 'dropdown',
           'route' => 'project.show',
           'params' => ['project' => '{project_id}'],
           'show_when' => ['only' => ['show','edit']],
           'can' => ['system access global admin','project document create']
          ],
          ['label' => 'Add Documents',
           'type' => 'dropdown',
           'route' => 'project.project_document.create',
           'params' => ['project' => '{project_id}'],
           'show_when' => ['any' => ['show','edit']],
           'can' => ['system access global admin','project document create']
          ],
          ['label' => 'Project Documents',
           'type' => 'dropdown',
           'route' => 'project.project_documents',
           'params' => ['project' => '{project_id}'],
           'show_when' => ['any' => ['show','edit','create']],
           'can' => []
          ],
          ['label' => 'Delete',
           'type' => 'dropdown',
           'as' => 'button',          // optional: render <button> instead of <a>
           'method' => 'deleteProject',// handled by this component
           'class' => 'text-rose-600 hover:bg-rose-50',
           'divider_before' => true,
           'show_when' => ['only' => ['show','edit']],
           'can' => ['system access global admin']
          ],
      ]"
  />
--}}

<?php
use function Livewire\Volt\{state, mount, computed};
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

state([
    // Props
    'title' => '',
    'projectId' => null,
    'pageType' => 'project', // 'project' | 'project_document' | 'project_document_attachment' | 'project_reviewer'
    'mode' => 'show',        // 'show' | 'create' | 'edit'
    'links' => [],           // configurable actions (buttons + dropdown)

    // Data
    'project' => null,
]);

mount(function ($title = '', $projectId = null, $pageType = 'project', $mode = 'show', $links = []) {
    $this->title = $title;
    $this->projectId = $projectId;
    $this->pageType = $pageType;
    $this->mode = $mode;
    $this->links = $links;

    if ($projectId) {
        $this->project = \App\Models\Project::query()->find($projectId);
    }
});

$resolved = computed(function () {
    // Replace placeholder tokens (e.g., {project_id}) inside params
    $tokenMap = [
        '{project_id}' => $this->projectId,
    ];

    $partitioned = [ 'buttons' => [], 'dropdown' => [] ];

    foreach ($this->links as $i => $link) {
        $item = [
            'label' => Arr::get($link, 'label', 'Action'),
            'type' => Arr::get($link, 'type', 'button'),
            'route' => Arr::get($link, 'route'),
            'params' => Arr::get($link, 'params', []),
            'class' => Arr::get($link, 'class', ''),
            'divider_before' => Arr::get($link, 'divider_before', false),
            'as' => Arr::get($link, 'as'),              // 'button' => render <button>
            'method' => Arr::get($link, 'method'),      // optional action method
            'can' => Arr::get($link, 'can', []),
            'show_when' => Arr::get($link, 'show_when', ['any' => ['show','edit','create']]),
        ];

        // Visibility by mode
        $mode = $this->mode;
        $visible = true;
        if (isset($item['show_when']['only'])) {
            $visible = in_array($mode, (array)$item['show_when']['only'], true);
        } elseif (isset($item['show_when']['any'])) {
            $visible = in_array($mode, (array)$item['show_when']['any'], true);
        }

        if (!$visible) continue;

        // Permission check (AND across given perms OR skip if none)
        $perms = (array) $item['can'];
        foreach ($perms as $perm) {
            if (!Auth::user() || !Auth::user()->can($perm)) {
                $visible = false; break;
            }
        }
        if (!$visible) continue;

        // Resolve params placeholders
        $params = [];
        foreach ($item['params'] as $k => $v) {
            if (is_string($v)) {
                $params[$k] = str_replace(array_keys($tokenMap), array_values($tokenMap), $v);
            } else {
                $params[$k] = $v;
            }
        }
        $item['params'] = $params;

        // Bucketize
        if ($item['type'] === 'dropdown') {
            $partitioned['dropdown'][] = $item;
        } else {
            $partitioned['buttons'][] = $item;
        }
    }

    return $partitioned;
});

// Example action that can be called by a dropdown item with 'method' => 'deleteProject'
$deleteProject = function () {
    if (!$this->projectId) return;
    if (!Auth::user() || !Auth::user()->can('system access global admin')) return;
    optional(\App\Models\Project::find($this->projectId))->delete();
    return redirect()->route('project.index');
};
?>

<!-- Header Card -->
<section class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 sm:p-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-semibold text-sky-900">
                    {{ $title ?: ($project->name ?? 'Untitled Project') }}
                </h1>

                <!-- Page Type chip -->
                @php
                    $pageTypeMap = [
                        'project' => ['label' => 'Project (Mother)', 'class' => 'bg-slate-100 text-slate-700'],
                        'project_document' => ['label' => 'Project Document (Child)', 'class' => 'bg-sky-100 text-sky-700'],
                        'project_document_attachment' => ['label' => 'Attachment (Child of Doc)', 'class' => 'bg-violet-100 text-violet-700'],
                        'project_reviewer' => ['label' => 'Project Reviewer (Child)', 'class' => 'bg-emerald-100 text-emerald-700'],
                    ];
                    $pt = $pageTypeMap[$pageType] ?? $pageTypeMap['project'];
                @endphp
                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $pt['class'] }}">
                    {{ $pt['label'] }}
                </span>
            </div>

            @if($project && filled($project->description))
                <p class="mt-1 max-w-prose text-slate-600">{{ $project->description }}</p>
            @endif

            <div class="mt-3 flex flex-wrap items-center gap-2 text-sm">
                @if(Auth::user()?->hasPermissionTo('system access global admin'))
                    <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-slate-700">
                        Project #:
                        <span class="font-medium text-slate-900">{{ $project->project_number ?? 'NOT SET' }}</span>
                    </span>
                @endif

                <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-slate-700">
                    RC #:
                    <span class="font-medium text-slate-900">{{ $project->rc_number ?? 'NOT SET' }}</span>
                </span>
            </div>
        </div>

        <!-- Right side: Actions + Status -->
        <div class="flex flex-col items-start gap-2 sm:items-end">
            <!-- Primary Actions -->
            <div class="flex items-center gap-2" x-data="{ open:false }">
                @foreach($resolved['buttons'] as $btn)
                    @if(($btn['as'] ?? null) === 'button' && ($btn['method'] ?? false))
                        <button wire:click="{{ $btn['method'] }}"
                                class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 {{ $btn['class'] }}">
                            {{ $btn['label'] }}
                        </button>
                    @else
                        <a href="{{ $btn['route'] ? route($btn['route'], $btn['params']) : '#' }}"
                           @if($btn['route']) wire:navigate @endif
                           class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 {{ $btn['class'] }}">
                           {{ $btn['label'] }}
                        </a>
                    @endif
                @endforeach

                <!-- More dropdown -->
                <div class="relative" @keydown.escape="open=false" @click.away="open=false">
                    <button @click="open=!open" class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        More
                        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.21 8.27a.75.75 0 0 1 .02-1.06z"/>
                        </svg>
                    </button>

                    <div x-show="open" x-transition
                         class="absolute right-0 z-50 mt-2 w-56 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
                        <div class="py-1 text-sm">
                            @foreach($resolved['dropdown'] as $i => $dd)
                                @if($dd['divider_before'] ?? false)
                                    <div class="my-1 border-t border-slate-200"></div>
                                @endif

                                @if(($dd['as'] ?? null) === 'button' && ($dd['method'] ?? false))
                                    <button wire:click="{{ $dd['method'] }}"
                                            class="block w-full px-3 py-2 text-left hover:bg-slate-50 {{ $dd['class'] ?? '' }}">
                                        {{ $dd['label'] }}
                                    </button>
                                @else
                                    <a href="{{ $dd['route'] ? route($dd['route'], $dd['params']) : '#' }}"
                                       @if($dd['route']) wire:navigate @endif
                                       class="block px-3 py-2 hover:bg-slate-50 {{ $dd['class'] ?? '' }}">
                                        {{ $dd['label'] }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status / Submission lock (only for Project type) -->
            @php
                $status = $project->status ?? 'in_review';
                $statusMap = [
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

            @if($pageType === 'project' && $project)
                <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium {{ $statusMap[$status] ?? 'bg-slate-100 text-slate-700' }}">
                    <span class="size-1.5 rounded-full bg-current"></span>
                    {{ Str::title(str_replace('_',' ', $status)) }}
                </span>

                <div class="text-xs text-slate-600">
                    <span class="mr-1">Submission:</span>
                    @if(!($project->allow_project_submission ?? true))
                        <span class="rounded-md bg-slate-100 px-2 py-1 text-slate-700" title="Locked until review is done">Locked</span>
                    @else
                        <span class="rounded-md bg-emerald-100 px-2 py-1 text-emerald-700">Allowed</span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>
