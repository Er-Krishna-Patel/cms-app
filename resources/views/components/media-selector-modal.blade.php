{{-- Reusable Media Selector Modal - Use anywhere on the site --}}
<div class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4"
    id="mediaSelectorModal" 
    x-data="mediaSelectorData()" 
    @keydown.escape="closeModal()"
    @click.self="closeModal()">
    
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-6xl max-h-[90vh] flex flex-col overflow-hidden" @click.stop>
        <!-- Header -->
        <div class="flex items-center justify-between border-b bg-gray-50 px-6 py-4">
            <h2 class="text-xl font-semibold text-gray-900">Select Media</h2>
            <button @click="closeModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-hidden flex flex-col lg:flex-row">
            <!-- Main Media Area -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Toolbar -->
                <div class="bg-white border-b p-4 space-y-3">
                    <div class="flex flex-wrap gap-3 items-center">
                        <!-- Upload Button in Modal -->
                        <label class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 cursor-pointer transition font-medium text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Upload New
                            <input type="file" multiple hidden @change="uploadInModal($event)" accept="image/*,video/*,application/pdf">
                        </label>

                        <!-- Search -->
                        <div class="flex-1 min-w-[200px]">
                            <input type="text" placeholder="🔍 Search media..." x-model="searchTerm"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>

                        <!-- Filter -->
                        <select x-model="filterType" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="">All Types</option>
                            <option value="image">🖼️ Images</option>
                            <option value="video">🎬 Videos</option>
                            <option value="pdf">📄 PDFs</option>
                        </select>

                        <!-- View Toggle -->
                        <div class="flex gap-1 bg-gray-100 rounded-lg p-1">
                            <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-white shadow-sm' : ''" class="px-2 py-1 rounded text-sm">⊞</button>
                            <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-white shadow-sm' : ''" class="px-2 py-1 rounded text-sm">≡</button>
                        </div>
                    </div>

                    <!-- Bulk Actions -->
                    <div x-show="selectedItems.size > 0" class="flex items-center gap-2 bg-blue-50 p-3 rounded-lg border border-blue-200">
                        <span class="text-sm font-medium text-blue-900" x-text="`${selectedItems.size} item(s) selected`"></span>
                        <button @click="selectedItems.clear(); $nextTick()" class="ml-auto text-sm text-blue-600 hover:text-blue-700 font-medium">Clear</button>
                    </div>

                    <!-- Upload Progress -->
                    <div x-show="isUploading" class="space-y-2">
                        <div class="flex justify-between text-xs font-medium text-gray-700">
                            <span>Uploading...</span>
                            <span x-text="`${uploadProgress}%`"></span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all" :style="`width: ${uploadProgress}%`"></div>
                        </div>
                    </div>
                </div>

                <!-- Media Grid/List -->
                <div class="flex-1 overflow-y-auto p-4 bg-gray-50">
                    <!-- Empty State -->
                    <div x-show="filteredMedia.length === 0" class="flex flex-col items-center justify-center h-full text-gray-500">
                        <div class="text-6xl mb-2">📭</div>
                        <p class="font-medium">No media found</p>
                    </div>

                    <!-- Grid View -->
                    <div x-show="viewMode === 'grid' && filteredMedia.length > 0" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2">
                        <template x-for="item in filteredMedia" :key="item.id">
                            <div @click="toggleSelect(item.id)" 
                                :class="selectedItems.has(item.id) ? 'ring-4 ring-blue-500' : ''"
                                class="relative aspect-square bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg cursor-pointer transition-all group">
                                
                                <!-- Checkbox -->
                                <div class="absolute top-1 left-1 z-10">
                                    <div :class="selectedItems.has(item.id) ? 'bg-blue-600' : 'bg-white border-2 border-gray-300'" 
                                        class="w-5 h-5 rounded flex items-center justify-center transition">
                                        <span x-show="selectedItems.has(item.id)" class="text-white text-xs">✓</span>
                                    </div>
                                </div>

                                <!-- Image/Icon -->
                                <template x-if="item.is_image">
                                    <img :src="item.thumb_url" :alt="item.name" loading="lazy" class="w-full h-full object-cover group-hover:scale-110 transition-transform">
                                </template>
                                <template x-if="!item.is_image">
                                    <div class="w-full h-full flex items-center justify-center bg-gray-100 text-2xl" x-text="getFileIcon(item.type)"></div>
                                </template>

                                <!-- Hover Overlay -->
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all flex items-center justify-center">
                                    <span class="text-white font-semibold text-sm opacity-0 group-hover:opacity-100 transition">Select</span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- List View -->
                    <div x-show="viewMode === 'list' && filteredMedia.length > 0" class="space-y-2">
                        <template x-for="item in filteredMedia" :key="item.id">
                            <div @click="toggleSelect(item.id)"
                                :class="selectedItems.has(item.id) ? 'bg-blue-50 border-blue-500' : 'border-gray-200 hover:bg-gray-50'"
                                class="flex items-center gap-3 p-3 bg-white rounded-lg border cursor-pointer transition">
                                
                                <!-- Checkbox -->
                                <div :class="selectedItems.has(item.id) ? 'bg-blue-600' : 'bg-white border-2 border-gray-300'" 
                                    class="w-5 h-5 rounded flex-shrink-0 flex items-center justify-center">
                                    <span x-show="selectedItems.has(item.id)" class="text-white text-xs">✓</span>
                                </div>

                                <!-- Thumbnail -->
                                <div class="w-12 h-12 bg-gray-100 rounded flex-shrink-0 overflow-hidden flex items-center justify-center">
                                    <template x-if="item.is_image">
                                        <img :src="item.thumb_url" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!item.is_image">
                                        <span class="text-lg" x-text="getFileIcon(item.type)"></span>
                                    </template>
                                </div>

                                <!-- Info -->
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 truncate text-sm" x-text="item.name"></p>
                                    <p class="text-xs text-gray-500"><span x-text="item.size"></span> • <span x-text="item.date"></span></p>
                                </div>

                                <span class="text-xs font-medium text-gray-600 whitespace-nowrap capitalize" x-text="item.type"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Preview Sidebar -->
            <div class="w-full lg:w-64 border-t lg:border-t-0 lg:border-l bg-white flex flex-col">
                <div class="px-4 py-3 border-b font-semibold text-gray-800 text-sm">Preview</div>
                
                <div class="flex-1 overflow-y-auto p-4 space-y-4">
                    <template x-for="item in allMedia" :key="item.id">
                        <div x-show="selectedItems.size === 1 && selectedItems.has(item.id)" class="space-y-3">
                            <!-- Image Preview -->
                            <div class="bg-gray-100 rounded-lg flex items-center justify-center" style="height: 150px; width: 100%;">
                                <template x-if="item.is_image">
                                    <img :src="item.medium_url" class="max-w-full max-h-full object-contain">
                                </template>
                                <template x-if="!item.is_image">
                                    <span class="text-4xl" x-text="getFileIcon(item.type)"></span>
                                </template>
                            </div>

                            <!-- Info -->
                            <div class="text-xs space-y-2">
                                <div>
                                    <p class="font-semibold text-gray-700 mb-1">Name</p>
                                    <p class="text-gray-900 break-all" x-text="item.name"></p>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-700 mb-1">Size</p>
                                    <p class="text-gray-600" x-text="item.size"></p>
                                </div>
                                <div x-show="item.is_image && item.width">
                                    <p class="font-semibold text-gray-700 mb-1">Dimensions</p>
                                    <p class="text-gray-600" x-text="`${item.width} × ${item.height}px`"></p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="selectedItems.size !== 1" class="flex flex-col items-center justify-center h-full text-gray-500">
                        <div class="text-3xl mb-2">👈</div>
                        <p class="text-xs text-center">Select one item<br>to preview</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="border-t bg-gray-50 px-6 py-4 flex justify-end gap-3">
            <button @click="closeModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium text-sm">
                Cancel
            </button>
            <button @click="selectMedia()" :disabled="selectedItems.size === 0" 
                :class="selectedItems.size === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-700'"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium text-sm transition">
                <span x-show="selectedItems.size === 0">Select Media</span>
                <span x-show="selectedItems.size > 0" x-text="`Select (${selectedItems.size})`"></span>
            </button>
        </div>
    </div>
</div>

<script>
function mediaSelectorData() {
    return {
        viewMode: 'grid',
        searchTerm: '',
        filterType: '',
        selectedItems: new Set(),
        isUploading: false,
        uploadProgress: 0,
        allMedia: {{ $media->map(fn($item) => [
            'id' => $item->id,
            'name' => $item->original_name,
            'type' => $item->file_type,
            'url' => $item->url,
            'thumb_url' => $item->thumb_url,
            'medium_url' => $item->medium_url,
            'is_image' => $item->is_image,
            'size' => $item->human_readable_size,
            'date' => $item->created_at->format('M d, Y'),
            'width' => $item->width,
            'height' => $item->height,
        ])->toJson() }},

        get filteredMedia() {
            return this.allMedia.filter(item => {
                const matchesSearch = this.searchTerm === '' || 
                    item.name.toLowerCase().includes(this.searchTerm.toLowerCase());
                const matchesType = this.filterType === '' || item.type === this.filterType;
                return matchesSearch && matchesType;
            });
        },

        getFileIcon(type) {
            const icons = {
                'video': '🎬',
                'audio': '🎵',
                'pdf': '📄',
                'document': '📝',
                'default': '📎'
            };
            return icons[type] || icons['default'];
        },

        toggleSelect(itemId) {
            if (this.selectedItems.has(itemId)) {
                this.selectedItems.delete(itemId);
            } else {
                this.selectedItems.add(itemId);
            }
        },

        selectMedia() {
            const selected = Array.from(this.selectedItems).map(id => {
                return this.allMedia.find(item => item.id === id);
            }).filter(Boolean);

            if (window.onMediaSelected) {
                window.onMediaSelected(selected);
            }
            this.closeModal();
        },

        uploadInModal(event) {
            const files = Array.from(event.target.files);
            if (files.length === 0) return;

            this.isUploading = true;
            this.uploadProgress = 0;

            const formData = new FormData();
            files.forEach(file => formData.append('files[]', file));

            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            if (csrf) formData.append('_token', csrf);

            const xhr = new XMLHttpRequest();

            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    this.uploadProgress = Math.round((e.loaded / e.total) * 100);
                }
            });

            xhr.addEventListener('load', () => {
                this.isUploading = false;
                this.uploadProgress = 0;
                if (xhr.status === 200 || xhr.status === 201) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.uploaded && response.uploaded.length > 0) {
                        response.uploaded.forEach(file => {
                            this.allMedia.push({
                                id: file.id,
                                name: file.original_name,
                                type: file.file_type,
                                url: file.url,
                                thumb_url: file.thumb_url,
                                medium_url: file.medium_url,
                                is_image: file.is_image,
                                size: file.human_readable_size,
                                date: new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }),
                                width: file.width,
                                height: file.height,
                            });
                        });
                    }
                }
                event.target.value = '';
            });

            xhr.open('POST', '{{ route("admin.media.store") }}');
            xhr.send(formData);
        },

        closeModal() {
            document.getElementById('mediaSelectorModal').classList.add('hidden');
            this.selectedItems.clear();
            this.searchTerm = '';
            this.filterType = '';
        }
    };
}

// Global function to open media selector
window.openMediaSelector = function(callback) {
    window.onMediaSelected = callback;
    document.getElementById('mediaSelectorModal').classList.remove('hidden');
};
</script>
