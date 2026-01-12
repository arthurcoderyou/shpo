<?php

use Livewire\Volt\Component; 
use App\Helpers\ProjectHelper;
use App\Models\ProjectDocument;
use App\Models\Project;
use App\Models\DocumentType;
use App\Helpers\ProjectDocumentHelpers;
use App\Models\user;

new class extends Component {

     protected $listeners = [
        'systemEvent' => '$refresh', 
        'projectEvent' => '$refresh',
        'projectDocumentEvent' => '$refresh',
    ];

    // Accept the project via prop
    public Project $project;

    // Optional: accept the project document via prop
    public ?ProjectDocument $project_document = null;

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


    public function searchProjectName(string $term): array
    {
        // Duplicate check
        $isDuplicate = Project::whereRaw('LOWER(name) = ?', [strtolower($term)])
            ->exists();

        // Suggestions (LIMIT 10)
        $suggestions = Project::query()
            ->where('name', 'like', "%{$term}%")
            ->orderBy('name')
            ->limit(10)
            ->pluck('name')
            ->toArray();

        return [
            'suggestions' => $suggestions,
            'isDuplicate' => $isDuplicate,
        ];
    }


    public function searchDocumentTypeName(string $term): array
    {
        // Check if the record exists
        $recordExists = DocumentType::whereRaw('LOWER(name) = ?', [strtolower($term)])
            ->exists();

        // Suggestions (LIMIT 10)
        $suggestions = DocumentType::query()
            ->where('name', 'like', "%{$term}%")
            ->orderBy('name')
            
            ->limit(10)
            ->pluck('name')
            ->toArray();

        return [
            'suggestions' => $suggestions,
            'recordExists' => $recordExists,
        ];
    }





    public function returnStatusConfig($status){
        return ProjectDocumentHelpers::returnStatusConfig($status);
    }

    public function returnFormattedLabel($status){
        return ProjectDocumentHelpers::returnFormattedLabel($status);
    }

    public function delete($id,$type){
        // dd($id);
        if($type == "project"){
            ProjectHelper::delete($id);

        }
        // dd($id);
    }



    public function submit_project_for_rc_evaluation($id){
 
        ProjectHelper::submit_project_for_rc_evaluation($id);
 
        // dd($id);
    }


    public function force_submit_project_for_rc_evaluation($id){
        ProjectHelper::submit_project_for_rc_evaluation($id,true);
    }



    public function returnFormattedDatetime($datetime){

        return ProjectDocumentHelpers::returnFormattedDatetime($datetime);

    }

    public function returnFormattedUser($userId){

        return ProjectDocumentHelpers::returnFormattedUser($userId);

    }
 

    

    public function returnReviewerName(ProjectDocument $project_document){
        return ProjectDocumentHelpers::returnReviewerName($project_document);
    }

    public function returnSlotData(ProjectDocument $project_document, $type){
        return ProjectDocumentHelpers::returnSlotData($project_document, $type); 
    }

    public function returnReviewStatus($project_document){
        return ProjectDocumentHelpers::returnReviewStatus($project_document); 
    }

    public function returnDueDate(ProjectDocument $project_document, $return_type){
        return ProjectDocumentHelpers::returnDueDate($project_document,$return_type); 

    }

    public function getUserRoles($user_id)
    {

        // dd($user_id);
        // Find the user by ID
        $user = User::find($user_id);

        // Return an empty array if not found
        if (!$user) {
            return [];
        }

        // Return the roles as an array (e.g. ['Admin', 'Reviewer'])
        return $user->getRoleNames()->toArray();
    }


    public function returnReviewFlagsStatus(ProjectDocument $project_document){
         return ProjectDocumentHelpers::returnReviewFlagsStatus($project_document); 
    }




};
?>
<div class="px-4 sm:px-6 lg:px-8 py-2 mx-auto grid grid-cols-12 gap-x-2"
    x-data="{
        openDiscussion : false, 
        openSubscribers : false, 
        openReferences: false,
    }"
    >
    <!-- Header Card -->
    <section class="col-span-12 rounded-2xl border border-slate-200 bg-white px-4 py-6">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">


            <div class="gap-2 lg:gap-0">
                

                <!-- If a Project Document is passed, show its name -->
                @if(!empty($project_document?->document_type->name))


                    <div class="grid lg:flex lg:items-center lg:gap-2">
                        <h1 class="text-xl font-semibold text-sky-900 grid ">
                           <span>
                                @if (request()->routeIs('project-document.review'))
                                    Document Review on 
                                @endif
                            
                                @if (request()->routeIs('project_document.review.flow'))
                                    Review Workflow on 
                                @endif
                    
                            
                            </span>
                            
                            
                            <a href="{{ route('project.project-document.show',[
                                        'project' => $project->id, 
                                        'project_document' => $project_document->id
                                    ]) }}"
                                    wire:navigate
                            
                            > 
                                {{ $project_document->document_type->name ?? 'Untitle Document' }}
                            </a>
                            
                        </h1>
                        <span>
                            <!-- Type chip -->
                            <span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-medium text-sky-700">
                                {{ ucfirst('Project Document') }}
                            </span>
                        </span>
                        
                    </div>
 
                    <div class="mt-1 lg:flex lg:items-center lg:gap-2">
                        

                        <h2 class="text-sm font-medium text-slate-700 truncate max-w-[70ch]" title="View {{ $project->name }}">


                            <a wire:navigate href="{{ route('project.show',['project' => $project->id]) }}">
                                {{ $project->name ?? 'Untitled Project' }} 
                            </a>

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

                                $status = $project->status;
                                $config = $this->returnStatusConfig($status); 
                            
                                $label = $this->returnFormattedLabel($status);
                            @endphp

                            <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium 
                                {{ $config['bg'] ?? 'bg-slate-100' }} 
                                {{ $config['text'] ?? 'text-slate-700' }} 
                                {{ $config['ring'] ?? 'ring-slate-700 ' }}
                                uppercase
                            ">
                                <span class="size-1.5 rounded-full bg-current"></span>
                                {{ $label }}  
                            </span>

                            
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

                            $status = $project->status;
                            $config = $this->returnStatusConfig($status); 
                        
                            $label = $this->returnFormattedLabel($status);
                        @endphp

                        <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium 
                            {{ $config['bg'] ?? 'bg-slate-100' }} 
                            {{ $config['text'] ?? 'text-slate-700' }} 
                            {{ $config['ring'] ?? 'ring-slate-700 ' }}
                            uppercase
                            ">
                            <span class="size-1.5 rounded-full bg-current"></span>
                            {{ $label }}
                        </span>
 

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
                {{-- @if(!empty($project_document?->document_type->name))
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
                @endif --}}

                <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-2 text-sm">

                    <!-- Lot -->
                    <div class="rounded-xl border border-slate-200 bg-white px-4 py-2 shadow-sm">
                        <div class="text-xs text-slate-500">Lot Number</div>
                        <div class="font-semibold text-slate-900">
                            {{ $project->lot_number ?? 'NOT SET' }}
                        </div>
                    </div>

                    <!-- RC -->
                    <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-2 shadow-sm">
                        <div class="text-xs text-blue-600">RC Number</div>
                        <div class="font-semibold text-blue-900">
                            {{ $project->rc_number ?? 'NOT SET' }}
                        </div>
                    </div>


                    @if(Auth::user()->can('system access admin') || Auth::user()->can('system access global admin'))
                        


                        @if(!empty($project) && empty($project->rc_number) && $project->status !== "draft" ) 

                        
                            @php 
                                $project_reviewer = $project->getCurrentReviewer() ?? null;
                            @endphp 

                            @if(!empty($project_reviewer))
                                

                                @if(Auth::user()->can('system access admin') || Auth::user()->can('system access global admin') || Auth::user()->can('system access reviewer'))
                                    <!-- Current Reviewer -->
                                    <div class="rounded-xl border border-amber-200 bg-white px-4 py-2 shadow-sm">
                                        <div class="text-xs text-amber-600">Current Reviewer</div>
                                        <div class="font-semibold text-amber-900">
                                            {{ $project_reviewer->user->name ?? 'NOT CLAIMED' }}
                                        </div>
                                    </div>
                                @endif

                                
                            @endif
                        @endif


                        {{-- @if(!empty($project->project_references))
                            @foreach ($project->project_references as $reference)
                            @php
                            $pr = $reference->referenced_project;
                            @endphp
                                <!-- Current Reviewer -->
                                    <a href="{{ route('project.show',['project' => $pr->id]) }}" class="rounded-xl border border-black bg-white px-4 py-2 shadow-sm">
                                        <div class="text-xs text-slate-600">Reference</div>
                                        <div class="font-semibold text-slate-900">
                                            {{ $pr->name ?? 'NOT SET' }} . <span class="text-slate-500">{{ $pr->rc_number ?? 'NO RC' }}</span>
                                        </div>
                                    </a>
                            @endforeach
                        @endif --}}


 

                    @endif





                </div>

            </div>

             

            


            <!-- Status / Submission lock -->
            <div class="flex flex-col items-start lg:items-end gap-2">
                <!-- Primary Actions -->
                <div class="grid grid-cols-1 text-wrap max-w-xl  lg:flex items-center gap-2" x-data="{ open:false }">

                    <div class="flex space-x-1">

                    
                        {{-- Project options --}}
                            @php
                                $actions = [
                                    [
                                        'display' => auth()->user()->can('project view') || auth()->user()->can('system access global admin'),
                                        'type' => 'link',
                                        'linkHref' =>  route('project.show',['project' => $project->id]),
                                        'linkLabel' => 'View Project'
                                    ],


                                    [
                                        'display' => auth()->user()->can('project edit') || auth()->user()->can('system access global admin'),
                                        'type' => 'link',
                                        'linkHref' =>  route('project.edit',['project' => $project->id]),
                                        'linkLabel' => 'Edit Project'
                                    ],

                                    // [
                                    //     'display' => auth()->user()->can('project delete') || auth()->user()->can('system access global admin'),
                                    //     'type' => 'button',
                                    //     'buttonAction' =>  'delete('.$project->id.',"project")',
                                    //     'buttonLabel' => 'Delete Project'
                                    // ]

                                ]; 
                            @endphp
                            <x-project.dropdown 
                                class="inline-flex items-center gap-1 rounded-xl border border-blue-200  px-3 py-2 text-sm font-medium text-blue-700 bg-white hover:bg-blue-50"

                                menuLabel="Project"
                                :actions="$actions"

                                displayTooltip="true"
                                tooltipText="Click for more project options"
                            />
                        {{-- ./ Project options --}}

                        {{-- Project Document options --}}
                            @php
                                $actions = [
                                    [
                                        'display' => auth()->user()->can('project view') || auth()->user()->can('system access global admin'),
                                        'type' => 'link',
                                        'linkHref' => route('project.project_documents', ['project' => $project->id]),
                                        'linkLabel' => 'View Documents',
                                    ],
                                ];

                                if (!empty($project_document)) {
                                    $actions = array_merge($actions, [
                                        [
                                            'display' => !empty($project_document) && $canReview,
                                            'type' => 'link',
                                            'linkHref' => route('project.document.reviewer.index', [
                                                'project' => $project->id,
                                                'project_document' => $project_document->id,
                                            ]),
                                            'linkLabel' => 'View Reviewers',
                                        ],


                                        // [
                                        //     'display' => !empty($project_document) && $canReview,
                                        //     'type' => 'link',
                                        //     'linkHref' => route('project_document.review.flow', [ 
                                        //         'project_document' => $project_document->id,
                                        //     ]),
                                        //     'linkLabel' => 'View Review Flow',
                                        // ],


                                    ]);




                                }





                                // $actions = array_merge($actions, [
                                //     [
                                //         'display' => auth()->user()->can('project delete') || auth()->user()->can('system access global admin'),
                                //         'type' => 'button',
                                //         'buttonAction' => 'delete(' . $project->id . ',"project")',
                                //         'buttonLabel' => 'Delete Project',
                                //     ],
                                // ]);
                            @endphp
                            <x-project.dropdown 
                                class="inline-flex items-center gap-1 rounded-xl border border-yellow-200  px-3 py-2 text-sm font-medium text-yellow-700 bg-white hover:bg-yellow-50"

                                menuLabel="Documents"
                                :actions="$actions"

                                displayTooltip="true"
                                tooltipText="Click for more project document options"
                            />
                        {{-- ./ Project Document options  --}}

                        {{-- <a href="{{ route('project.project-document.index',['project' => $project->id]) }}"
                        wire:navigate
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Documents
                        </a> --}}

                        @if(  $project->status == "draft" && empty($project->rc_number))
                            <x-ui.button
                                type="button"
                                label="Submit Project"
                                sr="Click to Submit Project"
                                {{-- wire:click="submit_project({{ $project->id }})" --}}
                                onclick="confirm('Are you sure you want to submit this project for administrative review?') || event.stopImmediatePropagation()" 
                                wire:click.prevent="submit_project_for_rc_evaluation({{ $project->id }})"
                                displayTooltip
                                position="top"
                                tooltipText="Click to Submit Project"

                                class="inline-flex items-center text-nowrap gap-1 rounded-xl border border-white  px-3 py-2 text-sm font-medium text-white bg-blue-500 hover:bg-blue-600"


                            >
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6 12L3 21l18-9L3 3l3 9zm0 0h6" />
                                </svg>
                            </x-ui.button>
                        @endif

                        @if(auth()->user()->can('system access admin') || auth()->user()->can('system access global admin'))
                            @if($project->status == "on_que" && empty($project->rc_number))
                                <x-ui.button
                                    type="button"
                                    label="Force Submit Project"
                                    sr="Click to Submit Project"
                                    {{-- wire:click="submit_project({{ $project->id }})" --}}
                                    onclick="confirm('Are you sure you want to force submit this project for administrative review?') || event.stopImmediatePropagation()" 
                                    wire:click.prevent="force_submit_project_for_rc_evaluation({{ $project->id }})"
                                    displayTooltip
                                    position="top"
                                    tooltipText="Click to Force Submit Project"

                                    class="inline-flex items-center gap-1 rounded-xl border border-white  px-3 py-2 text-sm font-medium text-white bg-blue-700 hover:bg-blue-900"


                                />  
                            @endif
                        @endif

                        @if(!empty($project->rc_number))
                        <x-project.button-link 
                            linkLabel=""
                            linkHref="{{ route('project.project-document.create',['project' => $project->id]) }}"

                            displayTooltip="true"
                            tooltipText="Create a new project document"

                            class="inline-flex items-center gap-1 rounded-xl border border-white  px-3 py-2 text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600"


                        >
                            <svg class="w-5 h-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.5">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>


                        </x-project.button-link>  
                        @endif
                     
                        <!-- Discussion button and box -->
                            <x-ui.project.page-header.project-discussion-box  :project="$project" />
                        <!-- ./ Discussion button and box -->

                        <!-- Project subscribers button and box -->
                            <x-ui.project.page-header.project-subs-box :project="$project" /> 
                        <!-- ./ Project subscribers button and box -->
                        

                        @if(!empty($project->rc_number))
                            @if(Auth::user()->can('system access global admin') || Auth::user()->can('system access admin') || Auth::user()->can('system access admin')    )
                            <!-- Project References -->
                                <x-ui.project.page-header.project-reference-box :project="$project" />
                            <!-- ./ Project References -->
                            @endif
                        @endif


                        @if(auth()->user()->can('project delete') || auth()->user()->can('system access global admin'))
                            <button
                                type="button" 
                                onclick="confirm('Are you sure, you want to delete this record?') || event.stopImmediatePropagation()"
                                wire:click.prevent="delete({{ $project->id }},'project')" 
                                class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow
                                hover:bg-red-700
                                focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700
                                transition"
                            > 
                                <!-- Minimal trash icon (clean, non-cartoon) -->
                                <svg class="w-5 h-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.6"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    aria-hidden="true">
                                    <path d="M4 7h16" />
                                    <path d="M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2" />
                                    <path d="M7 7l1 12a2 2 0 002 2h4a2 2 0 002-2l1-12" />
                                </svg>
                            </button>
                        @endif
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

        {{-- region Project document information || if project document exist --}}
        @if(!empty($project_document))
            {{-- -if project is not draft  --}}

            @if(
            $project_document->status !== "draft" && $project_document->status !== "on_que" 
            )
                {{-- <livewire:partials.table-reviewer-status-badge  
                    :status="$project_document->status"
                    :project-document="$project_document"
                /> --}}
                @php    
                    $config = $this->returnStatusConfig($project_document->status); 
                    $reviewerName = $this->returnReviewerName($project_document);
                    $slotType = $this->returnSlotData($project_document,'slot_type');
                    $slotRole = $this->returnSlotData($project_document,'slot_role');
                    $reviewStatus = $this->returnReviewStatus($project_document);

                    

                    $dueAtText = $this->returnDueDate($project_document, 'dueAtText');
                    $dueAtDiff = $this->returnDueDate($project_document, 'dueAtDiff');
                    
                    
                    $flags = $this->returnReviewFlagsStatus($project_document);
                @endphp


                <div class="p-2 align-top rounded-xl shadow border border-slate-100 hover:border-blue-500 mt-2"
                    x-data="{ 

                        roleColors: {
                            'global admin': 'bg-red-100 text-red-700 ring-red-200',
                            'admin':        'bg-amber-100 text-amber-800 ring-amber-200',
                            'reviewer':     'bg-sky-100 text-sky-700 ring-sky-200',
                            'user':         'bg-slate-100 text-slate-700 ring-slate-200',
                            '__none':       'bg-zinc-100 text-zinc-700 ring-zinc-200',
                        },

                        badgeCls(role){
                            const key = (role || '').toLowerCase();
                            const base = 'px-1.5 py-0.5 rounded-md text-xs ring-1';
                            return `${base} ${(this.roleColors[key] ?? this.roleColors['__none'])}`;
                        }, 
                    }"  

                    >
                        


                    @if($project_document->status != "approved")
                    <!-- Reviewer block -->
                    <div class="space-y-1 text-[11px] leading-4 text-slate-600 sm:flex sm:justify-between sm:space-x-4 ">
                        <div class="flex items-center gap-1 justify-end">
                            <!-- Eye/user icon -->
                            <svg class="size-3.5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <span class="font-medium text-slate-700">Reviewer:</span>
                            <span class="truncate">{{ $reviewerName }}</span>
                            
                                @if($slotType === 'person')

                                    @php 
                                        // dd($project_document->user_id);

                                        $project_reviewer = $project_document->getCurrentReviewerByProjectDocument(); // get the current reviewer


                                        $roles = $this->getUserRoles($project_reviewer->user_id);

                                        // dd($roles);
                                    @endphp

                                    {{-- <span class="ml-1 rounded border border-slate-200 bg-slate-50 px-1.5 py-0.5 text-[10px] text-slate-700">{{ $slotRole }}</span> --}}
                                    <!-- If this row has a userId (person or claimed open slot), show that user's roles -->
                                    @if(!empty($roles))
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            @foreach ($roles as $role)
                                                <span :class="badgeCls('{{ $role }}')"  >{{ $role }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span :class="badgeCls('')">No role</span>
                                    @endif


                                @elseif(($slotType === 'open'))

                                    @if($slotType === 'open' && empty($project_document->user_id)) 
                                        <span class="ml-1 rounded border border-amber-200 bg-amber-50 px-1.5 py-0.5 text-[10px] text-amber-700">Open</span>
                                        @php 
                                            // dd($project_document->user_id);

                                            $project_reviewer = $project_document->getCurrentReviewerByProjectDocument(); // get the current reviewer


                                            $roles = $this->getUserRoles($project_reviewer->user_id);

                                            // dd($roles);
                                        @endphp
                                        {{-- <span class="ml-1 rounded border border-slate-200 bg-slate-50 px-1.5 py-0.5 text-[10px] text-slate-700">{{ $slotRole }}</span> --}}
                                        <!-- If this row has a userId (person or claimed open slot), show that user's roles -->
                                        @if(!empty($roles))
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                @foreach ($roles as $role)
                                                    <span :class="badgeCls('{{ $role }}')"  >{{ $role }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span :class="badgeCls('')">No role</span>
                                        @endif

                                        
                                    @elseif($slotType === 'open' && !empty($project_document->user_id))
                                        <span class="ml-1 rounded border border-sky-200 bg-sky-50 px-1.5 py-0.5 text-[10px] text-sky-700">Claimed</span>
                                    @else
                                        <span class="ml-1 rounded border border-amber-200 bg-amber-50 px-1.5 py-0.5 text-[10px] text-amber-700">Open</span>
                                    @endif
                                @endif
                                    
                         

                        @if(!empty($reviewStatus))
                         
                            <svg class="size-3.5 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M10 18a8 8 0 100-16 8 8 0 000 16Zm3.707-9.707a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 10-1.414 1.414L9 13.414l4.707-4.707Z"/>
                            </svg>
                            <span class="font-medium text-slate-700">Review status:</span>
                            <span class="uppercase tracking-wide">{{ str_replace('_', ' ', $reviewStatus) }}</span>
                        
                        @endif

                        </div>
                    {{-- </div>
                    <div class="space-y-1 text-[11px] leading-4 text-slate-600 sm:flex sm:justify-between sm:space-x-4 "> --}}

                        <div class="flex items-center gap-1 justify-end">
                            <svg class="size-3.5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M3.5 9h17M5 20h14a2 2 0 0 0 2-2v-9H3v9a2 2 0 0 0 2 2Z"/>
                            </svg>
                            <span class="font-medium text-slate-700">
                                {{ $project_document->allow_project_submission == true ? 'Response due' : 'Review due'}}:
                            </span>
                            @if($dueAtText)
                                <span>{{ $dueAtText }}</span>
                                <span class="text-slate-400">({{ $dueAtDiff }})</span>
                            @else
                                <span class="italic text-slate-400">No due date</span>
                            @endif
                        </div>

                        <!-- Flags -->
                        <div class="flex flex-wrap items-center justify-end gap-1.5 pt-0.5">
                            @php
                                $flagLabels = [
                                    'requires_project_update' => 'Project update',
                                    // 'requires_document_update' => 'Document update',
                                    'requires_attachment_update' => 'Attachment update',
                                ];
                            @endphp

                            @foreach($flagLabels as $key => $label)
                                @if($flags[$key] ?? false)
                                    <span class="inline-flex items-center gap-1 justify-end rounded-md bg-amber-50 px-1.5 py-0.5 ring-1 ring-amber-200 text-[10px] font-medium text-amber-700">
                                        <svg class="size-3" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a8 8 0 100 16 8 8 0 000-16Zm.75 4a.75.75 0 00-1.5 0v5.25c0 .414.336.75.75.75h3.5a.75.75 0 000-1.5h-2.75V6Z"/></svg>
                                        {{ $label }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 justify-end rounded-md bg-slate-50 px-1.5 py-0.5 ring-1 ring-slate-200 text-[10px] text-slate-500">
                                        <svg class="size-3" viewBox="0 0 20 20" fill="currentColor"><path d="M10 18a8 8 0 100-16 8 8 0 000 16Zm-1-5 5-5-1.414-1.414L9 10.172 7.414 8.586 6 10l3 3Z"/></svg>
                                        {{ $label }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @elseif($project_document->status == "approved")
                    

                    <!-- Reviewer block -->
                    <div class="space-y-1 text-[11px] leading-4 text-slate-600">
                        <div class="flex items-center gap-1">
                            
                            <span class="font-medium text-slate-700">All reviewers had approved the project document</span> 

                        </div>
                    </div>
                    @endif

                </div>





            @endif
 

        @endif
        {{-- endregion Project document information || if project document exist --}}

    </section>








  


</div>
