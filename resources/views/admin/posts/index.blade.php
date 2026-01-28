<x-layouts.admin :title="'Posts'" :subtitle="'Manage all blog posts'">
    <div class="space-y-6">
        <div class="flex gap-4">
            <a href="{{ route('admin.posts.create') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">+ New Post</a>
            <div class="flex gap-2 ml-auto">
                <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">All</button>
                <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Published</button>
                <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Drafts</button>
            </div>
        </div>

        @if (session('status'))
            <div class="p-4 bg-green-100 text-green-800 rounded-lg">{{ session('status') }}</div>
        @endif

        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if ($posts->count())
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Title</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Author</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Category</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Published</th>
                            <th class="px-6 py-4 text-sm font-semibold text-gray-900">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($posts as $post)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $post->title }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $post->author->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ optional($post->category)->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $post->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($post->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ optional($post->published_at)->format('M d, Y') ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm space-x-3">
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="text-blue-600 hover:underline">Edit</a>
                                    <x-delete-form :action="route('admin.posts.destroy', $post)" :confirmMessage="'Delete this post?'" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t bg-gray-50">
                    {{ $posts->links() }}
                </div>
            @else
                <p class="px-6 py-8 text-center text-gray-600">No posts yet. <a href="{{ route('admin.posts.create') }}" class="text-blue-600 hover:underline">Create one</a></p>
            @endif
        </div>
    </div>
</x-layouts.admin>
