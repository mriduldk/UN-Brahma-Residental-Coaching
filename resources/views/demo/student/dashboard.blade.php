<x-demo-layout>
    <div class="mb-10 text-center md:text-left">
        <h2 class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] mb-2 font-sans">Candidate Portal</h2>
        <h1 class="text-4xl font-black text-slate-900 leading-tight italic">My <span class="text-blue-600">Admissions.</span></h1>
        <p class="text-sm text-slate-400 font-medium italic mt-2">Track your journey across registered institutions.</p>
    </div>

    <!-- Active Applications -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <!-- Application 1 -->
        <div class="bg-white rounded-[48px] border border-slate-100 shadow-2xl shadow-slate-200/50 p-10 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform"></div>
            
            <div class="flex items-center gap-6 mb-10">
                <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-[32px] flex items-center justify-center shadow-inner">
                    <i data-lucide="school" size="32"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-900 italic">Don Bosco Senior Sec</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic mt-1 font-sans">Class XI Science (Session 2025)</p>
                </div>
            </div>

            <div class="p-6 bg-slate-50 rounded-3xl mb-8 border border-slate-100 italic">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-xs font-bold text-slate-400">Application Status:</span>
                    <span class="px-4 py-1.5 bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-widest rounded-full">Provisionally Selected</span>
                </div>
                <div class="flex justify-between items-center text-xs font-bold text-slate-900">
                    <span>Registration ID:</span>
                    <span class="font-black">OS-94285-DB</span>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('demo.student.application-view') }}" class="flex-1 py-4 bg-slate-950 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl text-center hover:bg-blue-600 transition-all shadow-xl active:scale-95 leading-none flex items-center justify-center gap-3">
                    View Details
                    <i data-lucide="arrow-right" size="16"></i>
                </a>
                <a href="{{ route('demo.student.payment') }}" class="w-16 h-16 flex items-center justify-center bg-emerald-600 text-white rounded-2xl hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-600/20 active:scale-95">
                    <i data-lucide="credit-card" size="24"></i>
                </a>
            </div>
        </div>

        <!-- Application 2 -->
        <div class="bg-white rounded-[48px] border border-slate-100 shadow-2xl shadow-slate-200/50 p-10 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-amber-50 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform"></div>
            
            <div class="flex items-center gap-6 mb-10">
                <div class="w-20 h-20 bg-amber-50 text-amber-600 rounded-[32px] flex items-center justify-center shadow-inner">
                    <i data-lucide="flask-conical" size="32"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-slate-900 italic">Akash Academy</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic mt-1 font-sans">Pre-Medical Entrance Test</p>
                </div>
            </div>

            <div class="p-6 bg-slate-50 rounded-3xl mb-8 border border-slate-100 italic">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-xs font-bold text-slate-400">Application Status:</span>
                    <span class="px-4 py-1.5 bg-amber-100 text-amber-700 text-[10px] font-black uppercase tracking-widest rounded-full">Payment Pending</span>
                </div>
                <div class="flex justify-between items-center text-xs font-bold text-slate-900">
                    <span>Registration ID:</span>
                    <span class="font-black">OS-91024-AA</span>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('demo.student.payment') }}" class="flex-1 py-4 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl text-center hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/20 active:scale-95 leading-none flex items-center justify-center gap-3">
                    Pay Register Fee
                    <i data-lucide="wallet" size="16"></i>
                </a>
                <button class="w-16 h-16 flex items-center justify-center bg-slate-50 text-slate-400 rounded-2xl hover:text-rose-500 transition-all border border-slate-100 active:scale-95">
                    <i data-lucide="trash-2" size="24"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Available Institutions CTA -->
    <div class="bg-slate-900 rounded-[48px] p-12 text-white relative overflow-hidden text-center italic">
        <div class="absolute inset-0 bg-blue-600/5 blur-[120px]"></div>
        <div class="relative z-10">
            <h3 class="text-3xl font-black mb-4">Looking for more <span class="text-blue-400">opportunities?</span></h3>
            <p class="text-slate-400 font-medium mb-10 max-w-2xl mx-auto italic">Explore 100+ institutions currently accepting digital applications across Assam and Northeast India.</p>
            <a href="{{ route('demo.student.application-form') }}" class="inline-flex items-center gap-4 px-12 py-5 bg-blue-600 text-white font-black rounded-[24px] uppercase tracking-widest text-xs hover:bg-blue-500 transition-all shadow-2xl shadow-blue-600/20 active:scale-95">
                Apply for New Course
                <i data-lucide="plus" size="18"></i>
            </a>
        </div>
    </div>
</x-demo-layout>
