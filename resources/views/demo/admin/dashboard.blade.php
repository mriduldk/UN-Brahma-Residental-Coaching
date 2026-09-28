<x-demo-layout>
    <div class="mb-10">
        <h2 class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] mb-2">Management Console</h2>
        <h1 class="text-4xl font-black text-slate-900 leading-tight italic">Administration <span class="text-blue-600">Dashboard.</span></h1>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-xl shadow-slate-200/50 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-full -mr-12 -mt-12 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6">
                    <i data-lucide="users" size="20"></i>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 font-sans">Total Students</p>
                <p class="text-3xl font-black text-slate-900 italic">12,482</p>
                <div class="mt-4 flex items-center gap-2 text-emerald-500 font-bold text-[10px]">
                    <i data-lucide="trending-up" size="14"></i>
                    <span>+12% this month</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-xl shadow-slate-200/50 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-full -mr-12 -mt-12 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-6">
                    <i data-lucide="building" size="20"></i>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 font-sans">Institutions</p>
                <p class="text-3xl font-black text-slate-900 italic">482</p>
                <div class="mt-4 flex items-center gap-2 text-emerald-500 font-bold text-[10px]">
                    <i data-lucide="plus" size="14"></i>
                    <span>8 new onboarded</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-xl shadow-slate-200/50 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 rounded-full -mr-12 -mt-12 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-6">
                    <i data-lucide="dollar-sign" size="20"></i>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 font-sans">Total Revenue</p>
                <p class="text-3xl font-black text-slate-900 italic">₹2.4M</p>
                <div class="mt-4 flex items-center gap-2 text-emerald-500 font-bold text-[10px]">
                    <i data-lucide="trending-up" size="14"></i>
                    <span>+8% versus last month</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-xl shadow-slate-200/50 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-rose-50 rounded-full -mr-12 -mt-12 group-hover:scale-110 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mb-6">
                    <i data-lucide="file-text" size="20"></i>
                </div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 font-sans">Active Forms</p>
                <p class="text-3xl font-black text-slate-900 italic">1,240</p>
                <div class="mt-4 flex items-center gap-2 text-rose-500 font-bold text-[10px]">
                    <i data-lucide="activity" size="14"></i>
                    <span>42 pending review</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Application Chart Mock -->
        <div class="lg:col-span-8 bg-white p-10 rounded-[48px] border border-slate-100 shadow-2xl shadow-slate-200/50">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h3 class="text-xl font-black text-slate-900 italic">Admission Velocity</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Daily registration trends</p>
                </div>
                <div class="flex items-center gap-2 bg-slate-50 p-2 rounded-2xl">
                    <button class="px-4 py-2 bg-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-sm text-blue-600">7 Days</button>
                    <button class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl text-slate-400 hover:text-slate-600">30 Days</button>
                </div>
            </div>

            <!-- Mock SVG Chart -->
            <div class="h-64 flex items-end justify-between gap-4 px-4 overflow-hidden">
                <div class="flex-1 bg-blue-50 rounded-t-2xl hover:bg-blue-600 transition-all cursor-pointer relative group h-[40%]">
                    <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[8px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">120</div>
                </div>
                <div class="flex-1 bg-blue-100 rounded-t-2xl hover:bg-blue-600 transition-all cursor-pointer relative group h-[60%]">
                    <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[8px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">185</div>
                </div>
                <div class="flex-1 bg-blue-50 rounded-t-2xl hover:bg-blue-600 transition-all cursor-pointer relative group h-[30%]"></div>
                <div class="flex-1 bg-blue-200 rounded-t-2xl hover:bg-blue-600 transition-all cursor-pointer relative group h-[85%]">
                    <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[8px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">340</div>
                </div>
                <div class="flex-1 bg-blue-100 rounded-t-2xl hover:bg-blue-600 transition-all cursor-pointer relative group h-[50%]"></div>
                <div class="flex-1 bg-blue-600 rounded-t-2xl hover:bg-blue-700 transition-all cursor-pointer relative group h-[95%] shadow-xl shadow-blue-600/20">
                    <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[8px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity italic font-bold">412 Today</div>
                </div>
            </div>
            <div class="flex justify-between mt-6 px-4">
                <span class="text-[10px] font-black text-slate-400 uppercase">Mon</span>
                <span class="text-[10px] font-black text-slate-400 uppercase">Tue</span>
                <span class="text-[10px] font-black text-slate-400 uppercase">Wed</span>
                <span class="text-[10px] font-black text-slate-400 uppercase">Thu</span>
                <span class="text-[10px] font-black text-slate-400 uppercase">Fri</span>
                <span class="text-[10px] font-black text-slate-600 uppercase italic">Sat (Peak)</span>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="lg:col-span-4 bg-white p-10 rounded-[48px] border border-slate-100 shadow-2xl shadow-slate-200/50">
            <h3 class="text-xl font-black text-slate-900 italic mb-8">Recent Events</h3>
            <div class="space-y-8">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shrink-0">
                        <i data-lucide="check-circle" size="18"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900 italic">DAV Public School</p>
                        <p class="text-[10px] text-slate-500 font-medium">Onboarded as Gold Member</p>
                        <span class="text-[8px] font-black text-blue-600 uppercase mt-2 block tracking-widest">2 mins ago</span>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center shrink-0">
                        <i data-lucide="file-plus" size="18"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900 italic">Springfield Coaching</p>
                        <p class="text-[10px] text-slate-500 font-medium">Created "Maths Quiz" Form</p>
                        <span class="text-[8px] font-black text-blue-600 uppercase mt-2 block tracking-widest">15 mins ago</span>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center shrink-0">
                        <i data-lucide="alert-triangle" size="18"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-900 italic">System Alert</p>
                        <p class="text-[10px] text-slate-500 font-medium">Maintenance scheduled (02:00 AM)</p>
                        <span class="text-[8px] font-black text-blue-600 uppercase mt-2 block tracking-widest">1 hour ago</span>
                    </div>
                </div>
            </div>
            
            <button class="w-full mt-10 py-4 border border-slate-100 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-blue-600 hover:bg-slate-50 rounded-2xl transition-all">
                View Audit Logs
            </button>
        </div>
    </div>
</x-demo-layout>
