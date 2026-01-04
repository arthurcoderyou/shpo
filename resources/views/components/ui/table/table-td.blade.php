@props([
  'text' => 'text', 

  // Tooltip (optional)
  'displayTooltip' => false,
  'position' => 'top',
  'tooltipText' => '',
  'tooltipLabelTextArrMsg' => [],

  // Trigger look (like your reference)
  'linkHref' => null,            // if set, renders <a> instead of <button>
  'class' => "",
])

@php
  $posClass = match ($position) {
      'bottom' => 'top-full mt-2 left-1/2 -translate-x-1/2',
      'right'  => 'left-full ml-2 top-1/2 -translate-y-1/2',
      'left'   => 'right-full mr-2 top-1/2 -translate-y-1/2',
      default  => 'bottom-full mb-2 left-1/2 -translate-x-1/2',
  };
@endphp

<div x-data="{ open:false }" class="relative inline-flex items-center align-middle"
    @mouseenter="open=true"
    @mouseleave="open=false"
    @focusin="open=true"
    @focusout="open=false"
    >
 
    <span  
        {{ $attributes->merge(['class' => $class]) }}
    >
        {{ $text }}
    </span>
 

    @if($displayTooltip)
        <div x-show="open" x-cloak role="tooltip"
            class="absolute z-100 w-56 text-xs bg-slate-900 text-white rounded-md px-3 py-2 shadow-lg pointer-events-none {{ $posClass }}"
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
