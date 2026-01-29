<?php

namespace App\Http\Controllers\Admin;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ActivityLog;
use App\Services\MediaService;
use Illuminate\Routing\Controllers\Middleware;

class MediaController extends \App\Http\Controllers\Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
            new Middleware('role:admin'),
        ];
    }

    /**
     * Display media library
     */
    public function index(Request $request)
    {
        $query = Media::forUser(auth()->id())->recent();

        
        if ($request->filled('folder')) {
            $query->byFolder($request->query('folder'));
        }

        if ($request->filled('type')) {
            $type = $request->query('type');
            $query = match($type) {
                'images' => $query->images(),
                'videos' => $query->videos(),
                'documents' => $query->documents(),
                default => $query,
            };
        }

        if ($request->filled('search')) {
            $query->search($request->query('search'));
        }

        $media = $query->paginate(20);
        $folders = Media::getAvailableFolders();
        $currentFolder = $request->query('folder');

        return view('admin.media.index', compact('media', 'folders', 'currentFolder'));
    }

    /**
     * Show media edit form
     */
    public function edit(Media $media)
    {
        $this->authorizeMedia($media);
        return view('admin.media.edit', compact('media'));
    }

    /**
     * Update media metadata
     */
    public function update(Request $request, Media $media)
    {
        $this->authorizeMedia($media);

        $validated = $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:500',
            'description' => 'nullable|string',
        ]);

        $media->updateMetadata($validated);
        ActivityLog::log('updated', 'Media', $media->id, 'Updated media metadata');

        return redirect()
            ->route('admin.media.index')
            ->with('success', 'Media updated successfully');
    }

    /**
     * Upload media file(s)
     */
    public function store(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|max:10240',
            'folder' => 'nullable|string|max:50',
        ]);

        $folder = $request->input('folder');
        $uploaded = [];
        $errors = [];

        foreach ($request->file('files') as $file) {
            try {
                $media = MediaService::uploadFile($file, $folder);

                if ($media) {
                    ActivityLog::log('created', 'Media', $media->id, "Uploaded file: {$media->original_name}");
                    $uploaded[] = [
                        'id' => $media->id,
                        'name' => $media->original_name,
                        'url' => $media->url,
                        'thumb_url' => $media->thumb_url, // 300×300 thumbnail
                        'medium_url' => $media->medium_url, // 600×600
                        'large_url' => $media->large_url, // 1200×1200
                        'preview_url' => $media->preview_url,
                        'size' => $media->human_readable_size,
                        'type' => $media->file_type,
                    ];
                } else {
                    $errors[] = "Failed to upload {$file->getClientOriginalName()}";
                }
            } catch (\Exception $e) {
                $errors[] = "Error uploading {$file->getClientOriginalName()}";
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => count($errors) === 0,
                'uploaded' => $uploaded,
                'errors' => $errors,
            ], count($errors) > 0 ? 422 : 200);
        }

        $message = count($uploaded) . ' file(s) uploaded';
        return back()->with('success', $message)->with('errors', $errors);
    }

    /**
     * Move media to folder
     */
    public function moveToFolder(Request $request, Media $media)
    {
        $this->authorizeMedia($media);

        $validated = $request->validate([
            'folder' => 'required|string|max:50',
        ]);

        if ($media->moveToFolder($validated['folder'])) {
            ActivityLog::log('updated', 'Media', $media->id, "Moved to folder: {$validated['folder']}");
            return response()->json(['success' => true, 'message' => 'Media moved successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Failed to move media'], 422);
    }

    /**
     * Copy media
     */
    public function copy(Media $media)
    {
        $this->authorizeMedia($media);

        $copy = $media->copy();

        if ($copy) {
            ActivityLog::log('created', 'Media', $copy->id, "Copied from media ID: {$media->id}");
            return response()->json([
                'success' => true,
                'media' => [
                    'id' => $copy->id,
                    'name' => $copy->original_name,
                    'url' => $copy->url,
                ],
                'message' => 'Media copied successfully',
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Failed to copy media'], 422);
    }

    /**
     * Delete media
     */
    public function destroy(Media $media)
    {
        $this->authorizeMedia($media);

        $filename = $media->original_name;
        if ($media->deleteMedia()) {
            ActivityLog::log('deleted', 'Media', $media->id, "Deleted file: $filename");
            return redirect()
                ->route('admin.media.index')
                ->with('success', 'Media deleted successfully');
        }

        return back()->withErrors(['error' => 'Failed to delete media']);
    }

    /**
     * Bulk delete media
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        $deleted = 0;
        $failed = 0;

        foreach ($ids as $id) {
            try {
                $media = Media::findOrFail($id);
                $this->authorizeMedia($media);

                if ($media->deleteMedia()) {
                    ActivityLog::log('deleted', 'Media', $media->id, "Bulk deleted: {$media->original_name}");
                    $deleted++;
                } else {
                    $failed++;
                }
            } catch (\Exception $e) {
                $failed++;
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => $failed === 0,
                'deleted' => $deleted,
                'failed' => $failed,
            ]);
        }

        return back()->with('success', "$deleted file(s) deleted");
    }

    /**
     * API endpoint: Get all user's media (for modal selection)
     */
    public function api(Request $request)
    {
        $query = Media::forUser(auth()->id())->recent();

        if ($request->filled('folder')) {
            $query->byFolder($request->query('folder'));
        }

        if ($request->filled('type')) {
            $type = $request->query('type');
            $query = match($type) {
                'images' => $query->images(),
                'videos' => $query->videos(),
                'documents' => $query->documents(),
                default => $query,
            };
        }

        if ($request->filled('search')) {
            $query->search($request->query('search'));
        }

        $media = $query->paginate(50);

        return response()->json([
            'success' => true,
            'media' => $media->map(fn($m) => [
                'id' => $m->id,
                'filename' => $m->original_name,
                'url' => $m->url,
                'preview_url' => $m->preview_url,
                'type' => $m->file_type,
                'is_image' => $m->is_image,
                'size' => $m->human_readable_size,
                'dimensions' => $m->width && $m->height ? "{$m->width}x{$m->height}" : null,
                'uploaded' => $m->created_at->format('M d, Y'),
            ]),
            'folders' => Media::getAvailableFolders(),
        ]);
    }

    /**
     * Handle TinyMCE image uploads
     */
    public function tinymceUpload(Request $request)
    {
        if (!$request->hasFile('file') && !$request->hasFile('upload')) {
            return response()->json(['error' => 'No file uploaded.'], 422);
        }

        $file = $request->file('file') ?? $request->file('upload');

        $request->validate([
            'file' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp',
            'upload' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp',
        ]);

        $media = MediaService::uploadFile($file, 'tinymce');

        if ($media) {
            ActivityLog::log('created', 'Media', $media->id, "TinyMCE upload: {$media->original_name}");
            return response()->json(['location' => $media->url], 201);
        }

        return response()->json(['error' => 'Upload failed'], 422);
    }

    /**
     * Authorize media ownership
     */
    private function authorizeMedia(Media $media)
    {
        if ($media->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
    }
}

