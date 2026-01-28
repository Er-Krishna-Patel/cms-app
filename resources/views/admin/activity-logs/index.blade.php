<x-layouts.admin :title="'Activity Logs'" :subtitle="'Track all changes and user actions'">
    <div class="mb-6">
        <form method="GET" class="flex gap-2 flex-wrap">
            <select name="user" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">All Users</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>

            <select name="action" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">All Actions</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ $action }}</option>
                @endforeach
            </select>

            <select name="subject_type" class="px-4 py-2 border border-gray-300 rounded-lg">
                <option value="">All Types</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject }}" {{ request('subject_type') === $subject ? 'selected' : '' }}>{{ $subject }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Filter</button>
            <a href="{{ route('admin.activity-logs.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100">Reset</a>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($logs->count())
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">User</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Action</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Subject</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Description</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Date/Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $log->user?->name ?? 'System' }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium
                                    {{ $log->action === 'created' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $log->action === 'updated' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $log->action === 'deleted' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $log->action === 'published' ? 'bg-purple-100 text-purple-700' : '' }}
                                ">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $log->subject_type }} #{{ $log->subject_id }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $log->description }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $log->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-6 py-4 border-t">
                {{ $logs->links() }}
            </div>
        @else
            <div class="p-12 text-center text-gray-500">
                No activity logs found.
            </div>
        @endif
    </div>
</x-layouts.admin>
