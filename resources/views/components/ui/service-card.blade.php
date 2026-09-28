@props([
    'title',
    'icon',
    'variant' => 'blue',
])

@php
    $variants = [
        'blue' => 'from-white to-blue-50/30 border-blue-100 shadow-blue-200 bg-blue-600',
        'indigo' => 'from-white to-indigo-50/30 border-indigo-100 shadow-indigo-200 bg-indigo-600',
        'emerald' => 'from-white to-emerald-50/30 border-emerald-100 shadow-emerald-200 bg-emerald-600',
    ];
    $v = explode(' ', $variants[$variant] ?? $variants['blue']);
@endphp

<div {{ $attributes->merge(['class' => "bg-gradient-to-br $v[0] $v[1] p-8 rounded-3xl border $v[2] shadow-sm hover:shadow-xl transition-all group"]) }}>
    <x-ui.icon-box :icon="$icon" variant="primary" size="lg" class="mb-6 !text-white !bg-{{ $variant }}-600 shadow-lg shadow-{{ $variant }}-200 group-hover:scale-110" />
    <h4 class="text-lg font-black text-foreground mb-2">{{ $title }}</h4>
    <p class="text-sm text-muted leading-relaxed mb-6">{{ $slot }}</p>
    <x-ui.badge variant="{{ $variant }}" size="md">Contact Us</x-ui.badge>
</div>
