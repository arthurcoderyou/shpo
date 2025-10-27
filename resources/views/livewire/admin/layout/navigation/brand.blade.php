<?php

use Livewire\Volt\Component;
use Illuminate\Support\Str;

new class extends Component {
    // 'mobile' or 'desktop'
    public string $variant = 'desktop';

    // Auto-populate from auth by default
    public bool $useAuthBrand = true;

    // Shared brand content (can still be overridden via props if you want)
    public string $name = 'Your App';
    public string $subtitle = 'Guest';
    public ?string $initial = null;

    // Style hooks
    public string $badgeClass = 'bg-indigo-600 text-white';
    public string $wrapperClass = '';

    // Desktop-only UI behavior
    public bool $collapsedAware = true;

    // Button actions
    public string $mobileCloseAction = 'toggleSidebar(false)';
    public string $desktopCollapseAction = 'toggleSidebarCollapsed()';


    public function mount()
    {
        $user = auth()->user();

        if ($this->useAuthBrand && $user) {
            // Name & initial from the authenticated user
            $this->name = $user->name ?: $this->name;
            $this->initial = Str::of($this->name)->substr(0, 1)->upper();

            // Build subtitle from Spatie permissions
            // Map: permission => label
            $permMap = [
                'system access global admin' => 'Global Administrator',
                'system access admin'        => 'Admin',
                'system access reviewer'     => 'Reviewer',
                'system access user'         => 'Submitter',
            ];

            $labels = [];
            foreach ($permMap as $perm => $label) {
                // Works with Spatie via Gate: $user->can('permission-name')
                if ($user->can($perm)) {
                    $labels[] = $label;
                }
            }

            $this->subtitle = count($labels) ? implode(' / ', $labels) : 'Guest';
        }

        // If no initial provided (or guest mode), derive from current $name
        if (!$this->initial) {
            $this->initial = Str::of($this->name)->substr(0, 1)->upper();
        }
    }
}; ?>

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
                {{ $variant === 'mobile' ? ($name ) : $name }}
            </div>
            <div class="text-xs text-slate-500">
                {{ $variant === 'mobile' ? ($subtitle) : $subtitle }}
            </div>
        </div>
    </div>

    <!-- Actions (variant-specific) -->
    @if ($variant === 'mobile')
        <button aria-label="Close navigation"
                class="rounded-xl border p-2 hover:bg-gray-50"
                onclick="{{ $mobileCloseAction }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    @else
        <button onclick="{{ $desktopCollapseAction }}"
                class="inline-flex items-center justify-center rounded-lg border px-2 py-2 hover:bg-gray-50"
                aria-label="Toggle sidebar">
            <!-- show when expanded -->
            <svg id="icon-collapse" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <!-- show when collapsed -->
            <svg id="icon-expand" class="w-4 h-4 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
    @endif
</div>
