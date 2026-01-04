<?php

namespace App\View\Components\Layout\Navigation;

use Closure;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class Brand extends Component
{
    // 'mobile' or 'desktop'
    public string $variant;

    // Auto-populate from auth by default
    public bool $useAuthBrand;

    // Shared brand content
    public string $name;
    public string $subtitle;
    public ?string $initial;

    // Style hooks
    public string $badgeClass;
    public string $wrapperClass;

    // Desktop-only UI behavior
    public bool $collapsedAware;

    // Button actions
    public string $mobileCloseAction;
    public string $desktopCollapseAction;

    public function __construct(
        string $variant = 'desktop',
        bool $useAuthBrand = true,
        string $name = 'Your App',
        string $subtitle = 'Guest',
        ?string $initial = null,
        string $badgeClass = 'bg-indigo-600 text-white',
        string $wrapperClass = '',
        bool $collapsedAware = true,
        string $mobileCloseAction = 'toggleSidebar(false)',
        string $desktopCollapseAction = 'toggleSidebarCollapsed()',
    ) {
        $this->variant             = $variant;
        $this->useAuthBrand        = $useAuthBrand;
        $this->name                = $name;
        $this->subtitle            = $subtitle;
        $this->initial             = $initial;
        $this->badgeClass          = $badgeClass;
        $this->wrapperClass        = $wrapperClass;
        $this->collapsedAware      = $collapsedAware;
        $this->mobileCloseAction   = $mobileCloseAction;
        $this->desktopCollapseAction = $desktopCollapseAction;

        $this->bootFromAuth();
    }

    protected function bootFromAuth(): void
    {
        $user = Auth::user();

        if ($this->useAuthBrand && $user) {
            // Name & initial from the authenticated user
            $this->name = $user->name ?: $this->name;
            $this->initial = Str::of($this->name)->substr(0, 1)->upper();

            // Build subtitle from Spatie permissions
            $permMap = [
                'system access global admin' => 'Global Administrator',
                'system access admin'        => 'Admin',
                'system access reviewer'     => 'Reviewer',
                'system access user'         => 'Submitter',
            ];

            $labels = [];
            foreach ($permMap as $perm => $label) {
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


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.layout.navigation.brand');
    }
}
