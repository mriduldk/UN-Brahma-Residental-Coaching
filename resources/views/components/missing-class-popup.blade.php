@php
    $hasSession = true;
    $hasClass = true;
    $isSchoolAdmin = false;
    
    if (auth()->check() && auth()->user()->currentOrganization()) {
        $orgId = auth()->user()->currentOrganization()->id;
        $hasSession = \App\Models\School\AcademicYear::where('organization_id', $orgId)->exists();
        $hasClass = \App\Models\School\SchoolClass::where('organization_id', $orgId)->exists();
        $isSchoolAdmin = auth()->user()->role === 'administration' || auth()->user()->role === 'school';
    }
@endphp

@if($hasSession && !$hasClass)
<!-- Missing Class/Section Popup Component -->
<div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-md shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-300">
        <div class="bg-red-500 p-8 text-white relative flex flex-col items-center justify-center text-center">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-white/20 rounded-full blur-3xl"></div>
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mb-4 backdrop-blur-md">
                <i data-lucide="book-open" size="32" class="text-white"></i>
            </div>
            <h3 class="text-2xl font-black tracking-tight relative z-10">Class & Section Required</h3>
            <p class="text-red-100 text-sm mt-2 relative z-10">You need to configure at least one class first.</p>
        </div>
        <div class="p-8 text-center space-y-6">
            <p class="text-slate-600 font-medium text-sm">
                Classes and sections must be defined before you can view, manage, or map student enrollments.
                @if(!$isSchoolAdmin)
                    <br><br><strong>Please contact your institution's administrator to configure classes and sections.</strong>
                @endif
            </p>
            
            @if($isSchoolAdmin)
            <a href="{{ route('school.classes.index') }}" class="w-full inline-flex items-center justify-center py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black uppercase tracking-widest rounded-3xl shadow-lg shadow-indigo-200 transition-all gap-2 group">
                Create Class & Section <i data-lucide="arrow-right" size="18" class="group-hover:translate-x-1 transition-transform"></i>
            </a>
            <a href="{{ route('school.dashboard') }}" class="block text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">
                Go to Dashboard
            </a>
            @else
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}" class="block text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
                Go Back
            </a>
            @endif
        </div>
    </div>
</div>
@endif
