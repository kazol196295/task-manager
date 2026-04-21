<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_tasks_index(): void
    {
        Task::factory()->count(3)->create();
        $response = $this->get(route('tasks.index'));
        $response->assertStatus(200)->assertViewIs('tasks.index');
    }

    public function test_can_create_task(): void
    {
        $response = $this->post(route('tasks.store'), [
            'title' => 'New Task',
            'status' => 'pending',
            'priority' => 'high',
        ]);
        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', ['title' => 'New Task']);
    }

    public function test_create_validates_required_fields(): void
    {
        $response = $this->post(route('tasks.store'), []);
        $response->assertSessionHasErrors(['title', 'status', 'priority']);
    }

    public function test_can_update_task(): void
    {
        $task = Task::factory()->create();
        $response = $this->put(route('tasks.update', $task), [
            'title' => 'Updated',
            'status' => 'in_progress',
            'priority' => 'low',
        ]);
        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', ['title' => 'Updated']);
    }

    public function test_can_delete_task(): void
    {
        $task = Task::factory()->create();
        $response = $this->delete(route('tasks.destroy', $task));
        $response->assertRedirect(route('tasks.index'));
        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    public function test_can_filter_by_status(): void
    {
        Task::factory()->create(['status' => 'pending', 'title' => 'Pending']);
        Task::factory()->create(['status' => 'completed', 'title' => 'Done']);
        $response = $this->get(route('tasks.index', ['status' => 'pending']));
        $response->assertSee('Pending')->assertDontSee('Done');
    }
}
