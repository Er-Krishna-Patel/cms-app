<x-layouts.admin :title="'Dashboard'" :subtitle="'Welcome back, ' . auth()->user()->name">
    <!-- Stats Grid -->
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Posts</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $total_posts }}</p>
                </div>
                <span class="text-4xl text-blue-100">📝</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Published Posts</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $published_posts }}</p>
                </div>
                <span class="text-4xl text-green-100">✅</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Drafts</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $draft_posts }}</p>
                </div>
                <span class="text-4xl text-yellow-100">📋</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Categories</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $total_categories }}</p>
                </div>
                <span class="text-4xl text-purple-100">📂</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-pink-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Tags</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $total_tags }}</p>
                </div>
                <span class="text-4xl text-pink-100">🏷️</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Users</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $total_users }}</p>
                </div>
                <span class="text-4xl text-red-100">👤</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('admin.posts.create') }}" class="block bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow p-6 text-white hover:shadow-lg transition">
            <div class="text-3xl mb-2">✍️</div>
            <div class="font-semibold">Create New Post</div>
            <p class="text-sm text-blue-100 mt-1">Start writing</p>
        </a>

        <a href="{{ route('admin.posts.index') }}" class="block bg-gradient-to-br from-gray-600 to-gray-700 rounded-lg shadow p-6 text-white hover:shadow-lg transition">
            <div class="text-3xl mb-2">📚</div>
            <div class="font-semibold">View All Posts</div>
            <p class="text-sm text-gray-300 mt-1">{{ $total_posts }} posts</p>
        </a>

        <a href="{{ route('admin.categories.index') }}" class="block bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow p-6 text-white hover:shadow-lg transition">
            <div class="text-3xl mb-2">🗂️</div>
            <div class="font-semibold">Manage Categories</div>
            <p class="text-sm text-purple-100 mt-1">{{ $total_categories }} total</p>
        </a>

        <a href="{{ route('admin.tags.index') }}" class="block bg-gradient-to-br from-pink-500 to-pink-600 rounded-lg shadow p-6 text-white hover:shadow-lg transition">
            <div class="text-3xl mb-2">🏷️</div>
            <div class="font-semibold">Manage Tags</div>
            <p class="text-sm text-pink-100 mt-1">{{ $total_tags }} total</p>
        </a>
    </div>
</x-layouts.admin>
