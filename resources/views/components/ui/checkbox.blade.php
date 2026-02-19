{{-- resources/views/components/form/checkbox.blade.php --}}
@props([
  // Core
  'id',
  'name' => null,
  'value' => '1',            // submitted value when checked
  'checked' => false,        // bool OR truthy (you can pass old(...) result)
  'label' => null,
  'required' => false,
  'disabled' => false,
  'help' => null,
  'error' => null,
  'warning' => null,

  // Tooltip (optional)
  'displayTooltip' => false,
  'position' => 'top',       // top, right, bottom, left
  'tooltipText' => '',
  'tooltipLabelTextArrMsg' => [],

  // Trigger (label) link look
  'linkLabel' => null,
  'linkHref' => '#',
  'labelClass' => 'text-sm font-medium text-slate-700',
  'triggerClass' => 'inline-flex items-center gap-1 rounded-xl border border-slate-200 px-2 py-1 text-xs font-medium text-slate-700 bg-white hover:bg-slate-50',

  // Layout / styling
  'wrapperClass' => 'flex items-start gap-3',
  'checkboxClass' => '',
  'xInit' => null,
])

@php
  $name = $name ?? $id;

  $helpId = $help ? $id.'-help' : null;
  $errId  = $error ? $id.'-error' : null;
  $warnId = $warning ? $id.'-warning' : null;
  $describedBy = collect([$helpId, $errId, $warnId])->filter()->implode(' ');

  $posClass = match ($position) {
      'bottom' => 'top-full mt-2 left-1/2 -translate-x-1/2',
      'right'  => 'left-full ml-2 top-1/2 -translate-y-1/2',
      'left'   => 'right-full mr-2 top-1/2 -translate-y-1/2',
      default  => 'bottom-full mb-2 left-1/2 -translate-x-1/2',
  };

  // Determine checked state safely (supports old('field') values)
  $isChecked = filter_var(old($name, $checked), FILTER_VALIDATE_BOOLEAN);
@endphp

<div class="w-full">
  <div class="{{ $wrapperClass }}">
    {{-- Checkbox --}}
    <div class=" ">
      <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="checkbox"
        value="{{ $value }}"
        @checked($isChecked)
        @if($required) required @endif
        @if($disabled) disabled @endif
        @if($describedBy) aria-describedby="{{ $describedBy }}" @endif

        {{ $attributes->merge([
          'class' =>
            'h-4 w-4 rounded border-slate-300 text-sky-600 shadow-sm '.
            'focus:ring-sky-600 focus:ring-2 focus:ring-offset-0 '.
            ($error ? 'border-red-600 text-red-600 focus:ring-red-600' : '').
            ($disabled ? ' opacity-60 cursor-not-allowed' : ' cursor-pointer')
        ]) }}

        @if($xInit)
          x-init="{{ $xInit }}"
        @endif
      />
    </div>

    {{-- Label + tooltip --}}
    <div class="min-w-0">
      @if($label || $linkLabel)
        <div class="flex items-center gap-2">
          @if($linkLabel)
            <div x-data="{ open:false }" class="relative inline-flex items-center">
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
                    <div class="my-2 space-y-1 text-xs">
                      @foreach($tooltipLabelTextArrMsg as $msgKey => $msgText)
                        <div class="flex items-start gap-2">
                          <svg class="w-4 h-4 text-lime-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                          </svg>
                          <div>
                            <span class="font-semibold text-white">{{ $msgKey }}:</span>
                            <span class="text-slate-300">{{ $msgText }}</span>
                          </div>
                        </div>
                      @endforeach
                    </div>
                  @endif
                </div>
              @endif
            </div>
          @endif

          @if($label)
            <label for="{{ $id }}" class="{{ $labelClass }}">
              {{ $label }}
              @if($required)<span class="text-red-600">*</span>@endif
            </label>
          @endif

          {{-- Optional "?" tooltip when no linkLabel --}}
          @if(!$linkLabel && $displayTooltip)
            <div x-data="{ open:false }" class="relative">
              <button type="button"
                      @mouseenter="open = true" @mouseleave="open = false"
                      @focus="open = true" @blur="open = false"
                      @keydown.escape.window="open = false"
                      class="inline-flex items-center rounded-md border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700 hover:bg-slate-50">
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
                  <div class="my-2 space-y-1 text-xs">
                    @foreach($tooltipLabelTextArrMsg as $msgKey => $msgText)
                      <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-lime-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <div>
                          <span class="font-semibold text-white">{{ $msgKey }}:</span>
                          <span class="text-slate-300">{{ $msgText }}</span>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @endif
              </div>
            </div>
          @endif
        </div>
      @endif

      {{-- Help + Error --}}
      @if($help)
        <p id="{{ $helpId }}" class="mt-1 text-xs text-slate-500">{{ $help }}</p>
      @endif

      @if($error)
        <p id="{{ $errId }}" class="mt-1 text-sm text-red-700">{{ $error }}</p>
      @endif

      @if($warning)
        <p id="{{ $warnId }}" class="mt-1 text-sm text-blue-700">{{ $warning }}</p>
      @endif
    </div>


  </div>
</div>
