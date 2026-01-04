@props([ 

    'displayTooltip' => false,
    'position' => 'top', // top, right, bottom, left
    'tooltipText' => '',
    'tooltipLabelTextArrMsg' => [],
 
    'buttonLabel' => 'Label',
    'buttonAction' => '#',
    'confirm' => "",
    'confirmationMessage' => '',
 
    'class' => 'inline-flex items-center gap-1 rounded-xl border border-slate-200  px-3 py-2 text-sm font-medium text-slate-700 bg-white hover:bg-slate-50',

])

@php
    $posClass = match ($position) {
        'bottom' => 'top-full mt-2 left-1/2 -translate-x-1/2',
        'right'  => 'left-full ml-2 top-1/2 -translate-y-1/2',
        'left'   => 'right-full mr-2 top-1/2 -translate-y-1/2',
        default  => 'bottom-full mb-2 left-1/2 -translate-x-1/2', // top
    };
@endphp

{{-- Optional global x-cloak style (only include once globally)
<style>[x-cloak]{ display:none !important; }</style>
--}}
 
    
<div x-data="{ open: false }"
    class="relative inline-flex items-center align-middle"

    @mouseenter="open = true"
    @mouseleave="open = false"
    @focus="open = true"
    @blur="open = false"
    @keydown.escape.window="open = false"
    
    >
    @if($confirm == "no")

        <!-- Trigger -->
        <button
            type="button"
            class="{{ $class }}" 
            wire:click="{{ $buttonAction }}" 
        >
            

            <svg class="w-6 h-6 text-slate-600"  xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11.25V6a3 3 0 10-6 0m6 5.25H9a2.25 2.25 0 00-2.25 2.25v6A2.25 2.25 0 009 21.75h6a2.25 2.25 0 002.25-2.25v-6A2.25 2.25 0 0015 11.25z" />
            </svg>
            <span class="hidden sm:block">
                {{ $buttonLabel }}
            </span> 
            
            
        </button>
    
    @elseif($confirm == "yes" && !empty($confirmationMessage))

        <button
            type="button"
            class="{{ $class }}"
 
            onclick="confirm($confirmationMessage) || event.stopImmediatePropagation()"
            wire:click.prevent="{{ $buttonAction }}" 

            
        >
            

            <svg class="w-6 h-6 text-slate-600"  xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11.25V6a3 3 0 10-6 0m6 5.25H9a2.25 2.25 0 00-2.25 2.25v6A2.25 2.25 0 009 21.75h6a2.25 2.25 0 002.25-2.25v-6A2.25 2.25 0 0015 11.25z" />
            </svg>

            <span class="hidden sm:block">
                {{ $buttonLabel }}
            </span> 
            
        </button>

    @endif


    @if($displayTooltip )
        <!-- Tooltip panel -->
        <div x-show="open"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-1"
                x-cloak
                class="absolute z-50 w-56 h-auto text-xs bg-slate-900 text-white rounded-md px-3 py-2 shadow-lg pointer-events-none {{ $posClass }}"
                role="tooltip">
            {{ $tooltipText }}

            @if(!empty($tooltipLabelTextArrMsg))
                <div class="my-1 space-y-1">
                    @foreach ($tooltipLabelTextArrMsg as $msgKey => $msgText)
                        <div>
                            <span class="text-lime-500 uppercase">{{ $msgKey }}</span>: {{ $msgText }}
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    @endif





</div>  


 
