<x-guest-layout full-width>
    <div class="py-24 bg-slate-50 relative overflow-hidden min-h-screen">
        <!-- Background Decorations -->
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-100/30 rounded-full blur-[120px] -mr-48 -mt-48 transition-transform duration-1000"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-indigo-100/30 rounded-full blur-[100px] -ml-40 -mb-40 transition-transform duration-1000"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Header -->
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="text-sm font-black text-blue-600 uppercase tracking-[0.3em] mb-4">Transparent Pricing</h2>
                <h1 class="text-5xl md:text-6xl font-black text-slate-900 mb-6 leading-tight">Plans that fit your <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Scale.</span></h1>
                <p class="text-lg text-slate-500 font-medium leading-relaxed italic">Simple, upfront pricing for digital registrations and full-campus management.</p>
            </div>

            <!-- Section 2: Management Subscriptions -->
            <div>
                <div class="flex items-center gap-4 mb-10 overflow-hidden">
                    <div class="h-px flex-1 bg-slate-200"></div>
                    <h2 class="text-xs font-black text-slate-400 uppercase tracking-[0.3em] whitespace-nowrap">Campus Management Subscriptions</h2>
                    <div class="h-px flex-1 bg-slate-200"></div>
                </div>

                <!-- Free Months Banner -->
                <div class="flex justify-center mb-16">
                    <div class="bg-emerald-50 text-emerald-600 px-6 py-3 rounded-full border border-emerald-200 flex items-center gap-2 shadow-sm">
                        <i data-lucide="gift" size="18"></i>
                        <span class="text-sm font-black uppercase tracking-widest">Sign up and get first 3 months free</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Tuition Plan -->
                    <div class="bg-white/80 backdrop-blur-xl border border-white shadow-xl rounded-[40px] p-10 flex flex-col transition-all duration-500 hover:shadow-2xl">
                        <div class="mb-10 text-center">
                            <h3 class="text-xl font-black text-slate-900 mb-4">Tuition / Coaching</h3>
                            <div class="flex items-baseline justify-center gap-1">
                                <span class="text-slate-400 font-bold">₹</span>
                                <span class="text-5xl font-black text-slate-900">499</span>
                                <span class="text-slate-400 font-bold">/mo</span>
                            </div>
                        </div>
                        <ul class="space-y-4 mb-10 flex-grow">
                            <li class="flex items-center gap-3 text-sm text-slate-600 font-medium"><i data-lucide="check" size="18" class="text-blue-500"></i> Student & Batch Management</li>
                            <li class="flex items-center gap-3 text-sm text-slate-600 font-medium"><i data-lucide="check" size="18" class="text-blue-500"></i> Attendance Tracking</li>
                            <li class="flex items-center gap-3 text-sm text-slate-600 font-medium"><i data-lucide="check" size="18" class="text-blue-500"></i> Monthly Fee Collection (Online + Offline)</li>
                            <li class="flex items-center gap-3 text-sm text-slate-600 font-medium"><i data-lucide="check" size="18" class="text-blue-500"></i> Study Material Sharing</li>
                            <li class="flex items-center gap-3 text-sm text-slate-600 font-medium"><i data-lucide="check" size="18" class="text-blue-500"></i> Basic Reports & Analytics</li>
                            <li class="flex items-center gap-3 text-sm text-slate-600 font-medium"><i data-lucide="check" size="18" class="text-blue-500"></i> Payment Gateway Integration</li>
                        </ul>
                        <a href="{{ route('help') }}" class="block w-full py-4 text-center bg-slate-900 text-white font-black rounded-2xl hover:bg-slate-800 transition-colors uppercase tracking-widest text-xs shadow-xl shadow-slate-200">
                            Join as Tuition
                        </a>
                    </div>

                    <!-- School Plan (Featured) -->
                    <div class="bg-blue-600 text-white shadow-2xl rounded-[40px] p-10 flex flex-col transition-all duration-500 hover:scale-105 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4">
                            <i data-lucide="heart" class="text-white/20" size="48"></i>
                        </div>
                        <div class="mb-10 text-center relative z-10">
                            <h3 class="text-xl font-black mb-4">Full School ERP</h3>
                            <div class="flex items-baseline justify-center gap-1">
                                <span class="text-blue-200 font-bold">₹</span>
                                <span class="text-5xl font-black">899</span>
                                <span class="text-blue-200 font-bold">/mo</span>
                            </div>
                        </div>
                        <ul class="space-y-4 mb-10 flex-grow relative z-10">
                            <li class="flex items-center gap-3 text-sm text-blue-50 font-medium"><i data-lucide="check-circle" size="18" class="text-blue-300"></i> Student Lifecycle CRM (Admission → Alumni)</li>
                            <li class="flex items-center gap-3 text-sm text-blue-50 font-medium"><i data-lucide="check-circle" size="18" class="text-blue-300"></i> Attendance (Student + Staff)</li>
                            <li class="flex items-center gap-3 text-sm text-blue-50 font-medium"><i data-lucide="check-circle" size="18" class="text-blue-300"></i> Fee Management with Online Payments</li>
                            <li class="flex items-center gap-3 text-sm text-blue-50 font-medium"><i data-lucide="check-circle" size="18" class="text-blue-300"></i> Timetable Automation</li>
                            <li class="flex items-center gap-3 text-sm text-blue-50 font-medium"><i data-lucide="check-circle" size="18" class="text-blue-300"></i> Exam & Result Management</li>
                            <li class="flex items-center gap-3 text-sm text-blue-50 font-medium"><i data-lucide="check-circle" size="18" class="text-blue-300"></i> Teacher Management</li>
                            <li class="flex items-center gap-3 text-sm text-blue-50 font-medium"><i data-lucide="check-circle" size="18" class="text-blue-300"></i> Notifications & Alerts</li>
                        </ul>
                        <a href="{{ route('help') }}" class="block w-full py-5 text-center bg-white text-blue-600 font-black rounded-3xl hover:bg-blue-50 transition-all shadow-xl shadow-black/10 uppercase tracking-widest text-sm relative z-10">
                            Join as School
                        </a>
                    </div>

                    <!-- Coaching Center Plan -->
                    <div class="bg-white/80 backdrop-blur-xl border border-white shadow-xl rounded-[40px] p-10 flex flex-col transition-all duration-500 hover:shadow-2xl">
                        <div class="mb-10 text-center">
                            <h3 class="text-xl font-black text-slate-900 mb-4">Coaching Centers</h3>
                            <div class="flex items-baseline justify-center gap-1">
                                <span class="text-slate-400 font-bold">₹</span>
                                <span class="text-5xl font-black text-slate-900">999</span>
                                <span class="text-slate-400 font-bold">/mo</span>
                            </div>
                        </div>
                        <ul class="space-y-4 mb-10 flex-grow">
                            <li class="flex items-center gap-3 text-sm text-slate-600 font-medium"><i data-lucide="check" size="18" class="text-blue-500"></i> Batch & Course Management</li>
                            <li class="flex items-center gap-3 text-sm text-slate-600 font-medium"><i data-lucide="check" size="18" class="text-blue-500"></i> Student Enrollment System</li>
                            <li class="flex items-center gap-3 text-sm text-slate-600 font-medium"><i data-lucide="check" size="18" class="text-blue-500"></i> Attendance & Fee Management</li>
                            <li class="flex items-center gap-3 text-sm text-slate-600 font-medium"><i data-lucide="check" size="18" class="text-blue-500"></i> MCQ Exam Portal (Online Tests)</li>
                            <li class="flex items-center gap-3 text-sm text-slate-600 font-medium"><i data-lucide="check" size="18" class="text-blue-500"></i> Performance Reports (Rank & Analysis)</li>
                            <li class="flex items-center gap-3 text-sm text-slate-600 font-medium"><i data-lucide="check" size="18" class="text-blue-500"></i> Bulk Notifications (SMS/WhatsApp*)</li>
                            <li class="flex items-center gap-3 text-sm text-slate-600 font-medium"><i data-lucide="check" size="18" class="text-blue-500"></i> Study Materials & Notes</li>
                        </ul>
                        <a href="{{ route('help') }}" class="block w-full py-4 text-center bg-slate-100 text-slate-900 font-black rounded-2xl hover:bg-slate-200 transition-colors uppercase tracking-widest text-xs border-2 border-slate-200">
                            Join as Coaching
                        </a>
                    </div>
                </div>
            </div>

            <!-- Section 3: Job Portal / Sales -->
            <div class="mt-20">
                <div class="bg-gradient-to-br from-slate-900 to-indigo-950 rounded-[48px] p-12 text-center text-white relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10 blur-3xl bg-[radial-gradient(circle_at_50%_50%,#3b82f6,transparent)]"></div>
                    <div class="relative z-10">
                        <h2 class="text-4xl font-black mb-6">Need a custom <span class="text-blue-400">Job Portal?</span></h2>
                        <p class="text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed">Looking for industrial recruitment tools or a dedicated job board for your students? We offer tailored solutions for placements.</p>
                        <a href="{{ route('help') }}" class="inline-flex items-center gap-3 px-10 py-5 bg-blue-600 text-white font-black rounded-3xl hover:bg-blue-500 transition-all shadow-2xl shadow-blue-600/20 active:scale-95">
                            Contact Sales Team <i data-lucide="phone-outgoing" size="20"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Small Note -->
            <div class="mt-12 text-center">
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em]">*All prices exclude applicable GST and transaction fees charged by payment gateways.</p>
            </div>
        </div>
    </div>
</x-guest-layout>
