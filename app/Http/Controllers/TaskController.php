<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'priority', 'search', 'overdue']);
        $tasks = Task::query()->filter($filters)->latest()->paginate(10)->withQueryString();

        $stats = [
            'total' => Task::count(),
            'pending' => Task::pending()->count(),
            'in_progress' => Task::inProgress()->count(),
            'completed' => Task::completed()->count(),
            'overdue' => Task::overdue()->count(),
        ];

        return view('tasks.index', compact('tasks', 'filters', 'stats'));
    }

    public function create(): View { return view('tasks.create'); }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        Task::create($request->validated());
        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function show(Task $task): View { return view('tasks.show', compact('task')); }

    public function edit(Task $task): View { return view('tasks.edit', compact('task')); }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->validated());
        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        $request->validate(['status' => 'required|in:pending,in_progress,completed']);
        $task->update(['status' => $request->status]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Status updated.', 'task' => $task]);
        }
        return redirect()->route('tasks.index')->with('success', 'Task status updated.');
    }
}
