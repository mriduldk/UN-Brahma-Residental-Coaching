<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Help & Support — Upendra Nath Brahma Residential Free Coaching, BTC</title>
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

        /* Custom styled elements */
        .help-hero {
          background: linear-gradient(180deg, var(--white) 0%, rgba(224, 242, 254, 0.6) 100%);
          border-bottom: 1px solid var(--line);
        }

        .contact-card {
          background: var(--white);
          border: 1px solid var(--line);
          border-radius: 24px;
          box-shadow: 0 10px 25px -5px rgba(12, 74, 110, 0.05);
          transition: transform 0.2s ease, border-color 0.2s ease;
        }
        .contact-card:hover {
          transform: translateY(-4px);
          border-color: var(--gold-light);
        }
    </style>
</head>
<body class="antialiased">

    <!-- Header component -->
    <x-portal-header />

    <div class="min-h-screen flex flex-col justify-between">
        <div>
            <!-- Hero Section -->
            <section class="relative py-20 help-hero overflow-hidden">
                <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-sky-200/20 rounded-full blur-[100px] -mr-32 -mt-32"></div>
                <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-indigo-200/10 rounded-full blur-[100px] -ml-24 -mb-24"></div>
                
                <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10 text-center">
                    <h2 class="text-sm font-bold uppercase tracking-[0.3em] mb-4 font-mono" style="color: var(--maroon);">Help & Support</h2>
                    <h1 class="text-5xl md:text-6xl font-extrabold mb-8 leading-tight font-serif" style="color: var(--maroon-dark);">We're here to <span style="color: var(--maroon);">Help You.</span></h1>
                    <p class="text-lg font-medium max-w-2xl mx-auto leading-relaxed italic" style="color: var(--ink-soft);">Find answers to common questions or reach out to our dedicated support team directly.</p>
                </div>
            </section>

            <!-- Contact Info Cards -->
            <section class="py-12 -mt-10 mb-8">
                <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-20">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Phone -->
                        <div class="contact-card p-8 flex flex-col items-center text-center group">
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-sm" style="background: var(--paper-dark); color: var(--maroon);">
                                <i data-lucide="phone" size="28"></i>
                            </div>
                            <h4 class="text-[10px] font-bold uppercase tracking-widest mb-2 font-mono" style="color: var(--ink-soft); opacity: 0.7;">Call Us</h4>
                            <p class="text-xl font-bold" style="color: var(--maroon-dark);">+91 9101672624</p>
                            <p class="text-xs mt-4 font-medium italic" style="color: var(--ink-soft);">Monday - Saturday, 9am - 7pm</p>
                        </div>

                        <!-- Email -->
                        <div class="contact-card p-8 flex flex-col items-center text-center group">
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-sm" style="background: var(--paper-dark); color: var(--maroon);">
                                <i data-lucide="mail" size="28"></i>
                            </div>
                            <h4 class="text-[10px] font-bold uppercase tracking-widest mb-2 font-mono" style="color: var(--ink-soft); opacity: 0.7;">Email Us</h4>
                            <p class="text-xl font-bold underline decoration-sky-300 underline-offset-8 decoration-2" style="color: var(--maroon-dark);">info.jypko@gmail.com</p>
                            <p class="text-xs mt-4 font-medium italic" style="color: var(--ink-soft);">Average response: 2 hours</p>
                        </div>

                        <!-- Location -->
                        <div class="contact-card p-8 flex flex-col items-center text-center group">
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-sm" style="background: var(--paper-dark); color: var(--maroon);">
                                <i data-lucide="map-pin" size="28"></i>
                            </div>
                            <h4 class="text-[10px] font-bold uppercase tracking-widest mb-2 font-mono" style="color: var(--ink-soft); opacity: 0.7;">Office</h4>
                            <p class="text-xl font-bold" style="color: var(--maroon-dark);">Guwahati, Assam, India</p>
                            <p class="text-xs mt-4 font-medium italic" style="color: var(--ink-soft);">Serving Institutes nationwide</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div>
            <!-- Footer Help Link -->
            <section class="py-12 border-t italic" style="border-top-color: var(--line);">
                <div class="max-w-7xl mx-auto px-4 text-center">
                    <a href="{{ route('welcome') }}" class="font-bold text-sm transition-colors flex items-center justify-center gap-2" style="color: var(--maroon);">
                        <i data-lucide="arrow-left" size="16"></i> Return to Homepage
                    </a>
                </div>
            </section>

            <!-- Footer component -->
            <x-portal-footer />
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
