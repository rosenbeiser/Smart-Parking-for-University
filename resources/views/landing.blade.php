<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ParKar — AI-Assisted University Parking System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --orange: #F97316; --orange-dark: #EA580C; --orange-light: #FED7AA; --orange-pale: #FFF7ED; }
        body { font-family: 'Outfit', sans-serif; color: #1C1917; }

        /* Nav */
        nav {
            position: sticky; top: 0; z-index: 100;
            background: rgba(255,255,255,.95); backdrop-filter: blur(12px);
            border-bottom: 1px solid #E5E7EB;
            padding: 1rem 5%; display: flex; align-items: center; justify-content: space-between;
        }
        .nav-logo { font-size: 1.5rem; font-weight: 800; color: var(--orange); text-decoration: none; }
        .nav-links { display: flex; align-items: center; gap: 1rem; }
        .nav-links a {
            padding: .5rem 1rem; border-radius: 8px; text-decoration: none; font-weight: 500;
            color: #4B5563; transition: all .2s;
        }
        .nav-links a:hover { color: var(--orange); }
        .nav-links .btn-nav {
            background: var(--orange); color: white; padding: .5rem 1.25rem; border-radius: 8px;
            font-weight: 600; transition: all .2s;
        }
        .nav-links .btn-nav:hover { background: var(--orange-dark); transform: translateY(-1px); }

        /* Hero */
        .hero {
            min-height: 88vh; display: flex; align-items: center;
            background: linear-gradient(135deg, #fff7ed 0%, white 40%, #fff7ed 100%);
            padding: 4rem 5%;
        }
        .hero-inner { max-width: 1200px; margin: 0 auto; width: 100%;
            display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center; }
        .hero-badge {
            display: inline-flex; align-items: center; gap: .5rem;
            background: var(--orange-pale); color: var(--orange-dark); border: 1px solid var(--orange-light);
            padding: .35rem 1rem; border-radius: 20px; font-size: .85rem; font-weight: 600;
            margin-bottom: 1.5rem;
        }
        .hero-title { font-size: 3.5rem; font-weight: 900; line-height: 1.1; color: #1C1917; margin-bottom: 1.25rem; }
        .hero-title span { color: var(--orange); }
        .hero-desc { font-size: 1.1rem; color: #4B5563; line-height: 1.7; margin-bottom: 2.5rem; }
        .hero-btns { display: flex; gap: 1rem; flex-wrap: wrap; }
        .btn-hero-primary {
            padding: .9rem 2rem; background: var(--orange); color: white; border-radius: 10px;
            font-weight: 700; font-size: 1.05rem; text-decoration: none; transition: all .2s;
            display: inline-flex; align-items: center; gap: .5rem;
        }
        .btn-hero-primary:hover { background: var(--orange-dark); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(249,115,22,.35); }
        .btn-hero-outline {
            padding: .9rem 2rem; background: transparent; color: #374151; border: 2px solid #E5E7EB;
            border-radius: 10px; font-weight: 600; font-size: 1.05rem; text-decoration: none; transition: all .2s;
        }
        .btn-hero-outline:hover { border-color: var(--orange); color: var(--orange); }
        .hero-visual {
            position: relative; display: flex; align-items: center; justify-content: center;
        }
        .hero-card {
            background: white; border-radius: 20px; padding: 2rem;
            box-shadow: 0 20px 60px rgba(249,115,22,.15); border: 1px solid var(--orange-light);
            width: 100%; max-width: 360px;
        }
        .hero-card-top { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
        .hero-card-avatar { width: 48px; height: 48px; border-radius: 50%; background: var(--orange-pale); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .hero-card-name { font-weight: 700; font-size: 1rem; }
        .hero-card-id { font-size: .8rem; color: #9CA3AF; }
        .status-pill {
            display: inline-flex; align-items: center; gap: .4rem; padding: .35rem .9rem;
            border-radius: 20px; font-size: .8rem; font-weight: 700;
        }
        .status-approved { background: #ECFDF5; color: #065F46; }
        .status-pending  { background: #FFF7ED; color: var(--orange-dark); }
        .hero-card-detail { background: #F9FAFB; border-radius: 10px; padding: 1rem; font-size: .85rem; color: #4B5563; }
        .hero-card-detail div { display: flex; justify-content: space-between; padding: .3rem 0; border-bottom: 1px solid #F3F4F6; }
        .hero-card-detail div:last-child { border-bottom: none; }
        .hero-card-detail span:first-child { color: #9CA3AF; }
        .hero-card-detail span:last-child { font-weight: 600; color: #1C1917; }

        /* Features */
        .features { padding: 5rem 5%; background: white; }
        .section-label { text-align: center; font-size: .85rem; font-weight: 700; color: var(--orange); letter-spacing: .1em; text-transform: uppercase; margin-bottom: .75rem; }
        .section-title { text-align: center; font-size: 2.5rem; font-weight: 800; color: #1C1917; margin-bottom: 1rem; }
        .section-desc { text-align: center; color: #6B7280; max-width: 560px; margin: 0 auto 3rem; line-height: 1.6; }
        .features-grid { max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: repeat(3,1fr); gap: 2rem; }
        .feature-card {
            padding: 2rem; border-radius: 16px; border: 1px solid #F3F4F6; transition: all .25s;
        }
        .feature-card:hover { border-color: var(--orange-light); box-shadow: 0 8px 32px rgba(249,115,22,.1); transform: translateY(-4px); }
        .feature-icon { font-size: 2.2rem; margin-bottom: 1rem; }
        .feature-title { font-size: 1.1rem; font-weight: 700; color: #1C1917; margin-bottom: .5rem; }
        .feature-desc { color: #6B7280; font-size: .9rem; line-height: 1.6; }

        /* How it works */
        .how { padding: 5rem 5%; background: var(--orange-pale); }
        .steps { max-width: 900px; margin: 0 auto; display: grid; grid-template-columns: repeat(4,1fr); gap: 1.5rem; }
        .step { text-align: center; }
        .step-num {
            width: 52px; height: 52px; background: var(--orange); color: white;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem; font-weight: 800; margin: 0 auto 1rem;
        }
        .step-title { font-weight: 700; margin-bottom: .4rem; }
        .step-desc { font-size: .875rem; color: #6B7280; }

        /* CTA */
        .cta { padding: 5rem 5%; background: linear-gradient(135deg, var(--orange-dark) 0%, var(--orange) 100%); text-align: center; color: white; }
        .cta-title { font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; }
        .cta-desc { font-size: 1.05rem; opacity: .9; margin-bottom: 2.5rem; }
        .btn-cta { display: inline-flex; align-items: center; gap: .5rem; padding: .9rem 2.5rem; background: white; color: var(--orange-dark); border-radius: 10px; font-weight: 700; font-size: 1.05rem; text-decoration: none; transition: all .2s; }
        .btn-cta:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.2); }

        /* Footer */
        footer { padding: 2rem 5%; background: #1C1917; color: #9CA3AF; text-align: center; font-size: .875rem; }
        footer span { color: var(--orange); }

        @media (max-width: 900px) {
            .hero-inner, .features-grid, .steps { grid-template-columns: 1fr; }
            .hero-visual { display: none; }
            .hero-title { font-size: 2.5rem; }
        }
    </style>
</head>
<body>
    <nav>
        <a href="/" class="nav-logo">🚗 ParKar</a>
        <div class="nav-links">
            <a href="{{ route('login') }}">Sign In</a>
            <a href="{{ route('register') }}" class="btn-nav">Get Started →</a>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="hero-inner">
            <div>
                <div class="hero-badge">🤖 AI-Powered Document Verification</div>
                <h1 class="hero-title">University Parking,<br><span>Smarter & Fairer</span></h1>
                <p class="hero-desc">ParKar digitizes semester-based parking access at your university. Apply online, upload documents once, get AI verification, pay via BKash or Nagad, and download your digital permit.</p>
                <div class="hero-btns">
                    <a href="{{ route('register') }}" class="btn-hero-primary">🚀 Apply for Parking</a>
                    <a href="{{ route('login') }}" class="btn-hero-outline">Sign In</a>
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-card">
                    <div class="hero-card-top">
                        <div class="hero-card-avatar">🎓</div>
                        <div>
                            <div class="hero-card-name">Samiul Islam</div>
                            <div class="hero-card-id">ID: 20230104142 • CSE</div>
                        </div>
                        <span class="status-pill status-approved" style="margin-left:auto;">✓ Approved</span>
                    </div>
                    <div class="hero-card-detail">
                        <div><span>Vehicle</span><span>Toyota Corolla</span></div>
                        <div><span>Plate</span><span>DHK-CA-1742</span></div>
                        <div><span>Semester</span><span>Summer 2026</span></div>
                        <div><span>Ticket ID</span><span>PKT-20260707-001</span></div>
                        <div><span>Payment</span><span style="color:#065F46;">✅ bKash Confirmed</span></div>
                    </div>
                    <div style="margin-top:1rem;">
                        <div style="background:var(--orange);color:white;border-radius:8px;padding:.6rem;text-align:center;font-weight:700;font-size:.85rem;">📥 Download Permit PDF</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features">
        <div class="section-label">Features</div>
        <h2 class="section-title">Everything You Need</h2>
        <p class="section-desc">From application to permit — ParKar handles the full parking permission lifecycle digitally.</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🤖</div>
                <div class="feature-title">AI Document Verification</div>
                <div class="feature-desc">Uploaded documents are instantly checked for clarity, validity, and consistency using machine learning.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💳</div>
                <div class="feature-title">BKash & Nagad Payments</div>
                <div class="feature-desc">Pay your parking fee using the most popular mobile banking platforms in Bangladesh — no card required.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📄</div>
                <div class="feature-title">Digital Permit Download</div>
                <div class="feature-desc">Once approved and payment is confirmed, download your official parking permit as a PDF to show at the gate.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔄</div>
                <div class="feature-title">Smart Semester Renewal</div>
                <div class="feature-desc">Returning users can renew in seconds — your existing vehicle data and documents are carried forward automatically.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <div class="feature-title">Admin Control Panel</div>
                <div class="feature-desc">Administrators review applications with AI insights, approve or reject, and confirm payments — all in one dashboard.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔔</div>
                <div class="feature-title">Real-time Notifications</div>
                <div class="feature-desc">Students receive instant notifications at every stage — submission, review, approval, rejection, and payment confirmation.</div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how">
        <div class="section-label">Process</div>
        <h2 class="section-title">How It Works</h2>
        <p class="section-desc">Four simple steps from registration to parking access.</p>
        <div class="steps">
            <div class="step">
                <div class="step-num">1</div>
                <div class="step-title">Register & Apply</div>
                <div class="step-desc">Create an account and fill the parking application with your vehicle and document details.</div>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <div class="step-title">AI Verification</div>
                <div class="step-desc">Your documents are automatically analyzed for quality and validity by our AI system.</div>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <div class="step-title">Admin Review</div>
                <div class="step-desc">A university administrator reviews your application and makes the final approval decision.</div>
            </div>
            <div class="step">
                <div class="step-num">4</div>
                <div class="step-title">Pay & Get Permit</div>
                <div class="step-desc">Pay via BKash or Nagad, and download your official parking permit as a PDF.</div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta">
        <h2 class="cta-title">Ready to Park Smarter?</h2>
        <p class="cta-desc">Join hundreds of students and faculty members using ParKar this semester.</p>
        <a href="{{ route('register') }}" class="btn-cta">🚀 Apply Now — It's Free</a>
    </section>

    <footer>
        <p>© {{ date('Y') }} <span>ParKar</span> — AI-Assisted University Parking Intelligence System &nbsp;|&nbsp; AUST</p>
    </footer>
</body>
</html>
