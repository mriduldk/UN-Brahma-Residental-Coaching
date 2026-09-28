@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
    'loading' => false,
    'icon' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-bold transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-70 disabled:cursor-not-allowed';
    
    $variants = [
        'primary' => 'bg-primary text-white hover:bg-primary-hover shadow-xl shadow-primary/30 focus:ring-primary',
        'secondary' => 'bg-secondary text-white hover:bg-secondary-hover shadow-xl shadow-secondary/30 focus:ring-secondary',
        'success' => 'bg-success text-white hover:bg-success-hover shadow-xl shadow-success/30 focus:ring-success',
        'danger' => 'bg-danger text-white hover:bg-danger-hover shadow-xl shadow-danger/30 focus:ring-danger',
        'warning' => 'bg-warning text-white hover:bg-warning-hover shadow-xl shadow-warning/30 focus:ring-warning',
        'ghost' => 'bg-transparent text-muted hover:bg-secondary-light focus:ring-secondary',
        'outline' => 'bg-transparent border border-border text-foreground hover:bg-secondary-light focus:ring-secondary',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs rounded-xl',
        'md' => 'px-6 py-2.5 text-sm rounded-2xl',
        'lg' => 'px-8 py-4 text-lg rounded-2xl',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<button 
    {{ $attributes->merge(['type' => $type, 'class' => $classes]) }} 
    {{ $disabled || $loading ? 'disabled' : '' }}
>
    @if($loading)
        <i data-lucide="loader-2" class="animate-spin mr-2" size="{{ $size === 'lg' ? 24 : 18 }}"></i>
        <span>{{ $slot->isEmpty() ? 'Loading...' : $slot }}</span>
    @else
        @if($icon)
            <i data-lucide="{{ $icon }}" class="mr-2" size="{{ $size === 'lg' ? 24 : 18 }}"></i>
        @endif
        {{ $slot }}
    @endif
</button>
