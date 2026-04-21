@extends('layouts.app')

@section('content')
<style>
    .form-wrap { max-width: 680px; margin: 0 auto; }
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
    .form-card {
        background: white;
        border-radius: 20px;
        border: 1.5px solid #ede9fe;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(124,58,237,0.08);
    }
    .form-header {
        background: linear-gradient(135deg, #7c3aed 0%, #ec4899 100%);
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
    }
    .form-header::after {
        content: '✦';
        position: absolute;
        right: 2rem; top: 50%;
        transform: translateY(-50%);
        font-size: 5rem;
        color: rgba(255,255,255,0.1);
    }
    .form-header h1 {
        font-family: 'Syne', sans-serif;
        font-size: 1.6rem;
        font-weight: 800;
        color: white;
        letter-spacing: -0.5px;
    }
    .form-header p { color: rgba(255,255,255,0.8); font-size: 0.875rem; margin-top: 4px; }
    .form-body { padding: 2.5rem; }
    .form-body form { display: flex; flex-direction: column; gap: 1.5rem; }
    .field-group { display: flex; flex-direction: column; gap: 6px; }
    .field-label {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #6b7280;
    }
    .required { color: #ec4899; margin-left: 2px; }
    .field-input, .field-textarea, .field-select {
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 0.925rem;
        color: #1e1b4b;
        font-family: 'DM Sans', sans-serif;
        background: #fafafa;
        outline: none;
        transition: all 0.2s;
        width: 100%;
    }
    .field-input:focus, .field-textarea:focus, .field-select:focus {
        border-color: #7c3aed;
        background: white;
        box-shadow: 0 0 0 4px rgba(124,58,237,0.1);
    }
    .field-input.error { border-color: #ec4899; background: #fff0f6; }
    .field-textarea { resize: vertical; min-height: 110px; line-height: 1.6; }
    .error-msg { font-size: 0.78rem; color: #ec4899; font-weight: 500; }
    .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
    .form-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding-top: 1.5rem;
        border-top: 1.5px solid #f3f4f6;
    }
    .btn-cancel {
        padding: 0.625rem 1.5rem;
        background: #f3f4f6;
        color: #374151;
        font-weight: 600;
        font-size: 0.875rem;
        border-radius: 10px;
        text-decoration: none;
        transition: background 0.2s;
    }
    .btn-cancel:hover { background: #e5e7eb; }
    .btn-submit {
        padding: 0.625rem 1.75rem;
        background: linear-gradient(135deg, #7c3aed, #ec4899);
        color: white;
        font-weight: 700;
        font-size: 0.875rem;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        font-family: 'DM Sans', sans-serif;
        box-shadow: 0 4px 14px rgba(124,58,237,0.3);
        transition: all 0.2s;
    }
    .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(124,58,237,0.4); }
    .color-dot {
        display: inline-block;
        width: 8px; height: 8px;
        border-radius: 50%;
        margin-right: 6px;
    }
</style>

<div class="form-wrap">
    <a href="{{ route('tasks.index') }}" class="back-link">← Back to Tasks</a>

    <div class="form-card">
        <div class="form-header">
            <h1>Create New Task</h1>
            <p>Fill in the details to add a task to your board</p>
        </div>
        <div class="form-body">
            <form method="POST" action="{{ route('tasks.store') }}">
                @csrf

                <div class="field-group">
                    <label class="field-label">Title<span class="required">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        class="field-input {{ $errors->has('title') ? 'error' : '' }}"
                        placeholder="What needs to be done?">
                    @error('title')<p class="error-msg">{{ $message }}</p>@enderror
                </div>

                <div class="field-group">
                    <label class="field-label">Description</label>
                    <textarea name="description" class="field-textarea" placeholder="Add more details…">{{ old('description') }}</textarea>
                </div>

                <div class="two-col">
                    <div class="field-group">
                        <label class="field-label">Status<span class="required">*</span></label>
                        <select name="status" class="field-select">
                            <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>🟡 Pending</option>
                            <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>🔵 In Progress</option>
                            <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>🟢 Completed</option>
                        </select>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Priority<span class="required">*</span></label>
                        <select name="priority" class="field-select">
                            <option value="low" {{ old('priority', 'medium') === 'low' ? 'selected' : '' }}>🟢 Low</option>
                            <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>🟠 Medium</option>
                            <option value="high" {{ old('priority', 'medium') === 'high' ? 'selected' : '' }}>🔴 High</option>
                        </select>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">Due Date</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}" class="field-input">
                </div>

                <div class="form-footer">
                    <a href="{{ route('tasks.index') }}" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">Create Task →</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
