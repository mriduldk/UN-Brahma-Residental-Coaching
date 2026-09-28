@props(['href', 'active' => false, 'icon' => 'circle'])

<a href="{{ $href }}" 
   {{ $attributes->merge(['class' => ($active ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white') . ' flex items-center px-3 py-2.5 rounded-xl font-medium text-sm transition-all group overflow-hidden whitespace-nowrap gap-3']) }}>
    <i data-lucide="{{ $icon }}" 
       class="{{ $active ? 'text-white' : 'text-blue-400 group-hover:text-white' }} transition-colors" 
       size="18"></i>
    <span class="truncate">{{ $slot }}</span>
</a>
