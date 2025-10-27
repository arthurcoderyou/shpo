<?php
use Livewire\Attributes\Modelable;
use Livewire\Volt\Component;

new class extends Component {


    #[Modelable]
    public string $search = '';


    // Header/title + counter
    public string $header_title = 'Project Documents';
    public int $total = 0;

     
     
 
    // Example bound models (you can add/remove freely)
    public string $review = 'all';
    public string $sort = 'updated_at_desc';

    public array $filter_sets = [];

 


    public function mount(
        string $header_title = 'Projects',
        int $total = 0,
        array $filter_sets = [],
         
    ): void {
        $this->header_title = $header_title;
        $this->total = $total;
        $this->filter_sets = !empty($filter_sets) ? $filter_sets : [
            [
                'label' => 'Review',
                'model' => 'review',
                'placeholder' => 'All reviews',
                'options' => [
                    ['value' => 'all', 'label' => 'All reviews'],
                    ['value' => 'approved', 'label' => 'Approved'],
                    ['value' => 'in-review', 'label' => 'In review'],
                    ['value' => 'changes-requested', 'label' => 'Changes requested'],
                    ['value' => 'draft', 'label' => 'Draft'],
                ],
            ],
            [
                'label' => 'Sort',
                'model' => 'sort',
                'options' => [
                    ['value' => 'updated_at_desc', 'label' => 'Sort: Updated (desc)'],
                    ['value' => 'submitted_at_desc', 'label' => 'Submitted (desc)'],
                    ['value' => 'name_asc', 'label' => 'Name (A–Z)'],
                ],
            ],
        ];

         

        // // Ensure keys exist for every configured select
        // foreach ($this->filter_sets as $set) {
        //     if (!empty($set['model']) && !array_key_exists($set['model'], $this->filters)) {
        //         $this->filters[$set['model']] = $set['options'][0]['value'] ?? '';
        //     }
        // }
    }

 

    

    public function create(): void
    {
        // Replace with your actual create logic (modal, redirect, etc.)
        $this->dispatch('openModal', 'documents.create');
    }
};

?>

<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-3">
        <h1 class="text-xl font-semibold text-slate-900 sm:text-2xl">
            {{ $header_title }}
        </h1>
        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-200">
            {{ $total }} total
        </span>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        {{-- Search --}}
        <div class="relative">
            <input
                type="search"
                wire:model.live="search"
                placeholder="Search documents..."
                class="w-64 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm shadow-sm outline-none focus:border-slate-300"
            />
            <span class="pointer-events-none absolute right-3 top-2.5 text-slate-400">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </span>
        </div>

        {{-- Create Button --}}
        <button
            wire:click="create"
            class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2"
        >
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14m-7-7h14"/>
            </svg>
            Add New {{ $search }}
        </button>

        {{-- Dynamic Filter Selects --}}
        {{-- @foreach ($filter_sets as $set)
            @php $model = $set['model'] ?? null; @endphp
            @if($model)
                <label class="sr-only">{{ $set['label'] ?? '' }}</label>
                <select
                    wire:model.live="{{ $model }}"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm outline-none"
                >
                    @if(!empty($set['placeholder']))
                        <option value="">
                            {{ $set['placeholder'] }}
                        </option>
                    @endif

                    @foreach ($set['options'] ?? [] as $opt)
                        <option value="{{ $opt['value'] ?? '' }}">
                            {{ $opt['label'] ?? $opt['value'] ?? '' }}
                        </option>
                    @endforeach
                </select>
            @endif
        @endforeach --}}
    </div>

    

</div>
