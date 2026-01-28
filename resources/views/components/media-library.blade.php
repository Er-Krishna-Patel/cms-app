@props([
    'title' => 'Media Library',
])

<div class="bg-white rounded-lg shadow-sm border border-secondary-200 p-4">
    <h3 class="text-sm font-semibold text-secondary-900 mb-3">{{ $title }}</h3>
    
    <!-- Media Grid -->
    <div id="media-grid" class="grid grid-cols-3 gap-2 max-h-80 overflow-y-auto">
        <div class="col-span-3 flex items-center justify-center h-20 bg-secondary-50 rounded border border-dashed border-secondary-300 text-secondary-500 text-xs">
            Loading media...
        </div>
    </div>
</div>

<script>
(function() {
    const mediaGrid = document.getElementById('media-grid');

    // Load existing media files
    function loadMediaFiles() {
        fetch('{{ route("admin.media.index") }}')
            .then(r => r.text())
            .then(html => {
                // Parse media from the HTML response
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const mediaItems = doc.querySelectorAll('[data-media-file]');
                
                mediaGrid.innerHTML = '';
                
                if (mediaItems.length > 0) {
                    mediaItems.forEach(item => {
                        const filename = item.dataset.filename;
                        const url = item.dataset.url;
                        
                        const gridItem = document.createElement('div');
                        gridItem.className = 'relative group cursor-pointer';
                        gridItem.innerHTML = `
                            <div class="relative overflow-hidden rounded border border-secondary-200 bg-secondary-50 aspect-square">
                                <img src="${url}" alt="Media" class="w-full h-full object-cover group-hover:opacity-75 transition">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition flex items-center justify-center">
                                    <button type="button" class="insert-media opacity-0 group-hover:opacity-100 transition bg-primary-600 text-white px-2 py-1 rounded text-xs" data-url="${url}">
                                        Insert
                                    </button>
                                </div>
                            </div>
                        `;
                        mediaGrid.appendChild(gridItem);
                    });
                } else {
                    mediaGrid.innerHTML = '<div class="col-span-3 flex items-center justify-center h-20 bg-secondary-50 rounded text-secondary-500 text-xs">No media files yet</div>';
                }
                
                // Attach insert handlers
                document.querySelectorAll('.insert-media').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        const url = btn.dataset.url;
                        // Insert into TinyMCE if available
                        if (typeof tinymce !== 'undefined' && tinymce.activeEditor) {
                            tinymce.activeEditor.insertContent(`<img src="${url}" alt="Inserted media" style="max-width: 100%;">`);
                        }
                    });
                });
            })
            .catch(err => {
                console.error('Failed to load media:', err);
                mediaGrid.innerHTML = '<div class="col-span-3 text-secondary-500 text-xs">Failed to load media</div>';
            });
    }

    // Load media on load
    loadMediaFiles();
})();
</script>
