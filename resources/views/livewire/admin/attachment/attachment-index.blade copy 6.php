@php
// Demo data, in a real app you’d fetch this from storage or database
$root = [
    'id' => 'root',
    'name' => 'Root',
    // Root-level files (visible in main list)
    'files' => [
        ['id' => 'f8', 'folder_id' => 'root', 'name' => 'readme.md', 'size' => '2 KB', 'type' => 'md', 'modified' => '2025-09-01',],
        ['id' => 'f9', 'folder_id' => 'root', 'name' => 'changelog.txt', 'size' => '6 KB', 'type' => 'txt', 'modified' => '2025-09-10'],
    ],
    // Root contains Documents, Images, and Media folders
    'children' => [
        [
            'id' => 'docs',
            'name' => 'Documents',
            'files' => [
                ['id' => 'f1', 'folder_id' => 'docs', 'name' => 'Project_Proposal.pdf', 'size' => '1.2 MB', 'type' => 'pdf', 'modified' => '2025-08-01'],
                ['id' => 'f2', 'folder_id' => 'docs', 'name' => 'Budget.xlsx', 'size' => '322 KB', 'type' => 'xlsx', 'modified' => '2025-08-22'],
            ],
            'children' => [
                [
                    'id' => 'reports',
                    'name' => 'Reports',
                    'files' => [
                        ['id' => 'f3', 'folder_id' => 'reports',  'name' => 'Q3_Report.docx', 'size' => '812 KB', 'type' => 'docx', 'modified' => '2025-09-12'],
                        ['id' => 'f4', 'folder_id' => 'reports',  'name' => 'Summary.txt', 'size' => '4 KB', 'type' => 'txt', 'modified' => '2025-09-15'],
                    ],
                ],
            ],
        ],
        [
            'id' => 'images',
            'name' => 'Images',
            'files' => [
                ['id' => 'f5', 'folder_id' => 'images',  'name' => 'banner.png', 'size' => '640 KB', 'type' => 'png', 'modified' => '2025-07-30'],
                ['id' => 'f6', 'folder_id' => 'images',  'name' => 'logo.svg', 'size' => '6 KB', 'type' => 'svg', 'modified' => '2025-06-18'],
            ],
        ],
        [
            'id' => 'media',
            'name' => 'Media',
            'files' => [
                ['id' => 'f10', 'name' => 'intro.mp4', 'size' => '10.4 MB', 'type' => 'mp4', 'modified' => '2025-08-05'],
            ],
            'children' => [
                [
                    'id' => 'audio',
                    'name' => 'Audio',
                    'files' => [
                        ['id' => 'f11', 'name' => 'theme.mp3', 'size' => '3.2 MB', 'type' => 'mp3', 'modified' => '2025-09-03'],
                        ['id' => 'f12', 'name' => 'notification.wav', 'size' => '512 KB', 'type' => 'wav', 'modified' => '2025-09-07'],
                    ],
                ],
            ],
        ],
    ],
];
@endphp

<div class="h-[90vh] w-full grid grid-cols-12 gap-4 p-4 bg-white" x-data="fileManager()" x-init="init(@js($root))">
    <!-- Sidebar -->
    <aside class="col-span-12 md:col-span-3 xl:col-span-2 bg-slate-50 rounded-2xl p-3 shadow-sm border border-slate-100 overflow-auto">

        <!-- -->
        <div class="flex items-center gap-2 mb-3">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 7h7l2 2h9v8a2 2 0 0 1-2 2H3z"/></svg>
            <h2 class="text-sm font-semibold">Folders</h2>
            <button class="ml-auto rounded-xl border px-2 py-1 text-xs hover:bg-white">New <svg class="inline h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14m7-7H5"/></svg></button>
        </div>

        <!-- Go to Root button -->
        <button @click="goRoot()"
                class="mb-2 w-full flex items-center gap-2 px-2 py-1.5 rounded-lg border border-slate-200 hover:bg-white"
                :class="activeFolder.id === 'root' ? 'bg-slate-100' : ''">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 7h7l2 2h9v8a2 2 0 0 1-2 2H3z"/></svg>
            <span class="text-sm font-medium text-slate-700">/</span>
        </button>

        <!-- Top-level folders under root -->
        <template x-for="folder in root.children" :key="folder.id">
            <div>
                <button @click="selectFolder(folder.id)"
                    class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-slate-100 w-full"
                    :class="activeFolder.id === folder.id ? 'bg-slate-100' : ''">
                    {{-- :class="activeFolder.id === folder.id ? 'bg-slate-100' : ''"  
                    checks if the current folder if the active folder based on the project id 
                    --}} 

                        <!-- folder icon -->
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 7h7l2 2h9v8a2 2 0 0 1-2 2H3z"/></svg>
                        <!-- folder name -->
                        <span x-text="folder.name" class="text-sm font-medium text-slate-700"></span>
                </button>
                <!-- If it has children, show them indented -->
                <template x-if="(folder.children || []).length">
                    <div class="ml-4 mt-1 border-l border-slate-200 pl-2 space-y-1">
                        <template x-for="child in folder.children" :key="child.id">
                            <button @click="selectFolder(child.id)"
                                class="flex items-center gap-2 px-2 py-1 rounded-lg hover:bg-slate-100 w-full"
                                :class="activeFolder.id === child.id ? 'bg-slate-100' : ''">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 7h7l2 2h9v8a2 2 0 0 1-2 2H3z"/></svg>
                                <span x-text="child.name" class="text-sm text-slate-700"></span>
                            </button>
                        </template>
                    </div>
                </template>

                <!-- Top-level files under folder --> 
                <!-- If it has files, show them -->
                <template x-if="(folder.files || []).length">
                    <div class="ml-4 mt-1 border-l border-slate-200 pl-2 space-y-1">
                        <template x-for="file in folder.files" :key="file.id" >
                            <button @click="selectFolder(file.folder_id)"
                                class="flex items-center gap-2 px-2 py-1 rounded-lg hover:bg-slate-100 w-full" 
                                >
                                {{-- <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 7h7l2 2h9v8a2 2 0 0 1-2 2H3z"/></svg> --}}
                                <span x-text="file.name" class="text-sm text-slate-700"></span>
                            </button>
                        </template>
                    </div> 
                </template>



            </div>
        </template>


        <!-- Top-level files under root --> 
        <!-- If it has files, show them -->
        <template x-if="(root.files || []).length">
            <div class="mb-2 w-full space-y-1">
                <template x-for="file in root.files" :key="file.id" >
                    <button @click="selectFolder(file.folder_id)"
                        class="flex items-center gap-2 px-2 py-1 rounded-lg hover:bg-slate-100 w-full" 
                        >
                        {{-- <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 7h7l2 2h9v8a2 2 0 0 1-2 2H3z"/></svg> --}}
                        <span x-text="file.name" class="text-sm text-slate-700"></span>
                    </button>
                </template>
            </div> 
        </template>


    </aside>
    

    <!-- Main -->
    <section class="col-span-12 md:col-span-9 xl:col-span-10 rounded-2xl border border-slate-100 shadow-sm">
        <!-- Toolbar -->
        <div class="flex flex-wrap items-center gap-2 p-3 border-b bg-white rounded-t-2xl">
            <!-- Back to Root -->
            <button @click="goRoot()" class="rounded-lg border px-2 py-1 text-xs">Go to Root</button>

            <input x-model="query" type="text" placeholder="Search folders & files…"
                class="pl-3 pr-3 py-2 rounded-lg border text-sm w-56 ml-auto" />
        </div>

        <!-- Mixed listing: subfolders + files of active folder -->
        <div class="p-3 overflow-auto h-[calc(90vh-120px)]">
            <template x-if="filteredItems.length === 0">
                <div class="h-full grid place-items-center text-center text-slate-500">
                    <p class="font-medium">Empty folder</p>
                    <p class="text-sm">No subfolders or files match your filter.</p>
                </div>
            </template>

            <div class="space-y-2" x-show="filteredItems.length > 0">
                <template x-for="item in filteredItems" :key="item.key">
                    <div class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-50 border">
                        <template x-if="item.kind === 'folder'">
                            <div class="flex items-center gap-2 min-w-0">
                                <!-- Folder icon -->
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 7h7l2 2h9v8a2 2 0 0 1-2 2H3z"/></svg>
                                <button @click="selectFolder(item.id)" class="text-sm font-medium text-slate-700 hover:underline truncate" x-text="item.name"></button>
                                <span class="ml-2 text-[11px] px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-600">Folder</span>
                            </div>
                        </template>

                        <template x-if="item.kind === 'file'">
                            <>
                                <input type="checkbox" x-model="selected" :value="item.id" class="h-4 w-4" />
                                <div class="flex items-center gap-2 min-w-0">
                                    <!-- File icon -->
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                                    <span class="truncate text-sm font-medium text-slate-700" x-text="item.name"></span>
                                </div>
                                <span class="text-xs text-slate-500 ml-auto" x-text="item.size || '—'"></span>
                            </>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </section>
</div>

<script>
    function fileManager() {
        return {
            root: {},
            activeFolder: {},
            query: '',
            selected: [],
            init(data) {
                this.root = data;
                this.activeFolder = data; // start at root
            },
            goRoot() {
                this.activeFolder = this.root;
            },
            selectFolder(id) {
                const find = (node, id) => {
                    if (node.id === id) return node;
                    for (const child of node.children || []) {
                        const found = find(child, id);
                        if (found) return found;
                    }
                    return null;
                };
                const found = find(this.root, id);
                if (found) this.activeFolder = found;
            },
            // Computed: subfolders + files in current folder
            get items() {
                const folders = (this.activeFolder.children || []).map(f => ({
                    key: 'folder:' + f.id,
                    kind: 'folder',
                    id: f.id,
                    name: f.name
                }));
                const files = (this.activeFolder.files || []).map(f => ({
                    key: 'file:' + f.id,
                    kind: 'file',
                    id: f.id,
                    name: f.name,
                    size: f.size || null
                }));
                return [...folders, ...files];
            },
            // Search across both folders and files
            get filteredItems() {
                const q = this.query.trim().toLowerCase();
                if (!q) return this.items;
                return this.items.filter(i => (i.name || '').toLowerCase().includes(q));
            }
        }
    }
</script>
