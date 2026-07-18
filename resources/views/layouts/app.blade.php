<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ParKar') — Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --orange:      #F97316;
            --orange-dark: #C2410C;
            --orange-mid:  #EA580C;
            --orange-light:#FED7AA;
            --orange-pale: #FFF7ED;
            --white:       #FFFFFF;
            --gray-50:     #F9FAFB;
            --gray-100:    #F3F4F6;
            --gray-200:    #E5E7EB;
            --gray-300:    #D1D5DB;
            --gray-400:    #9CA3AF;
            --gray-500:    #6B7280;
            --gray-600:    #4B5563;
            --gray-700:    #374151;
            --gray-800:    #1F2937;
            --dark:        #0F172A;
            --sidebar-w:   268px;
            --radius:      14px;
            --radius-sm:   8px;
            --shadow-sm:   0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
            --shadow-md:   0 4px 16px rgba(0,0,0,.08);
            --shadow-lg:   0 12px 40px rgba(0,0,0,.12);
            --shadow-orange: 0 8px 24px rgba(249,115,22,.25);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #F1F5F9;
            color: var(--dark);
            display: flex;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ══════════════════════════════ SIDEBAR ══════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--dark);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            z-index: 200;
            transition: transform .3s cubic-bezier(.4,0,.2,1);
            overflow: hidden;
        }

        /* Subtle gradient accent on the sidebar left edge */
        .sidebar::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, var(--orange) 0%, #fb923c 50%, var(--orange-dark) 100%);
        }

        .sidebar-logo {
            padding: 1.5rem 1.5rem 1.25rem;
            display: flex;
            align-items: center;
            gap: .6rem;
            border-bottom: 1px solid rgba(255,255,255,.07);
        }
        .sidebar-logo-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
            box-shadow: var(--shadow-orange);
            flex-shrink: 0;
        }
        .sidebar-logo-text {
            font-size: 1.5rem;
            font-weight: 900;
            color: white;
            letter-spacing: -.5px;
        }
        .sidebar-logo-badge {
            font-size: .55rem;
            font-weight: 700;
            background: var(--orange);
            color: white;
            padding: .15rem .45rem;
            border-radius: 20px;
            letter-spacing: .05em;
            text-transform: uppercase;
            align-self: flex-start;
            margin-top: .1rem;
        }

        .sidebar-user {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.07);
            display: flex;
            align-items: center;
            gap: .85rem;
        }
        .sidebar-avatar {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--orange), #fb923c);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.05rem;
            font-weight: 800;
            color: white;
            flex-shrink: 0;
            letter-spacing: -.5px;
        }
        .sidebar-user-info { overflow: hidden; }
        .sidebar-user-name {
            font-weight: 700;
            font-size: .9rem;
            color: white;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar-user-role {
            display: inline-block;
            margin-top: .2rem;
            padding: .1rem .55rem;
            border-radius: 20px;
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            background: rgba(249,115,22,.2);
            color: #fdba74;
            border: 1px solid rgba(249,115,22,.3);
        }

        .sidebar-nav { flex: 1; padding: .75rem 0; overflow-y: auto; scrollbar-width: none; }
        .sidebar-nav::-webkit-scrollbar { display: none; }

        .nav-section-label {
            padding: .6rem 1.5rem .3rem;
            font-size: .65rem;
            font-weight: 800;
            color: rgba(255,255,255,.3);
            text-transform: uppercase;
            letter-spacing: .1em;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .6rem 1.25rem .6rem 1.4rem;
            margin: .1rem .75rem;
            border-radius: var(--radius-sm);
            color: rgba(255,255,255,.55);
            text-decoration: none;
            font-size: .875rem;
            font-weight: 500;
            transition: all .18s cubic-bezier(.4,0,.2,1);
            position: relative;
        }
        .nav-link:hover {
            background: rgba(255,255,255,.06);
            color: rgba(255,255,255,.9);
        }
        .nav-link.active {
            background: linear-gradient(135deg, rgba(249,115,22,.18), rgba(234,88,12,.12));
            color: #fed7aa;
            font-weight: 600;
            box-shadow: inset 0 0 0 1px rgba(249,115,22,.2);
        }
        .nav-link.active::before {
            content: '';
            position: absolute;
            left: -0.75rem;
            top: 50%; transform: translateY(-50%);
            width: 3px; height: 60%;
            background: var(--orange);
            border-radius: 0 2px 2px 0;
        }
        .nav-icon {
            font-size: 1rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,.07);
        }
        .logout-btn {
            display: flex;
            align-items: center;
            gap: .6rem;
            width: 100%;
            padding: .6rem 1rem;
            background: rgba(239,68,68,.1);
            color: #fca5a5;
            border: 1px solid rgba(239,68,68,.2);
            border-radius: var(--radius-sm);
            font-family: 'Outfit', sans-serif;
            font-size: .875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .18s;
        }
        .logout-btn:hover {
            background: rgba(239,68,68,.2);
            border-color: rgba(239,68,68,.4);
            color: #fecaca;
        }

        /* ═════════════════════════════ MAIN LAYOUT ═══════════════════════════ */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            background: white;
            border-bottom: 1px solid var(--gray-200);
            padding: .9rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
        }
        .topbar-left { display: flex; align-items: center; gap: 1rem; }
        .topbar-breadcrumb {
            font-size: .8rem;
            color: var(--gray-400);
            font-weight: 500;
        }
        .topbar-breadcrumb span { color: var(--gray-700); font-weight: 600; }
        .topbar-right { display: flex; align-items: center; gap: .75rem; }

        .notif-btn {
            position: relative;
            background: none;
            border: 1.5px solid var(--gray-200);
            border-radius: var(--radius-sm);
            padding: .45rem .65rem;
            cursor: pointer;
            font-size: 1rem;
            color: var(--gray-500);
            text-decoration: none;
            transition: all .2s;
        }
        .notif-btn:hover {
            border-color: var(--orange);
            color: var(--orange);
            background: var(--orange-pale);
            transform: translateY(-1px);
        }
        .notif-badge {
            position: absolute;
            top: -5px; right: -5px;
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            color: white;
            border-radius: 50%;
            width: 17px; height: 17px;
            font-size: .6rem;
            font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 2px 6px rgba(249,115,22,.4);
            animation: pulse-badge 2s infinite;
        }
        @keyframes pulse-badge {
            0%, 100% { box-shadow: 0 2px 6px rgba(249,115,22,.4); }
            50%       { box-shadow: 0 2px 12px rgba(249,115,22,.7); }
        }

        .topbar-user-chip {
            display: flex; align-items: center; gap: .5rem;
            padding: .35rem .75rem;
            background: var(--gray-50);
            border: 1.5px solid var(--gray-200);
            border-radius: 20px;
            font-size: .8rem;
            color: var(--gray-600);
        }
        .topbar-user-dot {
            width: 8px; height: 8px;
            background: #10B981;
            border-radius: 50%;
            flex-shrink: 0;
            box-shadow: 0 0 0 2px rgba(16,185,129,.2);
        }

        .main-content { flex: 1; padding: 2rem; }

        /* ══════════════════════════ CARDS & UTILITIES ════════════════════════ */
        .card {
            background: white;
            border-radius: var(--radius);
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            transition: box-shadow .2s, transform .2s;
        }
        .card:hover { box-shadow: var(--shadow-md); }

        .card-header {
            padding: 1.1rem 1.5rem;
            border-bottom: 1px solid var(--gray-100);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title { font-size: .95rem; font-weight: 700; color: var(--dark); }
        .card-body { padding: 1.5rem; }

        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: var(--radius);
            border: 1px solid var(--gray-200);
            padding: 1.5rem;
            display: flex; align-items: center; gap: 1.1rem;
            box-shadow: var(--shadow-sm);
            transition: transform .2s, box-shadow .2s;
            position: relative;
            overflow: hidden;
        }
        .stat-card::after {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 60px; height: 60px;
            border-radius: 0 var(--radius) 0 60px;
            opacity: .04;
        }
        .stat-card.orange-card::after { background: var(--orange); }
        .stat-card.green-card::after  { background: #10B981; }
        .stat-card.red-card::after    { background: #EF4444; }
        .stat-card.blue-card::after   { background: #3B82F6; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }

        .stat-icon {
            width: 52px; height: 52px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        .stat-icon.orange { background: linear-gradient(135deg, #fff7ed, #fed7aa); }
        .stat-icon.green  { background: linear-gradient(135deg, #ecfdf5, #a7f3d0); }
        .stat-icon.red    { background: linear-gradient(135deg, #fef2f2, #fecaca); }
        .stat-icon.blue   { background: linear-gradient(135deg, #eff6ff, #bfdbfe); }
        .stat-value { font-size: 2.1rem; font-weight: 900; color: var(--dark); line-height: 1; letter-spacing: -1px; }
        .stat-label { font-size: .78rem; color: var(--gray-500); margin-top: .2rem; font-weight: 500; }
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem; }

        /* ════════════════════════════ BADGES ════════════════════════════════ */
        .badge {
            display: inline-flex; align-items: center; gap: .3rem;
            padding: .25rem .75rem;
            border-radius: 20px;
            font-size: .75rem;
            font-weight: 700;
            white-space: nowrap;
            letter-spacing: .02em;
        }
        .badge-pending  { background: #FFF7ED; color: #C2410C; border: 1px solid #FED7AA; }
        .badge-approved { background: #ECFDF5; color: #065F46; border: 1px solid #6EE7B7; }
        .badge-rejected { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
        .badge-confirmed{ background: #ECFDF5; color: #065F46; border: 1px solid #6EE7B7; }
        .badge-failed   { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }

        /* ════════════════════════════ BUTTONS ═══════════════════════════════ */
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: .45rem;
            padding: .6rem 1.25rem;
            border-radius: var(--radius-sm);
            font-size: .875rem;
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
            border: none;
            cursor: pointer;
            transition: all .18s cubic-bezier(.4,0,.2,1);
            text-decoration: none;
            white-space: nowrap;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--orange), var(--orange-mid));
            color: white;
            box-shadow: 0 2px 8px rgba(249,115,22,.3);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--orange-mid), var(--orange-dark));
            transform: translateY(-1px);
            box-shadow: var(--shadow-orange);
        }
        .btn-outline {
            background: transparent;
            border: 1.5px solid var(--gray-200);
            color: var(--gray-700);
        }
        .btn-outline:hover {
            border-color: var(--orange);
            color: var(--orange);
            background: var(--orange-pale);
        }
        .btn-danger  { background: #EF4444; color: white; }
        .btn-danger:hover  { background: #DC2626; transform: translateY(-1px); }
        .btn-success { background: linear-gradient(135deg, #10B981, #059669); color: white; }
        .btn-success:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(16,185,129,.3); }
        .btn-sm { padding: .35rem .8rem; font-size: .78rem; border-radius: 6px; }

        /* ══════════════════════════════ TABLES ══════════════════════════════ */
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        th {
            padding: .7rem 1rem;
            text-align: left;
            font-size: .7rem;
            font-weight: 800;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: .07em;
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
        }
        td { padding: .875rem 1rem; border-bottom: 1px solid var(--gray-100); color: var(--gray-700); }
        tr:last-child td { border-bottom: none; }
        tbody tr { transition: background .1s; }
        tbody tr:hover td { background: #FFF7ED; }

        /* ══════════════════════════════ FORMS ═══════════════════════════════ */
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-size: .82rem; font-weight: 700; color: var(--gray-700); margin-bottom: .4rem; letter-spacing: .01em; }
        .form-control, .form-select {
            width: 100%;
            padding: .7rem 1rem;
            border: 1.5px solid var(--gray-200);
            border-radius: var(--radius-sm);
            font-size: .9rem;
            font-family: 'Outfit', sans-serif;
            color: var(--dark);
            background: white;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--orange);
            box-shadow: 0 0 0 3px rgba(249,115,22,.1);
        }
        .form-control.is-invalid, .form-select.is-invalid { border-color: #EF4444; }
        .invalid-feedback { color: #DC2626; font-size: .78rem; margin-top: .3rem; display: block; }
        .form-hint { color: var(--gray-400); font-size: .78rem; margin-top: .25rem; }
        .form-row   { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; }
        .section-title {
            font-size: .95rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 1rem;
            padding-bottom: .6rem;
            border-bottom: 2px solid var(--orange-pale);
            display: flex; align-items: center; gap: .5rem;
        }

        /* ══════════════════════════════ ALERTS ══════════════════════════════ */
        .alert {
            padding: .85rem 1.2rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.25rem;
            font-size: .875rem;
            display: flex; align-items: flex-start; gap: .6rem;
            animation: slideDown .25s ease;
        }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
        .alert-success { background: #F0FDF4; color: #166534; border: 1px solid #BBF7D0; }
        .alert-error   { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
        .alert-info    { background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; }
        .alert-warning { background: #FFFBEB; color: #92400E; border: 1px solid #FDE68A; }

        /* ══════════════════════════ PAGINATION ══════════════════════════════ */
        .pagination { display: flex; gap: .35rem; align-items: center; flex-wrap: wrap; padding: 1rem 0; }
        .pagination a, .pagination span {
            padding: .4rem .8rem;
            border-radius: 6px;
            font-size: .8rem;
            font-weight: 600;
            text-decoration: none;
            border: 1.5px solid var(--gray-200);
            color: var(--gray-700);
            transition: all .15s;
        }
        .pagination a:hover { border-color: var(--orange); color: var(--orange); background: var(--orange-pale); }
        .pagination .active { background: var(--orange); color: white; border-color: var(--orange); }
        .pagination .disabled { opacity: .35; pointer-events: none; }

        /* ════════════════════════════ PAGE HEADER ═══════════════════════════ */
        .page-header {
            margin-bottom: 1.75rem;
            display: flex; align-items: flex-start; justify-content: space-between;
            flex-wrap: wrap; gap: 1rem;
        }
        .page-title   { font-size: 1.65rem; font-weight: 900; color: var(--dark); letter-spacing: -.5px; }
        .page-subtitle { color: var(--gray-500); font-size: .875rem; margin-top: .2rem; }

        /* ════════════════════════ EMPTY STATE ════════════════════════════════ */
        .empty-state { text-align: center; padding: 3rem 1.5rem; color: var(--gray-400); }
        .empty-icon  { font-size: 3rem; margin-bottom: 1rem; opacity: .45; }

        /* ══════════════════════════ RESPONSIVE ═══════════════════════════════ */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-wrapper { margin-left: 0; }
            .form-row, .form-row-3 { grid-template-columns: 1fr; }
            .main-content { padding: 1rem; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- ════════════ Sidebar ════════════ -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">🚗</div>
            <div>
                <div class="sidebar-logo-text">ParKar</div>
            </div>
            <span class="sidebar-logo-badge">AI</span>
        </div>

        <div class="sidebar-user">
            <div class="sidebar-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
                <span class="sidebar-user-role">{{ Auth::user()->role }}</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            @if(Auth::user()->role === 'admin')
                <div class="nav-section-label">Admin Panel</div>
                <a href="{{ route('admin.dashboard') }}"    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><span class="nav-icon">📊</span> Dashboard</a>
                <a href="{{ route('admin.applications') }}" class="nav-link {{ request()->routeIs('admin.applications*') ? 'active' : '' }}"><span class="nav-icon">📋</span> Applications</a>
            @else
                <div class="nav-section-label">My Parking</div>
                <a href="{{ route('student.dashboard') }}"    class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}"><span class="nav-icon">🏠</span> Dashboard</a>
                <a href="{{ route('student.apply') }}"        class="nav-link {{ request()->routeIs('student.apply') ? 'active' : '' }}"><span class="nav-icon">➕</span> New Application</a>
                <a href="{{ route('student.applications') }}" class="nav-link {{ request()->routeIs('student.applications') ? 'active' : '' }}"><span class="nav-icon">📋</span> My Applications</a>
                <div class="nav-section-label">My Data</div>
                <a href="{{ route('student.documents') }}"    class="nav-link {{ request()->routeIs('student.documents') ? 'active' : '' }}"><span class="nav-icon">📄</span> Documents</a>
                <a href="{{ route('student.vehicles') }}"     class="nav-link {{ request()->routeIs('student.vehicles') ? 'active' : '' }}"><span class="nav-icon">🚗</span> Vehicles</a>
                <a href="{{ route('student.profile') }}"      class="nav-link {{ request()->routeIs('student.profile') ? 'active' : '' }}"><span class="nav-icon">👤</span> Profile</a>
                <a href="{{ route('student.notifications') }}" class="nav-link {{ request()->routeIs('student.notifications') ? 'active' : '' }}"><span class="nav-icon">🔔</span> Notifications</a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <span>🚪</span> Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- ════════════ Main Area ════════════ -->
    <div class="main-wrapper">
        <header class="topbar">
            <div class="topbar-left">
                <div class="topbar-breadcrumb">
                    ParKar &rsaquo; <span>@yield('title', 'Dashboard')</span>
                </div>
            </div>
            <div class="topbar-right">
                @if(Auth::user()->role !== 'admin')
                    <a href="{{ route('student.notifications') }}" class="notif-btn" title="Notifications">
                        🔔
                        @php $unread = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count(); @endphp
                        @if($unread > 0)<span class="notif-badge">{{ $unread > 9 ? '9+' : $unread }}</span>@endif
                    </a>
                @endif
                <div class="topbar-user-chip">
                    <span class="topbar-user-dot"></span>
                    {{ Auth::user()->email }}
                </div>
            </div>
        </header>

        <main class="main-content">
            @if(session('success'))
                <div class="alert alert-success">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">⚠️ {{ session('error') }}</div>
            @endif
            @if(session('error_notice'))
                <div class="alert alert-error">⚠️ {{ session('error_notice') }}</div>
            @endif
            @if(session('info'))
                <div class="alert alert-info">ℹ️ {{ session('info') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <div>
                        @foreach($errors->all() as $e)
                            <div>• {{ $e }}</div>
                        @endforeach
                    </div>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>
