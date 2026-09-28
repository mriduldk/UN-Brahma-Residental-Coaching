@extends('errors.layout')

@section('title', 'Session Expired')

@section('icon')
    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-yellow-500"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
@endsection

@section('code', '419')

@section('message', 'Session Expired')

@section('description')
    Your session has timed out due to inactivity. Please refresh the page or log in again to continue.
@endsection
