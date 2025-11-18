{{-- -
Required to add to the Parent Livewire Component .300ms

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



To Use:
<x-ui.project.project-name-input wire:model.live="name" />

--}}


@props([
    'label' => 'Project Name',
    'placeholder' => 'Enter project name...',
    'model' => $attributes->wire('model')->value(), // Livewire binding
])

<div
    x-data="{
        open: false,
        suggestions: [],
        isDuplicate: false,

        fetchSuggestions(term) {
            if (!term) {
                this.suggestions = [];
                this.isDuplicate = false;
                return;
            }

            // Call Livewire method (from parent)
            $wire.searchProjectName(term)
                 .then(result => {
                     this.suggestions  = result.suggestions;
                     this.isDuplicate  = result.isDuplicate;
                 });
        },
    }"
    @click.outside="open = false"
    class="relative w-full max-w-xl"
>
    <label class="block text-sm font-medium text-slate-700 mb-1">
        {{ $label }}
    </label>

    <input
        type="text"
        {{ $attributes->merge([
            'class' =>
                'block w-full rounded-lg border bg-white px-3 py-2 text-sm shadow-sm
                 focus:ring focus:ring-sky-200 focus:ring-offset-0 transition'
        ]) }}
        :class="isDuplicate
            ? 'border-rose-500 focus:border-rose-500'
            : 'border-slate-300 focus:border-sky-500'"
        placeholder="{{ $placeholder }}"
        @focus="open = true"
        @input.debounce.300ms="
            open = true;
            fetchSuggestions($el.value);
        "
        @keydown.escape.window="open = false"
        autocomplete="off"
    />

    {{-- Duplicate warning --}}
    <p
        x-show="isDuplicate"
        class="mt-1 text-xs text-rose-600"
    >
        This project name already exists.
    </p>

    {{-- Suggestions dropdown --}}
    <div
        x-show="open && suggestions.length"
        x-transition
        class="absolute z-20 mt-1 w-full rounded-lg border border-slate-200 bg-white shadow-lg max-h-60 overflow-y-auto text-sm"
    >
        <template x-for="name in suggestions" :key="name">
            <button
                type="button"
                @click="
                    $wire.set('{{ $model }}', name);
                    open = false;
                "
                class="flex w-full items-center px-3 py-2 text-left hover:bg-sky-50"
            >
                <span class="text-slate-800" x-text="name"></span>
            </button>
        </template>
    </div>
</div>
