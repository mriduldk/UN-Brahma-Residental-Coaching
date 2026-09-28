<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terms of Service — Upendra Nath Brahma Residential Free Coaching, BTC</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo/logo.png') }}">

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
        .terms-hero {
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
        <section class="relative py-20 terms-hero overflow-hidden">
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-sky-200/20 rounded-full blur-[100px] -mr-32 -mt-32"></div>
            
            <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
                <div class="max-w-3xl">
                    <h2 class="text-sm font-bold uppercase tracking-[0.3em] mb-4 font-mono" style="color: var(--maroon);">Legal Framework</h2>
                    <h1 class="text-5xl md:text-6xl font-extrabold mb-8 leading-tight font-serif" style="color: var(--maroon-dark);">Terms of <span style="color: var(--maroon);">Service.</span></h1>
                    <p class="text-lg font-medium leading-relaxed italic" style="color: var(--ink-soft);">These terms govern your use of the Director of Education, BTC platform. By choosing us as your institutional partner, you agree to the conditions outlined below.</p>
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
                            <a href="#acceptance" class="sidebar-nav-link active flex shrink-0 items-center gap-3 px-6 py-4 text-xs lg:text-sm font-bold rounded-2xl transition-all group border-b last:border-0" style="border-bottom-color: rgba(2, 132, 199, 0.1);">
                                <i data-lucide="info" size="18" class="hidden lg:block"></i> Acceptance
                            </a>
                            <a href="#usage" class="sidebar-nav-link flex shrink-0 items-center gap-3 px-6 py-4 text-xs lg:text-sm font-bold rounded-2xl transition-all group border-b last:border-0" style="border-bottom-color: rgba(2, 132, 199, 0.1);">
                                <i data-lucide="user" size="18" class="hidden lg:block"></i> Usage
                            </a>
                            <a href="#financials" class="sidebar-nav-link flex shrink-0 items-center gap-3 px-6 py-4 text-xs lg:text-sm font-bold rounded-2xl transition-all group border-b last:border-0" style="border-bottom-color: rgba(2, 132, 199, 0.1);">
                                <i data-lucide="credit-card" size="18" class="hidden lg:block"></i> Finance
                            </a>
                            <a href="#refunds" class="sidebar-nav-link flex shrink-0 items-center gap-3 px-6 py-4 text-xs lg:text-sm font-bold rounded-2xl transition-all group border-b last:border-0" style="border-bottom-color: rgba(2, 132, 199, 0.1);">
                                <i data-lucide="rotate-ccw" size="18" class="hidden lg:block"></i> Refunds
                            </a>
                            <a href="#liability" class="sidebar-nav-link flex shrink-0 items-center gap-3 px-6 py-4 text-xs lg:text-sm font-bold rounded-2xl transition-all group">
                                <i data-lucide="alert-triangle" size="18" class="hidden lg:block"></i> Liability
                            </a>
                        </div>

                        <div class="mt-8 p-8 rounded-[24px] text-white help-card shadow-lg">
                            <h4 class="font-extrabold text-lg mb-2 font-serif">Need Clarification?</h4>
                            <p class="text-sky-100 text-xs mb-6 font-medium">Have questions about our terms or specific policies?</p>
                            <a href="{{ route('help') }}" class="inline-block px-6 py-3 bg-white font-bold rounded-xl text-xs hover:bg-sky-50 transition-colors" style="color: var(--maroon);">Contact Support</a>
                        </div>
                    </div>

                    <!-- Policy Content -->
                    <div class="lg:col-span-8 space-y-16">
                        <!-- Acceptance -->
                        <div id="acceptance" class="policy-card p-8 md:p-10">
                            <h3 class="text-2xl font-bold font-serif mb-6" style="color: var(--maroon-dark);">1. Acceptance of Terms</h3>
                            <div class="font-medium leading-relaxed italic" style="color: var(--ink-soft);">
                                <p>By accessing the Director of Education, BTC platform, either as an Institute Administrator, Student, or Faculty, you agree to comply with these terms. If you are registering an institute, you represent that you have the legal authority to bind that entity to these conditions.</p>
                            </div>
                        </div>

                        <!-- Platform Usage -->
                        <div id="usage" class="policy-card p-8 md:p-10">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm" style="background: var(--paper-dark); color: var(--maroon);">
                                    <i data-lucide="user" size="24"></i>
                                </div>
                                <h3 class="text-2xl font-bold font-serif" style="color: var(--maroon-dark);">2. Platform Usage</h3>
                            </div>
                            <div class="space-y-6">
                                <div class="accent-box p-6 md:p-8">
                                    <h4 class="text-xs font-bold uppercase tracking-widest mb-3 font-mono" style="color: var(--maroon);">Institutional Responsibility</h4>
                                    <p class="font-medium leading-relaxed italic" style="color: var(--ink);">Institutes are responsible for the accuracy of information provided in admission forms, course details, and fee structures. Director of Education, BTC acts solely as the technology provider and is not liable for data entry errors by the institute.</p>
                                </div>
                                <div class="accent-box p-6 md:p-8">
                                    <h4 class="text-xs font-bold uppercase tracking-widest mb-3 font-mono" style="color: var(--maroon);">Account Security</h4>
                                    <p class="font-medium leading-relaxed italic" style="color: var(--ink);">All users are responsible for maintaining the confidentiality of their login credentials. Any activity occurring under an institute's master account is considered the authorized action of that institute.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Terms -->
                        <div id="financials" class="policy-card p-8 md:p-10">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm" style="background: var(--paper-dark); color: var(--maroon);">
                                    <i data-lucide="credit-card" size="24"></i>
                                </div>
                                <h3 class="text-2xl font-bold font-serif" style="color: var(--maroon-dark);">3. Financial Terms</h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div class="accent-box p-6 md:p-8 bg-white shadow-sm">
                                    <h4 class="font-bold mb-4 font-serif" style="color: var(--maroon-dark);">Per Form Charges</h4>
                                    <ul class="space-y-3 text-xs font-bold italic uppercase tracking-widest" style="color: var(--ink-soft);">
                                        <li class="flex items-center gap-2" style="color: var(--forest);"><i data-lucide="check" size="14"></i> 1st Form: FREE</li>
                                        <li class="flex items-center gap-2"><i data-lucide="arrow-right" size="14"></i> Additional: ₹ 2,000/form</li>
                                        <li class="flex items-center gap-2"><i data-lucide="plus" size="14"></i> Registration: +₹ 1,000</li>
                                        <li class="flex items-center gap-2"><i data-lucide="plus" size="14"></i> Admission: +₹ 1,500</li>
                                    </ul>
                                </div>
                                <div class="accent-box p-6 md:p-8 bg-white shadow-sm">
                                    <h4 class="font-bold mb-4 font-serif" style="color: var(--maroon-dark);">Subscriptions</h4>
                                    <p class="text-sm font-medium italic leading-relaxed mb-4" style="color: var(--ink-soft);">Monthly fees are billed in advance. Annual payments receive a 10% discount from the base monthly rate.</p>
                                    <div class="text-[10px] font-bold uppercase tracking-widest" style="color: var(--maroon);">Auto-Renewal and Cancellation</div>
                                </div>
                            </div>
                            <div class="dark-accent-box p-6 md:p-8 text-white">
                                <h4 class="text-xs font-bold uppercase tracking-widest mb-3 font-mono" style="color: var(--gold-light);">Payment Processing Fees</h4>
                                <p class="text-sm font-medium leading-relaxed italic" style="color: var(--paper);">Using the Director of Education, BTC integrated gateway incurs a 4% total deduction (2% PG fee + 2% processing/handling fee) on every successful transaction.</p>
                            </div>
                        </div>

                        <!-- Refund Policy -->
                        <div id="refunds" class="refund-box p-8 md:p-10">
                            <h3 class="text-2xl font-bold font-serif mb-6" style="color: var(--maroon-dark);">4. Refund Policy</h3>
                            <div class="space-y-4 font-medium leading-relaxed" style="color: var(--ink);">
                                <p><strong>Student Fees:</strong> All admission, registration, and examination fees collected online through the platform are non-refundable once the payment is successfully completed. In the event of duplicate or failed transactions where the amount is deducted but the application is not registered, the transaction will be audited and the extra amount will be refunded within 7–10 working days.</p>
                            </div>
                        </div>

                        <!-- Liability -->
                        <div id="liability" class="policy-card p-8 md:p-10">
                            <h3 class="text-2xl font-bold font-serif mb-6" style="color: var(--maroon-dark);">5. Limitation of Liability</h3>
                            <div class="font-medium leading-relaxed italic" style="color: var(--ink-soft);">
                                <p>Director of Education, BTC and its affiliates shall not be liable for any indirect, incidental, or consequential damages resulting from platform downtime, data loss, or system errors. Our total liability for any claim shall not exceed the amount paid by the user for the service in the 3 months preceding the claim.</p>
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
