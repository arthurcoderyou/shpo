@php
    $navWrap = $variant === 'mobile'
        ? 'px-3 py-4 h-[calc(100vh-64px)] overflow-y-auto flex-1'
        : 'flex-1 overflow-y-auto px-3 py-4';
@endphp

<nav class="{{ $navWrap }}">
    @foreach ($sections as $section)
        @if (! $canSeeSection($section))
            @continue
        @endif

        <p class="{{ $variant === 'desktop' ? 'label group-data-[collapsed=true]:hidden' : '' }} px-3 text-xs font-semibold text-slate-400">
            {{ $section['heading'] ?? '' }}
        </p>

        <ul class="mt-2 space-y-1">
            @foreach ($section['items'] as $item)
                @if (! $canSeeItem($item))
                    @continue
                @endif

                @php
                    $activeTop = $isActiveTree($item);
                @endphp

                {{-- LEVEL 1: link or group --}}
                @if (!empty($item['route']))
                    <li>
                        <a href="{{ $item['route'] ? route($item['route']) : '#' }}"
                           wire:navigate
                           class="{{ $linkClasses($activeTop, 1) }}">
                            <span class="flex items-center justify-between gap-2">
                                <span class="flex items-center gap-2">
                                    {!! $icon($item['icon'] ?? 'dot') !!}
                                    <span class="{{ $labelCls() }}">{{ $item['label'] }}</span>
                                </span>

                                @if ($showCount($item))
                                    <span class="{{ $badgeBase }} {{ $activeTop ? $badgeActive : $badgeMuted }}">
                                        {{ $formatCount((int) $item['count']) }}
                                    </span>
                                @endif
                            </span>
                        </a>
                    </li>
                @else
                    <li>
                        <details class="group" @if($activeTop) open @endif>
                            <summary class="flex items-center justify-between rounded-xl cursor-pointer {{ $linkClasses($activeTop, 1) }}">
                                <span class="flex items-center gap-2">
                                    {!! $icon($item['icon'] ?? 'dot') !!}
                                    <span class="{{ $labelCls() }}">{{ $item['label'] }}</span>
                                </span>
                                <svg class="{{ $chevronCls() }}" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </summary>

                            @if (!empty($item['children']))
                                <ul class="ml-6 mt-1 space-y-1">
                                    @foreach ($item['children'] as $child)
                                        @if (! $canSeeChildren($child))
                                            @continue
                                        @endif

                                        @php $activeChild = $isActiveTree($child); @endphp

                                        {{-- LEVEL 2: link or group --}}
                                        @if (!empty($child['route']))
                                            <li>
                                                <a href="{{ route($child['route']) }}"
                                                   wire:navigate
                                                   class="{{ $linkClasses($activeChild, 2) }}">
                                                    <span class="flex items-center justify-between">
                                                        <span>{{ $child['label'] }}</span>
                                                        @if ($showCount($child))
                                                            <span class="{{ $badgeBase }} {{ $activeChild ? $badgeActive : $badgeMuted }}">
                                                                {{ $formatCount((int) $child['count']) }}
                                                            </span>
                                                        @endif
                                                    </span>
                                                </a>
                                            </li>
                                        @else
                                            <li>
                                                <details @if($activeChild) open @endif>
                                                    <summary class="flex items-center justify-between cursor-pointer {{ $linkClasses($activeChild, 2) }}">
                                                        <span class="flex items-center gap-2">
                                                            {!! $icon($item['icon'] ?? 'dot') !!}
                                                            <span class="{{ $labelCls() }}">{{ $item['label'] }}</span>
                                                        </span>
                                                        <span class="flex items-center gap-2">
                                                            @if ($showCount($item))
                                                                <span class="{{ $badgeBase }} {{ $activeTop ? $badgeActive : $badgeMuted }}">
                                                                    {{ $formatCount((int) $item['count']) }}
                                                                </span>
                                                            @endif
                                                            <svg class="{{ $chevronCls() }}" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                            </svg>
                                                        </span>
                                                    </summary>

                                                    @if (!empty($child['children']))
                                                        <ul class="ml-5 mt-1 space-y-1">
                                                            @foreach ($child['children'] as $gchild)
                                                                @php $activeG = $isActiveTree($gchild); @endphp
                                                                <li>
                                                                    <a href="{{ route($gchild['route']) }}"
                                                                       wire:navigate
                                                                       class="{{ $linkClasses($activeG, 3) }}">
                                                                        <span>{{ $gchild['label'] }}</span>
                                                                        @if ($showCount($gchild))
                                                                            <span class="{{ $badgeBase }} {{ $activeG ? $badgeActive : $badgeMuted }}">
                                                                                {{ $formatCount((int) $gchild['count']) }}
                                                                            </span>
                                                                        @endif
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </details>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                        </details>
                    </li>
                @endif
            @endforeach
        </ul>

        @if (!$loop->last)
            <div class="mt-4"></div>
        @endif
    @endforeach
</nav>
