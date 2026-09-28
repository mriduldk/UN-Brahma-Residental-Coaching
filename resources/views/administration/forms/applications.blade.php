@extends('layouts.administration')

@section('title', 'Form Responses')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('administration.forms') }}" class="text-slate-500 hover:text-slate-800 bg-white p-2 rounded-lg border border-slate-200 hover:shadow-sm transition-all">
                <i data-lucide="arrow-left" size="20"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Applications</h2>
                <p class="text-slate-500 text-sm">Managing responses for <span class="font-bold text-blue-600">Regular Admission</span></p>
            </div>
        </div>
        <div class="flex space-x-2">
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" size="16"></i>
                <input type="text" placeholder="Search..." class="pl-9 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm w-64 bg-white">
            </div>
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm flex items-center font-medium shadow-sm transition-all hover:-translate-y-0.5">
                <i data-lucide="download" size="16" class="mr-2"></i> Export Excel
            </button>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4">App ID</th>
                    <th class="px-6 py-4">Applicant</th>
                    <th class="px-6 py-4">Trade</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-mono font-medium text-slate-600">ITI-2025-101</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">RV</div>
                            <span class="font-bold text-slate-800">Rahul Verma</span>
                        </div>
                    </td>
                    <td class="px-6 py-4"><span class="bg-blue-50 text-blue-700 px-2.5 py-1 rounded-lg text-xs font-bold border border-blue-100">Electrician</span></td>
                    <td class="px-6 py-4"><span class="bg-green-50 text-green-700 px-2.5 py-1 rounded-full text-xs font-bold border border-green-100 flex items-center w-fit"><i data-lucide="check" size="12" class="mr-1"></i> Approved</span></td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="window.location.href='{{ route('administration.applications.detail') }}'" class="text-slate-400 hover:text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition-all"><i data-lucide="eye" size="18"></i></button>
                    </td>
                </tr>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-mono font-medium text-slate-600">ITI-2025-102</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold text-xs">PG</div>
                            <span class="font-bold text-slate-800">Priya Gogoi</span>
                        </div>
                    </td>
                    <td class="px-6 py-4"><span class="bg-purple-50 text-purple-700 px-2.5 py-1 rounded-lg text-xs font-bold border border-purple-100">Fitter</span></td>
                    <td class="px-6 py-4"><span class="bg-yellow-50 text-yellow-700 px-2.5 py-1 rounded-full text-xs font-bold border border-yellow-100 flex items-center w-fit"><i data-lucide="clock" size="12" class="mr-1"></i> Pending</span></td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="window.location.href='{{ route('administration.applications.detail') }}'" class="text-slate-400 hover:text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition-all"><i data-lucide="eye" size="18"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
