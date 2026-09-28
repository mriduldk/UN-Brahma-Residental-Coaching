<x-demo-layout>
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
        <div>
            <h2 class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] mb-2 font-sans">Ecosystem Management</h2>
            <h1 class="text-4xl font-black text-slate-900 leading-tight italic">Partner <span class="text-blue-600">Institutions.</span></h1>
        </div>
        <button class="flex items-center gap-3 px-8 py-4 bg-blue-600 text-white font-black rounded-[24px] uppercase tracking-widest text-[10px] hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/20 active:scale-95 group">
            <i data-lucide="plus-circle" size="18" class="group-hover:rotate-90 transition-transform"></i>
            Register New Institute
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-xl shadow-slate-200/50 mb-10 flex flex-wrap items-center gap-6">
        <div class="flex-1 min-w-[300px] relative group">
            <i data-lucide="search" size="18" class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors"></i>
            <input type="text" placeholder="Search by name, location or principal..." 
                   class="w-full pl-14 pr-6 py-4 bg-slate-50 border-transparent rounded-2xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium italic">
        </div>
        
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4">Type:</span>
            <div class="flex bg-slate-100 p-1.5 rounded-2xl">
                <button class="px-6 py-2 bg-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-sm text-blue-600">All</button>
                <button class="px-6 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl text-slate-500 hover:text-slate-700">School</button>
                <button class="px-6 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl text-slate-500 hover:text-slate-700">Coaching</button>
            </div>
        </div>
    </div>

    <!-- Institute List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Institute 1 -->
        <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl shadow-slate-200/50 p-8 hover:-translate-y-2 transition-all duration-300 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/5 rounded-full -mr-16 -mt-16"></div>
            
            <div class="flex items-center gap-4 mb-8">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-[24px] flex items-center justify-center text-xl font-black italic shadow-inner">
                    DS
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900 group-hover:text-blue-600 transition-colors italic">Don Bosco Senior Sec</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Guwahati, Assam</p>
                </div>
            </div>

            <div class="space-y-4 mb-8">
                <div class="flex justify-between items-center text-xs font-medium italic">
                    <span class="text-slate-400">Subscription:</span>
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full font-black text-[9px] uppercase tracking-tighter">Full School ERP</span>
                </div>
                <div class="flex justify-between items-center text-xs font-medium italic">
                    <span class="text-slate-400">Active Students:</span>
                    <span class="text-slate-900 font-black tracking-tight">2,482</span>
                </div>
                <div class="flex justify-between items-center text-xs font-medium italic">
                    <span class="text-slate-400">Revenue Month:</span>
                    <span class="text-slate-900 font-black tracking-tight">₹42,000</span>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-50 flex items-center gap-3">
                <button class="flex-1 py-3 bg-slate-950 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-800 transition-all active:scale-95 shadow-lg">Manage Portal</button>
                <button class="w-12 h-12 flex items-center justify-center bg-slate-50 text-slate-400 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-all"><i data-lucide="settings" size="18"></i></button>
            </div>
        </div>

        <!-- Institute 2 -->
        <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl shadow-slate-200/50 p-8 hover:-translate-y-2 transition-all duration-300 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-600/5 rounded-full -mr-16 -mt-16"></div>
            
            <div class="flex items-center gap-4 mb-8">
                <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-[24px] flex items-center justify-center text-xl font-black italic shadow-inner">
                    AA
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900 group-hover:text-blue-600 transition-colors italic">Akash Academy</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Chandigarh</p>
                </div>
            </div>

            <div class="space-y-4 mb-8">
                <div class="flex justify-between items-center text-xs font-medium italic">
                    <span class="text-slate-400">Subscription:</span>
                    <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full font-black text-[9px] uppercase tracking-tighter">Coaching Center</span>
                </div>
                <div class="flex justify-between items-center text-xs font-medium italic">
                    <span class="text-slate-400">Active Students:</span>
                    <span class="text-slate-900 font-black tracking-tight">840</span>
                </div>
                <div class="flex justify-between items-center text-xs font-medium italic">
                    <span class="text-slate-400">Revenue Month:</span>
                    <span class="text-slate-900 font-black tracking-tight">₹12,500</span>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-50 flex items-center gap-3">
                <button class="flex-1 py-3 bg-slate-950 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-800 transition-all active:scale-95 shadow-lg">Manage Portal</button>
                <button class="w-12 h-12 flex items-center justify-center bg-slate-50 text-slate-400 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-all"><i data-lucide="settings" size="18"></i></button>
            </div>
        </div>

        <!-- Institute 3 -->
        <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl shadow-slate-200/50 p-8 hover:-translate-y-2 transition-all duration-300 relative overflow-hidden group border-dashed border-2 border-slate-200 flex flex-col items-center justify-center text-center">
            <div class="w-20 h-20 bg-slate-50 text-slate-300 rounded-[32px] flex items-center justify-center mb-6">
                <i data-lucide="plus" size="32"></i>
            </div>
            <h3 class="text-xl font-black text-slate-400 italic mb-2">New Partner</h3>
            <p class="text-xs text-slate-400 font-medium px-8 mb-8 leading-relaxed">Expand your ecosystem by registering a new educational entity.</p>
            <button class="px-8 py-3 bg-slate-100 text-[10px] font-black uppercase tracking-widest text-slate-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm active:scale-95">Start Application</button>
        </div>
    </div>
</x-demo-layout>
