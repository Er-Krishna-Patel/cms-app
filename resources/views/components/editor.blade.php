@props([
    'name' => 'content',
    'value' => '',
    'height' => 450,
    'required' => false,
])

@php
    $editor = config('editor.primary', 'tinymce');
    $editorConfig = config("editor.{$editor}", []);
    $uploadRoute = config("editor.upload_routes.{$editor}");
@endphp

<textarea 
    name="{{ $name }}" 
    id="editor-{{ $name }}" 
    rows="16" 
    class="form-textarea editor-{{ $editor }}"
    @if($required) required @endif
>{{ old($name, $value) }}</textarea>

@if($editor === 'ckeditor')
    {{-- CKEditor 4 Self-Hosted --}}
    @foreach(config('editor.ckeditor.js', []) as $script)
        <script src="{{ asset($script) }}"></script>
    @endforeach
    
    <script>
        (function() {
            if (typeof CKEDITOR === 'undefined') {
                console.error('CKEditor library not loaded');
                return;
            }

            const editorId = 'editor-{{ $name }}';
            const config = {!! json_encode(config('editor.ckeditor.config', [])) !!};
            
            @if($uploadRoute && \Illuminate\Support\Facades\Route::has($uploadRoute))
            config.filebrowserUploadUrl = '{{ route($uploadRoute) }}?_token={{ csrf_token() }}';
            config.filebrowserImageUploadUrl = '{{ route($uploadRoute) }}?_token={{ csrf_token() }}';
            @endif

            CKEDITOR.replace(editorId, config);
        })();
    </script>

@elseif($editor === 'tinymce')
    {{-- TinyMCE Self-Hosted --}}
    @foreach(config('editor.tinymce.js', []) as $script)
        <script src="{{ asset($script) }}" referrerpolicy="origin"></script>
    @endforeach
    
    <script>
        (function() {
            if (typeof tinymce === 'undefined') {
                console.error('TinyMCE library not loaded');
                return;
            }

            window.addEventListener('load', function() {
                const editorId = 'editor-{{ $name }}';
                const config = {!! json_encode(config('editor.tinymce.config', [])) !!};
                
                config.selector = `textarea#${editorId}`;
                
                @if($uploadRoute && \Illuminate\Support\Facades\Route::has($uploadRoute))
                config.images_upload_handler = async function(blobInfo, progress) {
                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    formData.append('_token', '{{ csrf_token() }}');
                    
                    try {
                        const response = await fetch('{{ route($uploadRoute) }}', {
                            method: 'POST',
                            body: formData
                        });
                        const data = await response.json();
                        if (data.location) return data.location;
                        throw new Error('Upload failed');
                    } catch (e) {
                        console.error(e);
                        throw e;
                    }
                };
                @endif

                tinymce.init(config);
            });
        })();
    </script>
@endif
