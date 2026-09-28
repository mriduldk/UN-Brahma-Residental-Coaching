<x-guest-layout full-width>
<div class="space-y-6 py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 min-h-[80vh]">
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('discover.index') }}" class="p-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition-colors">
                <i data-lucide="arrow-left" size="20"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800">{{ $institution->institution_name }} Applications</h2>
                <p class="text-slate-500">Browse and apply for available forms under this institution.</p>
            </div>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($forms as $form)
        <!-- Dynamic Card -->
        <div class="bg-white rounded-2xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] hover:shadow-xl transition-all duration-300 flex flex-col h-full border border-slate-100 group">
            <div class="flex justify-between items-start mb-4">
                <span class="bg-blue-50 text-blue-600 p-2 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                     <i data-lucide="file-text" size="20"></i>
                </span>
                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-md {{ $form->status == 'active' ? 'bg-green-50 text-green-600' : 'bg-slate-100 text-slate-500' }}">
                    {{ $form->status }}
                </span>
            </div>
            
            <h3 class="text-xl font-bold text-slate-800 mb-1 leading-tight group-hover:text-blue-600 transition-colors">{{ $form->application_name }}</h3>

            <p class="text-sm text-slate-500 mb-6 line-clamp-2 leading-relaxed">{{ $form->application_description ?? 'No description available for this application.' }}</p>
            
            <div class="mt-auto space-y-3">
                <div class="flex items-center text-sm text-slate-600 bg-slate-50 p-2.5 rounded-lg">
                    <i data-lucide="calendar" size="14" class="mr-2.5 text-slate-400"></i>
                    <span class="font-medium">Deadline:</span>
                    <span class="ml-auto font-bold">{{ $form->deadline ? $form->deadline->format('d M, Y') : 'N/A' }}</span>
                </div>
                
                <div class="grid grid-cols-2 gap-3 text-xs text-slate-500">
                    <div class="flex flex-col p-2 rounded-lg border border-slate-100 items-center justify-center bg-slate-50/50">
                        <span class="uppercase tracking-wider text-[10px] font-bold text-slate-400 mb-1">Fee</span>
                        <span class="font-bold text-slate-700 text-sm">₹{{ number_format($form->application_fee, 0) }}</span>
                    </div>
                    <div class="flex flex-col p-2 rounded-lg border border-slate-100 items-center justify-center bg-slate-50/50">
                        <span class="uppercase tracking-wider text-[10px] font-bold text-slate-400 mb-1">Session</span>
                        <span class="font-bold text-slate-700 text-sm">{{ $form->session }}</span>
                    </div>
                </div>

                <!-- Apply Now -->
                <a href="{{ route('student.application.form', ['form_id' => $form->id]) }}" class="block text-center w-full py-3 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 hover:scale-[1.02] shadow-lg shadow-slate-200 hover:shadow-indigo-200 transition-all duration-300">
                    Apply Now
                </a>
            </div>
        </div>
        @empty
        <!-- Empty State -->
        <div class="col-span-full text-center py-12">
            <div class="bg-slate-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 text-slate-400">
                <i data-lucide="inbox" size="32"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800">No Applications Available</h3>
            <p class="text-slate-500 mt-2">There are currently no open applications for this institution.</p>
        </div>
        @endforelse
    </div>
</div>
</x-guest-layout>
