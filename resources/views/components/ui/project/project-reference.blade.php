@props([
  // Core 
  'name' => '', 
  'rc_number' => '', 
  'location' => '', 

  // Tooltip (optional)
  'displayTooltip' => false,
  'position' => 'top',          // top, right, bottom, left
  'tooltipText' => '',
  'tooltipLabelTextArrMsg' => [],   
])

@php
  $name = $name ?? $id; 
  $posClass = match ($position) {
      'bottom' => 'top-full mt-2 left-1/2 -translate-x-1/2',
      'right'  => 'left-full ml-2 top-1/2 -translate-y-1/2',
      'left'   => 'right-full mr-2 top-1/2 -translate-y-1/2',
      default  => 'bottom-full mb-2 left-1/2 -translate-x-1/2',
  };
@endphp
 
<div x-data="{ open:false }" class=" w-full">
    

    <div class="flex items-center gap-3 min-w-0"
        @mouseenter="open = true" @mouseleave="open = false"
        @focus="open = true" @blur="open = false"
        @keydown.escape.window="open = false"
        >
        <div class="grid size-8 place-content-center rounded-full bg-slate-100 text-xs font-semibold text-slate-700">
            {{ strtoupper(Str::of( $name )->explode(' ')->map(fn($p)=>Str::substr($p,0,1))->take(2)->implode('')) }}
        </div>
        <div class="min-w-0">
            <p class="truncate text-sm font-medium text-slate-900">{{ $rc_number }}</p>
            <p class="truncate text-xs text-slate-600">{{ $location }}</p>
        </div>
    </div> 



   <div
        x-show="open"
        x-cloak
        role="tooltip"
        class="absolute z-50 w-72 rounded-lg
            bg-white text-slate-900
            border border-slate-300
            shadow-xl ring-1 ring-slate-200
            pointer-events-none {{ $posClass }}"
        x-transition:enter="transition ease-out duration-120"
        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-90"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
    >
        {{-- Header --}}
        <div class="px-3 pt-2 pb-2 border-b border-slate-200">
            <p class="text-sm font-semibold text-slate-900 leading-tight">
                {{ $tooltipText }}
            </p>
        </div>

        {{-- Content --}}
        <div class="px-3 py-2 space-y-2">
            @foreach($tooltipLabelTextArrMsg as $msgKey => $msgText)
                <div class="grid grid-cols-[14px_1fr] gap-2 items-start">
                    
                    {{-- Disc-style list bullet --}}
                    <svg
                        class="w-3.5 h-3.5 mt-1 text-slate-700"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 8 8"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <circle cx="4" cy="4" r="3" />
                    </svg>

                    {{-- Text --}}
                    <div class="leading-snug">
                        <span class="block text-[11px] font-semibold uppercase tracking-wide text-slate-700">
                            {{ $msgKey }}
                        </span>
                        <span class="text-slate-900">
                            {{ $msgText }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>



</div> 
