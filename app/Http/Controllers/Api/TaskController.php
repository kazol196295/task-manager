<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['status', 'priority', 'search', 'overdue']);
        $tasks = Task::query()->filter($filters)->latest()->paginate($request->get('per_page', 15));
        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create($request->validated());
        return response()->json(['message' => 'Task created successfully.', 'task' => new TaskResource($task)], 201);
    }

    public function show(Task $task): TaskResource
    {
        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());
        return response()->json(['message' => 'Task updated.', 'task' => new TaskResource($task)]);
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();
        return response()->json(['message' => 'Task deleted successfully.']);
    }

    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        $request->validate(['status' => 'required|in:pending,in_progress,completed']);
        $task->update(['status' => $request->status]);
        return response()->json(['message' => 'Status updated.', 'task' => new TaskResource($task)]);
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'total' => Task::count(),
            'pending' => Task::pending()->count(),
            'in_progress' => Task::inProgress()->count(),
            'completed' => Task::completed()->count(),
            'overdue' => Task::overdue()->count(),
        ]);
    }
}
