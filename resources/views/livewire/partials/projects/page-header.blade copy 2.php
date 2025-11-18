<?php

use Livewire\Volt\Component; 

new class extends Component {
    // Accept the project via prop
    public \App\Models\Project $project;

    // Optional: accept the project document via prop
    public ?\App\Models\ProjectDocument $project_document = null;

    // Optional: allow overriding chips/texts from parent if needed
    public ?string $fallbackDescription = 'This is a project';

    public function with()
    {
        return [
            'canEdit'   => auth()->user()->can('project edit') || auth()->user()->can('system access global admin'),
            'canView'   => auth()->user()->can('project document create') || auth()->user()->can('system access global admin'),
            'canReview'   => auth()->user()->can('review create') || auth()->user()->can('system access admin') || auth()->user()->can('system access reviewer') || auth()->user()->can('system access global admin'),
            'isGlobal'  => auth()->user()->hasPermissionTo('system access global admin'),
        ];
    }


    public function returnStatusConfig($status){
        return \App\Helpers\ProjectDocumentHelpers::returnStatusConfig($status);
    }

    public function returnFormattedLabel($status){
        return \App\Helpers\ProjectDocumentHelpers::returnFormattedLabel($status);
    }

};
?>
<div class="px-4 sm:px-6 lg:px-8 py-2 mx-auto grid grid-cols-12 gap-x-2">
    <!-- Header Card -->
    <section class="col-span-12 rounded-2xl border border-slate-200 bg-white px-4 py-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                

                <!-- If a Project Document is passed, show its name -->
                @if(!empty($project_document?->document_type->name))


                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-semibold text-sky-900">

                            @if (request()->routeIs('project-document.review'))
                                Document Review on 
                            @endif

                            <a href="{{ route('project.project-document.show',[
                                        'project' => $project->id, 
                                        'project_document' => $project_document->id
                                    ]) }}"
                                    wire:navigate
                            
                            > 
                                {{ $project_document->document_type->name ?? 'Untitle Document' }}
                            </a>
                            
                        </h1>

                        <!-- Type chip -->
                        <span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-medium text-sky-700">
                            {{ ucfirst('Project Document') }}
                        </span>
                    </div>
 
                    <div class="mt-1 flex items-center gap-2">
                        

                        <h2 class="text-sm font-medium text-slate-700 truncate max-w-[70ch]" title="View {{ $project->name }}">
                            <a wire:navigate href="{{ route('project.show',['project' => $project->id]) }}">
                                {{ $project->name ?? 'Untitled Project' }} 
                            </a>
                            
                        </h2>
                        
                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-medium text-indigo-700 ring-1 ring-inset ring-indigo-200">
                            Project
                        </span> 


                        {{-- <!-- Type chip -->
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                            {{ ucfirst($project->type ?? 'federal project') }}
                        </span> --}}

                    </div>
                @else 

                    <div class="flex items-center gap-2">
                        <h1 class="text-xl font-semibold text-sky-900">
                            <a wire:navigate href="{{ route('project.show',['project' => $project->id]) }}">
                                {{ $project->name ?? 'Untitled Project' }} 
                            </a>
                        </h1>

                        <!-- Type chip -->
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                            {{ ucfirst($project->type ?? 'federal project') }}
                        </span>
                    </div>


                @endif

                <p class="mt-2 max-w-prose text-slate-600">
                    {{ $project->description ?: $fallbackDescription }}
                </p>


                <!-- If a Project Document is passed, show its name -->
                @if(!empty($project_document?->document_type->name))
                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm">
                    @if($isGlobal)
                        <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-slate-700">
                            Project #:
                            <span class="font-medium text-slate-900">{{ $project_document->project_number ?? 'NOT SET' }}</span>
                        </span>
                    @endif

                    <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-slate-700">
                        RC #:
                        <span class="font-medium text-slate-900">{{ $project_document->rc_number ?? 'NOT SET' }}</span>
                    </span>
                </div>
                @endif
            </div>

            


            <!-- Status / Submission lock -->
            <div class="flex flex-col items-start sm:items-end gap-2">
                <!-- Primary Actions -->
                <div class="flex items-center gap-2" x-data="{ open:false }">


                    <x-project.dropdown 
                        menuLabel="Projects"
                        tooltipText="Click for more project options"
                    />



                    <a href="{{ route('project.project-document.index',['project' => $project->id]) }}"
                       wire:navigate
                       class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Documents
                    </a>

                    @if(!empty($project_document) && $canReview)
                       
                        
                        <a href="{{ route('project.document.reviewer.index',[
                                'project' => $project->id,
                                'project_document' => $project_document->id
                            ]) }}"
                            wire:navigate
                            class="rounded-xl border border-amber-200 bg-white px-3 py-2 text-sm font-medium text-amber-700 hover:bg-amber-50">
                            Document Reviewers
                        </a>
                    @endif

                    {{-- @if($canEdit)
                        <a href="{{ route('project.edit',['project' => $project->id]) }}"
                           wire:navigate
                           class="rounded-xl bg-slate-900 px-3 py-2 text-sm font-medium text-white hover:bg-slate-800">
                            Edit
                        </a>
                    @endif --}}

                    <!-- More Menu -->
                    <div class="relative" @keydown.escape="open=false" @click.away="open=false">
                        <button @click="open=!open"
                                class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            More
                            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z"/>
                            </svg>
                        </button>

                        <div x-show="open" x-transition
                             class="absolute right-0 z-50 mt-2 w-48 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
                            <div class="py-1 text-sm">
                                {{-- project --}}
                                    @if($canView)
                                        <a href="{{ route('project.show',['project' => $project->id]) }}"
                                        wire:navigate
                                        class="block px-3 py-2 hover:bg-slate-50">
                                            View Project
                                        </a>
                                    @endif

                                    @if($canEdit)
                                        <a href="{{ route('project.edit',['project' => $project->id]) }}"
                                        wire:navigate
                                        class="block px-3 py-2 hover:bg-slate-50">
                                            Edit Project
                                        </a>
                                    @endif
                                {{-- ./ project --}}

                                <div class="my-1 border-t border-slate-200"></div>
                                {{-- project document --}}
                                    <a href="{{ route('project.project-document.create',['project' => $project->id]) }}"
                                    wire:navigate
                                    class="block px-3 py-2 hover:bg-slate-50">
                                        Add Documents
                                    </a>

                                    <a href="{{ route('project.project-document.index',['project' => $project->id]) }}"
                                    wire:navigate
                                    class="block px-3 py-2 hover:bg-slate-50">
                                        Project Documents
                                    </a>
                                {{-- ./ project document --}}
                                <div class="my-1 border-t border-slate-200"></div>
                                @if(!empty($project_document))
                                {{-- project document and attachments --}}
                                    <a href="{{ route('project.project-document.show',[
                                        'project' => $project->id, 
                                        'project_document' => $project_document->id
                                    ]) }}"
                                    wire:navigate
                                    class="block px-3 py-2 hover:bg-slate-50">
                                        View Document
                                    </a>

                                    <a href="{{ route('project.project_document.edit_attachments',[
                                        'project' => $project->id,
                                        'project_document' => $project_document->id,
                                    ]) }}"
                                    wire:navigate
                                    class="block px-3 py-2 hover:bg-slate-50">
                                        Edit Document and Attachments
                                    </a>
                                {{-- ./ project document and attachments --}}
                                
                                <div class="my-1 border-t border-slate-200"></div>
                                @endif

                                <button class="block w-full px-3 py-2 text-left text-rose-600 hover:bg-rose-50">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


                @if(!empty($project_document))

                    @php
                        // $status = $project->status ?? 'in_review';
                        // $map = [
                        //     'draft'     => 'bg-slate-100 text-slate-700',
                        //     'submitted' => 'bg-sky-100 text-sky-700',
                        //     'in_review' => 'bg-amber-100 text-amber-800',
                        //     'approved'  => 'bg-emerald-100 text-emerald-700',
                        //     'rejected'  => 'bg-rose-100 text-rose-700',
                        //     'completed' => 'bg-emerald-100 text-emerald-700',
                        //     'cancelled' => 'bg-slate-200 text-slate-700',
                        //     'on_que'    => 'bg-violet-100 text-violet-700',
                        // ];

                        $status = $project_document->status;
                        $config = $this->returnStatusConfig($status); 
                       
                        $label = $this->returnFormattedLabel($status);
                    @endphp

                    <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium {{ $config['bg'] ?? 'bg-slate-100 text-slate-700' }}">
                        <span class="size-1.5 rounded-full bg-current"></span>
                        {{ $label }}
                    </span>

                    <div class="text-xs text-slate-600">
                        <span class="mr-1">Submission:</span>
                        @if(!($project_document->allow_project_submission ?? true))
                            <span class="rounded-md bg-slate-100 px-2 py-1 text-slate-700" title="Locked until review is done">Locked</span>
                        @else
                            <span class="rounded-md bg-emerald-100 px-2 py-1 text-emerald-700">Allowed</span>
                        @endif
                    </div>


                @endif




            </div>
        </div>
    </section>
</div>
