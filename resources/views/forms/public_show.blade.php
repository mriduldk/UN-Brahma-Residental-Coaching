<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $form->application_name }} - {{ $form->institution->institution_name }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="bg-[#F8FAFC] antialiased text-slate-900">
    <div class="min-h-screen flex flex-col items-center justify-center p-4 md:p-8 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-blue-50/50 via-transparent to-purple-50/50">
        
        <!-- Organization Branding -->
        <div class="mb-12 text-center">
            @if($form->institution->organization->logo)
                <img src="{{ asset('storage/' . $form->institution->organization->logo) }}" alt="{{ $form->institution->organization->organization_name }}" class="h-16 mx-auto mb-4 object-contain">
            @endif
            <h2 class="text-sm font-black uppercase tracking-[0.2em] text-slate-400 mb-1">{{ $form->institution->organization->organization_name }}</h2>
            <div class="h-1 w-12 bg-blue-600 mx-auto rounded-full"></div>
        </div>

        <div class="max-w-4xl w-full grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Main Content -->
            <div class="lg:col-span-12">
                <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-slate-100 flex flex-col md:flex-row">
                    
                    <!-- Left Strip: Institute Info -->
                    <div class="md:w-1/3 bg-slate-900 text-white p-8 md:p-12 flex flex-col justify-between relative overflow-hidden">
                        <!-- Decorative background element -->
                        <div class="absolute -top-12 -left-12 w-48 h-48 bg-blue-500/10 rounded-full blur-3xl"></div>
                        <div class="absolute -bottom-12 -right-12 w-48 h-48 bg-purple-500/10 rounded-full blur-3xl"></div>
                        
                        <div class="relative z-10">
                            <div class="w-16 h-16 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center mb-8 border border-white/10">
                                <i data-lucide="building-2" class="text-blue-400" size="32"></i>
                            </div>
                            <h3 class="text-2xl font-bold leading-tight mb-6">
                                {{ $form->institution->institution_name }}
                            </h3>
                            
                            <div class="space-y-6 text-sm">
                                <div class="flex items-start gap-4 text-slate-400 group cursor-default">
                                    <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center shrink-0 group-hover:bg-blue-500/20 transition-colors">
                                        <i data-lucide="map-pin" size="14"></i>
                                    </div>
                                    <p>{{ $form->institution->address }}</p>
                                </div>
                                <div class="flex items-center gap-4 text-slate-400 group cursor-default">
                                    <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center shrink-0 group-hover:bg-blue-500/20 transition-colors">
                                        <i data-lucide="phone" size="14"></i>
                                    </div>
                                    <p>{{ $form->institution->contact_number }}</p>
                                </div>
                                <div class="flex items-center gap-4 text-slate-400 group cursor-default">
                                    <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center shrink-0 group-hover:bg-blue-500/20 transition-colors">
                                        <i data-lucide="mail" size="14"></i>
                                    </div>
                                    <p>{{ $form->institution->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-12 pt-8 border-t border-white/5 relative z-10">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-2">Institution Code</p>
                            <span class="px-3 py-1 bg-white/5 rounded-lg text-blue-400 font-mono text-sm border border-white/5">
                                {{ $form->institution->institution_code }}
                            </span>
                        </div>
                    </div>

                    <!-- Right Content: Form Details -->
                    <div class="md:w-2/3 p-8 md:p-12">
                        <div class="flex flex-wrap items-center gap-3 mb-6">
                            <span class="px-4 py-1.5 bg-blue-50 text-blue-600 rounded-full text-xs font-bold uppercase tracking-wider">
                                Admission {{ $form->session }}
                            </span>
                            <span class="px-4 py-1.5 bg-slate-100 text-slate-600 rounded-full text-xs font-bold uppercase tracking-wider">
                                Code: {{ $form->application_code }}
                            </span>
                        </div>

                        <h1 class="text-4xl font-extrabold text-slate-900 mb-4 tracking-tight">
                            {{ $form->application_name }}
                        </h1>

                        <p class="text-slate-500 text-lg leading-relaxed mb-8">
                            {{ $form->application_description }}
                        </p>

                        <!-- Highlights Grid -->
                        <div class="grid grid-cols-2 gap-4 mb-10">
                            <div class="p-6 rounded-3xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:bg-white transition-all group">
                                <div class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 mb-4 group-hover:text-blue-600 group-hover:bg-blue-50 transition-colors">
                                    <i data-lucide="credit-card" size="20"></i>
                                </div>
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Registration Fee</span>
                                <span class="text-2xl font-bold text-slate-800">
                                    {{ $form->is_application_fee_required ? '₹' . number_format($form->application_fee, 0) : 'FREE' }}
                                </span>
                            </div>
                            <div class="p-6 rounded-3xl bg-slate-50 border border-slate-100 hover:border-blue-200 hover:bg-white transition-all group">
                                <div class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 mb-4 group-hover:text-purple-600 group-hover:bg-purple-50 transition-colors">
                                    <i data-lucide="calendar-days" size="20"></i>
                                </div>
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Deadline</span>
                                <span class="text-2xl font-bold text-slate-800 {{ $form->isDeadlinePassed() ? 'text-red-500' : '' }}">
                                    {{ $form->deadline ? $form->deadline->format('d M, Y') : 'No Deadline' }}
                                    @if($form->deadline && $form->deadline->isToday())
                                        <span class="text-xs text-amber-600 font-semibold block mt-0.5">(Closes Today 11:59 PM)</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- CTA Section -->
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            @if($form->isDeadlinePassed())
                                <div class="w-full flex items-center justify-center gap-3 py-4 bg-red-50 text-red-600 rounded-2xl border border-red-100 font-bold">
                                    <i data-lucide="alert-circle" size="20"></i>
                                    Application Period Ended
                                </div>
                            @else
                                <a href="{{ route('student.application.form', ['form_id' => $form->id]) }}" class="w-full sm:w-auto px-10 py-5 bg-blue-600 text-white rounded-[1.5rem] font-bold text-lg hover:bg-blue-700 shadow-[0_15px_30px_rgba(37,99,235,0.3)] hover:shadow-[0_15px_30px_rgba(37,99,235,0.5)] transition-all hover:-translate-y-1 flex items-center justify-center group/btn">
                                    <span>Apply Now</span>
                                    <i data-lucide="arrow-right" class="ml-3 group-hover/btn:translate-x-1 transition-transform" size="24"></i>
                                </a>
                            @endif
                            @if($form->is_application_fee_required)
                                <p class="text-[10px] text-slate-400 max-w-[200px] leading-tight text-center sm:text-left">
                                    * Registration fee is required to submit your application and is non-refundable.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer/Helper Section -->
            <div class="lg:col-span-12 text-center text-slate-400 text-xs py-8">
                <p>&copy; {{ date('Y') }} Director of Education, BTC. All rights reserved.</p>
                <div class="mt-4 flex items-center justify-center gap-4 font-medium">
                    <a href="{{ route('terms') }}" class="hover:text-blue-500 transition-colors">Terms of Use</a>
                    <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                    <a href="{{ route('privacy') }}" class="hover:text-blue-500 transition-colors">Privacy Policy</a>
                    <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                    <a href="{{ route('help') }}" class="hover:text-blue-500 transition-colors">Help Center</a>
                </div>
            </div>

        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
