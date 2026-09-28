@extends('errors.layout')

@section('title', 'Page Not Found')

@section('icon')
    <i data-lucide="search-x" class="w-12 h-12 text-orange-500"></i> <!-- data-lucide attributes changed to classes for consistent sizing if needed, but size passed via JS usually -->
    <!-- Using inline size if JS doesn't pick up class on icon render -->
    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-orange-500"><path d="m13.5 8.5-5 5"/><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
@endsection

@section('code', '404')

@section('message', 'Page Not Found')

@section('description')
    We couldn't find the resource you're looking for. It might have been removed, or you may not have permission to view it.
@endsection
