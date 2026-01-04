@props([
    'projects' => [],
    'selectedProjects' => [],
    'query' => null,              // wire:model binding
    'removeAction' => null,       // e.g. removeSubscriber
])

<div class="p-4 space-y-4 rounded-2xl border border-slate-200">

    <!-- Header -->
    <div>
        <h2 class="text-lg font-semibold text-gray-800">
            Project references
        </h2>
        <p class="text-sm text-gray-500">
           Projects that are reference to this project
        </p>
    </div>

    <!-- Search -->
    <div>
        <div class="relative mt-1">
             <x-ui.project.project-search
                name="query"
                label=""
                :value="$query"
                placeholder="Search or select project..."
                :options="$projects"    
                wire:model.live="query"   
                
            />
        </div>
    </div>

    <!-- Selected Project References -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
            <div>
                <h3 class="text-sm font-semibold text-slate-800">Selected Project References</h3>
                <p class="text-xs text-slate-500">
                    These projects will be referenced.
                </p>
            </div>

            @if(!empty($selectedProjects))
                <span class="inline-flex items-center rounded-full bg-sky-50 px-2.5 py-0.5 text-xs font-medium text-sky-700 border border-sky-100">
                    {{ count($selectedProjects) }} selected
                </span>
            @endif
        </div>

        <div class="max-h-[100vh] overflow-y-auto">
            @if(!empty($selectedProjects))
                <ul class="divide-y divide-slate-100">
                    @foreach($selectedProjects as $index => $project)
                        <li class="flex items-center justify-between px-4 py-2.5 hover:bg-sky-50/60 transition-colors">
                            {{-- <div class="flex items-center gap-3 min-w-0"> --}}

                                <a wire:navigate class="flex items-center gap-3 min-w-0" href="{{ route('project.show',['project' => $project['id'] ]) }}"> 
                                
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-sky-100 text-xs font-semibold text-sky-700">
                                        {{ mb_substr($project['name'], 0, 1) }}
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-slate-800 truncate">
                                            {{ $project['name'] }}
                                        </p>
                                        @if(!empty($project['rc_number'] ?? null))
                                            <p class="text-xs text-slate-500 truncate">
                                                {{ $project['rc_number'] }}
                                            </p>
                                        @endif

                                        @if(!empty($project['location'] ?? null))
                                            <p class="text-xs text-slate-500 truncate">
                                                {{ $project['location'] }}
                                            </p>
                                        @endif
                                    </div>

                                </a>

                            {{-- </div> --}}

                            <button
                                type="button"
                                @if($removeAction)
                                    wire:click="{{ $removeAction }}({{ $index }})"
                                @endif
                                class="inline-flex items-center justify-center rounded-full p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-red-400"
                                title="Remove subscriber"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 20 20"
                                     fill="currentColor"
                                     class="h-4 w-4">
                                    <path fill-rule="evenodd"
                                          d="M4.293 4.293a1 1 0 0 1 1.414 0L10 8.586l4.293-4.293a1 1 0 1 1 1.414 1.414L11.414 10l4.293 4.293a1 1 0 0 1-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 0 1-1.414-1.414L8.586 10 4.293 5.707a1 1 0 0 1 0-1.414Z"
                                          clip-rule="evenodd" />
                                </svg>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="px-4 py-6 text-center">
                    <div class="mx-auto mb-2 flex h-9 w-9 items-center justify-center rounded-full bg-slate-50 text-slate-300">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke-width="1.5"
                             stroke="currentColor"
                             class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 19.128a9.38 9.38 0 0 0 2.625.372A9.337 9.337 0 0 0 21 19.128V18a4.5 4.5 0 0 0-9 0v1.128ZM12 11.25A3.75 3.75 0 1 0 12 3.75a3.75 3.75 0 0 0 0 7.5Z" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-slate-500">
                        No projects selected.
                    </p>
                    <p class="mt-1 text-xs text-slate-400">
                        Use the search above to add projects to this list.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
