@extends('errors.layout')

@section('title', 'Access Denied')

@section('icon')
    <i data-lucide="shield-alert" class="w-12 h-12 text-red-500"></i>
@endsection

@section('code', 'Access Denied')

@section('message')
    {{ $exception->getMessage() ?: 'Restricted Area' }}
@endsection

@section('description')
    {{ $exception->getMessage() ? 'You do not have permission to perform this action.' : 'Sorry, you are not authorized to access or view this data. This area is restricted to authorized personnel only.' }}
@endsection
