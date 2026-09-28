@props([
    'title',
    'value',
    'icon',
    'variant' => 'primary',
    'subtitle' => null,
])

<x-ui.card animate padding="p-6" class="relative group overflow-hidden">
    {{-- Background Decoration --}}
    <div @class([
        'absolute top-0 right-0 -mr-4 -mt-4 w-24 h-24 rounded-full opacity-50 group-hover:scale-110 transition-transform',
        'bg-blue-50' => $variant === 'primary',
        'bg-teal-50' => $variant === 'teal',
        'bg-emerald-50' => $variant === 'emerald',
        'bg-green-50' => $variant === 'green',
        'bg-orange-50' => $variant === 'orange',
        'bg-indigo-50' => $variant === 'indigo',
        'bg-amber-50' => $variant === 'amber',
        'bg-rose-50' => $variant === 'rose',
    ])></div>

    <div class="relative">
        <x-ui.icon-box :icon="$icon" :variant="$variant" class="mb-4" />
        
        <p class="text-[10px] font-bold text-muted uppercase tracking-widest">{{ $title }}</p>
        
        @if($subtitle)
            <p class="text-[10px] text-muted font-bold">{{ $subtitle }}</p>
        @endif

        <div class="flex items-baseline gap-2 mt-1">
            <h3 @class([
                'text-3xl font-black text-foreground',
                'text-orange-600' => $variant === 'orange',
                'text-indigo-600' => $variant === 'indigo',
                'text-amber-600' => $variant === 'amber',
                'text-rose-600' => $variant === 'rose',
            ])>{{ $value }}</h3>
            {{ $slot }}
        </div>
    </div>
</x-ui.card>
