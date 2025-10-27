<div class="min-h-screen bg-gray-50 text-slate-800">
    
  
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tailwind – Sidebar with Dropdown Nav</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    function toggleSidebar(open) {
      const sidebar = document.getElementById('mobile-drawer');
      const backdrop = document.getElementById('backdrop');
      if (open) {
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
      } else {
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
      }
    }
  </script>  
  <!-- Mobile top bar -->
  <header class="lg:hidden flex items-center justify-between gap-2 px-4 py-3 border-b bg-white sticky top-0 z-40">
    <button aria-label="Open navigation" class="inline-flex items-center justify-center rounded-xl border px-3 py-2 hover:bg-gray-50 active:scale-95 transition" onclick="toggleSidebar(true)">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
      </svg>
    </button>
    <div class="font-semibold">Your App</div>
    <div class="flex items-center gap-3">
      <!-- Notifications dropdown -->
      <div class="relative">
        <button class="relative rounded-full p-2 hover:bg-gray-100" onclick="document.getElementById('notif-dd').classList.toggle('hidden')">
          <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C8.67 6.165 8 7.388 8 9v5.159c0 .538-.214 1.055-.595 1.436L6 17h5"/></svg>
          <span class="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full"></span>
        </button>
        <div id="notif-dd" class="hidden absolute right-0 mt-2 w-64 bg-white border rounded-xl shadow-lg text-sm">
          <ul class="divide-y">
            <li class="px-4 py-2 hover:bg-gray-50">New comment on Project A</li>
            <li class="px-4 py-2 hover:bg-gray-50">Document approved</li>
            <li class="px-4 py-2 hover:bg-gray-50">3 new tasks assigned</li>
          </ul>
        </div>
      </div>
      <!-- Profile dropdown -->
      <div class="relative">
        <button class="flex items-center gap-2 rounded-full border px-2 py-1 hover:bg-gray-50" onclick="document.getElementById('profile-dd').classList.toggle('hidden')">
          <img src="https://i.pravatar.cc/32" class="w-8 h-8 rounded-full" />
          <span class="hidden sm:inline text-sm font-medium">Arthur</span>
        </button>
        <div id="profile-dd" class="hidden absolute right-0 mt-2 w-48 bg-white border rounded-xl shadow-lg text-sm">
          <a href="#" class="block px-4 py-2 hover:bg-gray-50">Profile</a>
          <a href="#" class="block px-4 py-2 hover:bg-gray-50">Settings</a>
          <a href="#" class="block px-4 py-2 text-rose-600 hover:bg-gray-50">Sign out</a>
        </div>
      </div>
    </div>
  </header>

  <div class="flex">
    <!-- Desktop sidebar -->
    <aside class="hidden lg:flex lg:flex-col lg:w-72 lg:min-h-screen lg:sticky lg:top-0 lg:border-r bg-white">
      <div class="px-5 py-4 border-b flex items-center gap-3">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white font-bold">A</span>
        <div>
          <div class="font-semibold">Your App</div>
          <div class="text-xs text-slate-500">Admin Portal</div>
        </div>
      </div>
      <!-- Sidebar nav -->
      <nav class="flex-1 overflow-y-auto px-3 py-4">
        <p class="px-3 text-xs font-semibold text-slate-400">MAIN</p>
        <ul class="mt-2 space-y-1">
          <li>
            <details class="group">
              <summary class="flex items-center justify-between px-3 py-2.5 rounded-xl cursor-pointer hover:bg-indigo-50 hover:text-indigo-700">
                <span class="flex items-center gap-2">
                  <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>
                  Dashboard
                </span>
                <svg class="w-4 h-4 text-slate-400 group-open:rotate-180 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </summary>
              <ul class="ml-6 mt-1 space-y-1 text-sm">
                <li><a href="#" class="block px-3 py-1.5 rounded hover:bg-gray-50">Analytics</a></li>
                <li>
                  <details>
                    <summary class="flex items-center justify-between px-3 py-1.5 rounded cursor-pointer hover:bg-gray-50">
                      <span>Reports</span>
                      <svg class="w-4 h-4 text-slate-400 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <ul class="ml-5 mt-1 space-y-1">
                      <li><a href="#" class="block px-3 py-1 rounded hover:bg-gray-50">Monthly</a></li>
                      <li><a href="#" class="block px-3 py-1 rounded hover:bg-gray-50">Yearly</a></li>
                    </ul>
                  </details>
                </li>
              </ul>
            </details>
          </li>
          <li>
            <details class="group">
              <summary class="flex items-center justify-between px-3 py-2.5 rounded-xl cursor-pointer hover:bg-indigo-50 hover:text-indigo-700">
                <span class="flex items-center gap-2">
                  <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h8m-8 4h6"/></svg>
                  Tasks
                </span>
                <svg class="w-4 h-4 text-slate-400 group-open:rotate-180 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </summary>
              <ul class="ml-6 mt-1 space-y-1 text-sm">
                <li><a href="#" class="block px-3 py-1.5 rounded hover:bg-gray-50">Active</a></li>
                <li><a href="#" class="block px-3 py-1.5 rounded hover:bg-gray-50">Archived</a></li>
              </ul>
            </details>
          </li>
        </ul>
        <p class="mt-6 px-3 text-xs font-semibold text-slate-400">DOCUMENTS</p>
        <ul class="mt-2 space-y-1">
          <li>
            <details class="group">
              <summary class="flex items-center justify-between px-3 py-2.5 rounded-xl cursor-pointer hover:bg-indigo-50 hover:text-indigo-700">
                <span class="flex items-center gap-2">
                  <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 10-8 0v4M5 11h14l-1 10H6L5 11z"/></svg>
                  Documents
                </span>
                <svg class="w-4 h-4 text-slate-400 group-open:rotate-180 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </summary>
              <ul class="ml-6 mt-1 space-y-1 text-sm">
                <li><a href="#" class="block px-3 py-1.5 rounded hover:bg-gray-50">All Docs</a></li>
                <li><a href="#" class="block px-3 py-1.5 rounded hover:bg-gray-50">Shared</a></li>
                <li>
                  <details>
                    <summary class="flex items-center justify-between px-3 py-1.5 rounded cursor-pointer hover:bg-gray-50">
                      <span>Templates</span>
                      <svg class="w-4 h-4 text-slate-400 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </summary>
                    <ul class="ml-5 mt-1 space-y-1">
                      <li><a href="#" class="block px-3 py-1 rounded hover:bg-gray-50">Legal</a></li>
                      <li><a href="#" class="block px-3 py-1 rounded hover:bg-gray-50">Finance</a></li>
                    </ul>
                  </details>
                </li>
              </ul>
            </details>
          </li>
        </ul>
      </nav>
    </aside>

    <!-- Main content -->
    <main class="flex-1 min-w-0">
      <div class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
