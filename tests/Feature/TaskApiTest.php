<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    private string $api = '/api';

    // ── List Tests ──────────────────────────────────────

    public function test_can_list_tasks_via_api(): void
    {
        Task::factory()->count(5)->create();

        $response = $this->getJson("{$this->api}/tasks");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'status', 'priority', 'due_date', 'created_at']
            ],
            'links',
            'meta',
        ]);
    }

    public function test_list_returns_paginated_data(): void
    {
        Task::factory()->count(20)->create();

        $response = $this->getJson("{$this->api}/tasks");

        // Default pagination is 15, so we should have 15 items in data
        $response->assertJsonCount(15, 'data');
    }

    public function test_can_filter_tasks_by_status_via_api(): void
    {
        Task::factory()->create(['status' => 'pending', 'title' => 'Pending']);
        Task::factory()->create(['status' => 'completed', 'title' => 'Done']);

        $response = $this->getJson("{$this->api}/tasks?status=pending");

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.title', 'Pending');
    }

    public function test_can_search_tasks_via_api(): void
    {
        Task::factory()->create(['title' => 'Laravel API']);
        Task::factory()->create(['title' => 'React Frontend']);

        $response = $this->getJson("{$this->api}/tasks?search=Laravel");

        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.title', 'Laravel API');
    }

    // ── Create Tests ────────────────────────────────────

    public function test_can_create_task_via_api(): void
    {
        $response = $this->postJson("{$this->api}/tasks", [
            'title' => 'API Task',
            'description' => 'Created via API',
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('task.title', 'API Task');
        $response->assertJsonPath('message', 'Task created successfully.');

        $this->assertDatabaseHas('tasks', ['title' => 'API Task']);
    }

    public function test_api_create_validates_required_fields(): void
    {
        $response = $this->postJson("{$this->api}/tasks", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'status', 'priority']);
    }

    public function test_api_create_validates_invalid_status(): void
    {
        $response = $this->postJson("{$this->api}/tasks", [
            'title' => 'Test',
            'status' => 'unknown',
            'priority' => 'low',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['status']);
    }

    // ── Show Tests ──────────────────────────────────────

    public function test_can_show_task_via_api(): void
    {
        $task = Task::factory()->create();

        $response = $this->getJson("{$this->api}/tasks/{$task->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.id', $task->id);
        $response->assertJsonPath('data.title', $task->title);
    }

    public function test_show_returns_404_for_nonexistent_task(): void
    {
        $response = $this->getJson("{$this->api}/tasks/9999");

        $response->assertStatus(404);
    }

    // ── Update Tests ────────────────────────────────────

    public function test_can_update_task_via_api(): void
    {
        $task = Task::factory()->create(['title' => 'Old Title']);

        $response = $this->putJson("{$this->api}/tasks/{$task->id}", [
            'title' => 'New Title',
            'status' => 'in_progress',
            'priority' => 'high',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('task.title', 'New Title');

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'New Title']);
    }

    public function test_update_validates_invalid_data_via_api(): void
    {
        $task = Task::factory()->create();

        $response = $this->putJson("{$this->api}/tasks/{$task->id}", [
            'title' => '', // required
            'status' => 'invalid',
            'priority' => 'invalid',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'status', 'priority']);
    }

    public function test_update_returns_404_for_nonexistent_task(): void
    {
        $response = $this->putJson("{$this->api}/tasks/9999", [
            'title' => 'Test',
            'status' => 'pending',
            'priority' => 'low',
        ]);

        $response->assertStatus(404);
    }

    // ── Delete Tests ────────────────────────────────────

    public function test_can_delete_task_via_api(): void
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("{$this->api}/tasks/{$task->id}");

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Task deleted successfully.');

        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    public function test_delete_returns_404_for_nonexistent_task(): void
    {
        $response = $this->deleteJson("{$this->api}/tasks/9999");

        $response->assertStatus(404);
    }

    // ── Status Update Tests ─────────────────────────────

    public function test_can_update_status_via_api(): void
    {
        $task = Task::factory()->create(['status' => 'pending']);

        $response = $this->patchJson("{$this->api}/tasks/{$task->id}/status", [
            'status' => 'completed',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('task.status', 'completed');
    }

    public function test_update_status_validates_invalid_status_via_api(): void
    {
        $task = Task::factory()->create(['status' => 'pending']);

        $response = $this->patchJson("{$this->api}/tasks/{$task->id}/status", [
            'status' => 'archived',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['status']);
    }

    // ── Stats Tests ─────────────────────────────────────

    public function test_can_get_stats_via_api(): void
    {
        Task::factory()->create(['status' => 'pending']);
        Task::factory()->create(['status' => 'in_progress']);
        Task::factory()->create(['status' => 'completed']);

        $response = $this->getJson("{$this->api}/tasks/stats");

        $response->assertStatus(200);
        $response->assertJsonPath('total', 3);
        $response->assertJsonPath('pending', 1);
        $response->assertJsonPath('in_progress', 1);
        $response->assertJsonPath('completed', 1);
    }

    public function test_stats_returns_zero_when_no_tasks(): void
    {
        $response = $this->getJson("{$this->api}/tasks/stats");

        $response->assertStatus(200);
        $response->assertJsonPath('total', 0);
        $response->assertJsonPath('overdue', 0);
    }
}
