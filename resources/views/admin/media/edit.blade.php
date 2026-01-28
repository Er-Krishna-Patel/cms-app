@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('admin.media.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Library
            </a>
            <h1 class="text-4xl font-bold text-gray-900 mt-6">Edit Media</h1>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-8">
                <!-- Media Preview -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Preview</h2>
                    <div class="border border-gray-200 rounded-lg p-6 bg-gray-50">
                        @if($media->is_image)
                            <img src="{{ $media->url }}" alt="{{ $media->alt_text ?: $media->original_name }}" class="max-w-full h-auto mx-auto rounded-lg">
                        @else
                            <div class="flex items-center justify-center">
                                <div class="text-6xl text-gray-400 mr-4">{{ $media->getFileTypeIcon() }}</div>
                                <div>
                                    <p class="text-lg font-medium text-gray-800">{{ $media->original_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $media->human_readable_size }} • {{ $media->mime_type }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- File Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 pb-8 border-b border-gray-200">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filename</label>
                        <p class="text-gray-900 break-all">{{ $media->filename }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Original Name</label>
                        <p class="text-gray-900">{{ $media->original_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">File Type</label>
                        <p class="text-gray-900">{{ $media->mime_type }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">File Size</label>
                        <p class="text-gray-900">{{ $media->human_readable_size }}</p>
                    </div>
                    @if($media->width && $media->height)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dimensions</label>
                            <p class="text-gray-900">{{ $media->width }} × {{ $media->height }} px</p>
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Uploaded</label>
                        <p class="text-gray-900">{{ $media->created_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                <!-- Edit Form -->
                <form action="{{ route('admin.media.update', $media) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Alt Text -->
                    <x-ui.input 
                        name="alt_text"
                        label="Alt Text"
                        placeholder="Describe the image for accessibility"
                        :value="$media->alt_text"
                        class="mb-6"
                    />

                    <!-- Caption -->
                    <x-ui.textarea
                        name="caption"
                        label="Caption"
                        placeholder="Add a caption for this media"
                        :value="$media->caption"
                        rows="3"
                        class="mb-6"
                    />

                    <!-- Description -->
                    <x-ui.textarea
                        name="description"
                        label="Description"
                        placeholder="Longer description or notes about this media"
                        :value="$media->description"
                        rows="5"
                        class="mb-8"
                    />

                    <!-- Submit Button -->
                    <div class="flex gap-3">
                        <x-ui.button type="submit" variant="primary">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Save Changes
                        </x-ui.button>
                        <x-ui.button 
                            type="button" 
                            variant="secondary" 
                            onclick="window.history.back()"
                        >
                            Cancel
                        </x-ui.button>
                    </div>
                </form>

                <!-- Danger Zone -->
                <div class="mt-12 pt-8 border-t border-red-200 bg-red-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-red-900 mb-4">Delete Media</h3>
                    <p class="text-red-700 mb-4">This action cannot be undone. The file will be permanently deleted.</p>
                    
                    <x-delete-form 
                        action="{{ route('admin.media.destroy', $media) }}"
                        buttonText="Delete Media"
                        method="DELETE"
                    />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
