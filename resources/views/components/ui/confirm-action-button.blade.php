@props([
    'permission' => null,
    'buttonLabel' => 'Confirm',
    'modalTitle' => 'Confirm Action',
    'modalMessage' => 'Are you sure you want to proceed?',
    'wireAction' => null,

    // Tooltip (optional)
    'displayTooltip' => false,
    'position' => 'top',
    'tooltipText' => '',
    'tooltipLabelTextArrMsg' => [],

    // Trigger look (like your reference) 
    'class' => 'inline-flex items-center gap-2 rounded-lg bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-sky-700 focus-visible:outline focus-visible:ring',

])
@php
  $posClass = match ($position) {
      'bottom' => 'top-full mt-2 left-1/2 -translate-x-1/2',
      'right'  => 'left-full ml-2 top-1/2 -translate-y-1/2',
      'left'   => 'right-full mr-2 top-1/2 -translate-y-1/2',
      default  => 'bottom-full mb-2 left-1/2 -translate-x-1/2',
  };
@endphp


<div x-data="{ open:false }" class="relative inline-flex items-center align-middle">
  

    @if( Auth::user()->can('system access global admin') || ($permission && Auth::user()->hasPermissionTo($permission)) )
        <div x-data="{ openModal:false }" class="shrink-0">
            <!-- Trigger -->
            <button type="button"
                @click="openModal = true"
                @mouseenter="open = true" @mouseleave="open = false"
                @focus="open = true" @blur="open = false"
                @keydown.escape.window="open = false"

                class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent bg-indigo-600 text-white hover:bg-indigo-700 disabled:opacity-60"
                wire:loading.attr="disabled"
                wire:target="{{ $wireAction }}">
                {{ $buttonLabel }}
            </button>

            <!-- Backdrop -->
            <div x-show="openModal"
                x-transition.opacity
                class="fixed inset-0 z-40 bg-black/40"
                @keydown.escape.window="openModal = false"
                aria-hidden="true"></div>

            <!-- Modal -->
            <div x-show="openModal"
                x-transition
                class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div @click.away="openModal = false"
                    class="w-full max-w-md rounded-xl bg-white shadow-xl ring-1 ring-black/5">

                    <!-- Header -->
                    <div class="px-5 py-4 border-b border-slate-200">
                        <h2 class="text-base font-semibold text-slate-800">{{ $modalTitle }}</h2>
                    </div>

                    <!-- Body -->
                    <div class="px-5 py-4 space-y-3 text-sm text-slate-700">
                        <p>{{ $modalMessage }}</p>
                    </div>

                    <!-- Footer -->
                    <div class="px-5 py-4 flex items-center justify-end gap-2 border-t border-slate-200">
                        <button type="button"
                                @click="openModal = false"
                                class="px-3.5 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">
                            Cancel
                        </button>

                        <button type="button"
                                @click="$wire.{{ $wireAction }}(); openModal = false"
                                class="px-3.5 py-2 rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60"
                                wire:loading.attr="disabled"
                                wire:target="{{ $wireAction }}">
                            Yes, Proceed
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif



  @if($displayTooltip)
    <div x-show="open" x-cloak role="tooltip"
         class="absolute z-50 w-56 text-xs bg-slate-900 text-white rounded-md px-3 py-2 shadow-lg pointer-events-none {{ $posClass }}"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1">
      {{ $tooltipText }}
      @if(!empty($tooltipLabelTextArrMsg))
        <div class="my-1 space-y-1">
          @foreach($tooltipLabelTextArrMsg as $msgKey => $msgText)
            <div><span class="text-lime-500 uppercase">{{ $msgKey }}</span>: {{ $msgText }}</div>
          @endforeach
        </div>
      @endif
    </div>
  @endif
</div>





{{-- 
Example Usage:
<x-confirm-action-button 
    permission="project reviewer edit"
    button-label="Save All"
    modal-title="Confirm Save"
    modal-message="Some reviewers cannot be removed because they are already assigned. Are you sure you want to save these records?"
    wire-action="save"
/>

--}}
