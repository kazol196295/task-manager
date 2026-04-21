<?php

namespace Tests\Unit;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_scope(): void
    {
        Task::factory()->create(['status' => 'pending']);
        Task::factory()->create(['status' => 'completed']);
        $this->assertEquals(1, Task::pending()->count());
    }

    public function test_overdue_scope_excludes_completed(): void
    {
        Task::factory()->create(['status' => 'completed', 'due_date' => now()->subDays(5)]);
        Task::factory()->create(['status' => 'pending', 'due_date' => now()->subDays(5)]);
        $this->assertEquals(1, Task::overdue()->count());
    }

    public function test_is_overdue_accessor(): void
    {
        $overdue = Task::factory()->make(['status' => 'pending', 'due_date' => now()->subDay()]);
        $completed = Task::factory()->make(['status' => 'completed', 'due_date' => now()->subDay()]);
        $this->assertTrue($overdue->is_overdue);
        $this->assertFalse($completed->is_overdue);
    }

    public function test_soft_deletes(): void
    {
        $task = Task::factory()->create();
        $task->delete();
        $this->assertEquals(0, Task::count());
        $this->assertEquals(1, Task::withTrashed()->count());
    }
}
