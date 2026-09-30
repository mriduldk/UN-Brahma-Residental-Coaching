@extends('layouts.student')

@push('styles')
    <style>
        /* Custom scrollbar for dropdown */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endpush

@section('content')
<div class="space-y-8">
    
    <!-- Hero Filter Section -->
    <div class="relative z-20 bg-gradient-to-br from-indigo-50/80 via-white to-cyan-50/80 rounded-[2rem] p-8 md:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.06)] border border-blue-100 isolate">
        <!-- Abstract Background Elements -->
        <div class="absolute top-0 right-0 -z-10 w-[600px] h-[600px] bg-gradient-to-tr from-blue-300 to-indigo-400 rounded-full blur-[100px] opacity-30 translate-x-1/3 -translate-y-1/4 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -z-10 w-[500px] h-[500px] bg-gradient-to-tr from-emerald-300 to-teal-400 rounded-full blur-[100px] opacity-30 -translate-x-1/3 translate-y-1/3 pointer-events-none"></div>

        <div class="max-w-4xl mx-auto text-center mb-8 relative z-10">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight mb-3 drop-shadow-sm">
                Discover <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Applications</span>
            </h2>
            <p class="text-slate-600 text-lg font-medium">Search and filter to find the perfect application for your future.</p>
        </div>

        <form method="GET" action="{{ route('student.applications.index') }}" class="relative z-50 max-w-5xl mx-auto flex flex-col md:flex-row gap-4 w-full items-center p-2 bg-white/60 backdrop-blur-xl rounded-2xl md:rounded-[1.25rem] shadow-sm border border-slate-200/60 transition-shadow hover:shadow-md hover:border-blue-100/60 duration-300">
            
            <!-- Search Input -->
            <div class="relative w-full flex-1 group">
                <i data-lucide="search" class="absolute left-4 top-1/2 transform -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors" size="20"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, code, or keyword..." class="w-full pl-12 pr-4 py-3 bg-transparent border-none focus:ring-0 outline-none h-[48px] text-slate-700 placeholder:text-slate-400 font-medium">
            </div>

            <!-- Divider (Desktop) -->
            <div class="hidden md:block w-px h-10 bg-slate-200"></div>

            <!-- Custom Select Dropdown -->
            <div class="w-full md:w-[360px] shrink-0 border-t md:border-t-0 border-slate-100 pt-2 md:pt-0 relative" id="custom-select-container">
                <input type="hidden" name="institute_id" id="institute_id_input" value="{{ request('institute_id') }}">
                
                <!-- Select Button -->
                <button type="button" id="custom-select-button" class="w-full h-[48px] px-5 flex items-center justify-between bg-white/90 backdrop-blur-md border border-slate-200/80 rounded-[1rem] hover:border-blue-400 focus:outline-none focus:ring-4 focus:ring-blue-500/15 transition-all text-slate-700 font-medium text-[15px] shadow-sm">
                    <span id="custom-select-text" class="truncate pr-2">
                        @php
                            $selectedInst = $allActiveInstitutions->firstWhere('id', request('institute_id'));
                            echo $selectedInst ? ($selectedInst->institution_name . ' (' . $selectedInst->institution_code . ')') : 'All Institutions';
                        @endphp
                    </span>
                    <i data-lucide="chevron-down" size="18" class="text-slate-400 shrink-0 transition-transform duration-300" id="custom-select-icon"></i>
                </button>

                <!-- Dropdown Menu -->
                <div id="custom-select-dropdown" class="absolute z-[100] w-full mt-2 bg-white border border-slate-200 rounded-[1rem] shadow-xl shadow-slate-200/50 opacity-0 invisible transform scale-95 transition-all duration-200 origin-top overflow-hidden">
                    <!-- Search -->
                    <div class="p-3 border-b border-slate-100 bg-slate-50/80 backdrop-blur-sm">
                        <div class="relative flex items-center">
                            <i data-lucide="search" size="16" class="absolute left-3 text-slate-400"></i>
                            <input type="text" id="custom-select-search" class="w-full pl-9 pr-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 placeholder-slate-400 font-medium transition-all" placeholder="Search institutions..." autocomplete="off">
                        </div>
                    </div>
                    <!-- Options List -->
                    <ul id="custom-select-list" class="max-h-[280px] overflow-y-auto py-2 custom-scrollbar">
                        <li class="custom-option px-4 py-3 text-sm text-slate-700 hover:bg-blue-50 hover:text-blue-700 cursor-pointer transition-colors flex items-center justify-between mx-2 rounded-lg" data-value="">
                            <span class="font-bold">All Institutions</span>
                            @if(!request('institute_id'))
                                <i data-lucide="check" size="16" class="text-blue-600"></i>
                            @endif
                        </li>
                        @foreach($allActiveInstitutions as $inst)
                            <li class="custom-option px-4 py-2.5 text-sm text-slate-700 hover:bg-blue-50 cursor-pointer transition-colors flex items-center justify-between group mx-2 rounded-lg" data-value="{{ $inst->id }}" data-search="{{ strtolower($inst->institution_name . ' ' . $inst->institution_code) }}">
                                <div class="flex flex-col truncate pr-4">
                                    <span class="font-medium text-slate-800 group-hover:text-blue-800 transition-colors truncate">{{ $inst->institution_name }}</span>
                                    <span class="text-[11px] font-bold tracking-wide text-slate-400 uppercase mt-0.5 group-hover:text-blue-500 transition-colors">Code: {{ $inst->institution_code }}</span>
                                </div>
                                @if(request('institute_id') == $inst->id)
                                    <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                        <i data-lucide="check" size="14" class="text-blue-600"></i>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <!-- No Results -->
                    <div id="custom-select-no-results" class="hidden p-6 text-center text-sm text-slate-500 bg-slate-50/50">
                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i data-lucide="inbox" size="20" class="text-slate-400"></i>
                        </div>
                        <p class="font-medium text-slate-600">No institutions found.</p>
                        <p class="text-xs mt-1">Try a different search term</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="w-full md:w-auto flex gap-2 shrink-0 border-t md:border-t-0 border-slate-100 pt-2 md:pt-0 pl-0 md:pl-2">
                <button type="submit" class="flex-1 md:flex-none bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:from-blue-700 hover:to-indigo-700 focus:ring-4 focus:ring-blue-500/20 active:scale-[0.98] transition-all shadow-md shadow-blue-500/20 h-[48px] flex items-center justify-center gap-2 relative overflow-hidden group">
                    <span class="absolute inset-0 w-full h-full bg-white/20 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-500 ease-out"></span>
                    <span>Search</span>
                    <i data-lucide="arrow-right" size="18" class="group-hover:translate-x-1 transition-transform"></i>
                </button>
                
                @if(request('search') || request('institute_id'))
                    <a href="{{ route('student.applications.index') }}" class="w-auto text-slate-500 hover:text-red-500 hover:bg-red-50 px-4 py-3 rounded-xl font-medium transition-colors h-[48px] flex items-center justify-center border border-slate-200 hover:border-red-100" title="Clear Filters">
                        <i data-lucide="x" size="20"></i>
                    </a>
                @endif
            </div>
        </form>
        
        <div class="mt-8 flex flex-wrap justify-center gap-3 text-sm">
            @if(request('search') || request('institute_id'))
                <div class="flex items-center gap-2 px-4 py-2 bg-emerald-50 rounded-full border border-emerald-100 text-emerald-700 font-medium">
                    <i data-lucide="check-circle-2" size="16"></i>
                    Found {{ $forms->total() }} Results
                </div>
            @else
                <div class="flex items-center gap-2 px-4 py-2 bg-slate-50 rounded-full border border-slate-100 text-slate-600 font-medium">
                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                    {{ $forms->total() }} Total Applications
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($forms as $form)
        <!-- Dynamic Card -->
        <div class="bg-white rounded-2xl p-6 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] hover:shadow-xl transition-all duration-300 flex flex-col h-full border border-slate-100 group relative overflow-hidden">
            <!-- Decorative Background -->
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-full opacity-50 blur-2xl group-hover:bg-blue-100 transition-all duration-500"></div>

            <div class="flex justify-between items-start mb-4 relative z-10">
                <span class="bg-blue-50 text-blue-600 p-2 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                     <i data-lucide="file-text" size="20"></i>
                </span>
                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-md {{ $form->status == 'active' ? 'bg-green-50 text-green-600' : 'bg-slate-100 text-slate-500' }}">
                    {{ $form->status }}
                </span>
            </div>
            
            <h3 class="text-xl font-bold text-slate-800 mb-1 leading-tight group-hover:text-blue-600 transition-colors relative z-10">{{ $form->application_name }}</h3>

            <p class="text-sm text-slate-500 mb-6 line-clamp-2 leading-relaxed relative z-10">{{ $form->application_description ?? 'No description available for this application.' }}</p>
            
            <div class="mt-auto space-y-3 relative z-10">
                <div class="flex items-center text-sm text-slate-600 bg-slate-50 p-2.5 rounded-lg">
                    <i data-lucide="calendar" size="14" class="mr-2.5 text-slate-400"></i>
                    <span class="font-medium">Deadline:</span>
                    <span class="ml-auto font-bold text-right">
                        {{ $form->deadline ? $form->deadline->format('d M, Y') : 'N/A' }}
                        @if($form->deadline && $form->deadline->isToday())
                            <span class="text-[11px] text-amber-600 font-semibold block">Closes Today 11:59 PM</span>
                        @endif
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-3 text-xs text-slate-500">
                    <div class="flex flex-col p-2 rounded-lg border border-slate-100 items-center justify-center bg-slate-50/50 text-center">
                        <span class="uppercase tracking-wider text-[10px] font-bold text-slate-400 mb-1">Fee</span>
                        <span class="font-bold text-slate-700 text-[13px]">
                            @php
                                $app = $studentApplications[$form->id] ?? null;
                                $calculatedFee = 0;
                                if ($form->is_application_fee_required) {
                                    if ($student) {
                                        $calculatedFee = $form->getApplicationFeeFor($student, $app);
                                    } else {
                                        $calculatedFee = !empty($form->category_fees) ? -1 : $form->application_fee;
                                    }
                                }
                            @endphp
                            @if(!$form->is_application_fee_required || ($calculatedFee == 0))
                                Not Required
                            @elseif($calculatedFee == -1)
                                Varies
                            @else
                                ₹{{ number_format($calculatedFee, 0) }}
                            @endif
                        </span>
                    </div>
                    <div class="flex flex-col p-2 rounded-lg border border-slate-100 items-center justify-center bg-slate-50/50">
                        <span class="uppercase tracking-wider text-[10px] font-bold text-slate-400 mb-1">Session</span>
                        <span class="font-bold text-slate-700 text-sm">{{ $form->session }}</span>
                    </div>
                </div>

                @php
                    $isExpired = $form->isExpired();
                @endphp

                @if(isset($studentApplications[$form->id]))
                    @if($studentApplications[$form->id]->status == 'draft')
                        <!-- Draft: Update Application -->
                        @if($isExpired)
                            <button disabled class="block text-center w-full py-3 bg-slate-300 text-slate-500 font-medium rounded-xl cursor-not-allowed">
                                Application Closed
                            </button>
                        @else
                            <a href="{{ route('student.application.form', ['form_id' => $form->id]) }}" class="block text-center w-full py-3 bg-amber-500 text-white font-medium rounded-xl hover:bg-amber-600 hover:scale-[1.02] shadow-lg shadow-slate-200 transition-all duration-300">
                                Update Application
                            </a>
                        @endif
                    @else
                        <!-- Submitted/Completed: View Application -->
                        <a href="{{ route('student.application.show', $studentApplications[$form->id]->id) }}" class="block text-center w-full py-3 bg-slate-800 text-white font-medium rounded-xl hover:bg-slate-900 hover:scale-[1.02] shadow-lg shadow-slate-200 transition-all duration-300">
                            View Application
                        </a>
                    @endif
                @else
                    <!-- Not Applied Yet: Apply Now -->
                    @if($isExpired)
                        <button disabled class="block text-center w-full py-3 bg-slate-300 text-slate-500 font-medium rounded-xl cursor-not-allowed">
                            Application Closed
                        </button>
                    @else
                        <a href="{{ route('student.application.form', ['form_id' => $form->id]) }}" class="block text-center w-full py-3 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 hover:scale-[1.02] shadow-lg shadow-slate-200 hover:shadow-indigo-200 transition-all duration-300">
                            Apply Now
                        </a>
                    @endif
                @endif
            </div>
        </div>
        @empty
        <!-- Empty State -->
        <div class="col-span-full text-center py-12">
            <div class="bg-slate-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 text-slate-400">
                <i data-lucide="inbox" size="32"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800">No Applications Available</h3>
            <p class="text-slate-500 mt-2">There are currently no open applications for students.</p>
        </div>
        @endforelse
    </div>

    @if($forms->hasPages())
        <div class="mt-8 flex justify-center w-full">
            {{ $forms->links('pagination::tailwind') }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const button = document.getElementById('custom-select-button');
        const dropdown = document.getElementById('custom-select-dropdown');
        const input = document.getElementById('institute_id_input');
        const search = document.getElementById('custom-select-search');
        const options = document.querySelectorAll('.custom-option');
        const icon = document.getElementById('custom-select-icon');
        const form = button.closest('form');
        let isOpen = false;

        function toggleDropdown() {
            isOpen = !isOpen;
            if (isOpen) {
                dropdown.classList.remove('opacity-0', 'invisible', 'scale-95');
                dropdown.classList.add('opacity-100', 'visible', 'scale-100');
                icon.classList.add('rotate-180', 'text-blue-500');
                button.classList.add('border-blue-400', 'ring-4', 'ring-blue-500/15');
                setTimeout(() => search.focus(), 50);
            } else {
                dropdown.classList.add('opacity-0', 'invisible', 'scale-95');
                dropdown.classList.remove('opacity-100', 'visible', 'scale-100');
                icon.classList.remove('rotate-180', 'text-blue-500');
                button.classList.remove('border-blue-400', 'ring-4', 'ring-blue-500/15');
                search.value = '';
                filterOptions('');
            }
        }

        function filterOptions(searchTerm) {
            let hasVisible = false;
            options.forEach(opt => {
                const searchData = opt.getAttribute('data-search');
                // The 'All Institutions' option has no data-search and should always be visible unless searching specifically
                if (!searchData) {
                    opt.style.display = searchTerm === '' ? 'flex' : 'none';
                    return;
                }
                
                if (searchData.includes(searchTerm)) {
                    opt.style.display = 'flex';
                    hasVisible = true;
                } else {
                    opt.style.display = 'none';
                }
            });
            document.getElementById('custom-select-no-results').style.display = (!hasVisible && searchTerm !== '') ? 'block' : 'none';
        }

        button.addEventListener('click', function(e) {
            e.preventDefault();
            toggleDropdown();
        });

        search.addEventListener('input', function(e) {
            filterOptions(e.target.value.toLowerCase());
        });

        options.forEach(opt => {
            opt.addEventListener('click', function() {
                const val = this.getAttribute('data-value');
                input.value = val;
                
                // Show loading state on button before submit
                const textSpan = document.getElementById('custom-select-text');
                textSpan.innerHTML = '<div class="flex items-center gap-2"><i data-lucide="loader-2" class="animate-spin" size="16"></i> Applying Filter...</div>';
                lucide.createIcons();
                
                form.submit();
            });
        });

        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (isOpen && !document.getElementById('custom-select-container').contains(e.target)) {
                toggleDropdown();
            }
        });
        
        // Prevent form submission if enter pressed in custom search
        search.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });
    });
</script>
@endpush
