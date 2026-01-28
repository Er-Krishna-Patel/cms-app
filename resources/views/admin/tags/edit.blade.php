<x-app-layout>
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-4">Edit Tag</h1>
            <form action="{{ route('admin.tags.update', $tag) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-semibold">Name</label>
                    <input type="text" name="name" value="{{ old('name', $tag->name) }}" class="w-full border p-2 rounded" required>
                    @error('name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                </div>
                <div class="flex gap-2">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded">Update</button>
                    <a href="{{ route('admin.tags.index') }}" class="px-4 py-2 bg-gray-200 rounded">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
</x-app-layout>
