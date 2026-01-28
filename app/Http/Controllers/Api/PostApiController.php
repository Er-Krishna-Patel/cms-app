<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostApiController extends Controller
{
    public function index()
    {
        $posts = Post::with(['author','category','tags'])
            ->where('status', 'published')
            ->latest()
            ->paginate(10);
        return PostResource::collection($posts);
    }

    public function show(string $slug)
    {
        $post = Post::with(['author','category','tags'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();
        return new PostResource($post);
    }
}
