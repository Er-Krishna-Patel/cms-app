{{-- Helper component to use media selector anywhere --}}
{{-- Usage: 
    In any blade file:
    
    <button onclick="openMediaSelector(function(selected) {
        console.log('Selected media:', selected);
        // Do something with selected media
        // selected is an array of media objects with: id, name, url, thumb_url, etc.
    })">
        Select Media
    </button>
    
    Then include this component in your layout:
    @include('components.media-selector-modal', ['media' => Media::all()])
--}}

@push('scripts')
<script>
    /**
     * Global Media Selector Helper
     * Use anywhere in your site to open media selector
     * 
     * Usage:
     * openMediaSelector(function(selected) {
     *     console.log('User selected:', selected);
     *     // selected[0].url - Media URL
     *     // selected[0].id - Media ID
     *     // selected[0].thumb_url - Thumbnail
     * });
     */
    
    // Already defined in media-selector-modal.blade.php
    // This is just a reference file for documentation
</script>
@endpush
