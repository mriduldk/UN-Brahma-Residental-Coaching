<x-demo-layout>
    <div class="max-w-4xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
            <div>
                <h2 class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] mb-2 font-sans">Verification Details</h2>
                <h1 class="text-4xl font-black text-slate-900 leading-tight italic">Submission <span class="text-blue-600">Review.</span></h1>
            </div>
            <div class="flex items-center gap-3">
                <button class="px-6 py-3 bg-white border border-slate-200 text-slate-400 font-bold rounded-xl text-[10px] uppercase tracking-widest hover:text-blue-600 transition-all">
                    <i data-lucide="printer" size="14" class="inline-block mr-2"></i> Print Copy
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
            <!-- Main Content -->
            <div class="md:col-span-8 space-y-8">
                <!-- Status Timeline -->
                <div class="bg-white rounded-[48px] border border-slate-100 shadow-2xl shadow-slate-200/50 p-10">
                    <h3 class="text-xl font-black text-slate-900 mb-10 italic">Progress Tracking</h3>
                    
                    <div class="space-y-12 relative px-4">
                        <div class="absolute left-10 top-2 bottom-2 w-0.5 bg-slate-100 -z-10"></div>
                        
                        <!-- Step 1 -->
                        <div class="flex items-start gap-8">
                            <div class="w-12 h-12 bg-emerald-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20 italic font-black shrink-0 relative z-10">
                                <i data-lucide="check" size="20"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest italic">Application Submitted</h4>
                                <p class="text-[10px] text-slate-400 font-medium px-4 bg-slate-50 py-1 rounded-full border border-slate-100 mt-2 inline-block">24 Mar 2025, 04:12 PM</p>
                                <p class="text-xs text-slate-500 mt-4 leading-relaxed font-medium italic">Your form and base registration fee (₹200) were successfully recorded by the system.</p>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex items-start gap-8">
                            <div class="w-12 h-12 bg-emerald-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20 italic font-black shrink-0 relative z-10">
                                <i data-lucide="check" size="20"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest italic">Documents Verified</h4>
                                <p class="text-[10px] text-slate-400 font-medium px-4 bg-slate-50 py-1 rounded-full border border-slate-100 mt-2 inline-block">25 Mar 2025, 11:30 AM</p>
                                <p class="text-xs text-slate-500 mt-4 leading-relaxed font-medium italic">All uploaded documents (Marksheet, ID Proof) have been verified by the institutional authority.</p>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex items-start gap-8">
                            <div class="w-12 h-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center shadow-xl shadow-blue-600/20 italic font-black shrink-0 relative z-10">
                                <i data-lucide="loader-2" class="animate-spin" size="20"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-blue-600 uppercase tracking-widest italic">Provisionally Selected</h4>
                                <p class="text-[10px] text-blue-400 font-black px-4 bg-blue-50 py-1 rounded-full border border-blue-100 mt-2 inline-block">Awaiting Final Admission Fee</p>
                                <div class="mt-6 p-6 bg-slate-950 text-white rounded-3xl italic">
                                    <p class="text-xs font-medium leading-relaxed mb-6">Congratulations! You have been provisionally selected for <span class="text-blue-400 font-black">Class XI Science</span>. Please complete your admission fee payment to secure your seat.</p>
                                    <a href="{{ route('demo.student.payment') }}" class="inline-flex items-center gap-3 px-6 py-3 bg-blue-600 text-white font-black rounded-xl uppercase tracking-widest text-[9px] hover:bg-blue-500 transition-all">Proceed to Payment <i data-lucide="chevron-right" size="14"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div class="md:col-span-4 space-y-8">
                <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl shadow-slate-200/50 p-8">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6 italic">Institute Profile</h4>
                    <div class="flex items-center gap-4 mb-8 pb-8 border-b border-slate-50">
                        <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-[20px] flex items-center justify-center font-black italic shadow-inner shrink-0">DB</div>
                        <div>
                            <p class="text-sm font-black text-slate-900 italic">Don Bosco</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Senior Secondary</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4 font-sans italic">
                        <div class="flex justify-between items-center text-xs font-bold">
                            <span class="text-slate-400 italic">Registration ID:</span>
                            <span class="text-slate-900 font-black">OS-94285-DB</span>
                        </div>
                        <div class="flex justify-between items-center text-xs font-bold">
                            <span class="text-slate-400 italic">Academic Year:</span>
                            <span class="text-slate-900 font-black tracking-tight">2025-26</span>
                        </div>
                        <div class="flex justify-between items-center text-xs font-bold">
                            <span class="text-slate-400 italic">Quota Type:</span>
                            <span class="text-slate-900 font-black tracking-tight">General Merit</span>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-600 rounded-[40px] border border-blue-500 shadow-xl shadow-blue-600/20 p-8 text-white relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-16 -mt-16 group-hover:scale-110 transition-transform"></div>
                    <div class="relative z-10 flex flex-col items-center text-center">
                        <i data-lucide="qr-code" size="64" class="mb-4 text-white opacity-80"></i>
                        <p class="text-[10px] font-black uppercase tracking-widest mb-2 italic">Digi-Verification Code</p>
                        <p class="text-xs font-medium text-blue-100 leading-relaxed font-sans italic">Scan this code at the campus reception for instant profile retrieval.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-demo-layout>
