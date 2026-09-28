@props([
    'title',
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'mb-8']) }}>
    <h1 class="text-3xl font-black text-foreground tracking-tight">{{ $title }}</h1>
    @if($subtitle)
        <p class="text-muted font-medium mt-2">{{ $subtitle }}</p>
    @endif
</div>
