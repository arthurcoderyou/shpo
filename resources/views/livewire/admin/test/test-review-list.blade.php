<div>
    


 
  <!-- Page wrapper -->
  <div class="min-h-screen">
    <!-- Header -->
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60 border-b border-slate-200">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
          <div class="flex items-start sm:items-center gap-3">
            <div class="p-2 rounded-2xl bg-emerald-600 text-white shadow-sm">
              <!-- Heroicon: Clipboard Document Check -->
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                <path fill-rule="evenodd" d="M7.5 3.75A2.25 2.25 0 0 1 9.75 1.5h4.5A2.25 2.25 0 0 1 16.5 3.75V4.5h.75A2.25 2.25 0 0 1 19.5 6.75v12A2.25 2.25 0 0 1 17.25 21H6.75A2.25 2.25 0 0 1 4.5 18.75v-12A2.25 2.25 0 0 1 6.75 4.5h.75v-.75Zm6 0v.75h-3V3.75a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 .75.75ZM9.53 12.28a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.06 0l4.25-4.25a.75.75 0 1 0-1.06-1.06l-3.72 3.72-1.72-1.72Z" clip-rule="evenodd" />
              </svg>
            </div>
            <div>
              <h1 class="text-xl sm:text-2xl font-semibold tracking-tight">Review List</h1>
              <p class="text-slate-500 text-sm">All reviews for this project document</p>
            </div>
          </div>
          <div class="flex flex-wrap items-center gap-2">
            <button class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium shadow-sm hover:bg-slate-50">
              <!-- Heroicon: Arrow Path -->
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992V4.356M2.985 14.652H7.98v4.992M20.297 9.703A8.25 8.25 0 1 0 9.703 20.297M14.297 3.703A8.25 8.25 0 0 0 3.703 14.297" />
              </svg>
              Refresh
            </button>
            <button class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
              <!-- Heroicon: Plus -->
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                <path d="M12 4.5a.75.75 0 0 1 .75.75V11h5.75a.75.75 0 0 1 0 1.5H12.75v5.75a.75.75 0 0 1-1.5 0V12.5H5.5a.75.75 0 0 1 0-1.5h5.75V5.25A.75.75 0 0 1 12 4.5Z"/>
              </svg>
              Add Review
            </button>
          </div>
        </div>

        <!-- Toolbar -->
        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-12">
          <div class="lg:col-span-5">
            <label class="sr-only" for="search">Search</label>
            <div class="relative">
              <input id="search" type="text" placeholder="Search reviewer, status, notes…" class="w-full rounded-2xl border border-slate-300 bg-white px-10 py-2.5 text-sm placeholder-slate-400 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" />
              <!-- Heroicon: Magnifying Glass -->
              <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197M16.5 10.5a6 6 0 1 1-12 0 6 6 0 0 1 12 0Z" />
              </svg>
            </div>
          </div>
          <div class="lg:col-span-7 flex flex-wrap items-center gap-2">
            <div class="flex items-center gap-2">
              <span class="text-xs text-slate-500">Status:</span>
              <div class="flex flex-wrap gap-2">
                <button class="rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs px-3 py-1.5 hover:bg-emerald-100">Approved</button>
                <button class="rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs px-3 py-1.5 hover:bg-amber-100">In Review</button>
                <button class="rounded-full bg-orange-50 text-orange-700 border border-orange-200 text-xs px-3 py-1.5 hover:bg-orange-100">Changes Requested</button>
                <button class="rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-xs px-3 py-1.5 hover:bg-rose-100">Rejected</button>
                <button class="rounded-full bg-slate-100 text-slate-700 border border-slate-200 text-xs px-3 py-1.5 hover:bg-slate-200">All</button>
              </div>
            </div>
            <div class="ml-auto flex items-center gap-2">
              <div class="relative">
                <select class="appearance-none rounded-xl border border-slate-300 bg-white py-2 pl-3 pr-10 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                  <option>Newest</option>
                  <option>Oldest</option>
                  <option>Status</option>
                  <option>Reviewer</option>
                </select>
                <!-- Heroicon: Chevron Down -->
                <svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
              </div>
              <button class="inline-flex items-center gap-1 rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm hover:bg-slate-50">
                <!-- Heroicon: Funnel -->
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 4.5h18M6.75 9.75h10.5M10.5 15h3" />
                </svg>
                More Filters
              </button>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Content -->
    <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6">
      <!-- Summary cards -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <div class="text-xs text-slate-500">Total</div>
          <div class="mt-1 flex items-baseline justify-between">
            <p class="text-2xl font-semibold">12</p>
            <span class="text-xs rounded-full bg-slate-100 px-2 py-0.5">All</span>
          </div>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm">
          <div class="text-xs text-emerald-700">Approved</div>
          <div class="mt-1 flex items-baseline justify-between">
            <p class="text-2xl font-semibold text-emerald-800">5</p>
            <span class="text-xs rounded-full bg-white px-2 py-0.5 border border-emerald-200">✔</span>
          </div>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm">
          <div class="text-xs text-amber-700">In Review</div>
          <div class="mt-1 flex items-baseline justify-between">
            <p class="text-2xl font-semibold text-amber-800">3</p>
            <span class="text-xs rounded-full bg-white px-2 py-0.5 border border-amber-200">●</span>
          </div>
        </div>
        <div class="rounded-2xl border border-orange-200 bg-orange-50 p-4 shadow-sm">
          <div class="text-xs text-orange-700">Changes Requested</div>
          <div class="mt-1 flex items-baseline justify-between">
            <p class="text-2xl font-semibold text-orange-800">2</p>
            <span class="text-xs rounded-full bg-white px-2 py-0.5 border border-orange-200">↺</span>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
              <tr>
                <th class="whitespace-nowrap px-4 py-3 text-left font-semibold">Order</th>
                <th class="whitespace-nowrap px-4 py-3 text-left font-semibold">Reviewer</th>
                <th class="whitespace-nowrap px-4 py-3 text-left font-semibold">Type</th>
                <th class="whitespace-nowrap px-4 py-3 text-left font-semibold">Status</th>
                <th class="whitespace-nowrap px-4 py-3 text-left font-semibold">Notes</th>
                <th class="whitespace-nowrap px-4 py-3 text-left font-semibold">Updated</th>
                <th class="whitespace-nowrap px-4 py-3 text-left font-semibold text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <!-- Row: Approved -->
              <tr class="hover:bg-slate-50/50">
                <td class="px-4 py-3 font-medium text-slate-700">1</td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <img class="h-9 w-9 rounded-full ring-2 ring-white shadow" src="https://i.pravatar.cc/40?img=8" alt="Avatar" />
                    <div>
                      <div class="font-medium">Rico D.</div>
                      <div class="text-xs text-slate-500">Senior Reviewer</div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3"><span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">Person</span></td>
                <td class="px-4 py-3">
                  <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 text-xs font-semibold">Approved</span>
                </td>
                <td class="px-4 py-3 max-w-[18rem]">
                  <p class="truncate text-slate-600">Looks good. Proceed to final.</p>
                </td>
                <td class="px-4 py-3 text-slate-500">Oct 18, 2025 • 10:24 AM</td>
                <td class="px-4 py-3">
                  <div class="flex items-center justify-end gap-1">
                    <button class="rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 hover:bg-slate-50">View</button>
                    <button class="rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 hover:bg-slate-50">Download</button>
                    <button class="rounded-xl bg-slate-900 text-white px-3 py-1.5 hover:bg-slate-800">More</button>
                  </div>
                </td>
              </tr>

              <!-- Row: In Review -->
              <tr class="hover:bg-slate-50/50">
                <td class="px-4 py-3 font-medium text-slate-700">2</td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <span class="h-9 w-9 grid place-content-center rounded-full bg-slate-100 text-slate-500 ring-2 ring-white shadow">AR</span>
                    <div>
                      <div class="font-medium">Open Review</div>
                      <div class="text-xs text-slate-500">Any Admin</div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3"><span class="inline-flex items-center gap-1 rounded-full border border-sky-200 bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-700">Open</span></td>
                <td class="px-4 py-3">
                  <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 text-xs font-semibold">In Review</span>
                </td>
                <td class="px-4 py-3 max-w-[18rem]">
                  <p class="truncate text-slate-600">Pending assignment. Please take this slot.</p>
                </td>
                <td class="px-4 py-3 text-slate-500">Oct 19, 2025 • 2:03 PM</td>
                <td class="px-4 py-3">
                  <div class="flex items-center justify-end gap-1">
                    <button class="rounded-xl bg-indigo-600 text-white px-3 py-1.5 hover:bg-indigo-700">Assign Self</button>
                    <button class="rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 hover:bg-slate-50">View</button>
                    <button class="rounded-xl bg-slate-900 text-white px-3 py-1.5 hover:bg-slate-800">More</button>
                  </div>
                </td>
              </tr>

              <!-- Row: Changes Requested -->
              <tr class="hover:bg-slate-50/50">
                <td class="px-4 py-3 font-medium text-slate-700">3</td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <img class="h-9 w-9 rounded-full ring-2 ring-white shadow" src="https://i.pravatar.cc/40?img=15" alt="Avatar" />
                    <div>
                      <div class="font-medium">Pam S.</div>
                      <div class="text-xs text-slate-500">Document Reviewer</div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3"><span class="inline-flex items-center gap-1 rounded-full border border-fuchsia-200 bg-fuchsia-50 px-2.5 py-1 text-xs font-medium text-fuchsia-700">Admin</span></td>
                <td class="px-4 py-3">
                  <span class="inline-flex items-center gap-1 rounded-full bg-orange-50 text-orange-700 border border-orange-200 px-2.5 py-1 text-xs font-semibold">Changes Requested</span>
                </td>
                <td class="px-4 py-3 max-w-[18rem]">
                  <p class="truncate text-slate-600">Please add site map reference and lot number in section 2.1.</p>
                </td>
                <td class="px-4 py-3 text-slate-500">Oct 20, 2025 • 9:18 AM</td>
                <td class="px-4 py-3">
                  <div class="flex items-center justify-end gap-1">
                    <button class="rounded-xl bg-emerald-600 text-white px-3 py-1.5 hover:bg-emerald-700">Approve</button>
                    <button class="rounded-xl bg-orange-600 text-white px-3 py-1.5 hover:bg-orange-700">Request Changes</button>
                    <button class="rounded-xl bg-rose-600 text-white px-3 py-1.5 hover:bg-rose-700">Reject</button>
                  </div>
                </td>
              </tr>

              <!-- Row: Rejected -->
              <tr class="hover:bg-slate-50/50">
                <td class="px-4 py-3 font-medium text-slate-700">4</td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <img class="h-9 w-9 rounded-full ring-2 ring-white shadow" src="https://i.pravatar.cc/40?img=44" alt="Avatar" />
                    <div>
                      <div class="font-medium">Kent C.</div>
                      <div class="text-xs text-slate-500">Final Reviewer</div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3"><span class="inline-flex items-center gap-1 rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-xs font-medium text-rose-700">Person</span></td>
                <td class="px-4 py-3">
                  <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 px-2.5 py-1 text-xs font-semibold">Rejected</span>
                </td>
                <td class="px-4 py-3 max-w-[18rem]">
                  <p class="truncate text-slate-600">Please resubmit with correct permit attachments.</p>
                </td>
                <td class="px-4 py-3 text-slate-500">Oct 12, 2025 • 4:55 PM</td>
                <td class="px-4 py-3">
                  <div class="flex items-center justify-end gap-1">
                    <button class="rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 hover:bg-slate-50">View</button>
                    <button class="rounded-xl bg-slate-900 text-white px-3 py-1.5 hover:bg-slate-800">More</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Footer actions -->
        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between border-t border-slate-100 px-4 py-3 bg-slate-50/60">
          <div class="text-xs text-slate-500">Showing <span class="font-medium text-slate-700">1–4</span> of <span class="font-medium text-slate-700">12</span> reviews</div>
          <div class="flex items-center gap-2">
            <button class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm hover:bg-slate-50">Previous</button>
            <button class="rounded-xl bg-slate-900 text-white px-3 py-2 text-sm hover:bg-slate-800">Next</button>
          </div>
        </div>
      </div>

      <!-- Mobile Cards -->
      <section class="mt-6 grid gap-3 sm:hidden">
        <!-- Card sample -->
        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
              <img class="h-10 w-10 rounded-full ring-2 ring-white shadow" src="https://i.pravatar.cc/40?img=8" alt="Avatar" />
              <div>
                <h3 class="font-semibold">Rico D. <span class="text-xs text-slate-400">• #1</span></h3>
                <p class="text-xs text-slate-500">Person • Senior Reviewer</p>
              </div>
            </div>
            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 text-xs font-semibold">Approved</span>
          </div>
          <p class="mt-3 text-sm text-slate-600">Looks good. Proceed to final.</p>
          <div class="mt-4 flex flex-wrap items-center justify-between gap-2">
            <p class="text-xs text-slate-500">Oct 18, 2025 • 10:24 AM</p>
            <div class="flex items-center gap-1">
              <button class="rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-xs hover:bg-slate-50">View</button>
              <button class="rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-xs hover:bg-slate-50">Download</button>
            </div>
          </div>
        </article>
      </section>
    </main>
  </div>
 















</div>
