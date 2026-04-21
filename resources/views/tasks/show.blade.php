@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6"><a href="{{ route('tasks.index') }}" class="text-sm text-gray-500 hover:text-indigo-600">← Back to
                Tasks</a></div>
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="p-8 {{ $task->is_overdue ? 'bg-red-50' : 'bg-gradient-to-r from-indigo-50 to-white' }}">
                <div class="flex justify-between items-start">
                    <div>
                        <h1
                            class="text-2xl font-bold text-gray-900 {{ $task->status === 'completed' ? 'line-through text-gray-400' : '' }}">
                            {{ $task->title }}</h1>
                        @if($task->is_overdue) <span
                            class="inline-flex mt-2 text-xs font-semibold text-red-700 bg-red-100 rounded-full px-3 py-1">⚠️
                        Overdue</span> @endif
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('tasks.edit', $task) }}"
                            class="px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100">Edit</a>
                        <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                            onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="p-8 space-y-6">
                <div class="flex gap-3">
                    <span
                        class="text-sm font-semibold rounded-full px-3 py-1 {{ $task->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}{{ $task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}{{ $task->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
                    <span
                        class="text-sm font-semibold rounded-full px-3 py-1 {{ $task->priority === 'high' ? 'bg-red-100 text-red-800' : '' }}{{ $task->priority === 'medium' ? 'bg-orange-100 text-orange-800' : '' }}{{ $task->priority === 'low' ? 'bg-green-100 text-green-800' : '' }}">{{ ucfirst($task->priority) }}
                        Priority</span>
                </div>
                @if($task->description)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-2">Description</h3>
                        <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ $task->description }}</p>
                    </div>
                @endif
                <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                    <div><span class="text-sm text-gray-500">Due Date</span>
                        <p class="text-sm font-semibold {{ $task->is_overdue ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $task->due_date ? $task->due_date->format('F d, Y') : 'No due date' }}</p>
                    </div>
                    <div><span class="text-sm text-gray-500">Created</span>
                        <p class="text-sm font-semibold text-gray-900">{{ $task->created_at->format('F d, Y H:i') }}</p>
                    </div>
                </div>
                <div class="pt-4 border-t">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Quick Status Update</h3>
                    <form method="POST" action="{{ route('tasks.update-status', $task) }}" class="flex gap-2">
                        @csrf @method('PATCH')
                        <button type="submit" name="status" value="pending"
                            class="px-4 py-2 text-sm font-medium rounded-lg transition {{ $task->status === 'pending' ? 'bg-yellow-200 text-yellow-900 ring-2 ring-yellow-400' : 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100' }}">Pending</button>
                        <button type="submit" name="status" value="in_progress"
                            class="px-4 py-2 text-sm font-medium rounded-lg transition {{ $task->status === 'in_progress' ? 'bg-blue-200 text-blue-900 ring-2 ring-blue-400' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">In
                            Progress</button>
                        <button type="submit" name="status" value="completed"
                            class="px-4 py-2 text-sm font-medium rounded-lg transition {{ $task->status === 'completed' ? 'bg-green-200 text-green-900 ring-2 ring-green-400' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">Completed</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
