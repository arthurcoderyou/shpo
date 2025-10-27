<div class="min-h-screen bg-gray-50 text-slate-800">
 
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
    function toggleOffcanvas(open) {
      const panel = document.getElementById('notif-offcanvas');
      const backdrop = document.getElementById('offcanvas-backdrop');
      if (open) {
        panel.classList.remove('translate-x-full');
        backdrop.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
      } else {
        panel.classList.add('translate-x-full');
        backdrop.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
      }
    }
    function toggleProfileLg(){
      const dd = document.getElementById('profile-dd-lg');
      dd.classList.toggle('hidden');
    }
    window.addEventListener('click', (e)=>{
      const dd = document.getElementById('profile-dd-lg');
      if (dd && !e.target.closest('#profile-lg-parent')) dd.classList.add('hidden');
    });
    function setSidebarCollapsed(state){
      const aside = document.getElementById('desktop-sidebar');
      if (!aside) return;
      aside.setAttribute('data-collapsed', state ? 'true' : 'false');
      // Fallback for older Tailwind variants: also toggle width & labels explicitly
      aside.classList.toggle('w-72', !state);
      aside.classList.toggle('w-20', !!state);
      document.querySelectorAll('#desktop-sidebar .label').forEach(el=>{
        el.classList.toggle('hidden', !!state);
      });
      try { localStorage.setItem('sidebar-collapsed', state ? '1' : '0'); } catch(e) {}
    }
    function toggleSidebarCollapsed(){
      const aside = document.getElementById('desktop-sidebar');
      const isCollapsed = aside?.getAttribute('data-collapsed') === 'true';
      setSidebarCollapsed(!isCollapsed);
    }
    document.addEventListener('DOMContentLoaded', ()=>{
      const saved = (typeof localStorage !== 'undefined' && localStorage.getItem('sidebar-collapsed') === '1');
      setSidebarCollapsed(saved);
    });
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
      <button class="relative rounded-full p-2 hover:bg-gray-100" onclick="toggleOffcanvas(true)">
        <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C8.67 6.165 8 7.388 8 9v5.159c0 .538-.214 1.055-.595 1.436L6 17h5"/></svg>
        <span class="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full"></span>
      </button>
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
    <aside id="desktop-sidebar" data-collapsed="false" class="hidden lg:flex lg:flex-col w-72 data-[collapsed=true]:w-20 transition-[width] duration-300 ease-out lg:min-h-screen lg:sticky lg:top-0 lg:border-r bg-white group">
      <div class="px-5 py-4 border-b flex items-center justify-between">
        <div class="flex items-center gap-3">
          <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white font-bold">A</span>
          <div class="label group-data-[collapsed=true]:hidden">
            <div class="font-semibold">Your App</div>
            <div class="text-xs text-slate-500">Admin Portal</div>
          </div>
        </div>
        <button onclick="toggleSidebarCollapse()" class="rounded-lg border px-2 py-1 hover:bg-gray-50" aria-label="Toggle sidebar">
          <!-- show when expanded -->
          <svg class="w-4 h-4 group-data-[collapsed=true]:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
          <!-- show when collapsed -->
          <svg class="w-4 h-4 hidden group-data-[collapsed=true]:block" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
      </div>
      <!-- Sidebar nav -->
      <nav class="flex-1 overflow-y-auto px-3 py-4">
        <a href="#" title="Dashboard" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
          <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>
          <span class="label group-data-[collapsed=true]:hidden">Dashboard</span>
        </a>
        <a href="#" title="Tasks" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
          <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h8m-8 4h6"/></svg>
          <span class="label group-data-[collapsed=true]:hidden">Tasks</span>
        </a>
      </nav>
    </aside>

    <!-- Main content -->
    <main class="flex-1 min-w-0">
      <!-- Desktop top bar -->
      <div class="hidden lg:flex items-center justify-between px-6 py-3 border-b bg-white sticky top-0 z-40">
        <div class="flex items-center gap-3">
          <img src="https://dummyimage.com/120x40/4f46e5/ffffff&text=Logo" alt="Logo" class="h-8" />
        </div>
        <div class="flex items-center gap-3">
          <button class="relative rounded-full p-2 hover:bg-gray-100" onclick="toggleOffcanvas(true)">
            <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C8.67 6.165 8 7.388 8 9v5.159c0 .538-.214 1.055-.595 1.436L6 17h5"/></svg>
            <span class="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full"></span>
          </button>
          <div class="relative" id="profile-lg-parent">
            <button class="flex items-center gap-2 rounded-full border px-2 py-1 hover:bg-gray-50" onclick="toggleProfileLg()">
              <img src="https://i.pravatar.cc/32" class="w-8 h-8 rounded-full" />
              <span class="hidden sm:inline text-sm font-medium">Arthur</span>
            </button>
            <div id="profile-dd-lg" class="hidden absolute right-0 mt-2 w-48 bg-white border rounded-xl shadow-lg text-sm">
              <a href="#" class="block px-4 py-2 hover:bg-gray-50">Profile</a>
              <a href="#" class="block px-4 py-2 hover:bg-gray-50">Settings</a>
              <a href="#" class="block px-4 py-2 text-rose-600 hover:bg-gray-50">Sign out</a>
            </div>
          </div>
        </div>
      </div>

      <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <p class="text-slate-600">Main body content here...</p>
      </section>
    </main>
  </div>

  <!-- Offcanvas Notifications -->
  <div id="offcanvas-backdrop" class="hidden fixed inset-0 bg-black/40 z-50" onclick="toggleOffcanvas(false)"></div>
  <aside id="notif-offcanvas" class="fixed inset-y-0 right-0 w-full sm:w-[28rem] bg-white border-l z-50 translate-x-full transition-transform duration-200 ease-out">
    <div class="h-full flex flex-col">
      <div class="px-4 py-3 border-b flex items-center justify-between">
        <h3 class="font-semibold">Notifications</h3>
        <button class="rounded-lg border px-2 py-1 text-sm hover:bg-gray-50" onclick="toggleOffcanvas(false)">Close</button>
      </div>
      <div class="flex-1 overflow-y-auto divide-y">
        <div class="px-4 py-4">
          <p class="font-medium">Project Beta deadline moved</p>
          <p class="text-slate-500 text-sm">New due date: Sep 30. The client requested more time due to updated scope and stakeholder availability.</p>
        </div>
        <div class="px-4 py-4">
          <p class="font-medium">New comment from Kent</p>
          <p class="text-slate-500 text-sm">“Please review the UI spec and confirm acceptance criteria by EOD. Add notes to the task if anything is unclear.”</p>
        </div>
        <div class="px-4 py-4">
          <p class="font-medium">Document approved — Requirements.pdf</p>
          <p class="text-slate-500 text-sm">Version 1.8 has been approved by Legal and Finance. You can proceed with the vendor onboarding package.</p>
        </div>
      </div>
    </div>
  </aside> 
</div>

</div>
