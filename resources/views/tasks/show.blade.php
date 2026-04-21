@extends('layouts.app')

@section('content')
<style>
    .detail-wrap { max-width: 680px; margin: 0 auto; }
    .back-link {
        display: inline-flex; align-items: center; gap: 0.4rem;
        font-size: 0.85rem; color: #7c3aed; text-decoration: none;
        font-weight: 500; margin-bottom: 1.5rem;
        padding: 0.375rem 0.875rem;
        background: #ede9fe;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .back-link:hover { background: #ddd6fe; color: #5b21b6; }
    .detail-card {
        background: white;
        border-radius: 20px;
        border: 1.5px solid #ede9fe;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(124,58,237,0.08);
    }
    .detail-header {
        padding: 2rem 2.5rem;
        position: relative;
    }
    .detail-header.normal { background: linear-gradient(135deg, #f5f3ff 0%, #fce7f3 100%); }
    .detail-header.overdue { background: linear-gradient(135deg, #fff1f2 0%, #ffedd5 100%); }
    .overdue-badge {
        display: inline-flex; align-items: center; gap: 4px;
        background: #fee2e2; color: #b91c1c;
        font-size: 0.72rem; font-weight: 700; letter-spacing: 0.04em;
        padding: 0.3rem 0.75rem; border-radius: 999px;
        margin-bottom: 0.75rem;
    }
    .detail-title {
        font-family: 'Syne', sans-serif;
        font-size: 1.6rem;
        font-weight: 800;
        color: #1e1b4b;
        letter-spacing: -0.5px;
        line-height: 1.2;
    }
    .detail-title.done { text-decoration: line-through; color: #9ca3af; }
    .detail-actions { display: flex; gap: 0.5rem; position: absolute; top: 2rem; right: 2.5rem; }
    .btn-edit-detail {
        padding: 0.5rem 1.1rem;
        background: #ede9fe;
        color: #7c3aed;
        font-weight: 600;
        font-size: 0.8rem;
        border-radius: 9px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-edit-detail:hover { background: #7c3aed; color: white; }
    .btn-delete-detail {
        padding: 0.5rem 1.1rem;
        background: #fee2e2;
        color: #dc2626;
        font-weight: 600;
        font-size: 0.8rem;
        border-radius: 9px;
        border: none;
        cursor: pointer;
        font-family: 'DM Sans', sans-serif;
        transition: all 0.2s;
    }
    .btn-delete-detail:hover { background: #dc2626; color: white; }
    .detail-body { padding: 2rem 2.5rem; display: flex; flex-direction: column; gap: 1.75rem; }
    .badges-row { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .status-badge {
        display: inline-block;
        font-size: 0.78rem; font-weight: 700;
        padding: 0.35rem 0.9rem;
        border-radius: 999px;
        letter-spacing: 0.03em;
    }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-in_progress { background: #cffafe; color: #164e63; }
    .status-completed { background: #d1fae5; color: #064e3b; }
    .priority-badge {
        display: inline-block;
        font-size: 0.78rem; font-weight: 700;
        padding: 0.35rem 0.9rem;
        border-radius: 999px;
    }
    .priority-high { background: #fee2e2; color: #7f1d1d; }
    .priority-medium { background: #ffedd5; color: #7c2d12; }
    .priority-low { background: #d1fae5; color: #064e3b; }
    .section-title {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: #9ca3af;
        margin-bottom: 0.5rem;
    }
    .description-text {
        color: #374151;
        line-height: 1.7;
        font-size: 0.9rem;
        white-space: pre-line;
    }
    .meta-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        padding-top: 1.5rem;
        border-top: 1.5px solid #f3f4f6;
    }
    .meta-item { display: flex; flex-direction: column; gap: 4px; }
    .meta-label { font-size: 0.78rem; color: #9ca3af; font-weight: 500; }
    .meta-value { font-size: 0.925rem; font-weight: 600; color: #1e1b4b; }
    .meta-value.overdue-text { color: #dc2626; }
    .status-update-section {
        padding-top: 1.5rem;
        border-top: 1.5px solid #f3f4f6;
    }
    .status-buttons { display: flex; gap: 0.625rem; flex-wrap: wrap; margin-top: 0.75rem; }
    .status-btn {
        padding: 0.6rem 1.25rem;
        font-size: 0.85rem;
        font-weight: 600;
        border-radius: 10px;
        border: 2px solid transparent;
        cursor: pointer;
        font-family: 'DM Sans', sans-serif;
        transition: all 0.2s;
    }
    .status-btn-pending {
        background: #fef3c7; color: #92400e; border-color: #fde68a;
    }
    .status-btn-pending.active { background: #fbbf24; color: #451a03; border-color: #f59e0b; box-shadow: 0 2px 8px rgba(245,158,11,0.3); }
    .status-btn-pending:hover:not(.active) { background: #fde68a; }
    .status-btn-progress {
        background: #cffafe; color: #164e63; border-color: #a5f3fc;
    }
    .status-btn-progress.active { background: #22d3ee; color: #083344; border-color: #06b6d4; box-shadow: 0 2px 8px rgba(6,182,212,0.3); }
    .status-btn-progress:hover:not(.active) { background: #a5f3fc; }
    .status-btn-done {
        background: #d1fae5; color: #064e3b; border-color: #a7f3d0;
    }
    .status-btn-done.active { background: #34d399; color: #022c22; border-color: #10b981; box-shadow: 0 2px 8px rgba(16,185,129,0.3); }
    .status-btn-done:hover:not(.active) { background: #a7f3d0; }
</style>

<div class="detail-wrap">
    <a href="{{ route('tasks.index') }}" class="back-link">← Back to Tasks</a>

    <div class="detail-card">
        <div class="detail-header {{ $task->is_overdue ? 'overdue' : 'normal' }}">
            @if($task->is_overdue)
                <div class="overdue-badge">⚠ Overdue</div><br>
            @endif
            <h1 class="detail-title {{ $task->status === 'completed' ? 'done' : '' }}">{{ $task->title }}</h1>
            <div class="detail-actions">
                <a href="{{ route('tasks.edit', $task) }}" class="btn-edit-detail">✏ Edit</a>
                <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-delete-detail">🗑 Delete</button>
                </form>
            </div>
        </div>

        <div class="detail-body">
            <div class="badges-row">
                <span class="status-badge status-{{ $task->status }}">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</span>
                <span class="priority-badge priority-{{ $task->priority }}">{{ ucfirst($task->priority) }} Priority</span>
            </div>

            @if($task->description)
            <div>
                <div class="section-title">Description</div>
                <p class="description-text">{{ $task->description }}</p>
            </div>
            @endif

            <div class="meta-grid">
                <div class="meta-item">
                    <span class="meta-label">Due Date</span>
                    <span class="meta-value {{ $task->is_overdue ? 'overdue-text' : '' }}">
                        {{ $task->due_date ? $task->due_date->format('F d, Y') : 'No due date' }}
                    </span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Created</span>
                    <span class="meta-value">{{ $task->created_at->format('F d, Y · H:i') }}</span>
                </div>
            </div>

            <div class="status-update-section">
                <div class="section-title">Quick Status Update</div>
                <form method="POST" action="{{ route('tasks.update-status', $task) }}" class="status-buttons">
                    @csrf @method('PATCH')
                    <button type="submit" name="status" value="pending"
                        class="status-btn status-btn-pending {{ $task->status === 'pending' ? 'active' : '' }}">
                        🟡 Pending
                    </button>
                    <button type="submit" name="status" value="in_progress"
                        class="status-btn status-btn-progress {{ $task->status === 'in_progress' ? 'active' : '' }}">
                        🔵 In Progress
                    </button>
                    <button type="submit" name="status" value="completed"
                        class="status-btn status-btn-done {{ $task->status === 'completed' ? 'active' : '' }}">
                        🟢 Completed
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
