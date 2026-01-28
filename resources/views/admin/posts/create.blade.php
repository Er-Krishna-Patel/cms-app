<x-layouts.admin :title="'Create Post'" :subtitle="'Add a new blog post'">
    <!-- Meta tag for auto-save URL -->
    <meta name="auto-save-url" content="{{ route('admin.posts.auto-save') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" class="h-full flex flex-col">
        @csrf
        <input type="hidden" name="status" id="form-status" value="draft">
        <input type="hidden" name="post_id" id="post-id" value="">

        <!-- Top Toolbar -->
        <div class="bg-white border-b border-secondary-200 px-6 py-3 flex items-center justify-between sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.posts.index') }}" class="text-secondary-600 hover:text-secondary-900" title="Back to Posts">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div class="h-6 w-px bg-secondary-300"></div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Draft</span>
                    <span class="text-sm text-secondary-500">Not published</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.posts.index') }}" class="text-sm text-secondary-600 hover:text-secondary-900">Cancel</a>
                <button type="button" id="preview-btn" class="text-sm text-primary-600 hover:text-primary-700">Preview</button>
                <button type="submit" class="btn btn-secondary btn-sm">Save as Draft</button>
                <button type="submit" class="btn btn-primary btn-sm">Publish Now</button>
            </div>
        </div>

        <div class="flex-1 flex overflow-hidden">
            <!-- Main Content Area -->
            <div class="flex-1 overflow-y-auto bg-secondary-50 p-6">
                <div class="max-w-4xl mx-auto space-y-4">
                    <!-- Title Block -->
                    <div class="bg-white rounded-lg shadow-sm border border-secondary-200 p-6">
                        <input 
                            type="text" 
                            name="title" 
                            value="{{ old('title') }}" 
                            placeholder="Add title" 
                            class="w-full text-3xl font-bold border-0 focus:ring-0 p-0 placeholder-secondary-300" 
                            required
                        >
                        @error('title') <p class="form-error mt-2">{{ $message }}</p> @enderror
                        
                        <!-- Permalink Preview -->
                        <div class="mt-3 flex items-center text-sm text-secondary-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            <span>Permalink: <span class="text-primary-600" id="permalink-preview">{{ url('/blog/') }}/</span></span>
                        </div>
                    </div>

                    <!-- Content Editor Block -->
                    <div class="bg-white rounded-lg shadow-sm border border-secondary-200 p-6">
                        <label class="block text-sm font-medium text-secondary-700 mb-3">Content</label>
                        <div class="flex justify-end mb-2">
                            <button type="button" id="media-insert-btn" class="text-sm text-primary-600 hover:text-primary-700">
                                + Insert Media
                            </button>
                        </div>
                        <x-editor name="content" :value="old('content')" :required="false" />
                        @error('content') <p class="form-error mt-2">{{ $message }}</p> @enderror
                    </div>

                    <!-- Excerpt Block -->
                    <div class="bg-white rounded-lg shadow-sm border border-secondary-200 p-6">
                        <label class="block text-sm font-medium text-secondary-700 mb-3">Excerpt (Optional)</label>
                        <textarea 
                            name="excerpt" 
                            rows="3" 
                            class="form-textarea" 
                            placeholder="Write a short excerpt..."><?php echo old('excerpt'); ?></textarea>
                        <p class="text-xs text-secondary-500 mt-1">Brief summary shown in post listings and search results.</p>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="w-80 bg-white border-l border-secondary-200 overflow-y-auto">
                <div class="p-4 space-y-4">
                    <!-- Status & Visibility -->
                    <details class="group" open>
                        <summary class="flex items-center justify-between cursor-pointer font-medium text-secondary-900 py-2 border-b border-secondary-200">
                            <span>Status & Visibility</span>
                            <svg class="w-4 h-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                        <div class="pt-4 space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-secondary-700 mb-1">Status</label>
                                <select name="status" class="form-select text-sm">
                                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-secondary-700 mb-1">Visibility</label>
                                <select name="visibility" class="form-select text-sm">
                                    <option value="public" {{ old('visibility') === 'public' ? 'selected' : '' }}>Public</option>
                                    <option value="private" {{ old('visibility') === 'private' ? 'selected' : '' }}>Private</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-secondary-700 mb-1">Publish Date</label>
                                <input type="datetime-local" name="published_at" value="{{ old('published_at') }}" class="form-input text-sm">
                                <p class="text-xs text-secondary-500 mt-1">Leave empty to publish immediately.</p>
                            </div>
                        </div>
                    </details>

                    <!-- Permalink -->
                    <details class="group" open>
                        <summary class="flex items-center justify-between cursor-pointer font-medium text-secondary-900 py-2 border-b border-secondary-200">
                            <span>Permalink</span>
                            <svg class="w-4 h-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                        <div class="pt-4">
                            <label class="block text-sm font-medium text-secondary-700 mb-1">URL Slug</label>
                            <input type="text" name="slug" id="slug-input" value="{{ old('slug') }}" class="form-input text-sm" placeholder="auto-generated">
                            <p class="text-xs text-secondary-500 mt-1">URL-friendly version of the title.</p>
                        </div>
                    </details>

                    <!-- Categories -->
                    <details class="group" open>
                        <summary class="flex items-center justify-between cursor-pointer font-medium text-secondary-900 py-2 border-b border-secondary-200">
                            <span>Categories</span>
                            <svg class="w-4 h-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                        <div class="pt-4">
                            <div class="mb-3">
                                <select name="category_id" class="form-select text-sm">
                                    <option value="">-- Select Primary Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="text-xs text-secondary-500">Primary category. <a href="{{ route('admin.categories.create') }}" class="text-primary-600 hover:underline" target="_blank">Add new</a></p>
                        </div>
                    </details>

                    <!-- Tags -->
                    <details class="group" open>
                        <summary class="flex items-center justify-between cursor-pointer font-medium text-secondary-900 py-2 border-b border-secondary-200">
                            <span>Tags</span>
                            <svg class="w-4 h-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                        <div class="pt-4">
                            <input 
                                type="text" 
                                name="new_tags" 
                                value="{{ old('new_tags') }}" 
                                class="form-input text-sm" 
                                placeholder="laravel, php, tutorial"
                            >
                            <p class="text-xs text-secondary-500 mt-1">Separate with commas to create or add tags.</p>
                        </div>
                    </details>

                    <!-- Featured Image -->
                    <details class="group" open>
                        <summary class="flex items-center justify-between cursor-pointer font-medium text-secondary-900 py-2 border-b border-secondary-200">
                            <span>Featured Image</span>
                            <svg class="w-4 h-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                        <div class="pt-4">
                            <div id="featured-preview" class="featured-image-preview mb-3 hidden">
                                <img id="featured-img" src="" alt="Featured image">
                            </div>
                            <button type="button" id="featured-select-btn" class="btn btn-secondary btn-sm mb-2">
                                Select from Library
                            </button>
                            <p class="text-xs text-secondary-500">Recommended: 1200×630px</p>
                        </div>
                    </details>

                    <!-- SEO -->
                    <details class="group">
                        <summary class="flex items-center justify-between cursor-pointer font-medium text-secondary-900 py-2 border-b border-secondary-200">
                            <span>SEO</span>
                            <svg class="w-4 h-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                        <div class="pt-4 space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-secondary-700 mb-1">Meta Title</label>
                                <input type="text" name="meta_title" value="{{ old('meta_title') }}" class="form-input text-sm" placeholder="Custom SEO title">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-secondary-700 mb-1">Meta Description</label>
                                <textarea name="meta_description" rows="3" class="form-textarea text-sm" placeholder="SEO description for search engines"><?php echo old('meta_description'); ?></textarea>
                            </div>
                        </div>
                    </details>

                    <!-- Discussion -->
                    <details class="group">
                        <summary class="flex items-center justify-between cursor-pointer font-medium text-secondary-900 py-2 border-b border-secondary-200">
                            <span>Discussion</span>
                            <svg class="w-4 h-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </summary>
                        <div class="pt-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="allow_comments" value="1" checked class="form-checkbox text-primary-600">
                                <span class="ml-2 text-sm text-secondary-700">Allow comments</span>
                            </label>
                        </div>
                    </details>
                </div>
            </div>
        </div>
    </form>

    <!-- Auto-generate slug and permalink preview -->
    <script>
        (function() {
            const title = document.querySelector('input[name="title"]');
            const slugInput = document.getElementById('slug-input');
            const permalinkPreview = document.getElementById('permalink-preview');
            const formStatus = document.getElementById('form-status');
            const form = document.querySelector('form');
            const previewBtn = document.getElementById('preview-btn');
            const featuredInput = document.getElementById('featured-input');
            const featuredPreview = document.getElementById('featured-preview');
            const featuredImg = document.getElementById('featured-img');
            let slugEdited = false;
            
            if (!title || !slugInput) return;
            
            const toSlug = s => s.toLowerCase().trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            
            slugInput.addEventListener('input', () => { 
                slugEdited = true;
                if (permalinkPreview) {
                    permalinkPreview.textContent = '{{ url('/blog/') }}/' + (slugInput.value || 'post-slug');
                }
            });
            
            title.addEventListener('input', () => {
                if (!slugEdited) {
                    const newSlug = toSlug(title.value || '');
                    slugInput.value = newSlug;
                    if (permalinkPreview) {
                        permalinkPreview.textContent = '{{ url('/blog/') }}/' + (newSlug || 'post-slug');
                    }
                }
            });

            // Handle button clicks to set status
            document.querySelectorAll('button[type="submit"]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    if (btn.textContent.includes('Draft')) {
                        formStatus.value = 'draft';
                    } else if (btn.textContent.includes('Publish')) {
                        formStatus.value = 'published';
                    }
                });
            });

            // Preview button - save draft first, then open preview in new tab
            if (previewBtn) {
                previewBtn.addEventListener('click', async (e) => {
                    e.preventDefault();
                    
                    // Check required fields
                    if (!title.value || !document.querySelector('textarea[name="content"]').value) {
                        alert('Please fill in title and content before previewing.');
                        return;
                    }
                    
                    // Submit form as draft to save first
                    formStatus.value = 'draft';
                    form.submit();
                    
                    // Note: Preview will happen after redirect
                });
            }

            // Featured image preview
            if (featuredInput) {
                featuredInput.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (event) => {
                            featuredImg.src = event.target.result;
                            featuredPreview.classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
        </div>
    </form>

    <!-- Media Modals -->
    <x-media-modal :modalId="'media-insert-modal'" :purpose="'insert'" :title="'Insert Media into Content'" />
    <x-media-modal :modalId="'featured-image-modal'" :purpose="'featured'" :title="'Select Featured Image'" />

    <!-- Load external scripts -->
    <script src="{{ asset('js/post-editor.js') }}"></script>
    <script>
        // Initialize featured image modal button
        document.addEventListener('DOMContentLoaded', function() {
            const featuredSelectBtn = document.getElementById('featured-select-btn');
            const mediaInsertBtn = document.getElementById('media-insert-btn');

            if (featuredSelectBtn && window.mediaModals) {
                featuredSelectBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    window.mediaModals['featured-image-modal']?.open();
                });
            }

            if (mediaInsertBtn && window.mediaModals) {
                mediaInsertBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    window.mediaModals['media-insert-modal']?.open();
                });
            }
        });
    </script>
</x-layouts.admin>
