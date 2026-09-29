<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Privacy Policy — Upendra Nath Brahma Residential Free Coaching, BTC</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo/logo-em.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Serif+4:ital,opsz,wght@0,8..60,300;0,8..60,400;0,8..60,600;0,8..60,700;1,8..60,400;1,8..60,600&family=IBM+Plex+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN as requested) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
          --maroon:       #0284c7; /* Primary Sky Blue */
          --maroon-dark:  #0c4a6e; /* Deep Ocean Navy */
          --forest:       #0e7490; /* Cyan-700 */
          --gold:         #0ea5e9; /* Sky Blue */
          --gold-light:   #38bdf8; /* Lighter Sky Blue */
          --paper:        #f0f9ff; /* Soft ice-blue background */
          --paper-dark:   #e0f2fe; /* Light ice-blue background for tables/cards */
          --ink:          #0c4a6e; /* Dark Ocean Navy text */
          --ink-soft:     #4b6a8a; /* Slate Blue text */
          --line:         #bae6fd; /* Soft sky blue border color */
          --white:        #ffffff; /* Clean White */
          /* Adapted Aronai palette for light blue theme */
          --ar-red:       #0ea5e9;
          --ar-black:     #0c4a6e;
          --ar-yellow:    #38bdf8;
          --ar-green:     #7dd3fc;
          --ar-cream:     #eff6ff;
        }

        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        
        body {
          background: var(--paper);
          color: var(--ink);
          font-family: 'IBM Plex Sans', sans-serif;
          line-height: 1.6;
        }

        .font-serif {
          font-family: 'Source Serif 4', serif;
        }

        .font-mono {
          font-family: 'JetBrains Mono', monospace;
        }

        /* Custom scrollbar for sidebar */
        .no-scrollbar::-webkit-scrollbar {
          display: none;
        }
        .no-scrollbar {
          -ms-overflow-style: none;
          scrollbar-width: none;
        }

        /* Custom styled elements */
        .privacy-hero {
          background: linear-gradient(180deg, var(--white) 0%, rgba(224, 242, 254, 0.6) 100%);
          border-bottom: 1px solid var(--line);
        }

        .sidebar-nav-link {
          color: var(--ink-soft);
          border: 1px solid transparent;
          transition: all 0.2s ease;
        }
        .sidebar-nav-link i {
          color: var(--ink-soft);
          opacity: 0.7;
          transition: color 0.2s ease, opacity 0.2s ease;
        }
        .sidebar-nav-link:hover, .sidebar-nav-link.active {
          color: var(--maroon);
          background-color: var(--white);
          box-shadow: 0 4px 6px -1px rgba(12, 74, 110, 0.05);
          border-color: var(--line);
        }
        .sidebar-nav-link:hover i, .sidebar-nav-link.active i {
          color: var(--maroon);
          opacity: 1;
        }

        .help-card {
          background: var(--maroon);
          border-top: 4px solid var(--maroon-dark);
          position: relative;
          overflow: hidden;
        }
        .help-card::after {
          content: '';
          position: absolute; bottom: 0; left: 0; right: 0; height: 4px;
          background: repeating-linear-gradient(90deg,
            var(--ar-red) 0 6px, var(--ar-yellow) 6px 12px,
            var(--ar-green) 12px 18px, var(--ar-black) 18px 24px);
        }

        .policy-card {
          background: var(--white);
          border: 1px solid var(--line);
          border-radius: 16px;
          box-shadow: 0 4px 10px rgba(12, 74, 110, 0.03);
        }

        .accent-box {
          background: var(--paper-dark);
          border: 1px solid var(--line);
          border-radius: 12px;
        }
        
        .dark-accent-box {
          background: var(--maroon-dark);
          border-radius: 12px;
        }

        .refund-box {
          background: rgba(2, 132, 199, 0.05);
          border: 1px solid var(--line);
          border-radius: 16px;
        }
    </style>
</head>
<body class="antialiased">

    <!-- Header component -->
    <x-portal-header />

    <div class="min-h-screen">
        <!-- Hero Section -->
        <section class="relative py-20 privacy-hero overflow-hidden">
            <div class="absolute top-0 left-0 w-[500px] h-[500px] bg-sky-200/20 rounded-full blur-[100px] -ml-32 -mt-32"></div>
            
            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                <div class="max-w-3xl">
                    <h2 class="text-sm font-bold uppercase tracking-[0.3em] mb-4 font-mono" style="color: var(--maroon);">Legal & Trust</h2>
                    <h1 class="text-5xl md:text-6xl font-extrabold mb-8 leading-tight font-serif" style="color: var(--maroon-dark);">Privacy <span style="color: var(--maroon);">Policy.</span></h1>
                    <p class="text-lg font-medium leading-relaxed italic" style="color: var(--ink-soft);">At Director of Education, BTC, we are committed to protecting the integrity and confidentiality of your institutional and personal data. This policy outlines how we handle data across our entire ecosystem.</p>
                    <p class="mt-6 text-xs font-bold uppercase tracking-widest font-mono" style="color: var(--ink-soft); opacity: 0.7;">Last updated: {{ date('F d, Y') }}</p>
                </div>
            </div>
        </section>

        <!-- Main Content Section -->
        <section class="py-20 relative">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                    
                    <!-- Sidebar Navigation -->
                    <div class="lg:col-span-4 lg:sticky lg:top-32 h-fit mb-12 lg:mb-0">
                        <div class="space-y-1 p-2 rounded-[24px] border flex lg:flex-col overflow-x-auto lg:overflow-visible no-scrollbar" style="background: var(--paper-dark); border-color: var(--line);">
                            <a href="#introduction" class="sidebar-nav-link active flex shrink-0 items-center gap-3 px-6 py-4 text-xs lg:text-sm font-bold rounded-2xl transition-all group border-b last:border-0" style="border-bottom-color: rgba(2, 132, 199, 0.1);">
                                <i data-lucide="info" size="18" class="hidden lg:block"></i> Intro
                            </a>
                            <a href="#data-collection" class="sidebar-nav-link flex shrink-0 items-center gap-3 px-6 py-4 text-xs lg:text-sm font-bold rounded-2xl transition-all group border-b last:border-0" style="border-bottom-color: rgba(2, 132, 199, 0.1);">
                                <i data-lucide="database" size="18" class="hidden lg:block"></i> Data
                            </a>
                            <a href="#institutional-data" class="sidebar-nav-link flex shrink-0 items-center gap-3 px-6 py-4 text-xs lg:text-sm font-bold rounded-2xl transition-all group border-b last:border-0" style="border-bottom-color: rgba(2, 132, 199, 0.1);">
                                <i data-lucide="building" size="18" class="hidden lg:block"></i> Institute
                            </a>
                            <a href="#payment-security" class="sidebar-nav-link flex shrink-0 items-center gap-3 px-6 py-4 text-xs lg:text-sm font-bold rounded-2xl transition-all group border-b last:border-0" style="border-bottom-color: rgba(2, 132, 199, 0.1);">
                                <i data-lucide="shield-check" size="18" class="hidden lg:block"></i> Security
                            </a>
                            <a href="#user-rights" class="sidebar-nav-link flex shrink-0 items-center gap-3 px-6 py-4 text-xs lg:text-sm font-bold rounded-2xl transition-all group">
                                <i data-lucide="user-check" size="18" class="hidden lg:block"></i> Rights
                            </a>
                        </div>
                        
                        <div class="mt-8 p-8 rounded-[24px] text-white help-card shadow-lg">
                            <h4 class="font-extrabold text-lg mb-2 font-serif">Need Clarification?</h4>
                            <p class="text-sky-100 text-xs mb-6 font-medium">Have questions about how we handle your specific data?</p>
                            <a href="{{ route('help') }}" class="inline-block px-6 py-3 bg-white font-bold rounded-xl text-xs hover:bg-sky-50 transition-colors" style="color: var(--maroon);">Contact Support</a>
                        </div>
                    </div>

                    <!-- Policy Content -->
                    <div class="lg:col-span-8 space-y-16">
                        <!-- Introduction -->
                        <div id="introduction" class="policy-card p-8 md:p-10">
                            <h3 class="text-2xl font-bold font-serif mb-6" style="color: var(--maroon-dark);">1. Introduction</h3>
                            <div class="font-medium leading-relaxed italic" style="color: var(--ink-soft);">
                                <p>Director of Education, BTC operates as an "Operating System for Institutes," acting as a data processor for educational entities and a data controller for the primary platform services. Our privacy-first approach ensures that your data is never sold or used for unauthorized advertising.</p>
                            </div>
                        </div>

                        <!-- Data Collection -->
                        <div id="data-collection" class="policy-card p-8 md:p-10">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm" style="background: var(--paper-dark); color: var(--maroon);">
                                    <i data-lucide="database" size="24"></i>
                                </div>
                                <h3 class="text-2xl font-bold font-serif" style="color: var(--maroon-dark);">2. Data Collection</h3>
                            </div>
                            <div class="space-y-6 font-medium leading-relaxed" style="color: var(--ink-soft);">
                                <p>We collect information through multiple channels:</p>
                                <ul class="space-y-4 list-none pl-0">
                                    <li class="flex gap-4">
                                        <i data-lucide="check-circle" size="20" class="shrink-0 mt-1" style="color: var(--maroon);"></i>
                                        <span><strong style="color: var(--ink);">Account Data:</strong> Names, emails, and contact numbers provided during institute registration or student profile creation.</span>
                                    </li>
                                    <li class="flex gap-4">
                                        <i data-lucide="check-circle" size="20" class="shrink-0 mt-1" style="color: var(--maroon);"></i>
                                        <span><strong style="color: var(--ink);">Academic Records:</strong> Course enrollments, attendance logs, and MCQ exam performance metrics.</span>
                                    </li>
                                    <li class="flex gap-4">
                                        <i data-lucide="check-circle" size="20" class="shrink-0 mt-1" style="color: var(--maroon);"></i>
                                        <span><strong style="color: var(--ink);">Portal Data:</strong> Documents uploaded for admission forms and previous year question practiced (PYQ) progress.</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Institutional Data -->
                        <div id="institutional-data" class="refund-box p-8 md:p-10">
                            <h3 class="text-2xl font-bold font-serif mb-6" style="color: var(--maroon-dark);">3. Institutional Sovereignty</h3>
                            <p class="font-medium leading-relaxed mb-6" style="color: var(--ink-soft);">Every institute using Director of Education, BTC retains full ownership of their dedicated database partition. We do not access student or teacher records for institutional analytics without explicit permission for support purposes.</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-white p-6 rounded-3xl border shadow-sm" style="border-color: var(--line);">
                                    <h4 class="font-bold mb-2 font-serif" style="color: var(--maroon-dark);">Teacher Management</h4>
                                    <p class="text-xs italic font-medium leading-relaxed" style="color: var(--ink-soft);">Faculty data is encrypted and accessible only by the assigned Institute administrators.</p>
                                </div>
                                <div class="bg-white p-6 rounded-3xl border shadow-sm" style="border-color: var(--line);">
                                    <h4 class="font-bold mb-2 font-serif" style="color: var(--maroon-dark);">Result Integrity</h4>
                                    <p class="text-xs italic font-medium leading-relaxed" style="color: var(--ink-soft);">Automated performance tracking results are stored securely and never shared with external third-party rankers.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Security -->
                        <div id="payment-security" class="policy-card p-8 md:p-10">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm" style="background: var(--paper-dark); color: var(--maroon);">
                                    <i data-lucide="shield-check" size="24"></i>
                                </div>
                                <h3 class="text-2xl font-bold font-serif" style="color: var(--maroon-dark);">4. Payment Security</h3>
                            </div>
                            <div class="dark-accent-box p-8 md:p-10 text-white relative overflow-hidden">
                                <div class="absolute inset-0 opacity-10 blur-3xl bg-[radial-gradient(circle_at_50%_0%,#3b82f6,transparent)]"></div>
                                <div class="relative z-10">
                                    <p class="text-lg font-medium leading-relaxed italic mb-8" style="color: var(--paper);">We prioritize your financial safety. All payment processing is handled through PCI DSS compliant gateways.</p>
                                    <ul class="space-y-4">
                                        <li class="flex items-center gap-4 text-blue-200">
                                            <i data-lucide="check" size="20" style="color: var(--gold-light);"></i>
                                            <span class="text-sm font-bold uppercase tracking-widest" style="color: var(--paper);">No Card Data Stored on Our Servers</span>
                                        </li>
                                        <li class="flex items-center gap-4 text-blue-200">
                                            <i data-lucide="check" size="20" style="color: var(--gold-light);"></i>
                                            <span class="text-sm font-bold uppercase tracking-widest" style="color: var(--paper);">Encrypted Razorpay Tokenization</span>
                                        </li>
                                        <li class="flex items-center gap-4 text-blue-200">
                                            <i data-lucide="check" size="20" style="color: var(--gold-light);"></i>
                                            <span class="text-sm font-bold uppercase tracking-widest" style="color: var(--paper);">Secure 256-bit SSL Transmission</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- User Rights -->
                        <div id="user-rights" class="policy-card p-8 md:p-10">
                            <h3 class="text-2xl font-bold font-serif mb-6" style="color: var(--maroon-dark);">5. Your Digital Rights</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <section>
                                    <h4 class="text-sm font-bold uppercase tracking-widest mb-3 font-mono" style="color: var(--maroon);">Right to Access</h4>
                                    <p class="text-sm font-medium leading-relaxed italic" style="color: var(--ink-soft);">You can request a copy of all data associated with your profile or institute at any time via the Help Center.</p>
                                </section>
                                <section>
                                    <h4 class="text-sm font-bold uppercase tracking-widest mb-3 font-mono" style="color: var(--maroon);">Right to Deletion</h4>
                                    <p class="text-sm font-medium leading-relaxed italic" style="color: var(--ink-soft);">Users can request full account deletion, which permanently scrubs all personal identifiers from our production databases within 30 days.</p>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer Help Link -->
        <section class="py-12 border-t italic" style="border-top-color: var(--line);">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <a href="{{ route('welcome') }}" class="font-bold text-sm transition-colors flex items-center justify-center gap-2" style="color: var(--maroon);">
                    <i data-lucide="arrow-left" size="16"></i> Return to Homepage
                </a>
            </div>
        </section>
    </div>

    <!-- Footer component -->
    <x-portal-footer />

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Scroll spy for sidebar links
            const sections = document.querySelectorAll('div[id]');
            const navLinks = document.querySelectorAll('.sidebar-nav-link');
            
            window.addEventListener('scroll', () => {
                let current = '';
                sections.forEach(s => {
                    if (window.scrollY >= s.offsetTop - 160) {
                        current = s.id;
                    }
                });
                navLinks.forEach(a => {
                    a.classList.remove('active');
                    if (a.getAttribute('href') === '#' + current) {
                        a.classList.add('active');
                    }
                });
            }, { passive: true });
        });
    </script>
</body>
</html>
