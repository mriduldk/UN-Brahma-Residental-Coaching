<x-demo-layout>
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
        <div>
            <h2 class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] mb-2 font-sans">Student Lifecycle</h2>
            <h1 class="text-4xl font-black text-slate-900 leading-tight italic">Admission <span class="text-blue-600">CRM.</span></h1>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-6 py-3 bg-white border border-slate-200 text-[10px] font-black uppercase text-slate-400 rounded-xl hover:text-slate-600 transition-all">Export CSV</button>
            <button class="px-6 py-3 bg-white border border-slate-200 text-[10px] font-black uppercase text-slate-400 rounded-xl hover:text-slate-600 transition-all">Bulk Actions</button>
        </div>
    </div>

    <!-- Quick Filters & Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-lg shadow-slate-200/50 flex items-center justify-between group cursor-pointer hover:border-blue-200 transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center"><i data-lucide="clock" size="20"></i></div>
                <div>
                    <h4 class="text-lg font-black italic text-slate-900">412 Today</h4>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">New Applications</p>
                </div>
            </div>
            <i data-lucide="chevron-right" size="16" class="text-slate-200 group-hover:text-blue-600"></i>
        </div>

        <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-lg shadow-slate-200/50 flex items-center justify-between group cursor-pointer hover:border-emerald-200 transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center"><i data-lucide="user-check" size="20"></i></div>
                <div>
                    <h4 class="text-lg font-black italic text-slate-900">128 Total</h4>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Enrollments</p>
                </div>
            </div>
            <i data-lucide="chevron-right" size="16" class="text-slate-200 group-hover:text-emerald-600"></i>
        </div>

        <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-lg shadow-slate-200/50 flex items-center justify-between group cursor-pointer hover:border-rose-200 transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center"><i data-lucide="alert-circle" size="20"></i></div>
                <div>
                    <h4 class="text-lg font-black italic text-slate-900">12 Total</h4>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Flagged Apps</p>
                </div>
            </div>
            <i data-lucide="chevron-right" size="16" class="text-slate-200 group-hover:text-rose-600"></i>
        </div>

        <div class="bg-slate-900 p-6 rounded-[32px] text-white flex items-center justify-between group cursor-pointer shadow-xl shadow-slate-900/10">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center"><i data-lucide="filter" size="20"></i></div>
                <div>
                    <h4 class="text-lg font-black italic">Advanced</h4>
                    <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest">Filter System</p>
                </div>
            </div>
            <i data-lucide="chevron-right" size="16" class="text-blue-400"></i>
        </div>
    </div>

    <!-- Application Table -->
    <div class="bg-white rounded-[48px] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden">
        <div class="overflow-x-auto no-scrollbar">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Applicant</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Application Date</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Applied To</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Processing</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 font-sans">
                    <tr class="hover:bg-slate-50/50 transition-all group">
                        <td class="px-10 py-6 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-slate-100 rounded-full border-2 border-white shadow-sm flex items-center justify-center font-black italic text-slate-400">AM</div>
                                <div>
                                    <p class="text-xs font-black text-slate-900 italic">Arindam Mazumdar</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase">A-94285-OS</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-6 whitespace-nowrap italic text-xs font-bold text-slate-500">25 Mar, 04:12 PM</td>
                        <td class="px-10 py-6 whitespace-nowrap italic text-xs font-bold text-slate-500">Class XI Science</td>
                        <td class="px-10 py-6 whitespace-nowrap">
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[9px] font-black underline uppercase tracking-tighter rounded-lg italic">Verification Pending</span>
                        </td>
                        <td class="px-10 py-6 text-right whitespace-nowrap">
                            <button class="px-6 py-2.5 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-600 transition-all shadow-md active:scale-95 group-hover:-translate-x-1">Review Details</button>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-50/50 transition-all group border-l-4 border-emerald-500">
                        <td class="px-10 py-6 whitespace-nowrap text-emerald-600">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-emerald-50 rounded-full border-2 border-white shadow-sm flex items-center justify-center font-black italic text-emerald-400">PB</div>
                                <div>
                                    <p class="text-xs font-black text-slate-900 italic">Priya Bharadwaj</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase">A-91024-OS</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-6 whitespace-nowrap italic text-xs font-bold text-slate-500">25 Mar, 02:45 PM</td>
                        <td class="px-10 py-6 whitespace-nowrap italic text-xs font-bold text-slate-500">Pre-Medical Entrance</td>
                        <td class="px-10 py-6 whitespace-nowrap">
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-tighter rounded-lg italic">Form Verified</span>
                        </td>
                        <td class="px-10 py-6 text-right whitespace-nowrap">
                            <button class="px-6 py-2.5 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-600 transition-all shadow-md active:scale-95 group-hover:-translate-x-1">Review Details</button>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-50/50 transition-all group">
                        <td class="px-10 py-6 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-slate-100 rounded-full border-2 border-white shadow-sm flex items-center justify-center font-black italic text-slate-400">SK</div>
                                <div>
                                    <p class="text-xs font-black text-slate-900 italic">Sohan Kalita</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase">A-88412-OS</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-6 whitespace-nowrap italic text-xs font-bold text-slate-500">24 Mar, 11:30 PM</td>
                        <td class="px-10 py-6 whitespace-nowrap italic text-xs font-bold text-slate-500">Class XI Science</td>
                        <td class="px-10 py-6 whitespace-nowrap">
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[9px] font-black underline uppercase tracking-tighter rounded-lg italic">Verification Pending</span>
                        </td>
                        <td class="px-10 py-6 text-right whitespace-nowrap">
                            <button class="px-6 py-2.5 bg-slate-900 text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-600 transition-all shadow-md active:scale-95 group-hover:-translate-x-1">Review Details</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="p-8 border-t border-slate-50 bg-slate-50/30 flex items-center justify-between">
            <span class="text-[10px] font-black text-slate-400 uppercase italic">Showing 3 of 1,248 total applications</span>
            <div class="flex items-center gap-2">
                <button class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 text-slate-400 rounded-xl hover:bg-slate-50"><i data-lucide="chevron-left" size="18"></i></button>
                <button class="w-10 h-10 flex items-center justify-center bg-blue-600 text-white font-black rounded-xl shadow-lg">1</button>
                <button class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 text-slate-900 font-bold rounded-xl">2</button>
                <button class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 text-slate-400 rounded-xl hover:bg-slate-50"><i data-lucide="chevron-right" size="18"></i></button>
            </div>
        </div>
    </div>
</x-demo-layout>
