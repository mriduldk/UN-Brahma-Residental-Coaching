@props([
    'title',
    'subtitle' => null,
    'href' => null,
    'linkText' => 'View All',
])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8']) }}>
    <div>
        <h2 class="text-2xl font-bold text-foreground">{{ $title }}</h2>
        @if($subtitle)
            <p class="text-muted text-sm mt-1">{{ $subtitle }}</p>
        @endif
    </div>
    @if($href)
        <x-ui.button variant="ghost" :href="$href" size="sm" icon="arrow-right-circle">
            {{ $linkText }}
        </x-ui.button>
    @endif
</div>
