<div>
    <div
    x-data="{ open: @entangle('showGuide') }"
    >

        {{-- 🟢 Floating Help Button --}}
        <button
            type="button"
            @click="open = true"
            class="fixed bottom-6 right-6 z-40 inline-flex h-11 w-11 items-center justify-center
                rounded-full bg-blue-600 text-white shadow-lg hover:bg-blue-700 focus:outline-none
                focus:ring-2 focus:ring-blue-400 focus:ring-offset-2"
            title="Need help with project submission?"
        >
            {{-- Question mark icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 18h.01M8.257 8.257A3 3 0 0114 9c0 1.657-1.5 2.25-2.25 2.75S11 13 11 14m1-10a9 9 0 100 18 9 9 0 000-18z" />
            </svg>
        </button>
        
     
        <div
            x-show="open"
            x-transition
            @keydown.escape.window="open = false; $wire.closeGuide()"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        >
            <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 space-y-4">

                <h2 class="text-lg font-semibold text-slate-800">
                    Project Submitted Successfully
                </h2>

                <p class="text-sm text-slate-600 leading-relaxed">
                    Thank you for submitting your project. Your submission has been successfully
                    received and is now <strong>pending administrative review</strong>.
                </p>

                <p class="text-sm text-slate-600 leading-relaxed">
                    At this stage, no further action is required from you. Please allow the SHPO
                    administrator time to review and process your project.
                </p>

                <div class="border-l-4 border-blue-400 bg-blue-50 p-3 rounded">
                    <p class="text-sm text-slate-700 font-medium">
                        What happens next:
                    </p>
                    <ol class="list-decimal list-inside text-sm text-slate-700 space-y-1 mt-2">
                        <li>The administrator will review your submitted project.</li>
                        <li>An <strong>RC Number</strong> will be assigned once the review is completed.</li>
                        <li>After RC assignment, <strong>project document submission</strong> will be unlocked and available.</li>
                    </ol>
                </div>

                <p class="text-xs text-slate-500 mt-2">
                    You will be notified once the review is complete and the next steps are available.
                    We appreciate your patience during this process.
                </p>


                <div class="flex justify-between gap-3 mt-6">
                    <div class="flex items-center justify-between mt-4">
                        <label for="dsa_status" class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                            <input  
                                id="dsa_status"
                                type="checkbox"
                                wire:model.live="dsa_status"
                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                            >
                            Do not show again
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 mt-4">
                        <button
                            type="button"
                            class="px-3 py-1.5 rounded-lg border text-slate-600 text-sm hover:bg-slate-50"
                            @click="
                                open = false;
                                $wire.closeGuide();
                            "
                        >
                            Close
                        </button> 
                    </div>


                </div>

            </div>
        </div>
 
    </div>
</div>
