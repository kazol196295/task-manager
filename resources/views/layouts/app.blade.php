<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Task Manager')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --violet: #7c3aed;
            --violet-light: #ede9fe;
            --violet-dark: #5b21b6;
            --pink: #ec4899;
            --pink-light: #fce7f3;
            --amber: #f59e0b;
            --amber-light: #fef3c7;
            --cyan: #06b6d4;
            --cyan-light: #cffafe;
            --emerald: #10b981;
            --emerald-light: #d1fae5;
            --red: #ef4444;
            --red-light: #fee2e2;
            --bg: #f5f3ff;
            --surface: #ffffff;
            --text: #1e1b4b;
            --text-muted: #6b7280;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: var(--bg);
            background-image:
                radial-gradient(circle at 15% 0%, #ede9fe 0%, transparent 40%),
                radial-gradient(circle at 85% 100%, #fce7f3 0%, transparent 40%);
            min-height: 100vh;
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
        }

        nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1.5px solid #ede9fe;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 64px;
        }

        .nav-logo {
            font-family: 'Syne', sans-serif;
            font-size: 1.4rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--violet), var(--pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
            letter-spacing: -0.5px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-link {
            color: var(--text-muted);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .nav-link:hover {
            background: var(--violet-light);
            color: var(--violet);
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 1.25rem;
            background: linear-gradient(135deg, var(--violet), var(--pink));
            color: white;
            font-size: 0.875rem;
            font-weight: 600;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(124, 58, 237, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(124, 58, 237, 0.4);
        }

        .alert-success {
            max-width: 1200px;
            margin: 1rem auto 0;
            padding: 0 1.5rem;
        }

        .alert-success>div {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            border: 1.5px solid #6ee7b7;
            color: #065f46;
            padding: 0.875rem 1.25rem;
            border-radius: 12px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }
    </style>
</head>

<body>
    <nav>
        <div class="nav-inner">
            <a href="{{ route('tasks.index') }}" class="nav-logo">✦ TaskFlow</a>
            <div class="nav-links">
                <a href="{{ route('tasks.index') }}" class="nav-link">All Tasks</a>
                <a href="{{ route('tasks.create') }}" class="btn-primary">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    New Task
                </a>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="alert-success">
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition>
                ✓ {{ session('success') }}
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>
</body>

</html>
