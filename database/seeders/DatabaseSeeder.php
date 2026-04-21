<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Task::factory(20)->create();

        Task::factory()->create(['title' => 'Overdue Task Example', 'status' => 'in_progress', 'priority' => 'high', 'due_date' => now()->subDays(5)]);
        Task::factory()->create(['title' => 'Completed Task Example', 'status' => 'completed', 'priority' => 'low', 'due_date' => now()->addDays(2)]);
    }
}
