<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Admin routes for CMS
Route::middleware(['auth','role:admin,editor'])->prefix('admin')->as('admin.')->group(function () {
    Route::resource('posts', AdminPostController::class);
    Route::post('/posts/auto-save', [AdminPostController::class, 'autoSave'])->name('posts.auto-save');
    Route::get('/posts/{post}/preview', [AdminPostController::class, 'preview'])->name('posts.preview');
    Route::resource('categories', CategoryController::class);
    Route::resource('tags', TagController::class);
    Route::resource('media', MediaController::class);
    Route::post('/media/bulk-delete', [MediaController::class, 'bulkDelete'])->name('media.bulk-delete');
    Route::post('/media/{media}/move', [MediaController::class, 'moveToFolder'])->name('media.move');
    Route::post('/media/{media}/copy', [MediaController::class, 'copy'])->name('media.copy');
    Route::get('/media/api', [MediaController::class, 'api'])->name('media.api');
    // CKEditor 4 image/file upload endpoint
    Route::post('/ckeditor/upload', [MediaController::class, 'ckeditorUpload'])->name('ckeditor.upload');
    // TinyMCE image upload endpoint
    Route::post('/tinymce/upload', [MediaController::class, 'tinymceUpload'])->name('tinymce.upload');
});

// Admin-only user management and settings
Route::middleware(['auth','role:admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');
    
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
});

// Public route to display published post by slug
Route::get('/blog/{slug}', function (string $slug) {
    $post = Post::where('slug', $slug)
        ->where('status', 'published')
        ->firstOrFail();
    return view('blog.show', compact('post'));
})->name('blog.show');
