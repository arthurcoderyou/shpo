<div x-data="signaturePad()" class=" px-4 py-3 sm:px-6 lg:px-8  mx-auto" >
  <!-- ... your inputs ... -->

  <div class="rounded-xl border p-4">
    <div class="text-xs text-slate-500 mb-2">Sign inside the box:</div>

    <!-- Important: make the canvas the size you want via CSS -->
    <div class="bg-white rounded-lg border relative">
      <canvas id="sig-canvas"
              class="block w-full"
              style="height: 220px; touch-action: none;"></canvas>
    </div>

    <div class="mt-3 flex items-center gap-2">
      <button type="button" @click="clearCanvas()"
              class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700">
        Clear
      </button>
      <button type="button" @click="saveImage()"
              class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white">
        Save Signature
      </button>
    </div>
  </div>







  <!-- Card -->
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 max-w-full w-full align-middle ">

  
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm  overflow-x-auto ">
                


                     <!-- Header -->
                    <div class="w-full   px-4 py-2 border-b border-gray-200 bg-white">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-y-2">
                            <!-- Title and Subtitle -->
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                                    Signatures
                                    <span class="inline-flex justify-center items-center w-6 h-6 text-xs font-medium bg-black text-white rounded-full">
                                        {{ count($signatures) ?? 0 }}
                                    </span>
                                </h2>
                                <p class="text-sm text-gray-500">List of Signatures</p>
                            </div>

                            <!-- Action Bar -->
                            <div class="flex  flex-wrap items-center gap-2 justify-start sm:justify-end mt-2 sm:mt-0">

                                <!-- Search -->
                                <input type="text" wire:model.live="search"
                                    class="min-w-[160px] px-3 py-1.5 border border-gray-300 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Search"> 

                                {{-- <select wire:model.live="document_status" class="min-w-[140px] text-sm py-1.5 px-2 border rounded-md">
                                    <option value="">Document Status</option>
                                    @foreach ($document_status_options as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>

                                <select wire:model.live="document_type_id" class="min-w-[140px] text-sm py-1.5 px-2 border rounded-md">
                                    <option value="">Document Type</option>
                                    @foreach ($document_type_options as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select> --}}

                                {{-- @if(  $route !== "project.project-document.index")
                                    <button
                                        
                                        @click="openModal()"
                                        type="button"
                                        @keydown.window="handleKeydown" 
                                        class="py-2 px-3 inline-flex min-w-12 items-center gap-x-2 border-2 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50   "
                                    
                                        >
                                        
                                        {{ !empty($project->name) ? $project->name : "Search Project" }} 

                                        <svg class="shrink-0 size-[.8em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M233.4 406.6c12.5 12.5 32.8 12.5 45.3 0l192-192c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L256 338.7 86.6 169.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l192 192z"/></svg>
                                    </button>
                                @endif --}}


                                {{-- <select wire:model.live="sort_by" class="min-w-[160px] text-sm py-1.5 px-2 border rounded-md">
                                    <option value="">Sort By</option>
                                    
                                    <option>Document Name A - Z</option>
                                    <option>Document Name Z - A</option>
                                    <option>Project Name A - Z</option>
                                    <option>Project Name Z - A</option>
                                    <!-- Add rest of the sort options -->
                                </select> --}}

                                 

                                @if(!empty($project) && !empty($project->id))
                                <!-- Create Button -->
                                <a href="{{ route('project.project-document.create',['project' => $project->id]) }}"
                                wire:navigate
                                    class="text-sm px-3 py-1.5 rounded-md bg-yellow-500 text-white hover:bg-yellow-600">
                                    + New
                                </a>
                                @endif
 

                            </div>
                        </div>
                    </div>
                    <!-- End Header -->  





        
                    <!-- Table -->
                    <table class="w-full divide-y divide-gray-200 overflow-x-auto">
                        <thead class="bg-gray-50 ">
                        <tr>
                            
                            <th class=" w-4    px-3 py-3  ">
                                <div class="flex items-center gap-x-2">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                                    Signature
                                    </span>
                                </div>
                            </th>
                             
 
                            <th scope="col" class="px-2 py-3 text-start">
                                <div class="flex items-center gap-x-2">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                                    Created
                                    </span>
                                </div>
                            </th>
    

                            

                            
                        </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 ">

                            @if(!empty($signatures) && count($signatures) > 0)
                                @foreach ($signatures as $signature)
                                    <tr>

                                        <td class="w-fill text-nowrap align-top px-4 py-3   ">
                                            <img src="{{ asset('storage/'.$signature->signature_path) }}" alt="signature" class="max-w-2xl h-auto">


                                            <p>
                                              <code>
                                                {{ $signature->signature_path }}
                                              </code>
                                            </p>
                                        </td>
 

                                        <td class="px-4 py-2 text-slate-600 align-top">
                                        
                                            @php    
                                                $formatted = $this->returnFormattedDatetime($signature->created_at); 
                                            @endphp

                                            @if($formatted)
                                                <div class="text-sm">{{ $formatted }}</div>
                                                <div class="text-xs text-slate-400">
                                                    Signed by {{ $signature->user->name }}
                                                </div>
                                            @else
                                                <div class="text-sm text-slate-400 italic">No data yet</div>
                                            @endif
                                        </td>


                                        
    

                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <th scope="col" colspan="6" class="px-6 py-3 text-start">
                                        <div class="flex items-center gap-x-2">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 ">
                                            No records found
                                            </span>
                                        </div>
                                    </th>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <!-- End Table -->

                    <!-- Footer -->
                    <div class="px-6 py-4 grid gap-3 md:flex md:justify-between md:items-center border-t border-gray-200 ">
                        {{ $signatures->links() }}

                        <div class="inline-flex items-center gap-x-2">
                            <p class="text-sm text-gray-600 ">
                            Showing:
                            </p>
                            <div class="max-w-sm space-y-3">
                            <select wire:model.live="record_count" class="py-2 px-3 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 ">
                                <option>10</option>
                                <option>25</option>
                                <option>50</option>
                                <option>100</option>
                                <option>200</option>
                            </select>
                            </div>
                            <p class="text-sm text-gray-600 ">
                                {{ count($signatures) > 0 ? 'of '.$signatures->total()  : '' }}
                            </p>
                        </div>


                    </div>
                    <!-- End Footer -->


                </div>
            </div>
        </div>
    </div>
    <!-- End Card -->




























  {{-- <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('signaturePad', () => ({
        canvas: null,
        ctx: null,
        drawing: false,
        last: { x: 0, y: 0 },

        init() {
          this.canvas = this.$el.querySelector('#sig-canvas');
          this.ctx = this.canvas.getContext('2d', { willReadFrequently: false });

          const setSize = () => {
            // Get CSS pixel size
            const rect = this.canvas.getBoundingClientRect();
            const dpr = window.devicePixelRatio || 1;

            // Set the internal pixel buffer to match CSS size * DPR
            this.canvas.width = Math.max(1, Math.floor(rect.width * dpr));
            this.canvas.height = Math.max(1, Math.floor(rect.height * dpr));

            // Reset transform then scale so we can draw using CSS pixel coords
            this.ctx.setTransform(1, 0, 0, 1, 0, 0);
            this.ctx.scale(dpr, dpr);

            // Style
            this.ctx.lineWidth = 2;
            this.ctx.lineCap = 'round';
            this.ctx.strokeStyle = '#111827';
          };

          // Initial size + on resize
          setSize();
          new ResizeObserver(setSize).observe(this.canvas);

          // Pointer Events (unified mouse/touch/pen)
          this.canvas.addEventListener('pointerdown', (e) => {
            this.canvas.setPointerCapture(e.pointerId);
            const p = this._pos(e);
            this.drawing = true;
            this.last = p;
          }, { passive: true });

          this.canvas.addEventListener('pointermove', (e) => {
            if (!this.drawing) return;
            const p = this._pos(e);
            this.ctx.beginPath();
            this.ctx.moveTo(this.last.x, this.last.y);
            this.ctx.lineTo(p.x, p.y);
            this.ctx.stroke();
            this.last = p;
          }, { passive: true });

          const end = () => { this.drawing = false; };
          this.canvas.addEventListener('pointerup', end, { passive: true });
          this.canvas.addEventListener('pointercancel', end, { passive: true });

          // Livewire clear hook
          window.addEventListener('signature:clear', () => this.clearCanvas());
        },

        _pos(e) {
          const r = this.canvas.getBoundingClientRect();
          // Return CSS pixel coords; we scaled the context so 1 unit = 1 CSS pixel
          return { x: e.clientX - r.left, y: e.clientY - r.top };
        },

        clearCanvas() {
          // Clear using CSS pixel coords (context is scaled already)
          const r = this.canvas.getBoundingClientRect();
          this.ctx.clearRect(0, 0, r.width, r.height);
        },

        saveImage() {
          // Ensure crisp export: toDataURL uses the internal pixel buffer (already sized to DPR)
          const data = this.canvas.toDataURL('image/png');
          this.$wire.signatureData = data;
          this.$wire.save();
        }
      }));
    });
  </script> --}}


  <script>
    document.addEventListener('alpine:init', () => {
  Alpine.data('signaturePad', () => ({
    canvas: null,
    ctx: null,
    drawing: false,
    last: { x: 0, y: 0 },

    // throttling / state
    _syncInFlight: false,
    _lastSyncAt: 0,
    _throttleMs: 300,
    _throttleTimer: null,

    init() {
      this.canvas = this.$refs.sigCanvas;
      this.ctx = this.canvas.getContext('2d', { willReadFrequently: false });

      const setSize = () => {
        const rect = this.canvas.getBoundingClientRect();
        const dpr = window.devicePixelRatio || 1;
        const w = Math.max(1, Math.round(rect.width * dpr));
        const h = Math.max(1, Math.round(rect.height * dpr));
        if (this.canvas.width !== w || this.canvas.height !== h) {
          this.canvas.width = w;
          this.canvas.height = h;
          this.ctx.setTransform(1, 0, 0, 1, 0, 0);
          this.ctx.scale(dpr, dpr);
          this.ctx.lineWidth = 2;
          this.ctx.lineCap = 'round';
          this.ctx.strokeStyle = '#111827';
        }
      };

      this.$nextTick(() => {
        setSize();
        new ResizeObserver(setSize).observe(this.canvas);
      });

      const start = (e) => {
        e.preventDefault();
        this.canvas.setPointerCapture(e.pointerId);
        this.drawing = true;
        this.last = this._pos(e);
      };

      const move = (e) => {
        if (!this.drawing) return;
        e.preventDefault();
        const p = this._pos(e);
        this.ctx.beginPath();
        this.ctx.moveTo(this.last.x, this.last.y);
        this.ctx.lineTo(p.x, p.y);
        this.ctx.stroke();
        this.last = p;

        // optional: throttle updates while drawing
        this._throttleSync();
      };

      const end = () => {
        this.drawing = false;
        // force a sync at the end of each stroke
        this._syncNow();
      };

      this.canvas.addEventListener('pointerdown', start, { passive: false });
      this.canvas.addEventListener('pointermove',  move,  { passive: false });
      this.canvas.addEventListener('pointerup',    end,   { passive: true  });
      this.canvas.addEventListener('pointercancel',end,   { passive: true  });

      window.addEventListener('signature:clear', () => this.clearCanvas());
    },

    _pos(e) { return { x: e.offsetX, y: e.offsetY }; },

    clearCanvas() {
      const r = this.canvas.getBoundingClientRect();
      this.ctx.clearRect(0, 0, r.width, r.height);
      // reflect in Livewire immediately
      $wire.set('signatureData', null);
    },

    // Public: a button you can call for final save if needed
    async finalSave() {
      await this._syncNow();       // make sure latest pixels are uploaded
      await $wire.save();          // run your Livewire save()
    },

    // --- sync helpers ---
    _throttleSync() {
      const now = performance.now();
      if (this._syncInFlight || (now - this._lastSyncAt) < this._throttleMs) {
        // schedule one trailing sync
        clearTimeout(this._throttleTimer);
        this._throttleTimer = setTimeout(() => this._syncNow(), this._throttleMs);
        return;
      }
      this._syncNow();
    },

    async _syncNow() {
      clearTimeout(this._throttleTimer);
      this._throttleTimer = null;

      // wait one frame to ensure the last stroke segment is painted
      await new Promise((r) => requestAnimationFrame(r));
      try {
        this._syncInFlight = true;
        const dataUrl = this.canvas.toDataURL('image/png'); // data:image/png;base64,...
        await $wire.set('signatureData', dataUrl);          // Livewire v3: ensures property is updated
        this._lastSyncAt = performance.now();
      } finally {
        this._syncInFlight = false;
      }
    },
  }));
});


  </script>
</div>
