<x-layouts.admin :title="'Settings'" :subtitle="'Configure your CMS'">
    <div class="max-w-3xl mx-auto">
        @if ($errors->any())
            <div class="alert alert-danger mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success mb-6">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.settings.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- General Settings -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">General Settings</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Site Title *</label>
                        <input type="text" name="site_title" value="{{ old('site_title', \App\Models\Setting::get('site_title', 'CustomCMS')) }}" required class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Site Tagline</label>
                        <input type="text" name="site_tagline" value="{{ old('site_tagline', \App\Models\Setting::get('site_tagline', '')) }}" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Site URL *</label>
                        <input type="url" name="site_url" value="{{ old('site_url', \App\Models\Setting::get('site_url', config('app.url'))) }}" required class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Admin Email *</label>
                        <input type="email" name="admin_email" value="{{ old('admin_email', \App\Models\Setting::get('admin_email', config('mail.from.address'))) }}" required class="form-input">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Timezone *</label>
                            <select name="timezone" required class="form-select">
                                @php
                                    $timezone = old('timezone', \App\Models\Setting::get('timezone', config('app.timezone')));
                                    foreach (timezone_identifiers_list() as $tz) {
                                        echo "<option value=\"$tz\" " . ($tz === $timezone ? 'selected' : '') . ">$tz</option>";
                                    }
                                @endphp
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Date Format *</label>
                            <input type="text" name="date_format" value="{{ old('date_format', \App\Models\Setting::get('date_format', 'Y-m-d')) }}" placeholder="e.g., Y-m-d" required class="form-input">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reading Settings -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Reading Settings</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Posts Per Page *</label>
                        <input type="number" name="posts_per_page" value="{{ old('posts_per_page', \App\Models\Setting::get('posts_per_page', 10)) }}" min="1" max="100" required class="form-input">
                    </div>
                </div>
            </div>

            <!-- Writing Settings -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Writing Settings</h3>
                </div>
                <div class="card-body">
                
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="posts_use_categories" value="1" {{ old('posts_use_categories', \App\Models\Setting::get('posts_use_categories')) ? 'checked' : '' }} class="rounded">
                        <span class="ml-2 text-sm text-gray-700">Use categories for posts</span>
                    </label>

                    <label class="flex items-center">
                        <input type="checkbox" name="require_post_category" value="1" {{ old('require_post_category', \App\Models\Setting::get('require_post_category')) ? 'checked' : '' }} class="rounded">
                        <span class="ml-2 text-sm text-gray-700">Require at least one category</span>
                    </label>

                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="enable_comments" value="1" {{ old('enable_comments', \App\Models\Setting::get('enable_comments')) ? 'checked' : '' }} class="form-checkbox">
                        <span class="ml-2 text-sm text-secondary-700">Enable comments on posts</span>
                    </label>

                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="moderate_comments" value="1" {{ old('moderate_comments', \App\Models\Setting::get('moderate_comments')) ? 'checked' : '' }} class="form-checkbox">
                        <span class="ml-2 text-sm text-secondary-700">Moderate comments before publishing</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Media Settings -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">📁 Media Settings</h3>
            </div>
            <div class="card-body space-y-6">
                
                <!-- Image Processing Options -->
                <div class="space-y-3">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Image Processing</h4>
                    
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="media_generate_thumbnails" value="1" {{ old('media_generate_thumbnails', \App\Models\Setting::get('media_generate_thumbnails', 'true')) === 'true' ? 'checked' : '' }} class="form-checkbox">
                        <span class="ml-2 text-sm text-gray-700">Generate thumbnails automatically</span>
                    </label>

                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="media_convert_to_webp" value="1" {{ old('media_convert_to_webp', \App\Models\Setting::get('media_convert_to_webp', 'true')) === 'true' ? 'checked' : '' }} class="form-checkbox">
                        <span class="ml-2 text-sm text-gray-700">Convert images to WebP format (recommended)</span>
                    </label>
                </div>

                <!-- Compression Quality -->
                <div class="form-group">
                    <label class="form-label">WebP Compression Quality (%)</label>
                    <input type="number" name="media_webp_quality" value="{{ old('media_webp_quality', \App\Models\Setting::get('media_webp_quality', '85')) }}" min="1" max="100" class="form-input">
                    <p class="text-xs text-gray-500 mt-1">1-100 (Recommended: 85 for optimal balance)</p>
                </div>

                <!-- Thumbnail Sizes -->
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Thumbnail Sizes (pixels)</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Thumb Size -->
                        <div class="space-y-2">
                            <label class="text-xs font-medium text-gray-600">Thumbnail (Grid View)</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="media_thumb_width" value="{{ old('media_thumb_width', \App\Models\Setting::get('media_thumb_width', '300')) }}" min="50" max="2000" placeholder="Width" class="form-input text-sm">
                                <input type="number" name="media_thumb_height" value="{{ old('media_thumb_height', \App\Models\Setting::get('media_thumb_height', '300')) }}" min="50" max="2000" placeholder="Height" class="form-input text-sm">
                            </div>
                        </div>

                        <!-- Medium Size -->
                        <div class="space-y-2">
                            <label class="text-xs font-medium text-gray-600">Medium (Preview)</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="media_medium_width" value="{{ old('media_medium_width', \App\Models\Setting::get('media_medium_width', '600')) }}" min="100" max="3000" placeholder="Width" class="form-input text-sm">
                                <input type="number" name="media_medium_height" value="{{ old('media_medium_height', \App\Models\Setting::get('media_medium_height', '600')) }}" min="100" max="3000" placeholder="Height" class="form-input text-sm">
                            </div>
                        </div>

                        <!-- Large Size -->
                        <div class="space-y-2">
                            <label class="text-xs font-medium text-gray-600">Large (Frontend)</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="media_large_width" value="{{ old('media_large_width', \App\Models\Setting::get('media_large_width', '1200')) }}" min="200" max="5000" placeholder="Width" class="form-input text-sm">
                                <input type="number" name="media_large_height" value="{{ old('media_large_height', \App\Models\Setting::get('media_large_height', '1200')) }}" min="200" max="5000" placeholder="Height" class="form-input text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Limits -->
                <div class="form-group">
                    <label class="form-label">Maximum Upload Size (KB)</label>
                    <input type="number" name="media_max_upload_size" value="{{ old('media_max_upload_size', \App\Models\Setting::get('media_max_upload_size', '10240')) }}" min="1024" max="102400" class="form-input">
                    <p class="text-xs text-gray-500 mt-1">10240 KB = 10 MB (Max: 100 MB)</p>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>
</div>
</x-layouts.admin>
