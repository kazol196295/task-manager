<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_tasks_api(): void
    {
        Task::factory()->count(3)->create();
        $this->getJson('/api/tasks')->assertStatus(200)->assertJsonStructure(['data']);
    }

    public function test_can_create_task_api(): void
    {
        $this->postJson('/api/tasks', [
            'title' => 'API Task',
            'status' => 'pending',
            'priority' => 'medium',
        ])->assertStatus(201)->assertJsonPath('task.title', 'API Task');
    }

    public function test_api_create_validates(): void
    {
        $this->postJson('/api/tasks', [])->assertStatus(422)->assertJsonValidationErrors(['title', 'status', 'priority']);
    }

    public function test_can_update_task_api(): void
    {
        $task = Task::factory()->create();
        $this->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated',
            'status' => 'completed',
            'priority' => 'high',
        ])->assertStatus(200)->assertJsonPath('task.title', 'Updated');
    }

    public function test_can_delete_task_api(): void
    {
        $task = Task::factory()->create();
        $this->deleteJson("/api/tasks/{$task->id}")->assertStatus(200);
        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    public function test_can_get_stats_api(): void
    {
        Task::factory()->create(['status' => 'pending']);
        $this->getJson('/api/tasks/stats')->assertJsonPath('total', 1)->assertJsonPath('pending', 1);
    }
}
