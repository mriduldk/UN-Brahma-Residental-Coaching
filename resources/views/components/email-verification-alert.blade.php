@if(auth()->check() && !auth()->user()->email_verified_at)
<div class="mb-6 group bg-white border border-amber-200 rounded-2xl p-4 shadow-sm hover:shadow-md transition-all duration-300 relative overflow-hidden">
    <!-- Subtle Gradient Ornament -->
    <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-amber-500/5 to-transparent rounded-bl-full -mr-10 -mt-10"></div>
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 shadow-sm border border-amber-100 flex-shrink-0">
                <i data-lucide="mail" size="24" class="animate-bounce-slow"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">Verify Your Email Address</h3>
                <p class="text-sm text-slate-500 font-medium mt-0.5">Please verify your email to ensure account security and receive important updates.</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('verification.notice') }}" 
               class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-amber-200 transition-all hover:-translate-y-0.5 flex items-center gap-2">
                <span>Verify Now</span>
                <i data-lucide="arrow-right" size="16"></i>
            </a>
            <button onclick="this.parentElement.parentElement.parentElement.remove()" class="p-2.5 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-xl transition-colors" title="Dismiss">
                <i data-lucide="x" size="18"></i>
            </button>
        </div>
    </div>
</div>

<style>
    @keyframes bounce-slow {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-3px); }
    }
    .animate-bounce-slow {
        animation: bounce-slow 2s infinite ease-in-out;
    }
</style>
<script>
    // Ensure icons are rendered if added dynamically
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
@endif
