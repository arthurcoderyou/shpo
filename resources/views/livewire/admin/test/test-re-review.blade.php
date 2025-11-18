<!-- ======================== -->
<!-- 🚀 Request Re-Review Button -->
<!-- ======================== -->
<div x-data="{ 
    openRereview: false,
    openPreReview: false
}">
    <button
        type="button"
        @click="openRereview = true"
        class="inline-flex items-center gap-2 rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-amber-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-700 transition"
    >
        <!-- Refresh icon -->
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M4 4v6h6M20 20v-6h-6M20 4h-6M4 20h6" />
        </svg>
        Request Re-Review
    </button>

    <!-- ======================== -->
    <!-- 🧾 Re-Review Modal -->
    <!-- ======================== -->
    <div
        x-show="openRereview"
        x-cloak
        x-transition.opacity
        @keydown.escape.window="openRereview = false"
        @click.self="openRereview = false"
        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-4"
        aria-modal="true" role="dialog"
    >
        <!-- Modal box -->
        <div
            x-transition
            @click.stop
            class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden"
        >
            <!-- Header -->
            <div class="flex items-center justify-between border-b bg-amber-50 px-5 py-3">
                <h3 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4 4v6h6M20 20v-6h-6M20 4h-6M4 20h6" />
                    </svg>
                    Request Re-Review
                </h3>
                <button
                    @click="openRereview = false"
                    class="text-slate-500 hover:text-slate-700 transition"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">
                @if(!empty($last_review))
                <!-- Previuos -->
                <div>
                    <label class="block text-sm font-medium text-slate-700">Previous Reviewer</label>
                    {{-- <select
                        x-model="$wire.rereview.to_reviewer_id"
                        @change="$wire.loadLastReview($wire.rereview.to_reviewer_id)"
                        class="mt-1 w-full rounded-md border-slate-300 focus:border-amber-600 focus:ring-amber-600 text-sm"
                    >
                        <option value="">Select a reviewer</option>
                        @foreach($previous_reviewers as $r)
                            <option value="{{ $r['id'] }}">
                                {{ $r['name'] }} — Iter {{ $r['iteration'] ?? 1 }}
                            </option>
                        @endforeach
                    </select> --}}

                    <span class="mt-1 w-full rounded-md border-slate-300 focus:border-amber-600 focus:ring-amber-600 text-sm">
                        {{ $last_review->creator->name }}
                    </span>

                </div>
 
                
                <div>
                    <div class="rounded-xl border bg-slate-50 p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-slate-800">Previous Review Summary</p>
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full"
                                  class=" bg-emerald-50 text-emerald-700"
                            >
                                {{ strtoupper( $last_review->review_status ) }}
                            </span>
                        </div>

                        <div class="text-sm text-slate-600">
                            {{-- <p><span class="font-medium">Reviewer:</span> {{ $wire.last_review.reviewer_name }}</p>
                            <p><span class="font-medium">Reviewed:</span> {{ $wire.last_review.reviewed_at }}</p>
                            <p><span class="font-medium">Iteration:</span> {{ $wire.last_review.iteration }}</p>
                            <p><span class="font-medium">Role:</span> {{ $wire.last_review.role }}</p> --}}
                        </div>

                        <div>
                            <p class="text-xs text-slate-500 mb-1">Notes</p>
                            <div class="rounded-lg bg-white border p-3 text-sm text-slate-700">
                                {{ $last_review->project_review ?? 'No notes provided' }}
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Reason -->
                <div>
                    <label class="block text-sm font-medium text-slate-700">Reason for Re-Review</label>
                    <textarea
                        x-model="$wire.rereview.reason"
                        class="mt-1 w-full rounded-md border-slate-300 focus:border-amber-600 focus:ring-amber-600 text-sm"
                        placeholder="Explain why this project needs to be re-reviewed…"
                        rows="4"
                    ></textarea>
                </div>

                <!-- Quick tags -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tag common issues</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($common_issues as $tag)
                            <button
                                type="button"
                                @click="$wire.toggleRereviewIssue('{{ $tag }}')"
                                class="rounded-full border px-3 py-1 text-xs font-medium transition"
                                :class="{{ json_encode(in_array($tag, $rereview['issues'])) }} 
                                    ? 'bg-amber-50 border-amber-300 text-amber-800'
                                    : 'text-slate-600 hover:bg-slate-50'"
                            >
                                {{ $tag }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t px-5 py-3 flex justify-end gap-2 bg-slate-50">
                <button
                    type="button"
                    @click="openRereview = false"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    wire:click="submitRereview"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 transition disabled:opacity-50"
                >
                    <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 018-8" />
                    </svg>
                    Submit Re-Review
                </button>
            </div>
        </div>
    </div>




    <button
        type="button"
        @click="openPreReview = true"
        class="inline-flex items-center gap-2 rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-sky-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-700 transition"
    >
        <!-- User-add icon -->
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15 19l2 2 4-4M17 11a4 4 0 10-8 0 4 4 0 008 0zm-9 8a7 7 0 1114 0v1H8v-1z"/>
        </svg>
        Request Additional Reviewer
    </button>

    <!-- ======================== -->
    <!-- 📋 Pre-Review Modal -->
    <!-- ======================== -->
    <div
        x-show="openPreReview"
        x-cloak
        x-transition.opacity
        @keydown.escape.window="openPreReview = false"
        @click.self="openPreReview = false"
        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/40 backdrop-blur-sm p-4"
        aria-modal="true" role="dialog"
    >
        <!-- Modal Box -->
        <div
            x-transition
            @click.stop
            class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden"
        >
            <!-- Header -->
            <div class="flex items-center justify-between border-b bg-sky-50 px-5 py-3">
                <h3 class="text-base font-semibold text-slate-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-sky-600" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15 19l2 2 4-4M17 11a4 4 0 10-8 0 4 4 0 008 0zm-9 8a7 7 0 1114 0v1H8v-1z"/>
                    </svg>
                    Request Additional Reviewer
                </h3>
                <button
                    @click="openPreReview = false"
                    class="text-slate-500 hover:text-slate-700 transition"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                         stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">

                <!-- Select additional reviewer(s) -->
                <div>
                    <label class="block text-sm font-medium text-slate-700">Reviewer(s)</label>
                    <select
                        multiple
                        x-model="$wire.pre.reviewers"
                        class="mt-1 w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600 text-sm"
                    >
                        @foreach($eligible_reviewers as $r)
                            <option value="{{ $r['id'] }}">
                                {{ $r['name'] }} — {{ $r['dept'] ?? 'No Department' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Select one or more reviewers who must review before you continue.</p>
                </div>

                <!-- Review scope -->
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700">Scope of Review</label>
                        <select
                            x-model="$wire.pre.scope"
                            class="mt-1 w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600 text-sm"
                        >
                            <option value="">Select scope</option>
                            <option value="technical">Technical</option>
                            <option value="legal">Legal / Compliance</option>
                            <option value="content">Content / Clarity</option>
                            <option value="full">Full Review</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">Due Date</label>
                        <input
                            type="date"
                            x-model="$wire.pre.due_on"
                            class="mt-1 w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600 text-sm"
                        />
                    </div>
                </div>

                <!-- Instructions -->
                <div>
                    <label class="block text-sm font-medium text-slate-700">Instructions for Reviewers</label>
                    <textarea
                        x-model="$wire.pre.instructions"
                        class="mt-1 w-full rounded-md border-slate-300 focus:border-sky-600 focus:ring-sky-600 text-sm"
                        rows="4"
                        placeholder="Tell them what to check, e.g. verify attachments, confirm compliance, etc."
                    ></textarea>
                </div>

                <!-- Options -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 rounded-xl border bg-slate-50 p-3">
                    <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input
                            type="checkbox"
                            x-model="$wire.pre.block_my_review"
                            class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-600"
                        />
                        Block my review until they finish
                    </label>

                    <label class="flex items-center gap-2 text-sm text-slate-700">
                        <input
                            type="checkbox"
                            x-model="$wire.pre.notify_all"
                            class="h-4 w-4 rounded border-slate-300 text-sky-600 focus:ring-sky-600"
                        />
                        Notify all reviewers by email
                    </label>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t px-5 py-3 flex justify-end gap-2 bg-slate-50">
                <button
                    type="button"
                    @click="openPreReview = false"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    wire:click="submitPreReview"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700 transition disabled:opacity-50"
                >
                    <svg wire:loading class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"
                         stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12a8 8 0 018-8" />
                    </svg>
                    Send Review Request
                </button>
            </div>
        </div>
    </div>











</div>
