@props([
  // Core
  'id',
  'name' => null,
  'type' => 'text',
  'value' => null,
  'label' => null,
  'required' => false,
  'disabled' => false,
  'placeholder' => '',
  'help' => null,               // small help text under the field
  'error' => null,              // pass $errors->first('field') if any
  'warning' => null,

  // Tooltip (optional)
  'displayTooltip' => false,
  'position' => 'top',          // top, right, bottom, left
  'tooltipText' => '',
  'tooltipLabelTextArrMsg' => [],

  // Trigger (label) link look (same idea as your reference)
  'linkLabel' => null,          // If set, label turns into a clickable styled trigger
  'linkHref' => '#',
  'labelClass' => 'block text-sm font-medium text-slate-700',
  'triggerClass' => 'inline-flex items-center gap-1 rounded-xl border border-slate-200 px-2 py-1 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50',

  'xInit' => null,
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
  {{-- Label + optional tooltip trigger --}}
  @if($label)
    <div class="flex items-center gap-2 mb-1">
      @if($linkLabel)
        <div x-data="{ open:false }" class="relative inline-flex items-center align-middle">
          <a href="{{ $linkHref }}"
             @mouseenter="open = true" @mouseleave="open = false"
             @focus="open = true" @blur="open = false"
             @keydown.escape.window="open = false"
             class="{{ $triggerClass }}">
            {{ $linkLabel }}
          </a>

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
        <label for="{{ $id }}" class="{{ $labelClass }}">{{ $label }} @if($required)<span class="text-red-600">*</span>@endif</label>
      @else
        <label for="{{ $id }}" class="{{ $labelClass }}">{{ $label }} @if($required)<span class="text-red-600">*</span>@endif</label>
        @if($displayTooltip)
          <div x-data="{ open:false }" class="relative">
            <button type="button"
                    @mouseenter="open = true" @mouseleave="open = false"
                    @focus="open = true" @blur="open = false"
                    @keydown.escape.window="open = false"
                    class="inline-flex items-center rounded-md border px-2 py-1 text-xs">
              ?
              <span class="sr-only">Help</span>
            </button>
            <div x-show="open" x-cloak role="tooltip"
                 class="absolute z-50 w-56 text-xs bg-slate-900 text-white rounded-md px-3 py-2 shadow-lg pointer-events-none {{ $posClass }}"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-1">
              {{ $tooltipText }}
              <div class="my-2 space-y-1 text-xs">
                @foreach($tooltipLabelTextArrMsg as $msgKey => $msgText)
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-lime-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M5 13l4 4L19 7" />
                        </svg>

                        <div>
                            <span class="font-semibold text-white">{{ $msgKey }}:</span>
                            <span class="text-slate-300">{{ $msgText }}</span>
                        </div>
                    </div>
                @endforeach
              </div>
            </div>
          </div>
        @endif
      @endif
    </div>
  @endif

  {{-- Input --}}
  <input
    id="{{ $id }}"
    name="{{ $name }}"
    type="{{ $type }}"
    value="{{ old($name, $value) }}"
    @if($required) required @endif
    @if($disabled) disabled @endif
    placeholder="{{ $placeholder }}"
    @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
    {{ $attributes->merge([
      'class' =>
        'block w-full rounded-md border-slate-300 shadow-sm '.
        'focus:border-sky-600 focus:ring-sky-600 '.
        ($error ? 'border-red-600 focus:border-red-600 focus:ring-red-600' : '')
    ]) }}

    @if($xInit)
        x-init="{{ $xInit }}"
    @endif


  />

  {{-- Help + Error --}}
  @if($help)
    <p id="{{ $helpId }}" class="mt-1 text-xs text-slate-500">{{ $help }}</p>
  @endif
  @if($error)
    <p id="{{ $errId }}" class="mt-1 text-sm text-red-700">{{ $error }}</p>
  @endif

  @if($warning)
    <p id="{{ $errId }}" class="mt-1 text-sm text-blue-700">{{ $warning }}</p>
  @endif

</div>
