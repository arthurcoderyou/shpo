@props([
  // Core
  'id',
  'name' => null,
  'label' => null,
  'value' => null,
  'required' => false,
  'disabled' => false,
  'placeholder' => '',
  'rows' => 4,
  'help' => null,
  'error' => null,

  // Tooltip
  'displayTooltip' => false,
  'position' => 'top',
  'tooltipText' => '',
  'tooltipLabelTextArrMsg' => [],

  // Label/trigger look
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
        <div x-data="{ open:false }" class="relative inline-flex">
          <a href="{{ $linkHref }}"
             @mouseenter="open=true" @mouseleave="open=false"
             @focus="open=true" @blur="open=false"
             @keydown.escape.window="open=false"
             class="{{ $triggerClass }}">{{ $linkLabel }}</a>
          @if($displayTooltip)
            <div x-show="open" x-cloak role="tooltip"
                 class="absolute z-50 w-56 text-xs bg-slate-900 text-white rounded-md px-3 py-2 shadow-lg pointer-events-none {{ $posClass }}"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:leave="transition ease-in duration-75">
              {{ $tooltipText }}
              @if(!empty($tooltipLabelTextArrMsg))
                <div class="my-1 space-y-1">
                  @foreach($tooltipLabelTextArrMsg as $k=>$v)
                    <div><span class="text-lime-500 uppercase">{{ $k }}</span>: {{ $v }}</div>
                  @endforeach
                </div>
              @endif
            </div>
          @endif
        </div>
      @endif
      <label for="{{ $id }}" class="{{ $labelClass }}">{{ $label }} @if($required)<span class="text-red-600">*</span>@endif</label>
    </div>
  @endif

  <textarea
    id="{{ $id }}"
    name="{{ $name }}"
    rows="{{ $rows }}"
    @if($required) required @endif
    @if($disabled) disabled @endif
    placeholder="{{ $placeholder }}"
    @if($describedBy) aria-describedby="{{ $describedBy }}" @endif
    aria-invalid="{{ $error ? 'true' : 'false' }}"
    {{ $attributes->merge([
      'class' =>
        'block w-full rounded-md border-slate-300 shadow-sm '.
        'focus:border-sky-600 focus:ring-sky-600 '.
        ($error ? 'border-red-600 focus:border-red-600 focus:ring-red-600' : '')
    ]) }}
  >{{ old($name, $value) }}</textarea>

  @if($help)
    <p id="{{ $helpId }}" class="mt-1 text-xs text-slate-500">{{ $help }}</p>
  @endif
  @if($error)
    <p id="{{ $errId }}" class="mt-1 text-sm text-red-700">{{ $error }}</p>
  @endif
</div>