<x-demo-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-12 text-center">
            <h2 class="text-[10px] font-black text-blue-600 uppercase tracking-[0.3em] mb-2 font-sans">Financial Clearance</h2>
            <h1 class="text-4xl font-black text-slate-900 leading-tight italic">Secure <span class="text-blue-600">Payment.</span></h1>
            <p class="text-sm text-slate-400 font-medium italic mt-2">Registration ID: OS-94285-DB | Don Bosco Senior Sec</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-10">
            <!-- Payment breakdown -->
            <div class="md:col-span-7">
                <div class="bg-white rounded-[48px] border border-slate-100 shadow-2xl shadow-slate-200/50 p-10 h-full flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-black text-slate-900 mb-8 italic">Fee Structure</h3>
                        
                        <div class="space-y-6 font-sans italic">
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <div>
                                    <p class="text-xs font-black text-slate-900">Admission Fee</p>
                                    <p class="text-[9px] text-slate-500 font-medium">Class XI Science (One-time)</p>
                                </div>
                                <span class="text-sm font-black text-slate-900">₹12,000</span>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <div>
                                    <p class="text-xs font-black text-slate-900">Development Fund</p>
                                    <p class="text-[9px] text-slate-500 font-medium">Institutional Infrastructure</p>
                                </div>
                                <span class="text-sm font-black text-slate-900">₹2,500</span>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-blue-50/50 rounded-2xl border border-blue-100/50 group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center"><i data-lucide="info" size="14"></i></div>
                                    <div>
                                        <p class="text-xs font-black text-blue-600 italic">Processing Charge (4%)</p>
                                        <p class="text-[9px] text-blue-400 font-medium italic">Standard Institutional Policy</p>
                                    </div>
                                </div>
                                <span class="text-sm font-black text-blue-600">₹580</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 pt-8 border-t border-slate-50">
                        <div class="flex items-center justify-between mb-8">
                            <span class="text-lg font-black text-slate-400 italic">Total Payable:</span>
                            <span class="text-4xl font-black text-slate-900 italic tracking-tighter">₹15,080</span>
                        </div>
                        <button class="w-full py-5 bg-blue-600 text-white font-black rounded-[24px] uppercase tracking-widest text-xs hover:bg-blue-500 transition-all shadow-2xl shadow-blue-600/20 active:scale-95 flex items-center justify-center gap-4">
                            Complete Payment Now
                            <i data-lucide="shield-check" size="20"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Guarantee Sidebar -->
            <div class="md:col-span-5 space-y-8">
                <div class="bg-slate-950 rounded-[40px] p-8 text-white relative overflow-hidden italic shadow-2xl shadow-slate-900/20">
                    <div class="absolute top-0 left-0 w-24 h-24 bg-blue-600/20 rounded-full blur-2xl -ml-12 -mt-12"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-6">
                            <i data-lucide="lock" class="text-blue-400" size="20"></i>
                            <h4 class="text-xs font-black uppercase tracking-widest">PCI DSS Compliant</h4>
                        </div>
                        <p class="text-xs text-slate-400 font-medium leading-relaxed mb-6 italic">Your transaction is protected by 256-bit bank-grade encryption via our secure payment gateway partner.</p>
                        
                        <div class="flex flex-wrap gap-4 opacity-40">
                            <i data-lucide="credit-card" size="24"></i>
                            <i data-lucide="smartphone" size="24"></i>
                            <i data-lucide="tablet" size="24"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl shadow-slate-200/50 p-8 flex flex-col items-center text-center italic">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6">
                        <i data-lucide="message-square" size="24"></i>
                    </div>
                    <h4 class="text-sm font-black text-slate-900 mb-2 italic">Payment Issues?</h4>
                    <p class="text-[10px] text-slate-400 font-medium mb-6 px-4">Contact our finance desk if your payment fails or for partial fee queries.</p>
                    <a href="{{ route('help') }}" class="text-[10px] font-black uppercase tracking-widest text-blue-600 hover:text-blue-500 transition-colors">Contact Billing Support</a>
                </div>
            </div>
        </div>
    </div>
</x-demo-layout>
