@extends('layouts.app')

@section('content')
    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="text-sm font-medium text-gray-500">Total</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-yellow-100 p-4">
            <div class="text-sm font-medium text-yellow-600">Pending</div>
            <div class="text-2xl font-bold text-yellow-700 mt-1">{{ $stats['pending'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-blue-100 p-4">
            <div class="text-sm font-medium text-blue-600">In Progress</div>
            <div class="text-2xl font-bold text-blue-700 mt-1">{{ $stats['in_progress'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-green-100 p-4">
            <div class="text-sm font-medium text-green-600">Completed</div>
            <div class="text-2xl font-bold text-green-700 mt-1">{{ $stats['completed'] }}</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-red-100 p-4">
            <div class="text-sm font-medium text-red-600">Overdue</div>
            <div class="text-2xl font-bold text-red-700 mt-1">{{ $stats['overdue'] }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
        <form method="GET" action="{{ route('tasks.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search tasks..."
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' }}>In
                        Progress</option>
                    <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed
                    </option>
                </select>
            </div>
            <div class="min-w-[150px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                <select name="priority"
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Priorities</option>
                    <option value="low" {{ ($filters['priority'] ?? '') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ ($filters['priority'] ?? '') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ ($filters['priority'] ?? '') === 'high' ? 'selected' : '' }}>High</option>
                </select>
            </div>
            <label class="flex items-center space-x-2 cursor-pointer pb-2">
                <input type="checkbox" name="overdue" value="1" {{ ($filters['overdue'] ?? '') ? 'checked' : '' }}
                    class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-500 focus:ring-red-500">
                <span class="text-sm font-medium text-red-600">Overdue Only</span>
            </label>
            <div class="flex gap-2">
                <button type="submit"
                    class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900">Filter</button>
                <a href="{{ route('tasks.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">Clear</a>
            </div>
        </form>
    </div>

    <!-- Task List -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        @if($tasks->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Task</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Due Date</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($tasks as $task)
                        <tr class="hover:bg-gray-50 transition {{ $task->is_overdue ? 'bg-red-50' : '' }}">
                            <td class="px-6 py-4">
                                <a href="{{ route('tasks.show', $task) }}"
                                    class="text-sm font-semibold text-gray-900 hover:text-indigo-600 {{ $task->status === 'completed' ? 'line-through text-gray-400' : '' }}">{{ $task->title }}</a>
                                @if($task->description)
                                    <p class="text-xs text-gray-500 mt-1">{{ Str::limit($task->description, 60) }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="POST" action="{{ route('tasks.update-status', $task) }}" class="inline-block">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="text-xs font-semibold rounded-full px-3 py-1 border-0 cursor-pointer focus:ring-2 focus:ring-indigo-500
                                                    {{ $task->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}">
                                        <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In
                                            Progress</option>
                                        <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed
                                        </option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs font-semibold rounded-full px-2.5 py-0.5
                                                {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $task->priority === 'medium' ? 'bg-orange-100 text-orange-800' : '' }}
                                                {{ $task->priority === 'low' ? 'bg-green-100 text-green-800' : '' }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm {{ $task->is_overdue ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                    {{ $task->due_date ? $task->due_date->format('M d, Y') : '—' }}
                                    @if($task->is_overdue) <span class="text-xs">(Overdue)</span> @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                <a href="{{ route('tasks.edit', $task) }}"
                                    class="text-gray-400 hover:text-yellow-600 transition">✏️</a>
                                <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                                    onsubmit="return confirm('Delete?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="bg-white px-6 py-4 border-t border-gray-100">
                {{ $tasks->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <h3 class="text-lg font-semibold text-gray-600 mb-2">No tasks found</h3>
                <a href="{{ route('tasks.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Create
                    New Task</a>
            </div>
        @endif
    </div>
@endsection
