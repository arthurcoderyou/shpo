@props([
    // Livewire boolean property name used with @entangle()
    'model' => 'showGuide',

    // Route/url to "Create Project" page
    'createRoute' => '#',
])
<div
    x-data="{ open: @entangle($model) }"
    x-init="
        if (open) {
            // Modal auto-opened for new user
        }
    "
>
    {{-- MODAL OVERLAY --}}
    <div
        x-show="open"
        x-transition
        @keydown.escape.window="open = false; $wire.closeGuide()"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    >
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-lg p-6 sm:p-7 space-y-5">

            {{-- Header --}}
            <div class="space-y-2">
                <h2 class="text-lg font-semibold text-slate-900 flex items-center gap-2">
                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-blue-50 text-blue-600 text-xl">
                        🧭
                    </span>
                    <span>Welcome! Let’s get your first project started</span>
                </h2>
                <p class="text-sm text-slate-600">
                    You don’t have any submitted projects yet.  
                    Follow these 3 quick steps to begin the project review process:
                </p>
            </div>

            {{-- HORIZONTAL 3-STEP PROGRESS --}}
            <div class="flex items-center gap-3">
                {{-- Step 1 --}}
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white text-sm font-semibold">
                        1
                    </div>
                    <span class="text-xs font-medium text-slate-800">
                        Create Project
                    </span>
                </div>

                <div class="flex-1 h-px bg-slate-200"></div>

                {{-- Step 2 --}}
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white text-sm font-semibold">
                        2
                    </div>
                    <span class="text-xs font-medium text-slate-800">
                        Fill Details
                    </span>
                </div>

                <div class="flex-1 h-px bg-slate-200"></div>

                {{-- Step 3 --}}
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white text-sm font-semibold">
                        3
                    </div>
                    <span class="text-xs font-medium text-slate-800">
                        Submit for Review
                    </span>
                </div>
            </div>

            {{-- Detailed step descriptions --}}
            <div class="space-y-4 mt-1">
                {{-- Step 1 --}}
                <div class="space-y-1">
                    <h3 class="text-sm font-semibold text-slate-800">
                        Step 1: Create your first project
                    </h3>
                    <p class="text-sm text-slate-600">
                        Click the <strong>"Create Project"</strong> button in the top-right corner of the page.
                    </p>
                </div>

                {{-- Step 2 --}}
                <div class="space-y-1">
                    <h3 class="text-sm font-semibold text-slate-800">
                        Step 2: Complete the project details
                    </h3>
                    <p class="text-sm text-slate-600">
                        Fill in all required fields such as project name, location, applicant, and RC number
                        (if already available).
                    </p>
                </div>

                {{-- Step 3 --}}
                <div class="space-y-1">
                    <h3 class="text-sm font-semibold text-slate-800">
                        Step 3: Submit the project for admin review
                    </h3>
                    <p class="text-sm text-slate-600">
                        After saving, open the project page and click the <strong>"Submit"</strong> button so
                        the admin can evaluate and approve it.
                    </p>
                </div>
            </div>

            <p class="text-xs text-slate-500">
                Once you’ve created and submitted your first project, this guide will no longer appear.
            </p>

            {{-- Actions --}}
            <div class="flex justify-end gap-3 pt-2">
                <button
                    type="button"
                    class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 text-sm hover:bg-slate-50"
                    @click="open = false; $wire.closeGuide()"
                >
                    Close
                </button>

            <button
                type="button"
                class="px-3 py-1.5 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700"
                @click="
                    open = false;
                    $wire.closeGuide();
                    window.location.href = '{{ $createRoute }}';
                "
            >
                Take me to Create Project
            </button>
            </div>

        </div>
    </div>
</div>


{{-- -
How to user 

 
<x-help.first-project-guide
    model="showGuide"
    :create-route="route('project.create')"
/>
--}}
