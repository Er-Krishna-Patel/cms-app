@extends('layouts.admin')

@section('content')
    <div class="flex h-screen" x-data="{
        viewMode: 'grid',
        selectedMedia: null,
        selectedItems: new Set(),
        searchTerm: '',
        selectedType: '',
        uploadingFiles: [],
        totalUploadProgress: 0,
        uploadMessage: '',
        uploadError: false,
        allMedia: {{ $media->map(
                fn($item) => [
                    'id' => $item->id,
                    'name' => $item->original_name,
                    'type' => $item->file_type,
                    'url' => $item->url,
                    'thumb_url' => $item->thumb_url,
                    'medium_url' => $item->medium_url,
                    'large_url' => $item->large_url,
                    'is_image' => $item->is_image,
                    'size' => $item->human_readable_size,
                    'date' => $item->created_at->format('M d, Y'),
                    'alt_text' => $item->alt_text,
                    'caption' => $item->caption,
                    'description' => $item->description,
                    'mime_type' => $item->mime_type,
                    'width' => $item->width,
                    'height' => $item->height,
                    'created_at' => $item->created_at->format('M d, Y H:i'),
                ],
            )->toJson() }},
        get filteredMedia() {
            return this.allMedia.filter(item => {
                const matchesSearch = this.searchTerm === '' ||
                    item.name.toLowerCase().includes(this.searchTerm.toLowerCase());
                const matchesType = this.selectedType === '' || item.type === this.selectedType;
                return matchesSearch && matchesType;
            });
        },
        toggleSelect(itemId) {
            if (this.selectedItems.has(itemId)) {
                this.selectedItems.delete(itemId);
            } else {
                this.selectedItems.add(itemId);
            }
        },
        selectAll() {
            this.filteredMedia.forEach(item => this.selectedItems.add(item.id));
        },
        clearSelection() {
            this.selectedItems.clear();
        },
        bulkDelete() {
            if (this.selectedItems.size === 0 || !confirm(`Delete ${this.selectedItems.size} item(s)?`)) return;
            
            const ids = Array.from(this.selectedItems);
            const token = document.querySelector('meta[name=\"csrf-token\"]')?.content;
            
            fetch('{{ route("admin.media.bulk-delete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ ids })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    this.allMedia = this.allMedia.filter(m => !ids.includes(m.id));
                    this.selectedItems.clear();
                    this.uploadMessage = `Deleted ${data.deleted} item(s)`;
                    this.uploadError = false;
                    setTimeout(() => this.uploadMessage = '', 3000);
                }
            });
        },
        getFileIcon(type) {
            const icons = {
                'video': '🎬',
                'audio': '🎵',
                'pdf': '📄',
                'document': '📝',
                'spreadsheet': '📊',
                'default': '📎'
            };
            return icons[type] || icons['default'];
        }
    }" data-upload-url="{{ route('admin.media.store') }}">
        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Toolbar -->
            <div class="bg-white border-b border-gray-200 p-4 shadow-sm">
                <div class="flex flex-wrap gap-3 items-center">
                    <!-- Upload -->
                    <label
                        class="flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 cursor-pointer transition font-medium shadow-sm hover:shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                            </path>
                        </svg>
                        <span>Upload Files</span>
                        <input type="file" multiple hidden accept="image/*" onchange="previewImages(event)">
                    </label>

                    <!-- Bulk Actions -->
                    <template x-if="selectedItems.size > 0">
                        <div class="flex gap-2 items-center px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg">
                            <span class="text-sm font-medium text-amber-900" x-text="`${selectedItems.size} selected`"></span>
                            <button @click="selectAll()" class="text-sm text-amber-600 hover:text-amber-700 font-medium">All</button>
                            <button @click="clearSelection()" class="text-sm text-amber-600 hover:text-amber-700 font-medium">Clear</button>
                            <button @click="bulkDelete()" class="ml-auto text-sm text-red-600 hover:text-red-700 font-medium flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete
                            </button>
                        </div>
                    </template>

                    <!-- Preview uploaded image -->
                    <div id="upload-preview" class="mt-3 h-28 overflow-hidden">
                        <div class="flex gap-2 items-center"></div>
                    </div>

                    <!-- Progress Bar -->
                    <div id="upload-progress" class="mt-2 w-full bg-gray-200 rounded-full h-1 overflow-hidden hidden">
                        <div id="upload-progress-bar" class="bg-blue-600 h-1 w-0 transition-all duration-300"></div>
                    </div>
                    
                    <!-- Search -->
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" placeholder="🔍 Search media..." x-model="searchTerm"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Filters -->
                    <select x-model="selectedType"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-medium">
                        <option value="">All Types</option>
                        <option value="image">🖼️ Images</option>
                        <option value="video">🎬 Videos</option>
                        <option value="pdf">📄 PDFs</option>
                        <option value="document">📝 Documents</option>
                    </select>

                    <!-- View Toggle -->
                    <div class="flex gap-1 bg-gray-100 rounded-lg p-1">
                        <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-white shadow-sm' : ''"
                            class="px-3 py-1.5 rounded-md transition text-sm font-medium">⊞ Grid</button>
                        <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-white shadow-sm' : ''"
                            class="px-3 py-1.5 rounded-md transition text-sm font-medium">≡ List</button>
                    </div>

                    <!-- Results Count -->
                    <div class="ml-auto text-sm text-gray-600 font-medium" x-show="filteredMedia.length > 0">
                        <span x-text="filteredMedia.length"></span> item(s)
                    </div>
                </div>

                <!-- Progress & Status -->
                <div x-show="uploadingFiles.length > 0" class="mt-4 space-y-3 max-h-64 overflow-y-auto">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Uploading <span
                                x-text="uploadingFiles.length"></span> file(s)...</span>
                        <span class="text-sm font-medium text-gray-700"
                            x-text="Math.round(totalUploadProgress) + '%'"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
                            :style="{ width: totalUploadProgress + '%' }"></div>
                    </div>

                    <!-- Individual File Progress -->
                    <template x-for="file in uploadingFiles" :key="file.id">
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <div class="flex items-center gap-2 mb-2">
                                <div
                                    class="w-10 h-10 bg-gray-100 rounded flex items-center justify-center flex-shrink-0 overflow-hidden">
                                    <img x-show="file.preview" :src="file.preview" class="w-full h-full object-cover">
                                    <span x-show="!file.preview" class="text-lg" x-text="file.icon"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-gray-900 truncate" x-text="file.name"></p>
                                    <p class="text-xs text-gray-500" x-text="file.progress + '%'"></p>
                                </div>
                            </div>
                            <div class="w-full bg-gray-300 rounded-full h-1.5">
                                <div class="bg-green-500 h-1.5 rounded-full transition-all duration-200"
                                    :style="{ width: file.progress + '%' }"></div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div x-show="uploadMessage"
                    :class="uploadError ? 'bg-red-50 text-red-600 border-red-200' :
                        'bg-green-50 text-green-600 border-green-200'"
                    class="mt-3 px-4 py-2 rounded-lg border text-sm font-medium" x-text="uploadMessage"></div>
            </div>

            <!-- Media Grid/List -->
            <div class="flex-1 overflow-y-auto p-6 bg-gray-50">
                <!-- Empty State -->
                <div x-show="filteredMedia.length === 0"
                    class="flex flex-col items-center justify-center h-full text-gray-500">
                    <div class="text-6xl mb-4">📭</div>
                    <p class="text-lg font-medium">No media files found</p>
                    <p class="text-sm" x-show="searchTerm || selectedType">Try adjusting your filters</p>
                </div>

                <!-- Grid View -->
                <div x-show="viewMode === 'grid' && filteredMedia.length > 0"
                    class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
                    <template x-for="item in filteredMedia" :key="item.id">
                        <div @click="selectedMedia = item.id"
                            :class="selectedMedia === item.id ? 'ring-4 ring-blue-500 ring-opacity-50' : ''"
                            class="relative aspect-square bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-xl cursor-pointer transition-all duration-200 group">
                            
                            <!-- Checkbox -->
                            <div class="absolute top-2 left-2 z-10" @click.stop="toggleSelect(item.id)">
                                <div :class="selectedItems.has(item.id) ? 'bg-blue-600' : 'bg-white border-2 border-gray-300'" 
                                    class="w-5 h-5 rounded flex items-center justify-center transition hover:scale-110">
                                    <span x-show="selectedItems.has(item.id)" class="text-white text-xs font-bold">✓</span>
                                </div>
                            </div>

                            <template x-if="item.is_image">
                                <img :src="item.thumb_url" :alt="item.name" loading="lazy" width="300"
                                    height="300"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            </template>
                            <template x-if="!item.is_image">
                                <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                    <span class="text-5xl" x-text="getFileIcon(item.type)"></span>
                                </div>
                            </template>
                            <!-- Hover Overlay with filename -->
                            <div
                                class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-200 flex items-end p-2">
                                <p class="text-white text-xs font-medium truncate opacity-0 group-hover:opacity-100 transition-opacity"
                                    x-text="item.name"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- List View -->
                <div x-show="viewMode === 'list' && filteredMedia.length > 0" class="space-y-2">
                    <template x-for="item in filteredMedia" :key="item.id">
                        <div @click="selectedMedia = item.id"
                            :class="selectedMedia === item.id ? 'bg-blue-50 border-blue-500 shadow-md' :
                                'border-gray-200 hover:bg-gray-50'"
                            class="flex items-center gap-4 p-3 bg-white rounded-lg border cursor-pointer transition-all">
                            
                            <!-- Checkbox -->
                            <div @click.stop="toggleSelect(item.id)" class="flex-shrink-0">
                                <div :class="selectedItems.has(item.id) ? 'bg-blue-600' : 'bg-white border-2 border-gray-300'" 
                                    class="w-5 h-5 rounded flex items-center justify-center transition hover:scale-110">
                                    <span x-show="selectedItems.has(item.id)" class="text-white text-xs font-bold">✓</span>
                                </div>
                            </div>

                            <div
                                class="w-14 h-14 bg-gray-100 rounded-lg flex-shrink-0 flex items-center justify-center overflow-hidden">
                                <template x-if="item.is_image">
                                    <img :src="item.thumb_url" :alt="item.name" width="56" height="56"
                                        class="w-full h-full object-cover">
                                </template>
                                <template x-if="!item.is_image">
                                    <span class="text-2xl" x-text="getFileIcon(item.type)"></span>
                                </template>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 truncate" x-text="item.name"></p>
                                <p class="text-xs text-gray-500"><span x-text="item.size"></span> • <span
                                        x-text="item.date"></span></p>
                            </div>
                            <span
                                class="px-3 py-1 bg-gray-100 text-xs font-medium text-gray-700 rounded-full whitespace-nowrap capitalize"
                                x-text="item.type"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="w-80 bg-white border-l border-gray-200 flex flex-col overflow-hidden shadow-lg">
            <div class="px-4 py-4 border-b border-gray-200 font-semibold text-gray-800">Attachment Details</div>

            <div class="flex-1 overflow-y-auto p-4">
                <template x-for="item in allMedia" :key="item.id">
                    <div x-show="selectedMedia === item.id" class="space-y-4">
                        <!-- Preview -->
                        <div class="bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden mx-auto"
                            style="width: 200px; height: 200px;">
                            <template x-if="item.is_image">
                                <img :src="item.medium_url" :alt="item.name" loading="lazy"
                                    class="max-w-full max-h-full object-contain">
                            </template>
                            <template x-if="!item.is_image">
                                <span class="text-6xl" x-text="getFileIcon(item.type)"></span>
                            </template>
                        </div>

                        <!-- Filename -->
                        <div>
                            <p class="text-xs font-semibold text-gray-700 uppercase mb-1">Filename</p>
                            <p class="text-sm text-gray-900 break-all font-medium" x-text="item.name"></p>
                        </div>

                        <!-- Metadata -->
                        <div>
                            <p class="text-xs font-semibold text-gray-700 uppercase mb-2">Metadata</p>
                            <div class="space-y-2">
                                <input type="text" placeholder="Alt text" :value="item.alt_text"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <textarea placeholder="Caption" rows="2"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    x-text="item.caption"></textarea>
                                <textarea placeholder="Description" rows="2"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    x-text="item.description"></textarea>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="bg-gray-50 rounded-lg p-3 space-y-2 text-xs">
                            <p><span class="text-gray-600">Size:</span> <span class="text-gray-900 font-medium"
                                    x-text="item.size"></span></p>
                            <p><span class="text-gray-600">Type:</span> <span class="text-gray-900 font-medium"
                                    x-text="item.mime_type"></span></p>
                            <template x-if="item.is_image && item.width">
                                <p><span class="text-gray-600">Dimensions:</span> <span class="text-gray-900 font-medium"
                                        x-text="item.width + '×' + item.height"></span></p>
                            </template>
                            <p><span class="text-gray-600">Uploaded:</span> <span class="text-gray-900 font-medium"
                                    x-text="item.created_at"></span></p>
                        </div>

                        <!-- Actions -->
                        <div class="space-y-2">
                            <button @click="navigator.clipboard.writeText(item.url).then(() => alert('✓ URL Copied!'))"
                                class="w-full px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg text-sm font-medium transition">📋
                                Copy URL</button>
                            <a :href="item.url" target="_blank"
                                class="block w-full px-3 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition text-center">🔗
                                Open File</a>
                            <form :action="'/admin/media/' + item.id" method="POST"
                                @submit.prevent="if(confirm('Delete this file permanently?')) $el.submit()">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full px-3 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition">🗑️
                                    Delete</button>
                            </form>
                        </div>
                    </div>
                </template>

                <div x-show="!selectedMedia" class="flex flex-col items-center justify-center h-full text-gray-500">
                    <div class="text-5xl mb-3">👈</div>
                    <p class="text-sm font-medium">Select a media item</p>
                    <p class="text-xs text-gray-400 mt-1">to view details</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        @vite('resources/js/media-upload.js')
    @endpush
@endsection



{{-- * Preview selected image files and show  progress bar --}}
<script>
    function previewImages(event) {
        const container = document.querySelector('#upload-preview > div');
        const progressWrapper = document.getElementById('upload-progress');
        const progressBar = document.getElementById('upload-progress-bar');

        container.innerHTML = '';
        progressBar.style.width = '0%';
        progressWrapper.classList.remove('hidden');

        const files = Array.from(event.target.files);
        let fakeProgress = 0;

        //  smooth fake progress
        const interval = setInterval(() => {
            if (fakeProgress < 90) {
                fakeProgress += 2;
                progressBar.style.width = fakeProgress + '%';
            }
        }, 50); // speed control (increase = slower)

        files.forEach(file => {
            if (!file.type.startsWith('image/')) return;

            const reader = new FileReader();

            reader.onload = e => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'w-20 h-20 object-cover rounded-md border shadow-sm';
                container.appendChild(img);
            };

            reader.readAsDataURL(file);
        });

        // finish progress smoothly
        setTimeout(() => {
            clearInterval(interval);
            progressBar.style.width = '100%';

            setTimeout(() => {
                progressWrapper.classList.add('hidden');
            }, 500);
        }, 1200); // total visible time
    }
</script>
