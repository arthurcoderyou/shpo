<div>
    
    <script>
        // Simple toggle for mobile sidebar
        function toggleSidebar(open) {
            const sidebar = document.getElementById('mobile-drawer');
            const backdrop = document.getElementById('backdrop');
            if (open) {
                sidebar.classList.remove('translate-x-[-100%]');
                backdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            } else {
                sidebar.classList.add('translate-x-[-100%]');
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }
    </script>


 
    <!-- Mobile top bar -->
    <header class="lg:hidden flex items-center justify-between gap-2 px-4 py-3 border-b bg-white sticky top-0 z-40">
        <button aria-label="Open navigation" class="inline-flex items-center justify-center rounded-xl border px-3 py-2 hover:bg-gray-50 active:scale-95 transition" onclick="toggleSidebar(true)">
        <!-- Hamburger -->
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        </button>
        <div class="font-semibold">Your App</div>
        <a href="#" class="inline-flex items-center gap-2 text-sm font-medium px-3 py-2 rounded-lg border hover:bg-gray-50">Sign in</a>
    </header>

    <!-- Layout wrapper -->
    <div class="flex">
        <!-- Static sidebar (desktop) -->
        <aside class="hidden lg:flex lg:flex-col lg:w-72 lg:min-h-screen lg:sticky lg:top-0 lg:border-r bg-white">
        <!-- Brand -->
        <div class="px-5 py-4 border-b">
            <div class="flex items-center gap-3">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white font-bold">A</span>
            <div>
                <div class="font-semibold">Your App</div>
                <div class="text-xs text-slate-500">Admin Portal</div>
            </div>
            </div>
        </div>
        <!-- Nav -->
        <nav class="flex-1 overflow-y-auto px-3 py-4">
            <ul class="space-y-1">
            <li>
                <a href="#" class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium hover:bg-indigo-50 hover:text-indigo-700">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>
                Dashboard
                </a>
            </li>
            <li>
                <a href="#" class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium hover:bg-indigo-50 hover:text-indigo-700">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20 13V7a2 2 0 00-2-2h-3M9 5H6a2 2 0 00-2 2v6m0 4a2 2 0 002 2h12a2 2 0 002-2m-6 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v8"/></svg>
                Projects
                </a>
            </li>
            <li>
                <a href="#" class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium hover:bg-indigo-50 hover:text-indigo-700">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h8m-8 4h6"/></svg>
                Tasks
                </a>
            </li>
            <li>
                <a href="#" class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium hover:bg-indigo-50 hover:text-indigo-700">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 10-8 0v4M5 11h14l-1 10H6L5 11z"/></svg>
                Documents
                </a>
            </li>
            <li>
                <a href="#" class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium hover:bg-indigo-50 hover:text-indigo-700">
                <svg class="w-5 h-5 text-slate-400 group-hover:text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 20h5V10h-5M2 20h5V4H2m7 16h6V14H9"/></svg>
                Reports
                </a>
            </li>
            </ul>
        </nav>
        <div class="px-4 py-4 border-t">
            <button class="w-full inline-flex items-center justify-center gap-2 rounded-xl border px-3 py-2 text-sm font-medium hover:bg-gray-50">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/></svg>
            Logout
            </button>
        </div>
        </aside>

        <!-- Mobile drawer -->
        <div id="backdrop" class="lg:hidden fixed inset-0 bg-black/50 hidden z-40" onclick="toggleSidebar(false)"></div>
        <aside id="mobile-drawer" class="lg:hidden fixed inset-y-0 left-0 w-72 bg-white border-r z-50 translate-x-[-100%] transition-transform duration-200 ease-out">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <div class="flex items-center gap-3">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white font-bold">A</span>
            <div>
                <div class="font-semibold">Your App</div>
                <div class="text-xs text-slate-500">Admin Portal</div>
            </div>
            </div>
            <button aria-label="Close navigation" class="rounded-xl border p-2 hover:bg-gray-50" onclick="toggleSidebar(false)">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <nav class="px-3 py-4 h-[calc(100vh-64px)] overflow-y-auto">
            <ul class="space-y-1">
            <li><a class="block rounded-xl px-3 py-2.5 text-sm font-medium hover:bg-indigo-50 hover:text-indigo-700" href="#">Dashboard</a></li>
            <li><a class="block rounded-xl px-3 py-2.5 text-sm font-medium hover:bg-indigo-50 hover:text-indigo-700" href="#">Projects</a></li>
            <li><a class="block rounded-xl px-3 py-2.5 text-sm font-medium hover:bg-indigo-50 hover:text-indigo-700" href="#">Tasks</a></li>
            <li><a class="block rounded-xl px-3 py-2.5 text-sm font-medium hover:bg-indigo-50 hover:text-indigo-700" href="#">Documents</a></li>
            <li><a class="block rounded-xl px-3 py-2.5 text-sm font-medium hover:bg-indigo-50 hover:text-indigo-700" href="#">Reports</a></li>
            </ul>
        </nav>
        </aside>

        <!-- Main content -->
        <main class="flex-1 min-w-0">
        <!-- Header w/ breadcrumb + section (tabs) -->
        <div class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm py-3" aria-label="Breadcrumb">
                <a href="#" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-700">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>
                Home
                </a>
                <span class="text-slate-400">/</span>
                <a href="#" class="text-slate-500 hover:text-slate-700">Projects</a>
                <span class="text-slate-400">/</span>
                <span class="font-medium text-slate-800">Overview</span>
            </nav>
            <!-- Section header -->
            <div class="py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                <h1 class="text-xl sm:text-2xl font-semibold">Projects Overview</h1>
                <p class="text-sm text-slate-500">Track active items, deadlines, and recent activity.</p>
                </div>
                <div class="flex items-center gap-2">
                <button class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm font-medium hover:bg-gray-50">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Project
                </button>
                <button class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm font-medium hover:bg-gray-50">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                    Filters
                </button>
                </div>
            </div>
            <!-- Section tabs -->
            <div class="flex items-center gap-1">
                <a href="#" class="px-3 py-2 text-sm rounded-lg font-medium bg-indigo-600 text-white">Overview</a>
                <a href="#" class="px-3 py-2 text-sm rounded-lg font-medium hover:bg-gray-100">Active</a>
                <a href="#" class="px-3 py-2 text-sm rounded-lg font-medium hover:bg-gray-100">Archived</a>
                <a href="#" class="px-3 py-2 text-sm rounded-lg font-medium hover:bg-gray-100">Settings</a>
            </div>
            </div>
        </div>

        <!-- Page content -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Example content grid -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <article class="bg-white border rounded-2xl p-5 shadow-sm">
                <div class="flex items-start justify-between">
                <h3 class="font-semibold">Project Alpha</h3>
                <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">On Track</span>
                </div>
                <p class="mt-2 text-sm text-slate-600">Modernize legacy modules and migrate data.</p>
                <div class="mt-4 flex items-center justify-between text-sm text-slate-500">
                <span>12 tasks</span>
                <span>Due Oct 20</span>
                </div>
            </article>
            <article class="bg-white border rounded-2xl p-5 shadow-sm">
                <div class="flex items-start justify-between">
                <h3 class="font-semibold">Project Beta</h3>
                <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700">At Risk</span>
                </div>
                <p class="mt-2 text-sm text-slate-600">Integrate payment gateway and receipts.</p>
                <div class="mt-4 flex items-center justify-between text-sm text-slate-500">
                <span>7 tasks</span>
                <span>Due Sep 30</span>
                </div>
            </article>
            <article class="bg-white border rounded-2xl p-5 shadow-sm">
                <div class="flex items-start justify-between">
                <h3 class="font-semibold">Project Gamma</h3>
                <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-700">Backlog</span>
                </div>
                <p class="mt-2 text-sm text-slate-600">Refactor notifications and broadcast events.</p>
                <div class="mt-4 flex items-center justify-between text-sm text-slate-500">
                <span>18 tasks</span>
                <span>No due date</span>
                </div>
            </article>
            </div>

            <!-- Example table -->
            <div class="mt-8 overflow-hidden rounded-2xl border bg-white">
            <div class="px-4 py-3 border-b flex items-center justify-between">
                <h3 class="font-semibold">Recent Activity</h3>
                <div class="flex items-center gap-2">
                <input type="text" placeholder="Search..." class="w-40 sm:w-56 rounded-xl border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30" />
                <button class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm font-medium hover:bg-gray-50">
                    Export
                </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-slate-600">
                    <tr>
                    <th class="text-left font-medium px-4 py-3">When</th>
                    <th class="text-left font-medium px-4 py-3">Who</th>
                    <th class="text-left font-medium px-4 py-3">Action</th>
                    <th class="text-left font-medium px-4 py-3">Target</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                    <td class="px-4 py-3">Today 9:12 AM</td>
                    <td class="px-4 py-3">Arthur</td>
                    <td class="px-4 py-3">Updated document</td>
                    <td class="px-4 py-3">Requirements.pdf</td>
                    </tr>
                    <tr>
                    <td class="px-4 py-3">Yesterday</td>
                    <td class="px-4 py-3">Melissa</td>
                    <td class="px-4 py-3">Created task</td>
                    <td class="px-4 py-3">Review UI spec</td>
                    </tr>
                    <tr>
                    <td class="px-4 py-3">Sep 10</td>
                    <td class="px-4 py-3">Kent</td>
                    <td class="px-4 py-3">Commented</td>
                    <td class="px-4 py-3">Project Beta</td>
                    </tr>
                </tbody>
                </table>
            </div>
            </div>
        </section>
        </main>
    </div>
 






    







</div>
