 
<div class="bg-slate-100 p-6">
  <main class="mx-auto max-w-5xl">
    <!-- Header / Breadcrumbs -->
    <header class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <nav class="mb-1 text-xs text-slate-500" aria-label="Breadcrumb">
          <ol class="flex items-center gap-2">
            <li><span class="hover:text-slate-700">Projects</span></li>
            <li class="text-slate-400">/</li>
            <li><span class="hover:text-slate-700">PRJ-000124</span></li>
            <li class="text-slate-400">/</li>
            <li class="font-medium text-slate-700">Document Review</li>
          </ol>
        </nav>
        <h1 class="text-xl font-bold text-slate-900">Project Document Review</h1>
        <p class="text-xs text-slate-500">Provide your review, set a status, and attach supporting files.</p>
      </div>

      <!-- Overall Status Badge -->
      <div class="inline-flex items-center gap-2 rounded-full border border-amber-200 bg-amber-50 px-3 py-1">
        <span class="h-2 w-2 rounded-full bg-amber-400"></span>
        <span class="text-xs font-medium text-amber-800">In Review (Iteration 2)</span>
      </div>
    </header>

    <!-- Card -->
    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
      <!-- Tabs / Sections head (visual) -->
      <div class="flex items-center gap-4 border-b px-5 pt-4">
        <button type="button" class="relative rounded-t-lg px-3 py-2 text-sm font-semibold text-slate-900">
          Review
          <span class="absolute inset-x-0 -bottom-px h-0.5 bg-slate-900"></span>
        </button>
        <button type="button" class="rounded-t-lg px-3 py-2 text-sm text-slate-500 hover:text-slate-700">History</button>
        <button type="button" class="rounded-t-lg px-3 py-2 text-sm text-slate-500 hover:text-slate-700">Activity</button>
      </div>

      <div class="grid gap-6 p-5 md:grid-cols-12">
        <!-- Left column -->
        <div class="md:col-span-8 space-y-6">
          <!-- Review (long text) -->
          <div>
            <label for="review" class="mb-1 block text-sm font-medium text-slate-700">Review</label>
            <div class="relative">
              <textarea id="review" rows="8" placeholder="Write your detailed review, requested changes, and rationale…"
                        class="w-full resize-y rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200"></textarea>
              <!-- Char counter (visual only) -->
              <div class="pointer-events-none absolute bottom-2 right-3 text-[11px] text-slate-400">0 / 2000</div>
            </div>
            <!-- Example helper / error states (visual only) -->
            <p class="mt-1 text-xs text-slate-500">Be specific and actionable. Markdown accepted (design only).</p>
            <!-- <p class="mt-1 text-xs text-red-600">Review text is required.</p> -->
          </div>

          <!-- Review Status -->
          <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
              <label for="review_status" class="mb-1 block text-sm font-medium text-slate-700">Review Status</label>
              <div class="relative">
                <select id="review_status" class="w-full appearance-none rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                  <option value="pending">Pending</option>
                  <option value="changes_requested">Changes Requested</option>
                  <option value="approved">Approved</option>
                  <option value="returned">Returned</option>
                </select>
                <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
              </div>
            </div>

            <div>
              <label for="iteration" class="mb-1 block text-sm font-medium text-slate-700">Iteration</label>
              <input id="iteration" type="text" value="2"
                     class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 outline-none focus:border-slate-300" />
              <p class="mt-1 text-[11px] text-slate-500">Auto-handled by workflow (read-only example).</p>
            </div>
          </div>

          <!-- Attachments -->
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <label class="text-sm font-medium text-slate-700">Attachments</label>
              <span class="text-xs text-slate-400">Max 10 files · 50MB each</span>
            </div>

            <!-- Dropzone (visual only) -->
            <div class="flex items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center">
              <div>
                <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-white shadow">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2A2.5 2.5 0 0 0 5.5 21h13a2.5 2.5 0 0 0 2.5-2.5v-2M12 3v12m0 0 3.5-3.5M12 15 8.5 11.5"/></svg>
                </div>
                <p class="text-sm font-medium text-slate-700">Drag & drop files here</p>
                <p class="text-xs text-slate-500">or</p>
                <button type="button" class="mt-2 inline-flex items-center rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">Browse files</button>
              </div>
            </div>

            <!-- File list (visual) -->
            <ul class="space-y-2">
              <!-- File item: image preview -->
              <li class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-white p-2">
                <div class="flex items-center gap-3">
                  <img src="https://images.unsplash.com/photo-1520975922284-9f8a5c3e0f55?q=80&w=200&auto=format&fit=crop" alt="preview" class="h-10 w-10 rounded object-cover" />
                  <div>
                    <p class="text-sm font-medium text-slate-800">site-photo-01.jpg</p>
                    <p class="text-[11px] text-slate-500">1.2 MB · JPG</p>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <button type="button" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50">Preview</button>
                  <button type="button" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50">Remove</button>
                </div>
              </li>

              <!-- File item: generic doc -->
              <li class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-white p-2">
                <div class="flex items-center gap-3">
                  <div class="flex h-10 w-10 items-center justify-center rounded bg-indigo-50 text-indigo-700">PDF</div>
                  <div>
                    <p class="text-sm font-medium text-slate-800">revised-drawings.pdf</p>
                    <p class="text-[11px] text-slate-500">4.8 MB · PDF</p>
                  </div>
                </div>
                <div class="flex items-center gap-2">
                  <button type="button" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50">Preview</button>
                  <button type="button" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs text-slate-700 hover:bg-slate-50">Remove</button>
                </div>
              </li>

              <!-- Error row example -->
              <li class="rounded-lg border border-red-200 bg-red-50 p-2">
                <div class="flex items-center justify-between gap-3">
                  <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded bg-red-100 text-red-700">EXE</div>
                    <div>
                      <p class="text-sm font-medium text-red-800">installer.exe</p>
                      <p class="text-[11px] text-red-700">Blocked file type. Allowed: PDF, DOCX, XLSX, JPG, PNG</p>
                    </div>
                  </div>
                  <button type="button" class="rounded-lg bg-red-600 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-red-700">Remove</button>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <!-- Right column -->
        <aside class="md:col-span-4 space-y-6">
          <!-- Reviewer meta -->
          <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <h4 class="mb-2 text-sm font-semibold text-slate-800">Reviewer</h4>
            <div class="flex items-center gap-3">
              <div class="h-9 w-9 rounded-full bg-slate-300"></div>
              <div>
                <p class="text-sm font-medium text-slate-800">You (Arthur)</p>
                <p class="text-xs text-slate-500">Environmental Review | Iter. 2</p>
              </div>
            </div>
            <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-2 text-xs">
              <div>
                <dt class="text-slate-500">Due</dt>
                <dd class="font-medium text-slate-800">Nov 18, 2025</dd>
              </div>
              <div>
                <dt class="text-slate-500">Status</dt>
                <dd><span class="rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-medium text-amber-800">In Review</span></dd>
              </div>
            </dl>
          </div>

          <!-- Company inputs (optional block to align with your earlier requirement) -->
          <div class="rounded-xl border border-slate-200 bg-white p-4">
            <div class="mb-2 flex items-center justify-between">
              <h4 class="text-sm font-semibold text-slate-800">Project Companies</h4>
              <span class="text-xs text-slate-400">(multiple allowed)</span>
            </div>
            <div class="space-y-2">
              <input type="text" placeholder="e.g., Baseline Construction Corp." class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200" />
              <input type="text" placeholder="e.g., Kings GPTA Holdings" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200" />
              <button type="button" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">+ Add another</button>
            </div>
          </div>
        </aside>
      </div>

      <!-- Footer actions -->
      <div class="flex flex-col-reverse items-stretch justify-between gap-3 border-t p-5 sm:flex-row sm:items-center">
        <p class="text-xs text-slate-500">Save your review to notify the next reviewer in the workflow.</p>
        <div class="flex items-center gap-2">
          <button type="button" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Cancel</button>
          <button type="button" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Save Draft</button>
          <button type="button" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Submit Review</button>
        </div>
      </div>
    </section>
  </main>
</div> 
