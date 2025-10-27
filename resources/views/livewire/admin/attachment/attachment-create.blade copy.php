<div>
    <!-- {{-- <x-filepond::upload wire:model="file" multiple /> --}} -->


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
