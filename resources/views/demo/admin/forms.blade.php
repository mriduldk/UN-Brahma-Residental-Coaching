<x-demo-layout>
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
        <div>
            <h2 class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] mb-2 font-sans">Dynamic Infrastructure</h2>
            <h1 class="text-4xl font-black text-slate-900 leading-tight italic">Admission <span class="text-blue-600">Forms.</span></h1>
        </div>
        <div class="flex items-center gap-4">
            <button class="px-8 py-4 bg-slate-100 text-slate-600 font-black rounded-[24px] uppercase tracking-widest text-[10px] hover:bg-slate-200 transition-all active:scale-95 shadow-sm">
                Import Template
            </button>
            <button @click="showBuilder = true" class="px-8 py-4 bg-blue-600 text-white font-black rounded-[24px] uppercase tracking-widest text-[10px] hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/20 active:scale-95 group">
                <i data-lucide="layout-template" size="18" class="inline-block mr-2"></i>
                Build New form
            </button>
        </div>
    </div>

    <!-- Form Builder Visual Mockup (Overlay or Section) -->
    <div class="bg-slate-900 rounded-[64px] p-12 text-white overflow-hidden relative group mb-10">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-600/10 rounded-full blur-[100px] -mr-[250px] -mt-[250px]"></div>
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 relative z-10 items-center">
            <div class="lg:col-span-4 italic">
                <h3 class="text-3xl font-black text-white mb-6">No-Code <span class="text-blue-400">Builder.</span></h3>
                <p class="text-slate-400 font-medium mb-12 leading-relaxed">Institutes can build complex, multi-step registration forms with document uploads and payment logic in under 5 minutes.</p>
                <div class="space-y-6">
                    <div class="flex items-center gap-4 p-5 bg-white/5 border border-white/10 rounded-[32px] shadow-2xl">
                        <i data-lucide="check-square" class="text-blue-400"></i>
                        <span class="text-xs font-black uppercase tracking-widest">Select Fields</span>
                    </div>
                    <div class="flex items-center gap-4 p-5 bg-white/5 border border-white/10 rounded-[32px] shadow-2xl">
                        <i data-lucide="alert-circle" class="text-blue-400"></i>
                        <span class="text-xs font-black uppercase tracking-widest">Define Required Rules</span>
                    </div>
                </div>
            </div>
            
            <div class="lg:col-span-8 bg-[#1a202c] rounded-[48px] border border-white/10 p-4 shadow-[0_0_100px_rgba(37,99,235,0.1)]">
                <div class="bg-[#0b1118] rounded-[40px] border border-white/5 h-[450px] relative overflow-hidden flex flex-col">
                    <div class="px-8 py-5 border-b border-white/5 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-rose-500 rounded-full"></div>
                            <div class="w-2 h-2 bg-amber-500 rounded-full"></div>
                            <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
                        </div>
                        <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest italic">Form Configuration Engine v4.0</span>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto no-scrollbar p-6">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="border-b border-white/5">
                                    <th class="pb-4 text-[9px] font-black text-slate-500 uppercase tracking-widest">Field Name</th>
                                    <th class="pb-4 text-[9px] font-black text-slate-500 uppercase tracking-widest text-center">Enable</th>
                                    <th class="pb-4 text-[9px] font-black text-slate-500 uppercase tracking-widest text-center">Required</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                <tr class="group">
                                    <td class="py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-blue-600/10 flex items-center justify-center text-blue-400">
                                                <i data-lucide="user" size="14"></i>
                                            </div>
                                            <span class="text-xs font-bold text-slate-200">Full Name</span>
                                        </div>
                                    </td>
                                    <td class="py-4 text-center">
                                        <div class="w-5 h-5 bg-blue-600 rounded-md mx-auto flex items-center justify-center">
                                            <i data-lucide="check" size="12" class="text-white"></i>
                                        </div>
                                    </td>
                                    <td class="py-4 text-center">
                                        <div class="w-5 h-5 bg-blue-600 rounded-md mx-auto flex items-center justify-center">
                                            <i data-lucide="check" size="12" class="text-white"></i>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="group">
                                    <td class="py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-emerald-600/10 flex items-center justify-center text-emerald-400">
                                                <i data-lucide="mail" size="14"></i>
                                            </div>
                                            <span class="text-xs font-bold text-slate-200">Email Address</span>
                                        </div>
                                    </td>
                                    <td class="py-4 text-center">
                                        <div class="w-5 h-5 bg-blue-600 rounded-md mx-auto flex items-center justify-center">
                                            <i data-lucide="check" size="12" class="text-white"></i>
                                        </div>
                                    </td>
                                    <td class="py-4 text-center">
                                        <div class="w-5 h-5 bg-white/5 border border-white/10 rounded-md mx-auto"></div>
                                    </td>
                                </tr>
                                <tr class="group">
                                    <td class="py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-amber-600/10 flex items-center justify-center text-amber-400">
                                                <i data-lucide="phone" size="14"></i>
                                            </div>
                                            <span class="text-xs font-bold text-slate-200">Mobile Number</span>
                                        </div>
                                    </td>
                                    <td class="py-4 text-center">
                                        <div class="w-5 h-5 bg-blue-600 rounded-md mx-auto flex items-center justify-center">
                                            <i data-lucide="check" size="12" class="text-white"></i>
                                        </div>
                                    </td>
                                    <td class="py-4 text-center">
                                        <div class="w-5 h-5 bg-blue-600 rounded-md mx-auto flex items-center justify-center">
                                            <i data-lucide="check" size="12" class="text-white"></i>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="group">
                                    <td class="py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-rose-600/10 flex items-center justify-center text-rose-400">
                                                <i data-lucide="credit-card" size="14"></i>
                                            </div>
                                            <span class="text-xs font-bold text-slate-200">Aadhaar Number</span>
                                        </div>
                                    </td>
                                    <td class="py-4 text-center">
                                        <div class="w-5 h-5 bg-white/5 border border-white/10 rounded-md mx-auto"></div>
                                    </td>
                                    <td class="py-4 text-center">
                                        <div class="w-5 h-5 bg-white/5 border border-white/10 rounded-md mx-auto opacity-20"></div>
                                    </td>
                                </tr>
                                <tr class="group">
                                    <td class="py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-purple-600/10 flex items-center justify-center text-purple-400">
                                                <i data-lucide="upload" size="14"></i>
                                            </div>
                                            <span class="text-xs font-bold text-slate-200">Passport Photo</span>
                                        </div>
                                    </td>
                                    <td class="py-4 text-center">
                                        <div class="w-5 h-5 bg-blue-600 rounded-md mx-auto flex items-center justify-center">
                                            <i data-lucide="check" size="12" class="text-white"></i>
                                        </div>
                                    </td>
                                    <td class="py-4 text-center">
                                        <div class="w-5 h-5 bg-blue-600 rounded-md mx-auto flex items-center justify-center">
                                            <i data-lucide="check" size="12" class="text-white"></i>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Forms Table -->
    <div class="bg-white rounded-[48px] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden mb-10">
        <div class="p-8 border-b border-slate-50 flex items-center justify-between">
            <h3 class="text-xl font-black text-slate-900 italic">Live Admission Channels</h3>
            <span class="px-4 py-1.5 bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-blue-100">12 Total Active</span>
        </div>
        
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Form Identity</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Target Institute</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Pricing Model</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center font-black italic shadow-inner shrink-0">B1</div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 italic">Class XI Science (2025)</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Registration Open</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-8 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <i data-lucide="building" size="14" class="text-slate-400"></i>
                                <span class="text-sm font-bold text-slate-600 italic">Don Bosco Senior Sec</span>
                            </div>
                        </td>
                        <td class="px-10 py-8 whitespace-nowrap">
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[9px] font-black uppercase tracking-tighter rounded-lg border border-blue-100">₹200 / Form</span>
                        </td>
                        <td class="px-10 py-8 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                                <span class="text-sm font-bold text-slate-900 font-sans">942 Responses</span>
                            </div>
                        </td>
                        <td class="px-10 py-8 text-right space-x-2">
                            <button class="px-6 py-2 bg-slate-950 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-600 transition-all shadow-md active:scale-95">Analyze</button>
                            <button class="w-10 h-10 bg-slate-50 text-slate-400 hover:text-slate-900 rounded-xl inline-flex items-center justify-center transition-all"><i data-lucide="external-link" size="16"></i></button>
                        </td>
                    </tr>
                    
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center font-black italic shadow-inner shrink-0">Q4</div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 italic">Pre-Medical Entrance Test</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">MCQ Based Form</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-8 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <i data-lucide="building" size="14" class="text-slate-400"></i>
                                <span class="text-sm font-bold text-slate-600 italic">Akash Academy</span>
                            </div>
                        </td>
                        <td class="px-10 py-8 whitespace-nowrap">
                            <span class="px-3 py-1 bg-slate-50 text-slate-600 text-[9px] font-black uppercase tracking-tighter rounded-lg border border-slate-100">Subscription Free</span>
                        </td>
                        <td class="px-10 py-8 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 bg-amber-500 rounded-full"></div>
                                <span class="text-sm font-bold text-slate-900 font-sans">184 Responses</span>
                            </div>
                        </td>
                        <td class="px-10 py-8 text-right space-x-2">
                            <button class="px-6 py-2 bg-slate-950 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-600 transition-all shadow-md active:scale-95">Analyze</button>
                            <button class="w-10 h-10 bg-slate-50 text-slate-400 hover:text-slate-900 rounded-xl inline-flex items-center justify-center transition-all"><i data-lucide="external-link" size="16"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-demo-layout>
