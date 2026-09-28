@props([
    'padding' => 'p-6',
    'animate' => false,
])

@php
    $baseClasses = 'bg-surface border border-border rounded-3xl shadow-sm transition-all';
    if ($animate) {
        $baseClasses .= ' hover:shadow-xl hover:-translate-y-1';
    }
@endphp

<div {{ $attributes->merge(['class' => $baseClasses . ' ' . $padding]) }}>
    {{ $slot }}
</div>
