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
                    Create a Project Document
                </h2>

                <p class="text-sm text-slate-600 leading-relaxed">
                    You are now on the <strong>Project Document</strong> page. Since your project
                    has been reviewed and assigned an <strong>RC Number</strong>, document creation
                    and file uploads are fully available.
                </p>

                <div class="border-l-4 border-blue-400 bg-blue-50 p-3 rounded">
                    <p class="text-sm text-slate-700 font-medium">
                        How to add a new project document:
                    </p>
                    <ol class="list-decimal list-inside text-sm text-slate-700 space-y-1 mt-2">
                        <li>Select the <strong>Document Type</strong> you want to add.</li>
                        <li>Upload the required files using the file upload section.</li>
                        <li>Once completed, save or submit the document as needed.</li>
                    </ol>
                </div>

                <p class="text-sm text-slate-600 leading-relaxed mt-3">
                    Each file may be up to <strong>20&nbsp;MB</strong>. The following file formats are supported:
                </p>

                <ul class="list-disc list-inside text-sm text-slate-600 mt-1 space-y-0.5">
                    <li>PNG, JPEG, JPG</li>
                    <li>PDF</li>
                    <li>DOCX</li>
                    <li>XLSX, CSV</li>
                    <li>TXT</li>
                    <li>ZIP</li>
                    <li>MP4</li>
                </ul>

                <div class="border-l-4 border-slate-300 bg-slate-50 p-3 rounded mt-3">
                    <p class="text-sm text-slate-700 font-medium">
                        Additional features on this page:
                    </p>
                    <ul class="list-disc list-inside text-sm text-slate-700 space-y-1 mt-2">
                        <li>Add and view <strong>project discussions</strong>.</li>
                        <li>Review <strong>project activity logs</strong> for tracking changes and actions.</li>
                    </ul>
                </div>

               <p class="text-xs text-slate-500 mt-3">
                    You may return to this page at any time to add more documents,
                    upload additional files, or review project activity.
                    To view all documents for this project, click the
                    <strong>Documents</strong> dropdown in the page header and select
                    <strong>“View Documents”</strong>, or use the <strong>Documents</strong> button below.
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
                        <x-project.button-link 
                            linkLabel=""
                            linkHref="{{ route('project.project-document.create',['project' => $project_id]) }}"

                            displayTooltip="true"
                            tooltipText="View project documents"

                            class="inline-flex items-center gap-1 rounded-xl border border-white  px-3 py-2 text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600"


                        >   
                            Documents 
                            


                        </x-project.button-link>  


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
