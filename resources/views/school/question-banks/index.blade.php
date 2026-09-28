@extends('layouts.school')

@section('title', 'Question Banks')

@section('content')
<div class="space-y-8" x-data="{ 
    confirmDelete(id, title) {
        Swal.fire({
            title: 'Delete Question Bank?',
            text: `You are about to delete '${title}'. This action will also remove all questions within this bank.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete Bank',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'rounded-[2rem]',
                confirmButton: 'rounded-xl font-bold py-3 px-6',
                cancelButton: 'rounded-xl font-bold py-3 px-6'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-bank-${id}`).submit();
            }
        })
    }
}">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-indigo-950 tracking-tight">Question Banks</h1>
            <p class="text-slate-500 text-sm mt-1">Manage centralized repositories of questions for your exams.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('administration.question-banks.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 border border-transparent rounded-2xl shadow-sm text-sm font-bold text-white hover:bg-indigo-700 transition-all hover:shadow-lg hover:-translate-y-0.5">
                <i data-lucide="plus-circle" size="18"></i>
                Create New Bank
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-3xl shadow-sm flex items-center gap-3 animate-in fade-in slide-in-from-top-4">
            <i data-lucide="check-circle" size="20"></i>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($banks as $bank)
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8 hover:shadow-xl hover:-translate-y-1 transition-all group relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-indigo-50 opacity-0 group-hover:opacity-100 rounded-full transition-opacity"></div>
                
                <div class="relative">
                    <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-6">
                        <i data-lucide="database" size="28"></i>
                    </div>

                    <h3 class="text-xl font-black text-slate-800 mb-2 truncate">{{ $bank->title }}</h3>
                    <p class="text-slate-400 text-sm line-clamp-2 mb-6 h-10">{{ $bank->description ?? 'No description provided.' }}</p>

                    <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Questions</span>
                            <span class="text-lg font-black text-indigo-600">{{ $bank->questions_count }}</span>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('administration.question-banks.show', $bank) }}" class="p-2.5 bg-slate-50 text-slate-400 hover:bg-indigo-600 hover:text-white rounded-xl transition-all" title="Manage Questions">
                                <i data-lucide="list-plus" size="20"></i>
                            </a>
                            <a href="{{ route('administration.question-banks.edit', $bank) }}" class="p-2.5 bg-slate-50 text-slate-400 hover:bg-blue-600 hover:text-white rounded-xl transition-all" title="Edit Bank">
                                <i data-lucide="edit-3" size="20"></i>
                            </a>
                            <form action="{{ route('administration.question-banks.destroy', $bank) }}" method="POST" id="delete-bank-{{ $bank->id }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" @click="confirmDelete('{{ $bank->id }}', '{{ $bank->title }}')" class="p-2.5 bg-slate-50 text-slate-400 hover:bg-rose-600 hover:text-white rounded-xl transition-all" title="Delete Bank">
                                    <i data-lucide="trash-2" size="20"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center bg-white rounded-[2.5rem] border border-slate-100 border-dashed">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                    <i data-lucide="database" size="32"></i>
                </div>
                <h3 class="text-slate-800 font-bold text-lg">No Question Banks</h3>
                <p class="text-slate-400 text-sm mb-6">Create your first bank to organize your examination questions.</p>
                <a href="{{ route('administration.question-banks.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-600/20">
                    <i data-lucide="plus" size="18"></i>
                    Create Bank
                </a>
            </div>
        @endforelse
    </div>

    @if($banks->hasPages())
        <div class="mt-8">
            {{ $banks->links() }}
        </div>
    @endif
</div>
@endsection
