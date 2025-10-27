<div>
    {{-- <x-filepond::upload wire:model="file" multiple /> --}}


    {{-- <form wire:submit="save">
        <div
            x-data="{ uploading: false, progress: 0 }"
            x-on:livewire-upload-start="uploading = true"
            x-on:livewire-upload-finish="uploading = false"
            x-on:livewire-upload-cancel="uploading = false"
            x-on:livewire-upload-error="uploading = false"
            x-on:livewire-upload-progress="progress = $event.detail.progress"
        >
            <!-- File Input -->
            <input type="file" wire:model="photo">

            <button type="submit">Save</button>
    
            <!-- Progress Bar -->
            <div x-show="uploading">
                <progress max="100" x-bind:value="progress"></progress>
            </div>
        </div>
    
        <!-- ... -->
    </form> --}}


    <div class="min-h-screen bg-gray-50 flex items-center justify-center p-6">
        <div class="max-w-4xl w-full grid md:grid-cols-2 gap-8">

            <!-- Single File Upload -->
            <form class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 space-y-4">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h5v-2H4V5h12v10h-5v2h5a2 2 0 002-2V5a2 2 0 00-2-2H4z" />
                <path d="M9 12l2-2-2-2v4z" />
                </svg>
                Single File Upload
            </h2>

            <label
                class="flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all p-6 text-center">
                <div class="flex flex-col items-center justify-center">
                <svg aria-hidden="true" class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 16v-8m0 0l3 3m-3-3L9 11m9 5v4H6v-4m13 0l-1.293-1.293a1 1 0 00-.707-.293H7a1 1 0 00-.707.293L5 16" />
                </svg>
                <p class="text-sm text-gray-600"><span class="font-medium text-blue-600">Click to upload</span> or drag and drop</p>
                <p class="text-xs text-gray-400 mt-1">Only one file allowed (PDF, PNG, JPG)</p>
                </div>
                <input type="file" class="hidden" name="single_file" />
            </label>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium transition">Upload
                File</button>
            </form>

            <!-- Multiple File Upload -->
            <form class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 space-y-4">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                <path
                    d="M4 3a2 2 0 00-2 2v6a2 2 0 002 2h1v4l4-4h7a2 2 0 002-2V5a2 2 0 00-2-2H4z" />
                </svg>
                Multiple File Upload
            </h2>

            <label
                class="flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all p-6 text-center">
                <div class="flex flex-col items-center justify-center">
                <svg aria-hidden="true" class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 16v-8m0 0l3 3m-3-3L9 11m9 5v4H6v-4m13 0l-1.293-1.293a1 1 0 00-.707-.293H7a1 1 0 00-.707.293L5 16" />
                </svg>
                <p class="text-sm text-gray-600"><span class="font-medium text-emerald-600">Click to upload</span> or drag and drop</p>
                <p class="text-xs text-gray-400 mt-1">You can upload multiple files (ZIP, PDF, DOCX, etc.)</p>
                </div>
                <input type="file" multiple class="hidden" name="multiple_files[]" />
            </label>

            <button type="submit"
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded-lg font-medium transition">Upload
                Files</button>
            </form>

        </div>
    </div>



    <form wire:submit="save">

        <div class=""
            x-data="{ uploading: false, progress: 0 }"
            x-on:livewire-upload-start="uploading = true"
            x-on:livewire-upload-finish="uploading = false"
            x-on:livewire-upload-cancel="uploading = false"
            x-on:livewire-upload-error="uploading = false"
            x-on:livewire-upload-progress="progress = $event.detail.progress"
        >
            
            @if ($photo) 
                <img src="{{ $photo->temporaryUrl() }}">
            @endif

            @error('photos.*') <span class="error">{{ $message }}</span> @enderror


            Single: 
            <input type="file" wire:model="photo">

            Multiple:
            <input type="file" wire:model="photos" multiple>


    
            @error('photo') <span class="error">{{ $message }}</span> @enderror
        
            <button type="submit">Save photo</button>
        
            <!-- Progress Bar -->
            <div x-show="uploading">
                <progress max="100" x-bind:value="progress"></progress>
            </div>
        </div>

    
        
    </form>



</div>
