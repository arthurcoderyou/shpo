<!-- Wrapper -->
<div class="px-5 py-4 border-b flex items-center justify-between {{ $wrapperClass }}">
    <div class="flex items-center gap-3">
        <!-- Badge / Initial -->
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl font-bold {{ $badgeClass }}">
            {{ $initial }}
        </span>

        <!-- Titles -->
        @php
            $labelClasses = $variant === 'desktop' && $collapsedAware
                ? 'label group-data-[collapsed=true]:hidden'
                : '';
        @endphp

        <div class="{{ $labelClasses }}">
            <div class="font-semibold">
                {{ $variant === 'mobile' ? $name : $name }}
            </div>
            <div class="text-xs text-slate-500">
                {{ $variant === 'mobile' ? $subtitle : $subtitle }}
            </div>
        </div>
    </div>

    <!-- Actions (variant-specific) -->
    @if ($variant === 'mobile')
        <button aria-label="Close navigation"
                class="rounded-xl border p-2 hover:bg-gray-50"
                onclick="{{ $mobileCloseAction }}">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="h-5 w-5"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    @else
        <button onclick="{{ $desktopCollapseAction }}"
                class="inline-flex items-center justify-center rounded-lg border px-2 py-2 hover:bg-gray-50"
                aria-label="Toggle sidebar">
            <!-- show when expanded -->
            <svg id="icon-collapse"
                 class="w-4 h-4"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 19l-7-7 7-7"/>
            </svg>
            <!-- show when collapsed -->
            <svg id="icon-expand"
                 class="w-4 h-4 hidden"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    @endif
</div>
