<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['author:id,name','category:id,name'])->select('id','title','slug','status','user_id','category_id','published_at','created_at')->latest()->paginate(15);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('admin.posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => ($request->input('status') === 'published' ? 'required' : 'nullable') . '|string',
            'excerpt' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'visibility' => 'required|in:public,private',
            'allow_comments' => 'nullable|boolean',
            'category_id' => 'nullable|exists:categories,id',
            'new_tags' => 'nullable|string',
            'featured_image' => 'nullable|image|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'published_at' => 'nullable|date',
        ]);

        $post = new Post();
        $post->fill($data);
        $post->content = Purifier::clean($data['content']);
        $post->user_id = Auth::id();
        if ($request->filled('published_at')) {
            $post->published_at = $request->date('published_at');
        } elseif ($data['status'] === 'published' && empty($post->published_at)) {
            $post->published_at = now();
        }

        if ($request->filled('category_id')) {
            $post->category_id = $request->input('category_id');
        }

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('uploads', 'public');
            $post->featured_image = $path;
        }

        $post->save();

        // Handle tags - both existing checkboxes and new comma-separated
        $tagIds = collect($request->input('tags', []));
        
        // Process new tags from comma-separated input
        if ($request->filled('new_tags')) {
            $newTagNames = collect(explode(',', $request->input('new_tags')))
                ->map(fn($t) => trim($t))
                ->filter()
                ->unique();
            
            foreach ($newTagNames as $name) {
                $tag = Tag::firstOrCreate(['name' => $name]);
                $tagIds->push($tag->id);
            }
        }
        
        $post->tags()->sync($tagIds->unique()->values());

        // If auto-save request, return JSON
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'post_id' => $post->id,
                'message' => 'Draft saved successfully'
            ]);
        }

        return redirect()->route('admin.posts.index')->with('status', 'Post created');
    }

    /**
     * Auto-save post draft via AJAX
     */
    public function autoSave(Request $request)
    {
        $data = $request->validate([
            'post_id' => 'nullable|exists:posts,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'excerpt' => 'nullable|string',
            'status' => 'in:draft,published',
            'visibility' => 'in:public,private',
            'allow_comments' => 'nullable|boolean',
            'category_id' => 'nullable|exists:categories,id',
            'new_tags' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
        ]);

        // Determine if this is a new or existing post
        $postId = $request->input('post_id');
        $post = $postId ? Post::findOrFail($postId) : new Post();

        // Only save title, excerpt, and tags on auto-save (avoid heavy operations)
        $post->title = $data['title'];
        $post->excerpt = $data['excerpt'] ?? null;
        $post->visibility = $data['visibility'] ?? 'public';
        $post->allow_comments = $request->boolean('allow_comments', true);
        $post->category_id = $data['category_id'] ?? null;
        $post->status = 'draft';
        
        if (!$post->exists) {
            $post->user_id = Auth::id();
            $post->status = 'draft';
            // Only sanitize if content is provided
            if (!empty($data['content'])) {
                $post->content = Purifier::clean($data['content']);
            }
        } else if (!empty($data['content'])) {
            // Update content if provided
            $post->content = Purifier::clean($data['content']);
        }

        $post->save();

        // Handle tags
        if ($request->filled('new_tags')) {
            $newTagNames = collect(explode(',', $request->input('new_tags')))
                ->map(fn($t) => trim($t))
                ->filter()
                ->unique();
            
            $tagIds = [];
            foreach ($newTagNames as $name) {
                $tag = Tag::firstOrCreate(['name' => $name]);
                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        }

        return response()->json([
            'success' => true,
            'post_id' => $post->id,
            'message' => 'Auto-saved successfully'
        ]);
    }

    public function edit(Post $post)
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'visibility' => 'required|in:public,private',
            'allow_comments' => 'nullable|boolean',
            'category_id' => 'nullable|exists:categories,id',
            'new_tags' => 'nullable|string',
            'featured_image' => 'nullable|image|max:2048',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'published_at' => 'nullable|date',
        ]);

        $post->fill($data);
        $post->content = Purifier::clean($data['content']);

        if ($request->filled('published_at')) {
            $post->published_at = $request->date('published_at');
        } elseif ($data['status'] === 'published' && empty($post->published_at)) {
            $post->published_at = now();
        } elseif ($data['status'] === 'draft') {
            $post->published_at = null;
        }

        if ($request->filled('category_id')) {
            $post->category_id = $request->input('category_id');
        } else {
            $post->category_id = null;
        }

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $path = $request->file('featured_image')->store('uploads', 'public');
            $post->featured_image = $path;
        }

        $post->allow_comments = $request->boolean('allow_comments', true);
        $post->allow_comments = $request->boolean('allow_comments', true);
        $post->save();

        // Handle tags from comma-separated input
        $tagIds = [];
        if ($request->filled('new_tags')) {
            $newTagNames = collect(explode(',', $request->input('new_tags')))
                ->map(fn($t) => trim($t))
                ->filter()
                ->unique();
            
            foreach ($newTagNames as $name) {
                $tag = Tag::firstOrCreate(['name' => $name]);
                $tagIds[] = $tag->id;
            }
        }
        
        $post->tags()->sync($tagIds);

        return redirect()->route('admin.posts.index')->with('status', 'Post updated');
    }

    public function preview(Post $post)
    {
        // Allow previewing any post (draft or published) by authenticated users
        return view('blog.show', compact('post'));
    }

    public function destroy(Post $post)
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        $post->tags()->detach();
        $post->delete();
        return redirect()->route('admin.posts.index')->with('status', 'Post deleted');
    }

    public function show(Post $post) {}
}
