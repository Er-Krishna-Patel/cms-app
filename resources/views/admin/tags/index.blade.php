<x-layouts.admin :title="'Tags'" :subtitle="'Manage post tags'">
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.tags.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">+ New Tag</a>
    </div>

    @if (session('status'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">{{ session('status') }}</div>
    @endif

    <div class="bg-white overflow-hidden shadow-sm rounded-lg">
        @if ($tags->count())
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-100 text-left border-b">
                        <th class="px-6 py-3 font-semibold">Name</th>
                        <th class="px-6 py-3 font-semibold">Slug</th>
                        <th class="px-6 py-3 font-semibold">Posts</th>
                        <th class="px-6 py-3 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tags as $tag)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-3">{{ $tag->name }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $tag->slug }}</td>
                        <td class="px-6 py-3">{{ $tag->posts_count ?? 0 }}</td>
                        <td class="px-6 py-3 space-x-3">
                            <a href="{{ route('admin.tags.edit', $tag) }}" class="text-blue-600 hover:underline">Edit</a>
                            <x-delete-form :action="route('admin.tags.destroy', $tag)" :confirmMessage="'Delete this tag?'" />
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4">{{ $tags->links() }}</div>
        @else
            <p class="p-6 text-gray-600">No tags yet.</p>
        @endif
    </div>
</x-layouts.admin>
