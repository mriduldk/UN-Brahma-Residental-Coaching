@props([
    'variant' => 'info',
    'size' => 'md',
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-bold rounded-full uppercase tracking-wider';
    
    $variants = [
        'primary' => 'bg-primary-light text-primary',
        'secondary' => 'bg-secondary-light text-secondary',
        'success' => 'bg-success-light text-success',
        'danger' => 'bg-danger-light text-danger',
        'warning' => 'bg-warning-light text-warning',
        'info' => 'bg-info-light text-info',
    ];

    $sizes = [
        'sm' => 'px-2 py-0.5 text-[10px]',
        'md' => 'px-3 py-1 text-xs',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['info']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
