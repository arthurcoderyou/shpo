<!--
  Project Document Review — Multi‑File (videos, photos, PDFs, common work files)
  Tailwind‑only UI template (no JS). Drop into a Blade view with Tailwind loaded.
  Includes: Assets overview, galleries per type, per‑file review states, comments, versioning, actions.
-->

<div class="min-h-screen bg-slate-50 p-6">
  <!-- Page Header -->
  <div class="mx-auto max-w-7xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
      <div class="space-y-1">
        <div class="flex flex-wrap items-center gap-2">
          <h1 class="text-2xl font-semibold text-slate-900">Project Document Review</h1>
          <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-[11px] font-medium text-indigo-700 ring-1 ring-inset ring-indigo-200">Package • v5</span>
        </div>
        <p class="max-w-2xl text-sm text-slate-600">This package contains multiple assets (videos, images, PDFs, Word/Excel, etc.) for the <span class="font-medium text-slate-800">Riverbend Redevelopment</span> review.</p>
        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-600">
          <span>Submitted: Aug 29, 2025 by <a href="#" class="font-medium hover:underline">Jane Cooper</a></span>
          <span class="h-1 w-1 rounded-full bg-slate-300"></span>
          <span>Package ID: #PKG-00873</span>
        </div>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">In Review</span>
        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-700 ring-1 ring-inset ring-slate-200">Due Sep 05, 2025</span>
      </div>
    </div>

    <!-- Assets Overview -->
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="text-xs text-slate-500">All files</div>
        <div class="mt-1 flex items-baseline gap-2"><div class="text-xl font-semibold text-slate-900">37</div><span class="text-[11px] text-slate-500">items</span></div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="text-xs text-slate-500">PDFs</div>
        <div class="mt-1 flex items-baseline gap-2"><div class="text-xl font-semibold text-slate-900">12</div><span class="text-[11px] text-slate-500">files</span></div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="text-xs text-slate-500">Images</div>
        <div class="mt-1 flex items-baseline gap-2"><div class="text-xl font-semibold text-slate-900">9</div><span class="text-[11px] text-slate-500">jpg/png</span></div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="text-xs text-slate-500">Videos</div>
        <div class="mt-1 flex items-baseline gap-2"><div class="text-xl font-semibold text-slate-900">3</div><span class="text-[11px] text-slate-500">mp4</span></div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="text-xs text-slate-500">Docs</div>
        <div class="mt-1 flex items-baseline gap-2"><div class="text-xl font-semibold text-slate-900">8</div><span class="text-[11px] text-slate-500">docx/xlsx</span></div>
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="text-xs text-slate-500">Other</div>
        <div class="mt-1 flex items-baseline gap-2"><div class="text-xl font-semibold text-slate-900">5</div><span class="text-[11px] text-slate-500">misc</span></div>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
      <!-- Left: Assets & Comments -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Assets: All Files grid (mixed types) -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
            <h2 class="text-sm font-semibold text-slate-800">Assets</h2>
            <div class="flex flex-wrap items-center gap-2 text-xs">
              <span class="rounded-full bg-slate-100 px-2.5 py-1 text-slate-700 ring-1 ring-inset ring-slate-200">All</span>
              <span class="rounded-full bg-slate-100 px-2.5 py-1 text-slate-700 ring-1 ring-inset ring-slate-200">PDF</span>
              <span class="rounded-full bg-slate-100 px-2.5 py-1 text-slate-700 ring-1 ring-inset ring-slate-200">Images</span>
              <span class="rounded-full bg-slate-100 px-2.5 py-1 text-slate-700 ring-1 ring-inset ring-slate-200">Videos</span>
              <span class="rounded-full bg-slate-100 px-2.5 py-1 text-slate-700 ring-1 ring-inset ring-slate-200">Docs</span>
            </div>
          </div>

          <div class="p-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
              <!-- Card: Image -->
              <div class="group overflow-hidden rounded-xl border border-slate-200">
                <div class="relative h-36 bg-slate-100">
                  <img alt="thumb" src="https://images.unsplash.com/photo-1482192596544-9eb780fc7f66?q=80&w=600" class="h-full w-full object-cover" />
                  <div class="absolute left-2 top-2 rounded-full bg-white/90 px-2 py-0.5 text-[10px] font-medium text-slate-700 ring-1 ring-slate-200">IMG</div>
                </div>
                <div class="flex items-center justify-between p-3">
                  <div>
                    <div class="line-clamp-1 text-sm font-medium text-slate-900">riverbend_site_01.jpg</div>
                    <div class="text-xs text-slate-500">1.2 MB • Updated Aug 30</div>
                  </div>
                  <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-700 ring-1 ring-inset ring-slate-200">Pending</span>
                </div>
                <div class="flex items-center justify-end gap-1 p-3 pt-0">
                  <a href="#" class="rounded-lg border border-slate-200 px-2 py-1 text-xs hover:bg-slate-50">Preview</a>
                  <a href="#" class="rounded-lg border border-slate-200 px-2 py-1 text-xs hover:bg-slate-50">Annotate</a>
                  <a href="#" class="rounded-lg bg-slate-900 px-2 py-1 text-xs text-white">Download</a>
                </div>
              </div>

              <!-- Card: Video -->
              <div class="group overflow-hidden rounded-xl border border-slate-200">
                <div class="relative h-36 bg-slate-900/90">
                  <div class="absolute inset-0 flex items-center justify-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/90 ring-1 ring-slate-200">
                      <svg class="h-5 w-5 text-slate-900" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                  </div>
                  <div class="absolute left-2 top-2 rounded-full bg-white/90 px-2 py-0.5 text-[10px] font-medium text-slate-700 ring-1 ring-slate-200">MP4</div>
                </div>
                <div class="flex items-center justify-between p-3">
                  <div>
                    <div class="line-clamp-1 text-sm font-medium text-slate-900">drone_sweep_north.mp4</div>
                    <div class="text-xs text-slate-500">214 MB • Updated Aug 28</div>
                  </div>
                  <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-medium text-amber-700 ring-1 ring-inset ring-amber-200">Changes requested</span>
                </div>
                <div class="flex items-center justify-end gap-1 p-3 pt-0">
                  <a href="#" class="rounded-lg border border-slate-200 px-2 py-1 text-xs hover:bg-slate-50">Preview</a>
                  <a href="#" class="rounded-lg border border-slate-200 px-2 py-1 text-xs hover:bg-slate-50">Timecodes</a>
                  <a href="#" class="rounded-lg bg-slate-900 px-2 py-1 text-xs text-white">Download</a>
                </div>
              </div>

              <!-- Card: PDF -->
              <div class="group overflow-hidden rounded-xl border border-slate-200">
                <div class="relative h-36 bg-slate-100">
                  <div class="absolute inset-0 flex items-center justify-center">
                    <div class="rounded-2xl bg-white px-3 py-2 text-xs font-semibold text-slate-800 ring-1 ring-slate-200">PDF PREVIEW</div>
                  </div>
                  <div class="absolute left-2 top-2 rounded-full bg-white/90 px-2 py-0.5 text-[10px] font-medium text-slate-700 ring-1 ring-slate-200">PDF</div>
                </div>
                <div class="flex items-center justify-between p-3">
                  <div>
                    <div class="line-clamp-1 text-sm font-medium text-slate-900">EIA_Section4_Mitigation.pdf</div>
                    <div class="text-xs text-slate-500">2.4 MB • Updated Aug 31</div>
                  </div>
                  <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">Approved</span>
                </div>
                <div class="flex items-center justify-end gap-1 p-3 pt-0">
                  <a href="#" class="rounded-lg border border-slate-200 px-2 py-1 text-xs hover:bg-slate-50">Preview</a>
                  <a href="#" class="rounded-lg border border-slate-200 px-2 py-1 text-xs hover:bg-slate-50">Annotate</a>
                  <a href="#" class="rounded-lg bg-slate-900 px-2 py-1 text-xs text-white">Download</a>
                </div>
              </div>

              <!-- Card: DOCX -->
              <div class="group overflow-hidden rounded-xl border border-slate-200">
                <div class="relative h-36 bg-slate-100">
                  <div class="absolute inset-0 flex items-center justify-center">
                    <div class="rounded-2xl bg-white px-3 py-2 text-xs font-semibold text-slate-800 ring-1 ring-slate-200">DOC PREVIEW</div>
                  </div>
                  <div class="absolute left-2 top-2 rounded-full bg-white/90 px-2 py-0.5 text-[10px] font-medium text-slate-700 ring-1 ring-slate-200">DOCX</div>
                </div>
                <div class="flex items-center justify-between p-3">
                  <div>
                    <div class="line-clamp-1 text-sm font-medium text-slate-900">Permit_Request_Form.docx</div>
                    <div class="text-xs text-slate-500">312 KB • Updated Aug 27</div>
                  </div>
                  <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-700 ring-1 ring-inset ring-slate-200">Pending</span>
                </div>
                <div class="flex items-center justify-end gap-1 p-3 pt-0">
                  <a href="#" class="rounded-lg border border-slate-200 px-2 py-1 text-xs hover:bg-slate-50">Preview</a>
                  <a href="#" class="rounded-lg border border-slate-200 px-2 py-1 text-xs hover:bg-slate-50">Track changes</a>
                  <a href="#" class="rounded-lg bg-slate-900 px-2 py-1 text-xs text-white">Download</a>
                </div>
              </div>

              <!-- Card: XLSX -->
              <div class="group overflow-hidden rounded-xl border border-slate-200">
                <div class="relative h-36 bg-slate-100">
                  <div class="absolute inset-0 flex items-center justify-center">
                    <div class="rounded-2xl bg-white px-3 py-2 text-xs font-semibold text-slate-800 ring-1 ring-slate-200">SHEET PREVIEW</div>
                  </div>
                  <div class="absolute left-2 top-2 rounded-full bg-white/90 px-2 py-0.5 text-[10px] font-medium text-slate-700 ring-1 ring-slate-200">XLSX</div>
                </div>
                <div class="flex items-center justify-between p-3">
                  <div>
                    <div class="line-clamp-1 text-sm font-medium text-slate-900">Sampling_Data_Q1_2024.xlsx</div>
                    <div class="text-xs text-slate-500">1.1 MB • Updated Aug 26</div>
                  </div>
                  <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-medium text-amber-700 ring-1 ring-inset ring-amber-200">Changes requested</span>
                </div>
                <div class="flex items-center justify-end gap-1 p-3 pt-0">
                  <a href="#" class="rounded-lg border border-slate-200 px-2 py-1 text-xs hover:bg-slate-50">Preview</a>
                  <a href="#" class="rounded-lg border border-slate-200 px-2 py-1 text-xs hover:bg-slate-50">Download</a>
                </div>
              </div>

              <!-- Add more cards as needed -->
            </div>
          </div>
        </div>

        <!-- Comments / Thread (package‑level) -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <div class="border-b border-slate-200 px-4 py-3">
            <div class="flex items-center justify-between">
              <h2 class="text-sm font-semibold text-slate-800">Package Comments</h2>
              <span class="text-xs text-slate-500">24 total</span>
            </div>
          </div>
          <div class="space-y-6 p-4">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
              <textarea rows="3" placeholder="Leave a package-level comment…" class="w-full resize-none rounded-lg border border-slate-200 bg-white p-3 text-sm outline-none focus:border-slate-300"></textarea>
              <div class="mt-2 flex items-center justify-between">
                <div class="text-xs text-slate-500">@mention reviewers • use #filename to reference a specific asset</div>
                <div class="flex items-center gap-2">
                  <button class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs hover:bg-slate-50">Attach</button>
                  <button class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-medium text-white">Comment</button>
                </div>
              </div>
            </div>

            <!-- Example comment -->
            <div class="flex gap-3">
              <img class="h-9 w-9 rounded-full ring-2 ring-white" src="https://i.pravatar.cc/72?img=1" alt="avatar" />
              <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2"><div class="text-sm font-medium text-slate-800">Alice Rivera</div><div class="text-xs text-slate-500">2h ago</div></div>
                <div class="mt-1 rounded-xl bg-slate-50 p-3 text-sm text-slate-800">For <span class="font-medium">drone_sweep_north.mp4</span>, can we annotate timecodes 00:41–01:10 for erosion points?</div>
                <div class="mt-2 flex items-center gap-3 text-xs text-slate-500"><button class="hover:text-slate-700">Reply</button><button class="hover:text-slate-700">Resolve</button></div>
              </div>
            </div>

            <!-- Example reply -->
            <div class="ml-12 flex gap-3">
              <img class="h-8 w-8 rounded-full ring-2 ring-white" src="https://i.pravatar.cc/72?img=2" alt="avatar" />
              <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2"><div class="text-sm font-medium text-slate-800">Mark Lee</div><div class="text-xs text-slate-500">1h ago</div></div>
                <div class="mt-1 rounded-xl bg-slate-50 p-3 text-sm text-slate-800">Logged as change request on the XLSX. Will upload v5.1.</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right: Review Panel -->
      <div class="space-y-6">
        <!-- Per‑file Filters / Batch Actions (stub) -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <div class="border-b border-slate-200 px-4 py-3">
            <h2 class="text-sm font-semibold text-slate-800">Filters & Batch</h2>
          </div>
          <div class="space-y-3 p-4 text-sm">
            <div class="grid grid-cols-2 gap-2">
              <select class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none">
                <option>All types</option><option>PDF</option><option>Image</option><option>Video</option><option>Doc</option><option>Sheet</option>
              </select>
              <select class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none">
                <option>All status</option><option>Approved</option><option>Pending</option><option>Changes requested</option>
              </select>
            </div>
            <div class="flex items-center gap-2">
              <button class="flex-1 rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50">Approve selected</button>
              <button class="flex-1 rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50">Request changes</button>
            </div>
          </div>
        </div>

        <!-- Reviewers -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <div class="border-b border-slate-200 px-4 py-3"><h2 class="text-sm font-semibold text-slate-800">Assigned Reviewers</h2></div>
          <div class="space-y-4 p-4">
            <div class="flex items-center justify-between"><div class="flex items-center gap-3"><img class="h-8 w-8 rounded-full ring-2 ring-white" src="https://i.pravatar.cc/72?img=11" alt=""/><div><div class="text-sm font-medium text-slate-800">Alice Rivera</div><div class="text-xs text-slate-500">Lead Reviewer</div></div></div><span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-medium text-emerald-700 ring-1 ring-inset ring-emerald-200">Approved</span></div>
            <div class="flex items-center justify-between"><div class="flex items-center gap-3"><img class="h-8 w-8 rounded-full ring-2 ring-white" src="https://i.pravatar.cc/72?img=5" alt=""/><div><div class="text-sm font-medium text-slate-800">Mark Lee</div><div class="text-xs text-slate-500">Environmental Analyst</div></div></div><span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-medium text-amber-700 ring-1 ring-inset ring-amber-200">Changes requested</span></div>
            <div class="flex items-center justify-between"><div class="flex items-center gap-3"><img class="h-8 w-8 rounded-full ring-2 ring-white" src="https://i.pravatar.cc/72?img=7" alt=""/><div><div class="text-sm font-medium text-slate-800">Dana Ortiz</div><div class="text-xs text-slate-500">Hydrology</div></div></div><span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-700 ring-1 ring-inset ring-slate-200">Pending</span></div>
          </div>
        </div>

        <!-- Version History -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <div class="border-b border-slate-200 px-4 py-3"><h2 class="text-sm font-semibold text-slate-800">Version History</h2></div>
          <ol class="divide-y divide-slate-200">
            <li class="flex items-center justify-between p-4"><div><div class="text-sm font-medium text-slate-800">v5</div><div class="text-xs text-slate-500">Aug 31, 2025 • Added drone footage & updated Section 4 PDFs</div></div><a href="#" class="rounded-lg border border-slate-200 px-2 py-1 text-xs hover:bg-slate-50">View</a></li>
            <li class="flex items-center justify-between p-4"><div><div class="text-sm font-medium text-slate-800">v4</div><div class="text-xs text-slate-500">Aug 20, 2025 • Images batch + budget sheet</div></div><a href="#" class="rounded-lg border border-slate-200 px-2 py-1 text-xs hover:bg-slate-50">View</a></li>
            <li class="flex items-center justify-between p-4"><div><div class="text-sm font-medium text-slate-800">v3</div><div class="text-xs text-slate-500">Aug 10, 2025 • Initial package</div></div><a href="#" class="rounded-lg border border-slate-200 px-2 py-1 text-xs hover:bg-slate-50">View</a></li>
          </ol>
        </div>

        <!-- Review Actions -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <div class="border-b border-slate-200 px-4 py-3"><h2 class="text-sm font-semibold text-slate-800">Review Actions</h2></div>
          <div class="space-y-3 p-4">
            <button class="w-full rounded-xl bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Approve Package</button>
            <button class="w-full rounded-xl bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700">Request Changes</button>
            <button class="w-full rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Return to Submitter</button>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <div class="border-b border-slate-200 px-4 py-3"><h2 class="text-sm font-semibold text-slate-800">Recent Activity</h2></div>
          <ul class="divide-y divide-slate-200">
            <li class="flex items-start gap-3 p-4"><span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200">✓</span><div><div class="text-sm text-slate-800">Alice approved <span class="font-medium">EIA_Section4_Mitigation.pdf</span></div><div class="text-xs text-slate-500">Today • 10:12</div></div></li>
            <li class="flex items-start gap-3 p-4"><span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200">!</span><div><div class="text-sm text-slate-800">Mark requested changes on <span class="font-medium">Sampling_Data_Q1_2024.xlsx</span></div><div class="text-xs text-slate-500">Yesterday • 16:40</div></div></li>
            <li class="flex items-start gap-3 p-4"><span class="mt-1 inline-flex h-5 w-5 items-center justify-center rounded-full bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-200">•</span><div><div class="text-sm text-slate-800">3 images uploaded by Jane</div><div class="text-xs text-slate-500">Aug 31, 2025 • 09:22</div></div></li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Sticky Footer Actions -->
    <div class="sticky bottom-4 z-10 mx-auto max-w-7xl">
      <div class="rounded-2xl border border-slate-200 bg-white/90 p-3 shadow-lg backdrop-blur">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <div class="flex items-center gap-2 text-xs text-slate-600"><span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 ring-1 ring-inset ring-slate-200"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Autosaved • a few seconds ago</span><span class="hidden sm:inline">|</span><span>Per‑file approvals roll up to Package status.</span></div>
          <div class="flex items-center gap-2">
            <a href="#" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</a>
            <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">Save Notes</button>
            <button class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Submit Review</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
