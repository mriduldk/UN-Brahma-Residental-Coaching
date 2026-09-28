
    <!-- Top Notification Bar -->
    <!-- <div class="bg-gradient-to-r from-orange-600 to-orange-500 text-white text-xs font-bold py-2 px-4 text-center">
        <span class="inline-flex items-center">
            <i data-lucide="bell" size="14" class="mr-2"></i> 
            Admissions for Session 2025-2026 are now OPEN! Closing Date: 15th August 2025.
        </span>
    </div> -->

    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-nav border-b border-slate-200 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('image/logo/logo-em.png') }}" alt="Director of Education, BTC Logo" class="w-6 h-10 group-hover:scale-105 transition-transform duration-300 object-cover">
                    <div class="sm:block">
                        <h1 class="font-extrabold text-xl leading-tight text-slate-900 tracking-tight">Director of Education, <span class="text-blue-600">BTC</span></h1>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Recruitment Portal</p>
                    </div>
                </a>

                <!-- Desktop Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ url('/') }}#home" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors">Home</a>
                    <a href="{{ route('discover.index') }}" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors">Discover Applications</a>
                    <a href="{{ route('pricing') }}" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition-colors">Pricing</a>
                    <a href="{{ url('help') }}" class="px-5 py-2.5 bg-blue-50 text-blue-600 text-sm font-bold rounded-full hover:bg-blue-100 transition-all">Contact Us</a>
                </div>

                <!-- Auth Switcher -->
                <div class="hidden md:flex items-center">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold rounded-full shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                                Dashboard
                            </a>
                        @else
                            <div class="flex p-1 bg-slate-200/50 backdrop-blur-sm rounded-full border border-slate-200/50">
                                <a href="{{ route('login') }}" class="px-5 py-2 text-xs font-bold rounded-full transition-all duration-300 {{ request()->routeIs('login') ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                                    Login
                                </a>
                                <a href="{{ route('register') }}" class="px-5 py-2 text-xs font-bold rounded-full transition-all duration-300 {{ (request()->routeIs('register') && request()->query('role') !== 'administration') ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                                    Join as Student
                                </a>
                                <a href="{{ route('register', ['role' => 'administration']) }}" class="px-5 py-2 text-xs font-bold rounded-full transition-all duration-300 {{ (request()->routeIs('register') && request()->query('role') === 'administration') ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                                    Join as Organization
                                </a>
                            </div>
                        @endauth
                    @endif
                </div>

                <!-- Mobile Menu Button -->
                <button class="md:hidden text-slate-600" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                    <i data-lucide="menu" size="24"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-slate-100">
            <div class="px-4 pt-2 pb-4 space-y-1">
                <a href="{{ url('/') }}#home" class="block px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-50 rounded-md">Home</a>
                <a href="{{ route('discover.index') }}" class="block px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-50 rounded-md">Discover Applications</a>
                <a href="{{ route('pricing') }}" class="block px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-50 rounded-md">Pricing</a>
                <a href="{{ url('help') }}" class="block px-3 py-2 text-base font-bold text-blue-600 hover:bg-blue-50 rounded-md">Contact Us</a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="block mt-4 w-full text-center px-4 py-3 bg-blue-600 text-white font-bold rounded-lg">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="block mt-4 w-full text-center px-4 py-3 border border-slate-200 text-slate-700 font-bold rounded-lg mb-2">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="block w-full text-center px-4 py-3 bg-blue-600 text-white font-bold rounded-lg">{{ __('Student Registration') }}</a>
                            <a href="{{ route('register', ['role' => 'administration']) }}" class="block w-full text-center px-4 py-3 mt-2 border border-blue-200 text-blue-600 font-bold rounded-lg">{{ __('Join as Organization') }}</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </nav>
