@props([
    'icon' => null,
    'variant' => 'primary',
    'size' => 'md',
])

@php
    $baseClasses = 'flex items-center justify-center rounded-2xl transition-transform';
    
    $variants = [
        'primary' => 'bg-primary-light text-primary',
        'secondary' => 'bg-secondary-light text-secondary',
        'success' => 'bg-success-light text-success',
        'danger' => 'bg-danger-light text-danger',
        'warning' => 'bg-warning-light text-warning',
        'info' => 'bg-info-light text-info',
        'blue' => 'bg-blue-50 text-blue-600',
        'emerald' => 'bg-emerald-50 text-emerald-600',
        'teal' => 'bg-teal-50 text-teal-600',
        'amber' => 'bg-amber-50 text-amber-600',
        'rose' => 'bg-rose-50 text-rose-600',
        'white' => 'bg-white/20 text-white backdrop-blur-md',
    ];

    $sizes = [
        'sm' => 'w-10 h-10',
        'md' => 'w-12 h-12',
        'lg' => 'w-14 h-14',
    ];

    $iconSizes = [
        'sm' => 20,
        'md' => 24,
        'lg' => 28,
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <i data-lucide="{{ $icon }}" size="{{ $iconSizes[$size] ?? 24 }}"></i>
    @endif
    {{ $slot }}
</div>
