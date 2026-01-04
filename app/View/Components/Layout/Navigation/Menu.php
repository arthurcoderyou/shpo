<?php

namespace App\View\Components\Layout\Navigation;

use Closure;
use App\Models\Project;
use App\Models\ActivityLog;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View; 
use Illuminate\Support\Facades\Route; 
use App\Models\ProjectDocument; 
use App\Models\DocumentType;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Menu extends Component
{
    /** 'mobile' or 'desktop' */
    public string $variant;

    /** Optional: override the whole menu structure from caller */
    public array $sections = [];

    // Badge styles (tweak to your liking)
    public string $badgeBase   = 'inline-flex min-w-[1.5rem] items-center justify-center rounded-full text-[10px] font-semibold px-1.5 py-0.5';
    public string $badgeMuted  = 'bg-slate-100 text-slate-700';
    public string $badgeActive = 'bg-indigo-600/10 text-indigo-700 ring-1 ring-inset ring-indigo-600/20';

    /** Tailwind classes for link states */
    public string $linkBase = 'block rounded-xl';
    public string $linkL1   = 'px-3 py-2.5';
    public string $linkL2   = 'px-3 py-1.5 text-sm';
    public string $linkL3   = 'px-3 py-1 text-sm';

    public string $linkActive   = 'bg-indigo-50 text-indigo-700 label group-data-[collapsed=true]:bg-white';
    public string $linkInactive = 'text-slate-700 hover:bg-indigo-50 hover:text-indigo-700  label group-data-[collapsed=true]:bg-white';

    public function __construct(string $variant = 'desktop', array $sections = [])
    {
        $this->variant  = $variant;
        $this->sections = $sections;

        $this->bootSections();
    }

    // --- Visibility helpers -------------------------------------------------

    protected function user()
    {
        return auth()->user();
    }

    protected function passAuth(?bool $auth): bool
    {
        if (is_null($auth)) return true;
        return $auth ? auth()->check() : !auth()->check();
    }

    protected function passAnyRole(bool $requireAnyRole = false): bool
    {
        if (! $requireAnyRole) return true;
        $u = $this->user();
        return $u ? $u->roles()->exists() : false;
    }

    protected function passRolesAny(array $roles = []): bool
    {
        if (empty($roles)) return true;
        $u = $this->user();
        return $u ? $u->hasAnyRole($roles) : false;
    }

    protected function passRolesAll(array $roles = []): bool
    {
        if (empty($roles)) return true;
        $u = $this->user();
        return $u ? $u->hasAllRoles($roles) : false;
    }

    protected function passPermsAny(array $perms = []): bool
    {
        if (empty($perms)) return true;
        $u = $this->user();
        return $u ? $u->hasAnyPermission($perms) : false;
    }

    protected function passPermsAll(array $perms = []): bool
    {
        if (empty($perms)) return true;
        $u = $this->user();
        if (! $u) return false;

        foreach ($perms as $p) {
            if (! $u->can($p)) return false;
        }

        return true;
    }

    protected function passModulesAny(array $mods = []): bool
    {
        if (empty($mods)) return true;

        foreach ($mods as $m) {
            if (function_exists('authorizeWithModules') && authorizeWithModules([$m])) {
                return true;
            }
        }

        return false;
    }

    protected function passModulesAll(array $mods = []): bool
    {
        if (empty($mods)) return true;
        if (! function_exists('authorizeWithModules')) return false;

        foreach ($mods as $m) {
            if (! authorizeWithModules([$m])) return false;
        }

        return true;
    }

    /**
     * Check if the user does NOT have any of the given permissions.
     *
     * @param  array  $mods  Array of permission names to disallow
     * @param  bool   $includeRolePermissions  true = include permissions inherited from roles
     */
    protected function mustNotHavePermissions(array $mods, bool $includeRolePermissions = true): bool
    {
        $u = $this->user();
        if (! $u) return false;

        if (empty($mods)) return true;

        $userPermissions = $includeRolePermissions
            ? $u->getAllPermissions()->pluck('name')->unique()->values()->all()
            : $u->permissions()->pluck('name')->unique()->values()->all();

        $userPermissions = array_map('strval', $userPermissions);

        foreach ($mods as $perm) {
            if (in_array($perm, $userPermissions, true)) {
                return false; // has forbidden permission
            }
        }

        return true;
    }

    protected function visibleBy(array $reqs = []): bool
    {
        $auth                   = $reqs['auth']             ?? null;
        $requireAnyRole         = $reqs['require_any_role'] ?? false;
        $rolesAny               = $reqs['roles_any']        ?? [];
        $rolesAll               = $reqs['roles_all']        ?? [];
        $mustNotHavePermissions = $reqs['must_not_have_permissions'] ?? [];
        $permsAny               = $reqs['permissions_any']  ?? [];
        $permsAll               = $reqs['permissions_all']  ?? [];
        $modsAny                = $reqs['modules_any']      ?? [];
        $modsAll                = $reqs['modules_all']      ?? [];

        return
            $this->passAuth($auth) &&
            $this->passAnyRole($requireAnyRole) &&
            $this->passRolesAny($rolesAny) &&
            $this->passRolesAll($rolesAll) &&
            $this->mustNotHavePermissions($mustNotHavePermissions) &&
            $this->passPermsAny($permsAny) &&
            $this->passPermsAll($permsAll) &&
            $this->passModulesAny($modsAny) &&
            $this->passModulesAll($modsAll);
    }

    /** These need to be public so Blade can call them */

    public function canSeeSection(array $section): bool
    {
        return $this->visibleBy($section['require'] ?? []);
    }

    public function canSeeItem(array $item): bool
    {
        if (isset($item['require']) && ! $this->visibleBy($item['require'])) {
            return false;
        }

        if (!empty($item['children'])) {
            foreach ($item['children'] as $child) {
                if ($this->canSeeItem($child)) return true;
            }
            return false;
        }

        if (!empty($item['route']) && ! Route::has($item['route'])) {
            return false;
        }

        return true;
    }

    public function canSeeChildren(array $child): bool
    {
        return $this->canSeeItem($child);
    }

    public function showCount(array $item): bool
    {
        return array_key_exists('count', $item);
    }

    public function formatCount(int $n): string
    {
        return (string) $n;
    }

    /** True if this item or any descendant matches current route */
    public function isActiveTree(array $item): bool
    {
        if (isset($item['route']) && request()->routeIs($item['route'])) return true;

        if (!empty($item['patterns'])) {
            foreach ($item['patterns'] as $pat) {
                if (request()->routeIs($pat)) return true;
            }
        }

        if (!empty($item['children'])) {
            foreach ($item['children'] as $child) {
                if ($this->isActiveTree($child)) return true;
            }
        }

        return false;
    }

    /** Compute link classes for any level */
    public function linkClasses(bool $active, int $level = 1): string
    {
        $pad = $level === 1
            ? $this->linkL1
            : ($level === 2 ? $this->linkL2 : $this->linkL3);

        return implode(' ', [
            $this->linkBase,
            $pad,
            $active ? $this->linkActive : $this->linkInactive,
        ]);
    }

    /** Tiny helper for desktop-only collapsed labels */
    public function labelCls(): string
    {
        return $this->variant === 'desktop'
            ? 'label group-data-[collapsed=true]:hidden'
            : '';
    }

    /** Chevron class differs a bit for desktop collapsed mode */
    public function chevronCls(): string
    {
        $base = 'w-4 h-4 text-slate-400 transition-transform group-open:rotate-180';

        return $this->variant === 'desktop'
            ? "label group-data-[collapsed=true]:hidden {$base}"
            : $base;
    }

    /** Icon renderer selector */
    public function icon(string $name): string
    {
        return match ($name) {
            // Existing
            'home' => '<svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>',
            'list' => '<svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h8m-8 4h6"/></svg>',
            'lock' => '<svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 10-8 0v4M5 11h14l-1 10H6L5 11z"/></svg>',

            // Projects
            'projects-all'   => '<svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18"/></svg>',
            'projects-own' => '<svg class="w-5 h-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M139.61 35.5a12 12 0 0 0-17 0L58.93 98.81l-22.7-22.12a12 12 0 0 0-17 0L3.53 92.41a12 12 0 0 0 0 17l47.59 47.4a12.78 12.78 0 0 0 17.61 0l15.59-15.62L156.52 69a12.09 12.09 0 0 0 .09-17zm0 159.19a12 12 0 0 0-17 0l-63.68 63.72-22.7-22.1a12 12 0 0 0-17 0L3.53 252a12 12 0 0 0 0 17L51 316.5a12.77 12.77 0 0 0 17.6 0l15.7-15.69 72.2-72.22a12 12 0 0 0 .09-16.9zM64 368c-26.49 0-48.59 21.5-48.59 48S37.53 464 64 464a48 48 0 0 0 0-96zm432 16H208a16 16 0 0 0-16 16v32a16 16 0 0 0 16 16h288a16 16 0 0 0 16-16v-32a16 16 0 0 0-16-16zm0-320H208a16 16 0 0 0-16 16v32a16 16 0 0 0 16 16h288a16 16 0 0 0 16-16V80a16 16 0 0 0-16-16zm0 160H208a16 16 0 0 0-16 16v32a16 16 0 0 0 16 16h288a16 16 0 0 0 16-16v-32a16 16 0 0 0-16-16z"/></svg>',

            'project-reviews'=> '<svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 20h9M12 4h9M4 9h16M4 15h16"/></svg>',

            // Settings
            'time-settings'  => '<svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2M12 22a10 10 0 100-20 10 10 0 000 20z"/></svg>',
            'doc-types'      => '<svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V7l-6-4H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>',
            'project-submit' => '<svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg>',

            // User Management
            'users'          => '<svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M12 12a4 4 0 100-8 4 4 0 000 8z"/></svg>',
            'roles'          => '<svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 11-4 0 2 2 0 014 0zm6 2h2m-2 0a2 2 0 110-4 2 2 0 010 4zM4 8H2m2 0a2 2 0 100-4 2 2 0 000 4zm2 12h12m-6-6a4 4 0 110-8 4 4 0 010 8z"/></svg>',
            'permissions'    => '<svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4-4m1-5h-6a2 2 0 00-2 2v6h2m2 4h6a2 2 0 002-2v-6h-2"/></svg>',

            // Misc
            'profile'        => '<svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z"/></svg>',

            'settings' => '<svg class="w-5 h-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M502.63 214.63l-45.25-45.25c-6-6-14.14-9.37-22.63-9.37H384V80c0-26.51-21.49-48-48-48H176c-26.51 0-48 21.49-48 48v80H77.25c-8.49 0-16.62 3.37-22.63 9.37L9.37 214.63c-6 6-9.37 14.14-9.37 22.63V320h128v-16c0-8.84 7.16-16 16-16h32c8.84 0 16 7.16 16 16v16h128v-16c0-8.84 7.16-16 16-16h32c8.84 0 16 7.16 16 16v16h128v-82.75c0-8.48-3.37-16.62-9.37-22.62zM320 160H192V96h128v64zm64 208c0 8.84-7.16 16-16 16h-32c-8.84 0-16-7.16-16-16v-16H192v16c0 8.84-7.16 16-16 16h-32c-8.84 0-16-7.16-16-16v-16H0v96c0 17.67 14.33 32 32 32h448c17.67 0 32-14.33 32-32v-96H384v16z"/></svg>',

            'activity-logs' => '<svg class="w-5 h-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M504 255.531c.253 136.64-111.18 248.372-247.82 248.468-59.015.042-113.223-20.53-155.822-54.911-11.077-8.94-11.905-25.541-1.839-35.607l11.267-11.267c8.609-8.609 22.353-9.551 31.891-1.984C173.062 425.135 212.781 440 256 440c101.705 0 184-82.311 184-184 0-101.705-82.311-184-184-184-48.814 0-93.149 18.969-126.068 49.932l50.754 50.754c10.08 10.08 2.941 27.314-11.313 27.314H24c-8.837 0-16-7.163-16-16V38.627c0-14.254 17.234-21.393 27.314-11.314l49.372 49.372C129.209 34.136 189.552 8 256 8c136.81 0 247.747 110.78 248 247.531zm-180.912 78.784l9.823-12.63c8.138-10.463 6.253-25.542-4.21-33.679L288 256.349V152c0-13.255-10.745-24-24-24h-16c-13.255 0-24 10.745-24 24v135.651l65.409 50.874c10.463 8.137 25.541 6.253 33.679-4.21z"/></svg>',

            'project-settings' => '<svg class="w-5 h-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M501.1 395.7L384 278.6c-23.1-23.1-57.6-27.6-85.4-13.9L192 158.1V96L64 0 0 64l96 128h62.1l106.6 106.6c-13.6 27.8-9.2 62.3 13.9 85.4l117.1 117.1c14.6 14.6 38.2 14.6 52.7 0l52.7-52.7c14.5-14.6 14.5-38.2 0-52.7zM331.7 225c28.3 0 54.9 11 74.9 31l19.4 19.4c15.8-6.9 30.8-16.5 43.8-29.5 37.1-37.1 49.7-89.3 37.9-136.7-2.2-9-13.5-12.1-20.1-5.5l-74.4 74.4-67.9-11.3L334 98.9l74.4-74.4c6.6-6.6 3.4-17.9-5.7-20.2-47.4-11.7-99.6.9-136.6 37.9-28.5 28.5-41.9 66.1-41.2 103.6l82.1 82.1c8.1-1.9 16.5-2.9 24.7-2.9zm-103.9 82l-56.7-56.7L18.7 402.8c-25 25-25 65.5 0 90.5s65.5 25 90.5 0l123.6-123.6c-7.6-19.9-9.9-41.6-5-62.7zM64 472c-13.2 0-24-10.8-24-24 0-13.3 10.7-24 24-24s24 10.7 24 24c0 13.2-10.7 24-24 24z"/></svg>',

            'file-manager' => '<svg class="w-5 h-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="currentColor" d="M572.694 292.093L500.27 416.248A63.997 63.997 0 0 1 444.989 448H45.025c-18.523 0-30.064-20.093-20.731-36.093l72.424-124.155A64 64 0 0 1 152 256h399.964c18.523 0 30.064 20.093 20.73 36.093zM152 224h328v-48c0-26.51-21.49-48-48-48H272l-64-64H48C21.49 64 0 85.49 0 112v278.046l69.077-118.418C86.214 242.25 117.989 224 152 224z"/></svg>',

            default => '<svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"/></svg>',
        };
    }

    protected function bootSections(): void
    {
        if ($this->sections) {
            return;
        }

        $projects_count = Project::countProjects('project.index.all');
        $log_count      = ActivityLog::getCount();

        $this->sections = [
                [   // MAIN
                    // 'heading' => $heading,
                    'items' => [
                        
                        [
                            'label' => 'Dashboard',
                            'icon'  => 'home',
                            'route' => 'dashboard',          // make sure this route exists
                            'patterns' => ['dashboard'],
                            'require' => [
                                'auth' => true,
                                // 'permissions_any' => [
                                //     'system access global admin', 
                                // ],
                            ],
                        ], 
                        [
                            'label' => 'File Manager',
                            'icon'  => 'file-manager',
                            'route' => 'file_manager.attachment.index',          // make sure this route exists
                            'patterns' => ['file_manager'],
                            'require' => [
                                'auth' => true,
                                'permissions_any' => [
                                    'system access global admin', 
                                ],
                            ],
                        ],
                    ],
                    'require' => [
                        'auth' => true, 
                    ],
                ],  // ./ MAIN

                 
                [   // PROJECT MANAGEMENT
                    // 'heading' => "Project Management",
                    'items' => [
                        [
                            'label' => 'All Projects',
                            
                            'icon'  => 'projects-all',
                           
                            'patterns' => [
                                'project.index.all',
                                'project.index.all.no-drafts',
                                'project.index.open-review',
                                'project.index.update-pending.all',
                                'project.index.review-pending.all',
                                'review.index',
                            ],
                            'children' => [
                                 

                                [
                                    'label' => 'All Projects', 
                                    'route' => 'project.index.all',    
                                    'auth' => true, 
                                    'require' => [ 
                                        // 'permissions_any' => [
                                        //     'system access global admin', 
                                        //     'project list view all',        // module: Project All Display
                                        // ],

                                         // for admin and global admin only
                                        'must_not_have_permissions' => [
                                            'system access reviewer',
                                            // 'system access global admin', 
                                            // 'system access admin',
                                            'system access user',
                                        ]
                                    ],
                                    'count' => Project::countProjects('project.index.all') ?? 0, // shows 0 if none
                                ],
                                [
                                    'label' => 'All Projects', 
                                    'route' => 'project.index.all.no-drafts',    
                                    'auth' => true,
                                    'require' => [ 
                                        // 'permissions_any' => [
                                        //     // 'system access global admin', 
                                        //     'system access reviewer',      // module: Project All Display
                                        // ],

                                        // for reviewer only
                                        'must_not_have_permissions' => [
                                            // 'system access reviewer',
                                            'system access global admin', 
                                            'system access admin',
                                            'system access user',
                                        ]

                                    ],
                                    'count' => Project::countProjects('project.index.all.no-drafts') ?? 0, // shows 0 if none
                                    
                                ],
                                [
                                    'label' => 'All Project Documents', 
                                    'route' => 'project-document.index',    
                                    'auth' => true,
                                    'require' => [ 
                                        'permissions_any' => [
                                            'system access global admin', 
                                            'system access admin',
                                            'system access reviewer',
                                        ],
                                    ],
                                    
                                    'count' => $projects_count = ProjectDocument::countProjectDocuments('project-document.index') ?? 0, // shows 0 if none
                                    
                                ],

                                // [
                                //     'label' => 'Open Review Project Documents', 
                                //     // 'route' => 'project.index.open-review',    
                                //     'route' => 'project-document.index.open-review',  
                                //     'auth' => true,
                                //     'require' => [ 
                                //         'permissions_any' => [
                                //             'system access global admin', 
                                //             'system access admin', 
                                //             'project list view open review',        // module: Project All Display
                                //         ],
                                //     ],
                                //     'count' => Project::countProjects('project.index.open-review') ?? 0, // shows 0 if none
                                    
                                // ],
                                // [
                                //     'label' => 'Update Pending', 
                                //     'route' => 'project.index.update-pending.all',    
                                //     'auth' => true,
                                //     'require' => [ 
                                //         'permissions_any' => [
                                //             'system access global admin', 
                                //             'project list view update pending all',     // module: Project All Display
                                //         ],
                                //     ],
                                //     'count' => Project::countProjects('project.index.update-pending.all') ?? 0, // shows 0 if none
                                    
                                // ],
                                 
                                // [
                                //     'label' => 'Review Pending', 
                                //     'route' => 'project.index.review-pending.all',    
                                //     'auth' => true,
                                //     'require' => [ 
                                //         'permissions_any' => [
                                //             'system access global admin', 
                                //             'project list view review pending all',     // module: Project All Display
                                //         ],
                                //     ],
                                //     'count' => Project::countProjects('project.index.review-pending.all') ?? 0, // shows 0 if none
                                    
                                // ],

                                [
                                    'label' => 'Project Reviews', 
                                    'route' => 'review.index',    
                                    'auth' => true,
                                    'require' => [ 
                                        'permissions_any' => [
                                            'system access global admin', 
                                            
                                            'review list view',             // module: Review
                                        ],
                                    ],
                                    
                                    
                                ],


                                [
                                    'label' => 'Re-review Requests', 
                                    'route' => 're-review.index',    
                                    'auth' => true,
                                    'require' => [ 
                                        'permissions_any' => [
                                            'system access global admin', 
                                            'system access admin', 
                                            
                                            'review list view',             // module: Review
                                        ],
                                    ],
                                    
                                    
                                ],
                                 
                            ], 
                            'require' => [
                                'auth' => true,
                                'modules_any' => [
                                    'Project All Display',
                                    'Review',
                                ], // only show this heading if helper authorizes this module
                            ],
                            

                        ],  
                    ],
                    'require' => [  // requirement to show the heading
                        'auth' => true,
                        // 'permissions_any' => [
                        //     'system access global admin', 
                        // ],
                        'modules_any' => [
                            'Project All Display',
                            'Review',
                        ],
                    ],
                     
                ],  // ./ PROJECT MANAGEMENT

                [   // PROJECT SETTINGS
                    // 'heading' => "Project Settings",
                    'items' => [
                        [
                            'label' => 'Project Settings',
                            
                            'icon'  => 'project-settings',
                           
                            'patterns' => [
                                'reviewer.*',
                                'project_timer.index',
                                'document_type.index',
                            ],
                            'children' => [
                                [
                                    'label' => 'Project Reviewer', 
                                    'route' => 'reviewer.index',    
                                    'auth' => true,
                                    'require' => [ 
                                        'permissions_any' => [
                                            'system access global admin', 
                                            'reviewer list view',
                                        ],
                                    ],
                                    
                                ],
                                [
                                    'label' => 'Time Settings', 
                                    'route' => 'project_timer.index',    
                                    'auth' => true,
                                    'require' => [ 
                                        'permissions_any' => [
                                            'system access global admin', 
                                            'timer list view',
                                        ],
                                    ],
                                    
                                ],
                                 
                                [
                                    'label' => 'Document Types', 
                                    'route' => 'document_type.index',    
                                    'auth' => true,
                                    'require' => [ 
                                        'permissions_any' => [
                                            'system access global admin', 
                                            'document type list view',
                                        ],
                                    ], 
                                    'count' => DocumentType::count() ?? 0, // shows 0 if none
                                ],
                                 
                                 
                            ], 
                            'require' => [ 
                                'modules_any' => [
                                    'Reviewer',
                                    'Timer',
                                    'Document Type',
                                ], // only show this heading if helper authorizes this module
                            ],

                        ], 
                    ],
                    'require' => [  // requirement to show the heading
                        'auth' => true,
                        // 'permissions_any' => [
                        //     'system access global admin', 
                        // ],
                        'modules_any' => [
                            'Reviewer',
                            'Timer',
                            'Document Type',
                        ],
                    ],
                ],  // ./ PROJECT SETTINGS


                [   // YOUR PROJECT MANAGEMENT
                    // 'heading' => "Your Project Management",
                    'items' => [ 
                        [
                            'label' => 'My Projects',
                            
                            'icon'  => 'projects-own',
                           
                            'patterns' => [
                                'project.create', 
                                'project.index', 
                                'project.index.update-pending', 
                                'project.index.review-pending', 
                                'project.create',  
                            ],
                            'children' => [
                                [
                                    'label' => 'Create New Project', 
                                    'route' => 'project.create',    
                                    'auth' => true,
                                    'require' => [ 
                                        'permissions_any' => [
                                            // 'system access global admin', 
                                            // 'project create',
                                            'system access user', 
                                        ],
                                    ], 
                                    
                                ],
                                [
                                    'label' => 'My Projects', 
                                    'route' => 'project.index',    
                                    'auth' => true,
                                    'require' => [ 
                                        'permissions_any' => [
                                            // 'system access global admin', 
                                            // 'project list view',
                                            'system access user',
                                        ],
                                    ],
                                    
                                    'count' => $projects_count = Project::countProjects('project.index') ?? 0, // shows 0 if none
                                    
                                ],

                                [
                                    'label' => 'My Project Documents', 
                                    'route' => 'project-document.index',    
                                    'auth' => true,
                                    'require' => [ 
                                        'permissions_any' => [
                                            // 'system access global admin', 
                                            // 'project list view',
                                            'system access user',
                                        ],
                                    ],
                                    
                                    'count' => $projects_count = ProjectDocument::countProjectDocuments('project-document.index') ?? 0, // shows 0 if none
                                    
                                ],

                                

                                
                                // [
                                //     'label' => 'My Project Documents with Requested Changes', 
                                //     'route' => 'project-document.index.changes-requested',    
                                //     'auth' => true,
                                //     'require' => [ 
                                //         'permissions_any' => [
                                //             // 'system access global admin', 
                                //             // 'project list view update pending',
                                //             'system access user',
                                //         ],
                                //     ],
                                    
                                //     'count' => $projects_count = ProjectDocument::countProjectDocuments('project-document.index.changes-requested') ?? 0, // shows 0 if none
                                // ], 

                                


                                // [
                                //     'label' => 'My Projects In Review', 
                                //     'route' => 'project.index.review-pending',    
                                //     'auth' => true,
                                //     'require' => [ 
                                //         'permissions_any' => [
                                //             // 'system access global admin', 
                                //             // 'project list view review pending',
                                //             'system access user',
                                //         ],
                                //     ],
                                    
                                //     'count' => $projects_count = Project::countProjects('project.index.review-pending') ?? 0, // shows 0 if none
                                // ],
                                  
                                 
                                 
                            ], 
                            'require' => [
                                'permissions_any' => [
                                    // 'system access global admin', 
                                    // 'project list view review pending',
                                    'system access user',
                                ],
                                'modules_any' => [
                                    // 'Project Own'    
                                ], // only show this heading if helper authorizes this module
                            ],

                        ], 
                    ],
                    'require' => [  // requirement to show the heading
                        'auth' => true,
                        // 'permissions_any' => [
                        //     'system access global admin', 
                        // ],
                        'modules_any' => [
                            'Project Own'
                        ],
                    ],
                ],  // ./ YOUR PROJECT MANAGEMENT



                // [   // PROJECTS TO REVIEW       || for Reviewer, Administrators 
                //     // 'heading' => "Projects to Review",
                //     'items' => [ 
                //         [
                //             // 'label' => 'Projects to Review',
                //             'label' => 'My Projects',
                            
                //             'icon'  => 'project-reviews',
                           
                //             'patterns' => [
                //                 'project.index.update-pending.all-linked',  
                //                 'project.index.review-pending.all-linked',
                //                 'project-document.index.review-pending',
                //             ],
                //             'children' => [

                //                 [
                //                     'label' => 'Review Pending', 
                //                     'route' => 'project-document.index.review-pending',    
                //                     'auth' => true,
                //                     'require' => [ 
                //                         'permissions_any' => [
                //                             // 'system access global admin', 
                //                             // 'project list view',
                //                             'system access admin',
                //                             'system access reviewer',
                //                         ],
                //                     ],
                                    
                //                     'count' => $projects_count = Project::countProjects('project.index') ?? 0, // shows 0 if none
                                    
                //                 ],


                //                 // [
                //                 //     // 'label' => 'Linked Projects Update Pending', 
                //                 //     'label' => 'Projects Update Pending', 
                //                 //     'route' => 'project.index.update-pending.all-linked',    
                //                 //     'auth' => true,
                //                 //     'require' => [ 
                //                 //         'permissions_any' => [
                //                 //             'system access global admin', 
                //                 //             'project list view update pending all linked',
                //                 //             'project list view review pending all linked',
                //                 //         ],
                //                 //     ],
                                    
                //                 //     'count' => $projects_count = Project::countProjects('project.index.update-pending.all-linked') ?? 0, // shows 0 if none
                                    
                //                 // ],

                //                 // [
                //                 //     // 'label' => 'Linked Projects Review Pending', 
                //                 //     'label' => 'Projects Review Pending', 
                //                 //     'route' => 'project.index.review-pending.all-linked',    
                //                 //     'auth' => true,
                //                 //     'require' => [ 
                //                 //         'permissions_any' => [
                //                 //             'system access global admin', 
                //                 //             'project list view update pending all linked',
                //                 //             'project list view review pending all linked',
                //                 //         ],
                //                 //     ],
                                    
                //                 //     'count' => $projects_count = Project::countProjects('project.index.review-pending.all-linked') ?? 0, // shows 0 if none
                //                 // ],
                                 
                                  
                                 
                                 
                //             ], 
                //             'require' => [
                //                 'permissions_any' => [
                //                     'system access admin', 
                //                     'system access reviewer',  
                //                 ],
                //                 'modules_any' => [
                //                     'Project All Display'
                //                 ], // only show this heading if helper authorizes this module
                //             ],

                //         ], 
                //     ],
                //     'require' => [  // requirement to show the heading
                //         'auth' => true,
                //         // 'permissions_any' => [
                //         //     'system access global admin', 
                //         // ],
                //         'modules_any' => [
                //             'Project All Display'
                //         ],
                //     ],
                // ],  // ./ PROJECTS TO REVIEW


                [   // USER MANAGEMENT
                    // 'heading' => "User Management",
                    'items' => [ 
                        [
                            'label' => 'User Manager',
                            
                            'icon'  => 'users',
                           
                            'patterns' => [
                                'user.index', 
                                'role.index',    
                                'permission.index',
                                 
                            ],
                            'children' => [
                                [
                                    'label' => 'Users', 
                                    'route' => 'user.index',    
                                    'auth' => true,
                                    'require' => [ 
                                        'permissions_any' => [
                                            'system access global admin', 
                                            'user list view', 
                                        ],
                                    ],
                                    
                                    'count' => User::count() ?? 0, // shows 0 if none
                                     
                                ],
                                [
                                    'label' => 'Roles', 
                                    'route' => 'role.index',    
                                    'auth' => true,
                                    'require' => [ 
                                        'permissions_any' => [
                                            'system access global admin', 
                                            'role list view', 
                                        ],
                                    ],
                                    
                                    'count' => Role::count() ?? 0, // shows 0 if none
                                ],
                                [
                                    'label' => 'Permissions', 
                                    'route' => 'permission.index',    
                                    'auth' => true,
                                    'require' => [ 
                                        'permissions_any' => [
                                            'system access global admin', 
                                            'permission list view', 
                                        ],
                                    ],
                                    
                                    'count' => Permission::count() ?? 0, // shows 0 if none
                                ],

                                  
                                 
                                 
                            ], 
                            'require' => [
                                
                                'modules_any' => [
                                    'Permission',
                                    'Role',
                                    'User',
                                ], // only show this heading if helper authorizes this module
                            ],

                        ], 
                    ],
                    'require' => [  // requirement to show the heading
                        'auth' => true,
                        // 'permissions_any' => [
                        //     'system access global admin', 
                        // ],
                        'modules_any' => [
                            'Permission',
                            'Role',
                            'User',
                        ],
                    ],
                ],  // ./ PROJECTS TO REVIEW



                [   // SETTINGS
                    // 'heading' => "Settings Management",
                    'items' => [ 
                        [
                            'label' => 'Setting Manager',
                            
                            'icon'  => 'settings',
                           
                            'patterns' => [
                                'setting.manager', 
                                'setting.index', 
                                 
                            ],
                            'children' => [
                                [
                                    'label' => 'Manage Settings', 
                                    'route' => 'setting.manager',    
                                    'auth' => true,
                                    'require' => [ 
                                        'permissions_any' => [
                                            'system access global admin',  
                                        ],
                                    ],
                                    
                                ],
                                [
                                    'label' => 'Setting Keys', 
                                    'route' => 'setting.index',    
                                    'auth' => true,
                                    'require' => [ 
                                        'permissions_any' => [
                                            'system access global admin', 
                                            'role list view', 
                                        ],
                                    ],
                                    
                                    
                                ], 
                                  
                                 
                                 
                            ], 
                            'require' => [
                                
                                'modules_any' => [
                                    'Setting'
                                ], // only show this heading if helper authorizes this module
                            ],

                        ], 
                    ],
                    'require' => [  // requirement to show the heading
                        'auth' => true,
                        'permissions_any' => [
                            'system access global admin', 
                        ],
                        'modules_any' => [
                            'Setting', 
                        ],
                    ],
                ],  // ./ SETTINGS
                




                [   // PROFILE
                    // 'heading' => "Profile Management",
                    'items' => [ 
                        [
                            'label' => 'Profile',
                            'icon'  => 'profile',
                            'route' => 'profile',          // make sure this route exists
                            'patterns' => ['profile'],
                            'auth' => true, 
                            'require' => [ 
                                'permissions_any' => [
                                    'profile update information',  
                                ],
                            ],
                            
                        ],
                    ],
                ],  // ./ PROFILE


                [   // LOG MANAGEMENT
                    // 'heading' => "Log Management",
                    'items' => [ 
                        [
                            'label' => 'Activity Logs',
                            'icon'  => 'activity-logs',
                            'route' => 'activity_logs.index',          // make sure this route exists
                            'patterns' => ['activity_logs.index'],
                            'auth' => true,
                            'require' => [ 
                                'modules_any' => [
                                    'Activity Logs'
                                ], // only show this heading if helper authorizes this module
                            ], 
                            'count' => $log_count,

                        ],
                    ],
                ],  // ./ LOG MANAGEMENT


                [   // TESTING LINKS
                    'heading' => "TESTING LINKS",
                    'items' => [ 

                        [
                            'label' => 'My Project Documents', 
                            'route' => 'test.project_document',     
                            'require' => [  // requirement to show the heading
                                'auth' => true,
                                'permissions_any' => [
                                    'system access global admin', 
                                ], 
                            ],
                            // 'count' => $projects_count = Project::countProjects('project.index') ?? 0, // shows 0 if none
                            
                        ],


                        [
                            'label' => 'Project Document Review', 
                            'route' => 'project.project_document.review',          // make sure this route exists
                            'patterns' => ['project.project_document.review'],
                            'require' => [  // requirement to show the heading
                                'auth' => true,
                                'permissions_any' => [
                                    'system access global admin', 
                                ], 
                            ], 

                        ],



                        [
                            'label' => 'Updated Project List', 
                            'route' => 'test.project.index',          // make sure this route exists
                            'patterns' => ['test.project.index'],
                            'require' => [  // requirement to show the heading
                                'auth' => true,
                                'permissions_any' => [
                                    'system access global admin', 
                                ], 
                            ],

                        ],

                         [
                            'label' => 'Project List Table', 
                            'route' => 'test.project.table',          // make sure this route exists
                            'patterns' => ['test.project.table'],
                            'require' => [  // requirement to show the heading
                                'auth' => true,
                                'permissions_any' => [
                                    'system access global admin', 
                                ], 
                            ],

                        ],
                        [
                            'label' => 'Project Show', 
                            'route' => 'test.project.show',          // make sure this route exists
                            'patterns' => ['test.project.show'],
                            'require' => [  // requirement to show the heading
                                'auth' => true,
                                'permissions_any' => [
                                    'system access global admin', 
                                ], 
                            ],

                        ],

                        [
                            'label' => 'Tennis', 
                            'route' => 'test.project.show_2', // make sure this route exists
                            'patterns' => ['test.project.show.2'],
                            'require' => [  // requirement to show the heading
                                'auth' => true,
                                'permissions_any' => [
                                    'system access global admin', 
                                ], 
                            ],

                        ],

                        [
                            'label' => 'Review List', 
                            'route' => 'test.review.list', // make sure this route exists
                            'patterns' => ['test.review.list'],
                            'require' => [  // requirement to show the heading
                                'auth' => true,
                                'permissions_any' => [
                                    'system access global admin', 
                                ], 
                            ],

                        ],

                        [
                            'label' => 'Re-Review', 
                            'route' => 'test.review.re_review', // make sure this route exists
                            'patterns' => ['test.review.re_review'],
                            'require' => [  // requirement to show the heading
                                'auth' => true,
                                'permissions_any' => [
                                    'system access global admin', 
                                ], 
                            ],

                        ],


                        [
                            'label' => 'Attachments', 
                            'route' => 'test.attachment.create', // make sure this route exists
                            'patterns' => ['test.attachment.create'],
                            'require' => [  // requirement to show the heading
                                'auth' => true,
                                'permissions_any' => [
                                    'system access global admin', 
                                ], 
                            ],

                        ],


                        [
                            'label' => 'Signature', 
                            'route' => 'test.review.digital_signature', // make sure this route exists
                            'patterns' => ['test.review.digital_signature'],
                            'require' => [  // requirement to show the heading
                                'auth' => true,
                                'permissions_any' => [
                                    'system access global admin', 
                                ], 
                            ],

                        ],

                        
            

                        


                    ],

                    'require' => [  // requirement to show the heading
                        'auth' => true,
                        'permissions_any' => [
                            'system access global admin', 
                        ], 
                    ],


                ],  // ./ TESTING LINKS
            ];

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render() 
    {
        return view('components.layout.navigation.menu');
    }
}
