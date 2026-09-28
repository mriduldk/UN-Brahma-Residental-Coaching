@props([
    'title',
    'icon',
    'href' => '#',
    'variant' => 'amber',
])

@php
    $variants = [
        'amber' => 'from-amber-500 to-amber-600 shadow-amber-200',
        'rose' => 'from-rose-500 to-rose-600 shadow-rose-200',
        'blue' => 'from-blue-500 to-blue-600 shadow-blue-200',
    ];
    $classes = $variants[$variant] ?? $variants['amber'];
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => "group bg-gradient-to-br $classes rounded-3xl p-6 text-white shadow-lg flex items-center justify-between transition-all hover:shadow-xl hover:-translate-y-1"]) }}>
    <div class="flex items-center gap-4">
        <x-ui.icon-box :icon="$icon" variant="white" size="lg" class="group-hover:scale-110" />
        <div>
            <h4 class="text-lg font-bold">{{ $title }}</h4>
            <p class="text-white/80 text-sm">{{ $slot }}</p>
        </div>
    </div>
    <div class="hidden sm:block">
         <i data-lucide="chevron-right" size="24" class="opacity-50"></i>
    </div>
</a>
