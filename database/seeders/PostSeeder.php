<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) return;

        $newsCategory = Category::firstOrCreate(
            ['name' => 'News'],
            ['slug' => 'news']
        );
        $laravelTag = Tag::firstOrCreate(
            ['name' => 'Laravel'],
            ['slug' => 'laravel']
        );
        $cmsTag = Tag::firstOrCreate(
            ['name' => 'CMS'],
            ['slug' => 'cms']
        );

        $post = Post::firstOrCreate(
            ['slug' => 'welcome-to-customcms'],
            [
                'title' => 'Welcome to CustomCMS',
                'content' => '<h2>Getting Started</h2>
<p>Welcome to <strong>CustomCMS</strong>, a powerful, modern content management system built on Laravel.</p>
<h3>Features</h3>
<ul>
  <li>Full-featured post management</li>
  <li>Rich text editing with CKEditor</li>
  <li>Media uploads and featured images</li>
  <li>SEO-friendly slugs and meta fields</li>
  <li>Role-based access control (admin, editor, author)</li>
  <li>Public blog with automatic slug-based routing</li>
  <li>RESTful JSON API for headless usage</li>
</ul>
<h3>Next Steps</h3>
<ol>
  <li>Log in to the admin panel at /admin/posts</li>
  <li>Create your first post</li>
  <li>Categorize and tag your content</li>
  <li>Publish and view on the public blog</li>
</ol>
<p>For detailed documentation, see README.md and TECHNICAL_SPEC.md in the project root.</p>',
                'status' => 'published',
                'user_id' => $admin->id,
                'category_id' => $newsCategory->id,
                'meta_title' => 'Welcome – CustomCMS',
                'meta_description' => 'Get started with CustomCMS, a modern Laravel-based content management system.',
                'published_at' => now(),
            ]
        );

        $post->tags()->sync([$laravelTag->id, $cmsTag->id]);
    }
}

