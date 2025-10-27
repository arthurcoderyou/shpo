<!--
  Project Documents — Tailwind-only, Responsive & Controls-First
  Goals:
  • Mobile-first cards (actions visible at top)
  • Desktop table with a STICKY first column for controls so users don’t scroll to the end
  • Clear project, review status, submitted, and updated columns
-->

<div class="space-y-6 p-4 sm:p-6">
  <!-- Toolbar -->
  <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-3">
      <h1 class="text-xl font-semibold text-slate-900 sm:text-2xl">Project Documents</h1>
      <span class="hidden sm:inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-200">128 total</span>
    </div>
    <div class="flex flex-wrap items-center gap-2">
      <div class="relative">
        <input type="search" placeholder="Search documents..." class="w-64 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm shadow-sm outline-none focus:border-slate-300" />
        <span class="pointer-events-none absolute right-3 top-2.5 text-slate-400">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        </span>
      </div>
      <select class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm outline-none">
        <option>All reviews</option><option>Approved</option><option>In review</option><option>Changes requested</option><option>Draft</option>
      </select>
      <select class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm outline-none">
        <option>Sort: Updated (desc)</option><option>Submitted (desc)</option><option>Name (A–Z)</option>
      </select>
    </div>
  </div>

  <!-- MOBILE: Card list (sm:hidden) -->
  <div class="grid grid-cols-1 gap-3 sm:hidden">
    <!-- Card -->
    <div class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
      <!-- Controls first -->
      <div class="mb-2 flex items-center gap-1">
        <button class="rounded-lg bg-slate-900 px-2.5 py-1 text-xs font-medium text-white">Open</button>
        <button class="rounded-lg border border-slate-200 px-2.5 py-1 text-xs">Download</button>
        <button class="rounded-lg border border-slate-200 px-2.5 py-1 text-xs">History</button>
        <button class="ml-auto rounded-lg border border-slate-200 px-2 py-1 text-xs">⋮</button>
      </div>
      <div class="flex items-start gap-3">
        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-50 ring-1 ring-slate-200">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
        </div>
        <div class="min-w-0 flex-1">
          <div class="flex flex-wrap items-center gap-2">
            <a href="#" class="truncate text-sm font-medium text-slate-900">EIA_Section4_Mitigation.pdf</a>
            <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">Approved</span>
          </div>
          <div class="mt-0.5 text-[13px] text-slate-700">
            <a href="#" class="font-medium hover:underline">Riverbend Redevelopment</a>
          </div>
          <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-500">
            <span>Submitted: Aug 29, 2025 • 14:18</span>
            <span class="h-1 w-1 rounded-full bg-slate-300"></span>
            <span>Updated: Aug 31, 2025 • 09:22</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Duplicate a few demo cards as needed... -->
  </div>

  <!-- DESKTOP: Table (hidden on mobile) with sticky actions column -->
  <div class="hidden overflow-auto rounded-2xl border border-slate-200 bg-white shadow-sm sm:block">
    <table class="min-w-full">
      <thead class="bg-slate-50">
        <tr class="text-left text-xs font-semibold text-slate-600">
          <th class="sticky left-0 z-10 w-44 border-r border-slate-200 bg-slate-50/95 px-3 py-3 backdrop-blur">Actions</th>
          <th class="px-4 py-3">Document</th>
          <th class="px-4 py-3">Project</th>
          <th class="px-4 py-3">Type</th>
          <th class="px-4 py-3">Review Status</th>
          <th class="px-4 py-3">Submitted</th>
          <th class="px-4 py-3">Last Updated</th>
          <th class="px-4 py-3">Size</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-200 text-sm">
        <!-- Row -->
        <tr class="bg-white hover:bg-slate-50">
          <!-- Sticky controls-first column -->
          <td class="sticky left-0 z-10 border-r border-slate-200 bg-white/95 px-3 py-2 backdrop-blur">
            <div class="flex flex-wrap items-center gap-1">
              <a href="#" class="rounded-md bg-slate-900 px-2.5 py-1 text-xs font-medium text-white">Open</a>
              <a href="#" class="rounded-md border border-slate-200 px-2.5 py-1 text-xs">Download</a>
              <a href="#" class="rounded-md border border-slate-200 px-2.5 py-1 text-xs">History</a>
            </div>
          </td>
          <td class="px-4 py-2">
            <div class="flex items-center gap-2">
              <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 ring-1 ring-slate-200">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
              </div>
              <div>
                <a href="#" class="font-medium text-slate-900 hover:underline">EIA_Section4_Mitigation.pdf</a>
                <div class="text-xs text-slate-500">Mitigation measures, rev 3</div>
              </div>
            </div>
          </td>
          <td class="px-4 py-2"><a href="#" class="font-medium text-slate-800 hover:underline">Riverbend Redevelopment</a></td>
          <td class="px-4 py-2 text-slate-600">PDF</td>
          <td class="px-4 py-2"><span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">Approved</span></td>
          <td class="px-4 py-2 text-slate-600">Aug 29, 2025 • 14:18</td>
          <td class="px-4 py-2 text-slate-600">Aug 31, 2025 • 09:22</td>
          <td class="px-4 py-2 text-slate-600">2.4 MB</td>
        </tr>

        <!-- Row -->
        <tr class="bg-white hover:bg-slate-50">
          <td class="sticky left-0 z-10 border-r border-slate-200 bg-white/95 px-3 py-2 backdrop-blur">
            <div class="flex flex-wrap items-center gap-1">
              <a href="#" class="rounded-md bg-slate-900 px-2.5 py-1 text-xs font-medium text-white">Open</a>
              <a href="#" class="rounded-md border border-slate-200 px-2.5 py-1 text-xs">Download</a>
              <a href="#" class="rounded-md border border-slate-200 px-2.5 py-1 text-xs">History</a>
            </div>
          </td>
          <td class="px-4 py-2">
            <div class="flex items-center gap-2">
              <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 ring-1 ring-slate-200">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
              </div>
              <div>
                <a href="#" class="font-medium text-slate-900 hover:underline">Sampling_Data_Q1_2024.xlsx</a>
                <div class="text-xs text-slate-500">Field readings (Q1)</div>
              </div>
            </div>
          </td>
          <td class="px-4 py-2"><a href="#" class="font-medium text-slate-800 hover:underline">Harbor Dredging</a></td>
          <td class="px-4 py-2 text-slate-600">XLSX</td>
          <td class="px-4 py-2"><span class="rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-700 ring-1 ring-inset ring-amber-200">Changes requested</span></td>
          <td class="px-4 py-2 text-slate-600">Aug 22, 2025 • 11:04</td>
          <td class="px-4 py-2 text-slate-600">Aug 30, 2025 • 16:40</td>
          <td class="px-4 py-2 text-slate-600">1.1 MB</td>
        </tr>
        
        <!-- Add more rows as needed... -->
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="flex items-center justify-between text-sm">
    <p class="text-slate-600">Showing 1–12 of 128</p>
    <nav class="isolate hidden -space-x-px rounded-xl shadow-sm ring-1 ring-slate-200 sm:inline-flex">
      <a href="#" class="relative inline-flex items-center rounded-l-xl px-3 py-2 text-slate-700 hover:bg-slate-50">Prev</a>
      <a href="#" class="relative z-10 inline-flex items-center bg-slate-900 px-3 py-2 font-medium text-white">1</a>
      <a href="#" class="relative inline-flex items-center px-3 py-2 text-slate-700 hover:bg-slate-50">2</a>
      <a href="#" class="relative inline-flex items-center px-3 py-2 text-slate-700 hover:bg-slate-50">3</a>
      <span class="relative inline-flex items-center px-3 py-2 text-slate-400">…</span>
      <a href="#" class="relative inline-flex items-center px-3 py-2 text-slate-700 hover:bg-slate-50">9</a>
      <a href="#" class="relative inline-flex items-center rounded-r-xl px-3 py-2 text-slate-700 hover:bg-slate-50">Next</a>
    </nav>
  </div>
</div>
