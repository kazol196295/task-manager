<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    // ── View / Index Tests ──────────────────────────────

    public function test_can_view_tasks_index_page(): void
    {
        Task::factory()->count(3)->create();

        $response = $this->get(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertViewIs('tasks.index');
        $response->assertViewHas('tasks');
    }

    public function test_index_displays_stats_correctly(): void
    {
        Task::factory()->create(['status' => 'pending']);
        Task::factory()->create(['status' => 'in_progress']);
        Task::factory()->create(['status' => 'completed']);

        $response = $this->get(route('tasks.index'));

        $response->assertViewHas('stats', function ($stats) {
            return $stats['total'] === 3 && $stats['pending'] === 1 && $stats['completed'] === 1;
        });
    }

    public function test_index_can_filter_by_status(): void
    {
        Task::factory()->create(['status' => 'pending', 'title' => 'Pending Task']);
        Task::factory()->create(['status' => 'completed', 'title' => 'Completed Task']);

        $response = $this->get(route('tasks.index', ['status' => 'pending']));

        $response->assertSee('Pending Task');
        $response->assertDontSee('Completed Task');
    }

    public function test_index_can_search_tasks(): void
    {
        Task::factory()->create(['title' => 'Laravel Bug Fix']);
        Task::factory()->create(['title' => 'React UI Update']);

        $response = $this->get(route('tasks.index', ['search' => 'Laravel']));

        $response->assertSee('Laravel Bug Fix');
        $response->assertDontSee('React UI Update');
    }

    public function test_index_shows_empty_state_when_no_tasks(): void
    {
        $response = $this->get(route('tasks.index'));
        $response->assertSee('No tasks found');
    }

    // ── Create Tests ────────────────────────────────────

    public function test_can_view_create_task_page(): void
    {
        $response = $this->get(route('tasks.create'));
        $response->assertStatus(200);
        $response->assertViewIs('tasks.create');
    }

    public function test_can_create_task_with_minimum_data(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title' => 'Minimal Task',
            'status' => 'pending',
            'priority' => 'low',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('tasks', ['title' => 'Minimal Task']);
    }

    public function test_can_create_task_with_all_data(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title' => 'Full Task',
            'description' => 'A detailed description',
            'status' => 'in_progress',
            'priority' => 'high',
            'due_date' => now()->addWeek()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', ['title' => 'Full Task', 'priority' => 'high']);
    }

    // ── Validation Tests ────────────────────────────────

    public function test_create_validates_required_fields(): void
    {
        $response = $this->post(route('tasks.store'), []);
        $response->assertSessionHasErrors(['title', 'status', 'priority']);
    }

    public function test_create_validates_title_max_length(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title' => str_repeat('a', 256), // 255 is max
            'status' => 'pending',
            'priority' => 'medium',
        ]);
        $response->assertSessionHasErrors(['title']);
    }

    public function test_create_validates_invalid_status(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title' => 'Test',
            'status' => 'archived', // Invalid
            'priority' => 'medium',
        ]);
        $response->assertSessionHasErrors(['status']);
    }

    public function test_create_validates_invalid_priority(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title' => 'Test',
            'status' => 'pending',
            'priority' => 'urgent', // Invalid
        ]);
        $response->assertSessionHasErrors(['priority']);
    }

    public function test_create_validates_past_due_date(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title' => 'Test',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => now()->subDay()->format('Y-m-d'),
        ]);
        $response->assertSessionHasErrors(['due_date']);
    }

    // ── Show Tests ──────────────────────────────────────

    public function test_can_view_task_detail(): void
    {
        $task = Task::factory()->create(['title' => 'Specific Task']);

        $response = $this->get(route('tasks.show', $task));

        $response->assertStatus(200);
        $response->assertSee('Specific Task');
    }

    // ── Update Tests ────────────────────────────────────

    public function test_can_view_edit_task_page(): void
    {
        $task = Task::factory()->create();

        $response = $this->get(route('tasks.edit', $task));

        $response->assertStatus(200);
        $response->assertViewIs('tasks.edit');
        $response->assertSee($task->title);
    }

    public function test_can_update_task(): void
    {
        $task = Task::factory()->create(['title' => 'Old Title']);

        $response = $this->put(route('tasks.update', $task), [
            'title' => 'New Title',
            'status' => 'in_progress',
            'priority' => 'high',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'New Title']);
    }

    public function test_update_validates_invalid_data(): void
    {
        $task = Task::factory()->create();

        $response = $this->put(route('tasks.update', $task), [
            'title' => '', // Required
            'status' => 'invalid',
            'priority' => 'invalid',
        ]);

        $response->assertSessionHasErrors(['title', 'status', 'priority']);
    }

    // ── Delete Tests ────────────────────────────────────

    public function test_can_delete_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('success');
        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    // ── Status Update Tests ─────────────────────────────

    public function test_can_update_task_status_via_web(): void
    {
        $task = Task::factory()->create(['status' => 'pending']);

        $response = $this->patch(route('tasks.update-status', $task), [
            'status' => 'completed',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'completed']);
    }

    public function test_update_status_validates_invalid_status(): void
    {
        $task = Task::factory()->create(['status' => 'pending']);

        $response = $this->patch(route('tasks.update-status', $task), [
            'status' => 'archived',
        ]);

        $response->assertSessionHasErrors(['status']);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'pending']); // Ensures it didn't change
    }
}
