<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ParKar') — AI Parking System</title>
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
            --gray-50:     #F8FAFC;
            --gray-100:    #F1F5F9;
            --gray-200:    #E2E8F0;
            --gray-400:    #94A3B8;
            --gray-500:    #64748B;
            --gray-600:    #475569;
            --gray-800:    #1E293B;
            --dark:        #0F172A;
            --radius:      14px;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--gray-50);
            min-height: 100vh;
            color: var(--dark);
            -webkit-font-smoothing: antialiased;
        }

        /* ══════════════════════════ LAYOUT ══════════════════════════════ */
        .guest-wrapper {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* ══════════════════════════ HERO PANEL ══════════════════════════ */
        .guest-hero {
            background: var(--dark);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem;
            position: relative;
            overflow: hidden;
        }

        /* Large glowing orbs */
        .guest-hero::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(249,115,22,.18) 0%, transparent 70%);
            top: -150px; right: -150px;
            pointer-events: none;
        }
        .guest-hero::after {
            content: '';
            position: absolute;
            width: 350px; height: 350px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(234,88,12,.15) 0%, transparent 70%);
            bottom: -100px; left: -80px;
            pointer-events: none;
        }

        .hero-floater {
            position: absolute;
            border-radius: 50%;
            background: rgba(249,115,22,.06);
            border: 1px solid rgba(249,115,22,.1);
            animation: floatUp 6s ease-in-out infinite;
        }
        .hero-floater:nth-child(1) { width:80px; height:80px; top:20%; left:10%; animation-delay:0s; }
        .hero-floater:nth-child(2) { width:40px; height:40px; top:60%; left:75%; animation-delay:2s; }
        .hero-floater:nth-child(3) { width:60px; height:60px; top:80%; left:20%; animation-delay:4s; }
        @keyframes floatUp {
            0%, 100% { transform: translateY(0) rotate(0); }
            50%       { transform: translateY(-18px) rotate(5deg); }
        }

        .guest-hero-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 400px;
        }

        .hero-logo-wrap {
            display: inline-flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1.5rem;
        }
        .hero-logo-icon {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, var(--orange), var(--orange-dark));
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.75rem;
            box-shadow: 0 8px 24px rgba(249,115,22,.35);
        }
        .hero-logo-text {
            font-size: 2.75rem;
            font-weight: 900;
            color: white;
            letter-spacing: -1.5px;
        }
        .hero-logo-badge {
            font-size: .6rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--orange), #fb923c);
            color: white;
            padding: .2rem .55rem;
            border-radius: 20px;
            letter-spacing: .08em;
            text-transform: uppercase;
            align-self: flex-start;
        }

        .hero-tagline {
            font-size: 1.1rem;
            color: rgba(255,255,255,.6);
            font-weight: 300;
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }

        .hero-features { list-style: none; text-align: left; }
        .hero-features li {
            padding: .55rem 0;
            font-size: .9rem;
            color: rgba(255,255,255,.75);
            display: flex;
            align-items: center;
            gap: .75rem;
            border-bottom: 1px solid rgba(255,255,255,.05);
        }
        .hero-features li:last-child { border-bottom: none; }
        .hero-check {
            width: 22px; height: 22px;
            background: linear-gradient(135deg, rgba(249,115,22,.2), rgba(249,115,22,.1));
            border: 1px solid rgba(249,115,22,.3);
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: .7rem;
            color: var(--orange);
            flex-shrink: 0;
        }

        /* Stats strip */
        .hero-stats {
            display: flex;
            gap: 2rem;
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .hero-stat-value {
            font-size: 1.6rem;
            font-weight: 900;
            color: white;
            letter-spacing: -1px;
        }
        .hero-stat-label {
            font-size: .72rem;
            color: rgba(255,255,255,.4);
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-top: .1rem;
        }

        /* ══════════════════════════ FORM PANEL ══════════════════════════ */
        .guest-form-area {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            background: white;
        }

        .guest-card {
            width: 100%;
            max-width: 420px;
        }

        .guest-card-title {
            font-size: 1.9rem;
            font-weight: 900;
            color: var(--dark);
            margin-bottom: .3rem;
            letter-spacing: -.5px;
        }
        .guest-card-subtitle {
            color: var(--gray-500);
            font-size: .9rem;
            margin-bottom: 2rem;
        }

        .form-group { margin-bottom: 1.1rem; }
        .form-label {
            display: block;
            font-size: .82rem;
            font-weight: 700;
            color: var(--gray-600);
            margin-bottom: .4rem;
            letter-spacing: .01em;
        }
        .form-control {
            width: 100%;
            padding: .75rem 1rem;
            border: 1.5px solid var(--gray-200);
            border-radius: 9px;
            font-size: .95rem;
            font-family: 'Outfit', sans-serif;
            color: var(--dark);
            background: var(--gray-50);
            transition: border-color .2s, background .2s, box-shadow .2s;
            outline: none;
        }
        .form-control:focus {
            border-color: var(--orange);
            background: white;
            box-shadow: 0 0 0 3px rgba(249,115,22,.12);
        }
        .form-control.is-invalid { border-color: #EF4444; }
        .invalid-feedback { color: #DC2626; font-size: .78rem; margin-top: .3rem; display: block; }

        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
            padding: .8rem 1.5rem;
            border-radius: 9px;
            font-size: .95rem;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            border: none;
            cursor: pointer;
            transition: all .18s;
            text-decoration: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--orange), var(--orange-mid));
            color: white;
            width: 100%;
            box-shadow: 0 4px 14px rgba(249,115,22,.3);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--orange-mid), var(--orange-dark));
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(249,115,22,.4);
        }
        .btn-outline {
            background: transparent;
            border: 1.5px solid var(--gray-200);
            color: var(--gray-800);
        }
        .btn-outline:hover { border-color: var(--orange); color: var(--orange); }

        .divider {
            display: flex; align-items: center; gap: .85rem;
            margin: 1.25rem 0;
            color: var(--gray-400);
            font-size: .8rem;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: var(--gray-200);
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: .875rem;
            color: var(--gray-500);
        }
        .auth-footer a { color: var(--orange); font-weight: 700; text-decoration: none; }
        .auth-footer a:hover { text-decoration: underline; }

        .alert {
            padding: .85rem 1.1rem;
            border-radius: 9px;
            margin-bottom: 1.1rem;
            font-size: .875rem;
            display: flex; align-items: flex-start; gap: .6rem;
        }
        .alert-success { background: #F0FDF4; color: #166534; border: 1px solid #BBF7D0; }
        .alert-error   { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
        .alert-info    { background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; }

        @media (max-width: 768px) {
            .guest-wrapper { grid-template-columns: 1fr; }
            .guest-hero    { display: none; }
            .guest-form-area { padding: 2rem 1.25rem; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="guest-wrapper">
        <!-- ═══ Hero Panel ═══ -->
        <div class="guest-hero">
            <div class="hero-floater"></div>
            <div class="hero-floater"></div>
            <div class="hero-floater"></div>

            <div class="guest-hero-content">
                <div class="hero-logo-wrap">
                    <div class="hero-logo-icon">🚗</div>
                    <div>
                        <div class="hero-logo-text">ParKar</div>
                    </div>
                    <span class="hero-logo-badge">AI</span>
                </div>

                <p class="hero-tagline">
                    Smart university parking management.<br>Apply, pay, and track — all in one place.
                </p>

                <ul class="hero-features">
                    <li><span class="hero-check">✓</span> Semester-based parking access management</li>
                    <li><span class="hero-check">✓</span> AI-powered document verification</li>
                    <li><span class="hero-check">✓</span> BKash &amp; Nagad payment support</li>
                    <li><span class="hero-check">✓</span> Instant digital parking permit</li>
                    <li><span class="hero-check">✓</span> Real-time application tracking</li>
                </ul>

                <div class="hero-stats">
                    <div>
                        <div class="hero-stat-value">AI</div>
                        <div class="hero-stat-label">Verified Docs</div>
                    </div>
                    <div>
                        <div class="hero-stat-value">100%</div>
                        <div class="hero-stat-label">Digital Process</div>
                    </div>
                    <div>
                        <div class="hero-stat-value">24/7</div>
                        <div class="hero-stat-label">Online Access</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ Form Panel ═══ -->
        <div class="guest-form-area">
            <div class="guest-card">
                @if(session('success'))
                    <div class="alert alert-success">✅ {{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error">⚠️ {{ session('error') }}</div>
                @endif
                @if(session('info'))
                    <div class="alert alert-info">ℹ️ {{ session('info') }}</div>
                @endif
                @if(session('debug_otp'))
                    <div class="alert alert-info">🔑 {{ session('debug_otp') }}</div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
