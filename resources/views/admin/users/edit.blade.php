<x-app-layout>
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex gap-2">
            <a href="{{ route('admin.posts.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">📄 Posts</a>
            <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">📁 Categories</a>
            <a href="{{ route('admin.tags.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">🏷️ Tags</a>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded font-semibold">👥 Users</a>
        </div>
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
    <h1 class="text-2xl font-bold mb-4">Edit User Role</h1>

    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PATCH')
        
        <div class="mb-4">
            <label class="block text-sm font-semibold mb-2">Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                class="w-full border rounded px-3 py-2 @error('name') border-red-500 @enderror">
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                class="w-full border rounded px-3 py-2 @error('email') border-red-500 @enderror">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold mb-2">Role</label>
            <select name="role" required class="w-full border rounded px-3 py-2 @error('role') border-red-500 @enderror">
                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="editor" {{ old('role', $user->role) === 'editor' ? 'selected' : '' }}>Editor</option>
                <option value="author" {{ old('role', $user->role) === 'author' ? 'selected' : '' }}>Author</option>
            </select>
            @error('role')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update User</button>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Cancel</a>
        </div>
    </form>
</div>
    </div>
</div>
</x-app-layout>
