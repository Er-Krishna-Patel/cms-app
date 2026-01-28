@props([
    'title' => 'Select Media',
    'modalId' => 'media-modal',
    'purpose' => 'insert', // 'insert' for content, 'featured' for featured image
])

<!-- Media Modal -->
<div 
    id="{{ $modalId }}" 
    class="media-modal"
    x-data="mediaModalComponent('{{ $modalId }}', '{{ $purpose }}')"
    @init="init()"
>
    <div class="media-modal-content" @click.stop>
        <!-- Header -->
        <div class="media-modal-header">
            <h2>{{ $title }}</h2>
            <button 
                type="button" 
                class="media-modal-close" 
                @click="closeModal()"
                aria-label="Close modal"
            >
                ✕
            </button>
        </div>

        <!-- Upload Section -->
        <div class="media-upload-section" @click="$refs.fileInput.click()">
            <label class="block text-sm font-medium text-gray-700 mb-2">Upload New Image</label>
            <input 
                type="file"
                x-ref="fileInput"
                class="media-upload-input" 
                accept="image/*" 
                @change="uploadFile($event)"
            >
            <p class="text-gray-600">Drag & drop or click to upload</p>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="media-grid-loading">
            <p>Loading media...</p>
        </div>

        <!-- Error State -->
        <div x-show="error" class="media-grid-error" x-text="error"></div>

        <!-- Media Grid -->
        <div x-show="!loading && !error" class="media-grid">
            <template x-for="media in mediaItems" :key="media.id">
                <div 
                    class="media-grid-item"
                    @click="selectMedia(media.id, media.url)"
                >
                    <img :src="media.url" :alt="media.filename">
                </div>
            </template>

            <!-- Empty State -->
            <div x-show="mediaItems.length === 0 && !loading && !error" class="media-grid-loading">
                <p>No media files yet</p>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/media-modal.css') }}">
@endpush

@push('scripts')
    <script type="module">
        import { setupMediaModal } from '{{ asset("js/modules/media-modal.ts") }}';
        
        window.mediaModalComponent = function(modalId, purpose) {
            return setupMediaModal(modalId, purpose);
        };
    </script>
@endpush
