<x-demo-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-12 text-center">
            <h2 class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] mb-2 font-sans">Admission Portal</h2>
            <h1 class="text-4xl font-black text-slate-900 leading-tight italic">Enrollment <span class="text-blue-600">Form.</span></h1>
            <p class="text-sm text-slate-400 font-medium italic mt-2">Class XI Science - Don Bosco Senior Sec</p>
        </div>

        <!-- Progress Steps -->
        <div class="flex items-center justify-center gap-4 mb-16 relative">
            <div class="absolute left-1/4 right-1/4 top-1/2 h-0.5 bg-slate-100 -z-10"></div>
            <div class="w-12 h-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center shadow-xl shadow-blue-600/20 font-black italic">1</div>
            <div class="w-12 h-12 bg-white text-slate-300 border border-slate-100 rounded-2xl flex items-center justify-center font-black italic">2</div>
            <div class="w-12 h-12 bg-white text-slate-300 border border-slate-100 rounded-2xl flex items-center justify-center font-black italic">3</div>
        </div>

        <!-- Form Container -->
        <div class="bg-white rounded-[48px] border border-slate-100 shadow-2xl shadow-slate-200/50 p-12">
            <h3 class="text-xl font-black text-slate-900 mb-10 italic border-b border-slate-50 pb-6 flex items-center gap-3">
                <i data-lucide="user-circle" class="text-blue-600"></i>
                Personal Information
            </h3>

            <form class="space-y-8 italic">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4">Student Full Name</label>
                        <input type="text" placeholder="Johnathan Doe" class="w-full px-6 py-4 bg-slate-50 border-transparent rounded-2xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium">
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4">Date of Birth</label>
                        <input type="date" class="w-full px-6 py-4 bg-slate-50 border-transparent rounded-2xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4">Guardian's Name</label>
                        <input type="text" placeholder="Robert Doe" class="w-full px-6 py-4 bg-slate-50 border-transparent rounded-2xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium">
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4">Guardian's Contact</label>
                        <div class="relative">
                            <span class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs">+91</span>
                            <input type="tel" placeholder="00000 00000" class="w-full pl-16 pr-6 py-4 bg-slate-50 border-transparent rounded-2xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium">
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4">Permanent Address</label>
                    <textarea rows="3" placeholder="Enter your full residential address..." class="w-full px-6 py-4 bg-slate-50 border-transparent rounded-2xl focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-medium h-32 resize-none"></textarea>
                </div>

                <div class="pt-10 flex items-center justify-between">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic flex items-center gap-2">
                        <i data-lucide="shield-check" size="14"></i>
                        Encrypted Data Securely Managed
                    </p>
                    <button type="button" class="px-12 py-5 bg-slate-950 text-white font-black rounded-[24px] uppercase tracking-widest text-[10px] hover:bg-blue-600 transition-all shadow-xl active:scale-95 leading-none flex items-center gap-4">
                        Continue to Academics
                        <i data-lucide="chevron-right" size="18"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Help Tooltip -->
        <div class="mt-10 p-6 bg-blue-50 rounded-[32px] border border-blue-100 text-blue-600 italic flex items-start gap-4 mx-8">
            <i data-lucide="help-circle" class="shrink-0"></i>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest mb-1">Need Assistance?</p>
                <p class="text-xs font-semibold leading-relaxed">If you're facing issues with document uploads or have questions about the eligibility criteria, please contact the institution directly at <span class="font-black underline">support@bosco.edu</span></p>
            </div>
        </div>
    </div>
</x-demo-layout>
