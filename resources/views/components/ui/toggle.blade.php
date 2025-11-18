@props([
  'id',
  'name' => null,
  'label' => null,          // visible label
  'onLabel' => 'On',
  'offLabel' => 'Off',
  'checked' => false,       // default state
  'help' => null,
  'error' => null,
  'disabled' => false,

  // Tooltip
  'displayTooltip' => false,
  'position' => 'top',
  'tooltipText' => '',
  'tooltipLabelTextArrMsg' => [],

  'labelClass' => 'text-sm font-medium text-slate-700',
  'triggerClass' => 'inline-flex items-center gap-1 rounded-xl border border-slate-200 px-2 py-1 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50',
  'linkLabel' => null,
  'linkHref' => '#',
])

@php
  $name = $name ?? $id;
  $helpId = $help ? $id.'-help' : null;
  $errId  = $error ? $id.'-error' : null;
  $describedBy = collect([$helpId, $errId])->filter()->implode(' ');
  $posClass = match ($position) {
      'bottom' => 'top-full mt-2 left-1/2 -translate-x-1/2',
      'right'  => 'left-full ml-2 top-1/2 -translate-y-1/2',
      'left'   => 'right-full mr-2 top-1/2 -translate-y-1/2',
      default  => 'bottom-full mb-2 left-1/2 -translate-x-1/2',
  };
@endphp

<div class="w-full">
  {{-- LABEL + OPTIONAL TOOLTIP --}}
  @if($label)
    <div x-data="{ open:false }" class="flex items-center gap-2 mb-1 relative">
      <label for="{{ $id }}" class="{{ $labelClass }}">
        {{ $label }}
      </label>

      {{-- If linkLabel is provided, show it as a separate trigger (optional) --}}
      @if($linkLabel)
        <a href="{{ $linkHref }}"
           @mouseenter="open=true" @mouseleave="open=false"
           @focus="open=true" @blur="open=false"
           @keydown.escape.window="open=false"
           class="{{ $triggerClass }}">
          {{ $linkLabel }}
        </a>
      @endif

      {{-- Tooltip icon on label (works even without linkLabel) --}}
      @if($displayTooltip)
        <button type="button"
                @mouseenter="open=true" @mouseleave="open=false"
                @focus="open=true" @blur="open=false"
                @keydown.escape.window="open=false"
                class="inline-flex items-center rounded-md border px-2 py-1 text-xs">
          ?
          <span class="sr-only">Help</span>
        </button>

        <div x-show="open" x-cloak role="tooltip"
             class="absolute z-50 max-w-xs text-xs bg-slate-900 text-white rounded-md px-3 py-2 shadow-lg {{ $posClass }}">
          {{ $tooltipText }}
          @if(!empty($tooltipLabelTextArrMsg))
            <div class="mt-1 space-y-1">
              @foreach($tooltipLabelTextArrMsg as $k=>$v)
                <div><span class="text-lime-400 uppercase">{{ $k }}</span>: {{ $v }}</div>
              @endforeach
            </div>
          @endif
        </div>
      @endif
    </div>
  @endif

  {{-- OUTER CLICKABLE ROW / CARD --}}
  <label
    for="{{ $id }}"
    class="flex w-full items-center justify-between px-3 py-2 rounded-xl border border-slate-200 bg-white shadow-sm
          @if(!$disabled) cursor-pointer hover:border-slate-300 @else opacity-60 cursor-not-allowed @endif
          transition-colors duration-150"
  >
    {{-- HIDDEN INPUT (PEER) --}}
    <input
      id="{{ $id }}"
      name="{{ $name }}"
      type="checkbox"
      class="sr-only peer"
      role="switch"
      aria-describedby="{{ $describedBy }}"
      @checked(old($name, $checked))
      @if($disabled) disabled @endif
      {{ $attributes->except('class') }} {{-- keep wire:model, etc. --}}
    >

    {{-- TRACK + THUMB (via ::before) --}}
    <span
      class="relative inline-flex w-11 h-6 rounded-full border border-slate-300 bg-slate-200/80
            transition-all duration-200 ease-out
            peer-checked:bg-sky-600 peer-checked:border-sky-700
            peer-focus-visible:ring-2 peer-focus-visible:ring-sky-400 peer-focus-visible:ring-offset-1 peer-focus-visible:ring-offset-white
            peer-checked:shadow-[0_0_0_2px_rgba(56,189,248,0.35)]
            before:content-[''] before:absolute before:top-0.5 before:left-0.5
            before:w-5 before:h-5 before:bg-white before:rounded-full before:shadow
            before:transition-transform before:duration-200 before:ease-out
            peer-checked:before:translate-x-5 peer-checked:before:scale-105"
    ></span>

    {{-- TEXT LABELS (YES / NO) --}}
    <span class="ml-2 text-sm text-slate-800 select-none peer-checked:hidden">
      {{ $offLabel }}
    </span>
    <span class="ml-2 text-sm text-slate-800 select-none hidden peer-checked:inline">
      {{ $onLabel }}
    </span>
  </label>

  @if($help)
    <p id="{{ $helpId }}" class="mt-1 text-xs text-slate-500">{{ $help }}</p>
  @endif
  @if($error)
    <p id="{{ $errId }}" class="mt-1 text-sm text-red-700">{{ $error }}</p>
  @endif
</div>
