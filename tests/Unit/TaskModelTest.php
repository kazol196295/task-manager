<?php

namespace Tests\Unit;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskModelTest extends TestCase
{
    use RefreshDatabase;

    // ── Scope Tests ─────────────────────────────────────

    public function test_pending_scope_returns_only_pending_tasks(): void
    {
        Task::factory()->create(['status' => 'pending']);
        Task::factory()->create(['status' => 'completed']);

        $this->assertEquals(1, Task::pending()->count());
    }

    public function test_in_progress_scope_returns_only_in_progress_tasks(): void
    {
        Task::factory()->create(['status' => 'in_progress']);
        Task::factory()->create(['status' => 'pending']);

        $this->assertEquals(1, Task::inProgress()->count());
    }

    public function test_completed_scope_returns_only_completed_tasks(): void
    {
        Task::factory()->count(2)->create(['status' => 'completed']);
        Task::factory()->create(['status' => 'pending']);

        $this->assertEquals(2, Task::completed()->count());
    }

    public function test_high_priority_scope(): void
    {
        Task::factory()->create(['priority' => 'high']);
        Task::factory()->create(['priority' => 'low']);

        $this->assertEquals(1, Task::highPriority()->count());
    }

    public function test_overdue_scope_excludes_completed_tasks(): void
    {
        Task::factory()->create(['status' => 'completed', 'due_date' => now()->subDays(5)]);
        Task::factory()->create(['status' => 'pending', 'due_date' => now()->subDays(5)]);

        $this->assertEquals(1, Task::overdue()->count());
    }

    public function test_overdue_scope_excludes_future_tasks(): void
    {
        Task::factory()->create(['status' => 'pending', 'due_date' => now()->addDays(5)]);
        Task::factory()->create(['status' => 'pending', 'due_date' => now()->subDays(5)]);

        $this->assertEquals(1, Task::overdue()->count());
    }

    // ── Filter Scope Tests ──────────────────────────────

    public function test_filter_scope_with_status(): void
    {
        Task::factory()->create(['status' => 'pending', 'title' => 'A']);
        Task::factory()->create(['status' => 'completed', 'title' => 'B']);

        $results = Task::filter(['status' => 'pending'])->get();

        $this->assertCount(1, $results);
        $this->assertEquals('A', $results->first()->title);
    }

    public function test_filter_scope_with_priority(): void
    {
        Task::factory()->create(['priority' => 'high', 'title' => 'A']);
        Task::factory()->create(['priority' => 'low', 'title' => 'B']);

        $results = Task::filter(['priority' => 'high'])->get();

        $this->assertCount(1, $results);
        $this->assertEquals('A', $results->first()->title);
    }

    public function test_filter_scope_with_search(): void
    {
        Task::factory()->create(['title' => 'Laravel Development']);
        Task::factory()->create(['title' => 'React Development']);

        $results = Task::filter(['search' => 'Laravel'])->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Laravel Development', $results->first()->title);
    }

    public function test_filter_scope_searches_description(): void
    {
        Task::factory()->create(['title' => 'Task A', 'description' => 'Fix the login bug']);
        Task::factory()->create(['title' => 'Task B', 'description' => 'Update documentation']);

        $results = Task::filter(['search' => 'login bug'])->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Task A', $results->first()->title);
    }

    public function test_filter_scope_with_multiple_filters(): void
    {
        Task::factory()->create(['status' => 'pending', 'priority' => 'high', 'title' => 'A']);
        Task::factory()->create(['status' => 'pending', 'priority' => 'low', 'title' => 'B']);
        Task::factory()->create(['status' => 'completed', 'priority' => 'high', 'title' => 'C']);

        $results = Task::filter(['status' => 'pending', 'priority' => 'high'])->get();

        $this->assertCount(1, $results);
        $this->assertEquals('A', $results->first()->title);
    }

    // ── Accessor Tests ──────────────────────────────────

    public function test_is_overdue_accessor_returns_true_for_past_due_pending_task(): void
    {
        $task = Task::factory()->make(['status' => 'pending', 'due_date' => now()->subDay()]);
        $this->assertTrue($task->is_overdue);
    }

    public function test_is_overdue_accessor_returns_false_for_future_due_task(): void
    {
        $task = Task::factory()->make(['status' => 'pending', 'due_date' => now()->addDay()]);
        $this->assertFalse($task->is_overdue);
    }

    public function test_is_overdue_accessor_returns_false_for_completed_past_due_task(): void
    {
        $task = Task::factory()->make(['status' => 'completed', 'due_date' => now()->subDay()]);
        $this->assertFalse($task->is_overdue);
    }

    public function test_is_overdue_accessor_returns_false_for_task_with_no_due_date(): void
    {
        $task = Task::factory()->make(['status' => 'pending', 'due_date' => null]);
        $this->assertFalse($task->is_overdue);
    }

    public function test_status_badge_color_accessor(): void
    {
        $this->assertEquals('yellow', Task::factory()->make(['status' => 'pending'])->statusBadgeColor);
        $this->assertEquals('blue', Task::factory()->make(['status' => 'in_progress'])->statusBadgeColor);
        $this->assertEquals('green', Task::factory()->make(['status' => 'completed'])->statusBadgeColor);
    }

    public function test_priority_badge_color_accessor(): void
    {
        $this->assertEquals('red', Task::factory()->make(['priority' => 'high'])->priorityBadgeColor);
        $this->assertEquals('orange', Task::factory()->make(['priority' => 'medium'])->priorityBadgeColor);
        $this->assertEquals('green', Task::factory()->make(['priority' => 'low'])->priorityBadgeColor);
    }

    // ── Cast & Soft Delete Tests ────────────────────────

    public function test_due_date_is_cast_to_carbon_instance(): void
    {
        $task = Task::factory()->create(['due_date' => '2025-12-31']);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $task->due_date);
    }

    public function test_task_uses_soft_deletes(): void
    {
        $task = Task::factory()->create();
        $task->delete();

        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
        $this->assertEquals(0, Task::count());
        $this->assertEquals(1, Task::withTrashed()->count());
    }

    public function test_task_can_be_force_deleted(): void
    {
        $task = Task::factory()->create();
        $task->forceDelete();

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
        $this->assertEquals(0, Task::withTrashed()->count());
    }
}
