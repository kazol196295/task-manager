@extends('layouts.app')

@section('content')
    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.75rem;
        }
        .page-title {
            font-family: 'Syne', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            color: #1e1b4b;
            letter-spacing: -0.5px;
        }
        .page-subtitle { font-size: 0.875rem; color: #6b7280; margin-top: 2px; }

        /* Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
            margin-bottom: 1.75rem;
        }
        @media (max-width: 768px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
        .stat-card {
            background: white;
            border-radius: 16px;
            padding:1.25rem;
            border: 1.5px solid #ede9fe;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
        }
        .stat-total::before { background: linear-gradient(90deg, #7c3aed, #ec4899); }
        .stat-pending::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
        .stat-progress::before { background: linear-gradient(90deg, #06b6d4, #38bdf8); }
        .stat-done::before { background: linear-gradient(90deg, #10b981, #34d399); }
        .stat-overdue::before { background: linear-gradient(90deg, #ef4444, #f87171); }
        .stat-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-total .stat-label { color: #7c3aed; }
        .stat-pending .stat-label { color: #d97706; }
        .stat-progress .stat-label { color: #0891b2; }
        .stat-done .stat-label { color: #059669; }
        .stat-overdue .stat-label { color: #dc2626; }
        .stat-number {
            font-family: 'Syne', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            margin-top: 0.25rem;
            line-height: 2;
        }
        .stat-total .stat-number { color: #1e1b4b; }
        .stat-pending .stat-number { color: #92400e; }
        .stat-progress .stat-number { color: #164e63; }
        .stat-done .stat-number { color: #064e3b; }
        .stat-overdue .stat-number { color: #7f1d1d; }

        /* Filter bar */
        .filter-card {
            background: white;
            border-radius: 16px;
            border: 1.5px solid #ede9fe;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;
        }
        .filter-group { display: flex; flex-direction: column; gap: 4px; }
        .filter-label { font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.04em; }
        .filter-input, .filter-select {
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.5rem 0.875rem;
            font-size: 0.875rem;
            color: #1e1b4b;
            font-family: 'DM Sans', sans-serif;
            background: white;
            outline: none;
            transition: border-color 0.2s;
            min-width: 160px;
        }
        .filter-input:focus, .filter-select:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,0.1); }
        .filter-search { min-width: 220px; flex: 1; }
        .overdue-check { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding-bottom: 0.5rem; }
        .overdue-check input[type="checkbox"] { width: 16px; height: 16px; accent-color: #ef4444; }
        .overdue-check span { font-size: 0.875rem; font-weight: 600; color: #ef4444; }
        .filter-actions { display: flex; gap: 0.5rem; align-items: flex-end; }
        .btn-filter {
            padding: 0.5rem 1.25rem;
            background: #1e1b4b;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            transition: background 0.2s;
        }
        .btn-filter:hover { background: #312e81; }
        .btn-clear {
            padding: 0.5rem 1.25rem;
            background: #f3f4f6;
            color: #374151;
            border: none;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-family: 'DM Sans', sans-serif;
        }
        .btn-clear:hover { background: #e5e7eb; }

        /* Table */
        .task-table-wrap {
            background: white;
            border-radius: 16px;
            border: 1.5px solid #ede9fe;
            overflow: hidden;
        }
        .task-table { width: 100%; border-collapse: collapse; }
        .task-table thead { background: linear-gradient(135deg, #f5f3ff, #fce7f3); }
        .task-table th {
            padding: 1rem 1.25rem;
            text-align: left;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #7c3aed;
        }
        .task-table th:last-child { text-align: right; }
        .task-table tbody tr {
            border-top: 1px solid #f3f4f6;
            transition: background 0.15s;
        }
        .task-table tbody tr:hover { background: #fafafa; }
        .task-table tbody tr.overdue-row { background: #fff1f2; }
        .task-table tbody tr.overdue-row:hover { background: #ffe4e6; }
        .task-table td { padding: 1rem 1.25rem; vertical-align: middle; }
        .task-title-link {
            font-weight: 600;
            font-size: 0.9rem;
            color: #1e1b4b;
            text-decoration: none;
            transition: color 0.2s;
        }
        .task-title-link:hover { color: #7c3aed; }
        .task-title-link.done { text-decoration: line-through; color: #9ca3af; }
        .task-desc { font-size: 0.78rem; color: #9ca3af; margin-top: 2px; }
        .status-select {
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 999px;
            padding: 0.3rem 0.75rem;
            border: none;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
            appearance: none;
            outline: none;
            transition: opacity 0.2s;
        }
        .status-select:hover { opacity: 0.85; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-in_progress { background: #cffafe; color: #164e63; }
        .status-completed { background: #d1fae5; color: #064e3b; }
        .badge {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            border-radius: 999px;
            padding: 0.25rem 0.75rem;
            letter-spacing: 0.02em;
        }
        .badge-high { background: #fee2e2; color: #7f1d1d; }
        .badge-medium { background: #ffedd5; color: #7c2d12; }
        .badge-low { background: #d1fae5; color: #064e3b; }
        .due-date { font-size: 0.85rem; color: #6b7280; }
        .due-overdue { color: #dc2626; font-weight: 700; }
        .due-overdue-tag { font-size: 0.7rem; font-weight: 600; background: #fee2e2; color: #dc2626; padding: 1px 6px; border-radius: 4px; margin-left: 4px; }
        .action-btns { display: flex; justify-content: flex-end; gap: 0.5rem; align-items: center; }
        .btn-edit, .btn-delete {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            border: none; cursor: pointer; font-size: 0.95rem;
            transition: all 0.15s; text-decoration: none;
        }
        .btn-edit { background: #ede9fe; color: #7c3aed; }
        .btn-edit:hover { background: #7c3aed; color: white; }
        .btn-delete { background: #fee2e2; color: #dc2626; }
        .btn-delete:hover { background: #dc2626; color: white; }
        .empty-state {
            text-align: center;
            padding: 5rem 2rem;
        }
        .empty-icon { font-size: 3rem; margin-bottom: 1rem; }
        .empty-title { font-family: 'Syne', sans-serif; font-size: 1.25rem; font-weight: 700; color: #1e1b4b; margin-bottom: 0.5rem; }
        .empty-sub { color: #9ca3af; font-size: 0.9rem; margin-bottom: 1.5rem; }
        .btn-create {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.625rem 1.5rem;
            background: linear-gradient(135deg, #7c3aed, #ec4899);
            color: white; font-weight: 600; font-size: 0.875rem;
            border-radius: 10px; text-decoration: none;
            box-shadow: 0 4px 14px rgba(124,58,237,0.3);
            transition: all 0.2s;
        }
        .btn-create:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(124,58,237,0.4); }
        .pagination-wrap { padding: 1rem 1.25rem; border-top: 1px solid #f3f4f6; }
    </style>

    <div class="page-header">
        <div>
            <h1 class="page-title">My Tasks</h1>
            <p class="page-subtitle">Manage and track your work</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card stat-total">
            <div class="stat-label">Total</div>
            <div class="stat-number">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card stat-pending">
            <div class="stat-label">Pending</div>
            <div class="stat-number">{{ $stats['pending'] }}</div>
        </div>
        <div class="stat-card stat-progress">
            <div class="stat-label">In Progress</div>
            <div class="stat-number">{{ $stats['in_progress'] }}</div>
        </div>
        <div class="stat-card stat-done">
            <div class="stat-label">Completed</div>
            <div class="stat-number">{{ $stats['completed'] }}</div>
        </div>
        <div class="stat-card stat-overdue">
            <div class="stat-label">Overdue</div>
            <div class="stat-number">{{ $stats['overdue'] }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('tasks.index') }}" style="display:flex;flex-wrap:wrap;gap:1rem;align-items:flex-end;width:100%;">
            <div class="filter-group" style="flex:1;">
                <span class="filter-label">Search</span>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search tasks…" class="filter-input filter-search">
            </div>
            <div class="filter-group">
                <span class="filter-label">Status</span>
                <select name="status" class="filter-select">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div class="filter-group">
                <span class="filter-label">Priority</span>
                <select name="priority" class="filter-select">
                    <option value="">All Priorities</option>
                    <option value="low" {{ ($filters['priority'] ?? '') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ ($filters['priority'] ?? '') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ ($filters['priority'] ?? '') === 'high' ? 'selected' : '' }}>High</option>
                </select>
            </div>
            <label class="overdue-check">
                <input type="checkbox" name="overdue" value="1" {{ ($filters['overdue'] ?? '') ? 'checked' : '' }}>
                <span>Overdue Only</span>
            </label>
            <div class="filter-actions">
                <button type="submit" class="btn-filter">Filter</button>
                <a href="{{ route('tasks.index') }}" class="btn-clear">Clear</a>
            </div>
        </form>
    </div>

    <!-- Task Table -->
    <div class="task-table-wrap">
        @if($tasks->count() > 0)
            <table class="task-table">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                    <tr class="{{ $task->is_overdue ? 'overdue-row' : '' }}">
                        <td>
                            <a href="{{ route('tasks.show', $task) }}" class="task-title-link {{ $task->status === 'completed' ? 'done' : '' }}">{{ $task->title }}</a>
                            @if($task->description)
                                <div class="task-desc">{{ Str::limit($task->description, 65) }}</div>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('tasks.update-status', $task) }}" style="display:inline-block;">
                                @csrf @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="status-select status-{{ $task->status }}">
                                    <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <span class="badge badge-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
                        </td>
                        <td>
                            <span class="due-date {{ $task->is_overdue ? 'due-overdue' : '' }}">
                                {{ $task->due_date ? $task->due_date->format('M d, Y') : '—' }}
                                @if($task->is_overdue)<span class="due-overdue-tag">Overdue</span>@endif
                            </span>
                        </td>
                        <td>
                            <div class="action-btns">
                                <a href="{{ route('tasks.edit', $task) }}" class="btn-edit">✏️</a>
                                <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-delete">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination-wrap">
                {{ $tasks->withQueryString()->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <div class="empty-title">No tasks found</div>
                <div class="empty-sub">Try adjusting your filters or create a new task to get started.</div>
                <a href="{{ route('tasks.create') }}" class="btn-create">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create New Task
                </a>
            </div>
        @endif
    </div>
@endsection
