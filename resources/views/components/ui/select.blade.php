@props([
  // Core
  'id',
  'name' => null,
  'label' => null,
  'required' => false,
  'disabled' => false,
  'placeholder' => null,      // optional first empty option
  'help' => null,
  'error' => null,
  'options' => [],            // ['value' => 'Label', ...]

  // Tooltip (optional)
  'displayTooltip' => false,
  'position' => 'top',
  'tooltipText' => '',
  'tooltipLabelTextArrMsg' => [],

  'linkLabel' => null,
  'linkHref' => '#',
  'labelClass' => 'block text-sm font-medium text-slate-700',
  'triggerClass' => 'inline-flex items-center gap-1 rounded-xl border border-slate-200 px-2 py-1 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50',
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
              @if(!empty($tooltipLabelTextArrMsg))
                <div class="my-1 space-y-1">
                  @foreach($tooltipLabelTextArrMsg as $msgKey => $msgText)
                    <div><span class="text-lime-500 uppercase">{{ $msgKey }}</span>: {{ $msgText }}</div>
                  @endforeach
                </div>
              @endif
            </div>
          </div>
        @endif
      @endif
    </div>
  @endif

  <select
    id="{{ $id }}"
    name="{{ $name }}"
    @if($required) required @endif
    @if($disabled) disabled @endif
    @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
    {{ $attributes->merge([
      'class' =>
        'block w-full rounded-md border-slate-300 bg-white shadow-sm '.
        'focus:border-sky-600 focus:ring-sky-600 '.
        ($error ? 'border-red-600 focus:border-red-600 focus:ring-red-600' : '')
    ]) }}
  >
    @if(!is_null($placeholder))
      <option value="">{{ $placeholder }}</option>
    @endif

    {{-- Prefer the options prop, but allow slot override --}}
    @if($options && empty(trim($slot)))
      @foreach($options as $val => $text)
        <option value="{{ $val }}" @selected(old($name) == $val)>{{ $text }}</option>
      @endforeach
    @else
      {{ $slot }}
    @endif
  </select>

  @if($help)
    <p id="{{ $helpId }}" class="mt-1 text-xs text-slate-500">{{ $help }}</p>
  @endif
  @if($error)
    <p id="{{ $errId }}" class="mt-1 text-sm text-red-700">{{ $error }}</p>
  @endif
</div>
