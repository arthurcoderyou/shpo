@props([
  'id',
  'name' => null,          // use name="things[]" for multiple
  'label' => null,
  'required' => false,     // note: real "required" for groups is custom-validated
  'disabled' => false,
  'options' => [],         // ['value' => 'Label', ...]
  'selected' => [],        // array of selected values
  'inline' => false,
  'help' => null,
  'error' => null,

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
  $name = $name ?? ($id.'[]');
  $helpId = $help ? $id.'-help' : null;
  $errId  = $error ? $id.'-error' : null;
  $describedBy = collect([$helpId, $errId])->filter()->implode(' ');
  $sel = collect(old(Str::beforeLast($name,'[]'), $selected ?? []))->map(fn($v)=>(string)$v)->all();
  $posClass = match ($position) {
      'bottom' => 'top-full mt-2 left-1/2 -translate-x-1/2',
      'right'  => 'left-full ml-2 top-1/2 -translate-y-1/2',
      'left'   => 'right-full mr-2 top-1/2 -translate-y-1/2',
      default  => 'bottom-full mb-2 left-1/2 -translate-x-1/2',
  };
@endphp

<fieldset role="group" aria-describedby="{{ $describedBy }}" aria-invalid="{{ $error ? 'true':'false' }}">
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
                 class="absolute z-50 w-56 text-xs bg-slate-900 text-white rounded-md px-3 py-2 shadow-lg pointer-events-none {{ $posClass }}">
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
      <legend class="{{ $labelClass }}">{{ $label }} @if($required)<span class="text-red-600">*</span>@endif</legend>
    </div>
  @endif

  <div class="{{ $inline ? 'flex flex-wrap gap-4' : 'space-y-2' }}">
    @foreach($options as $val => $text)
      @php $optionId = $id.'_'.Str::slug((string)$val, '_'); @endphp
      <label for="{{ $optionId }}" class="inline-flex items-center gap-2">
        <input
          id="{{ $optionId }}"
          type="checkbox"
          name="{{ $name }}"
          value="{{ $val }}"
          @checked(in_array((string)$val, $sel, true))
          @if($disabled) disabled @endif
          {{ $attributes->class('text-sky-600 border-slate-300 focus:ring-sky-600') }}
        >
        <span class="text-sm text-slate-800">{{ $text }}</span>
      </label>
    @endforeach
  </div>

  @if($help)
    <p id="{{ $helpId }}" class="mt-1 text-xs text-slate-500">{{ $help }}</p>
  @endif
  @if($error)
    <p id="{{ $errId }}" class="mt-1 text-sm text-red-700">{{ $error }}</p>
  @endif
</fieldset>