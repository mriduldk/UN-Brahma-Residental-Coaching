@extends('errors.layout')

@section('title', 'Server Error')

@section('icon')
    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-500"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><line x1="9" y1="10" x2="9.01" y2="10"/><line x1="15" y1="10" x2="15.01" y2="10"/><line x1="12" y1="10" x2="12.01" y2="10"/></svg>
@endsection

@section('code', '500')

@section('message', 'Something Went Wrong')

@section('description')
    Our servers encountered an unexpected issue. We're working on fixing it. Please try again later.
@endsection
