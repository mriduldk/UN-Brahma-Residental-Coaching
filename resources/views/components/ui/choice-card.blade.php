@props([
    'name',
    'value',
    'title',
    'description' => null,
    'icon' => null,
])

<label class="group relative flex items-center p-6 bg-slate-50 border-2 border-slate-200 rounded-2xl cursor-pointer hover:border-blue-600 hover:bg-blue-50 transition-all duration-300">
    <input type="radio" name="{{ $name }}" value="{{ $value }}" {{ $attributes->merge(['class' => 'hidden peer']) }}>
    
    <x-ui.icon-box :icon="$icon" variant="blue" size="md" class="mr-5 !bg-white group-hover:!text-blue-600 shadow-sm" />
    
    <div class="flex-1">
        <h3 class="font-bold text-slate-900 group-hover:text-blue-600 transition-colors">{{ $title }}</h3>
        @if($description)
            <p class="text-sm text-slate-500 group-hover:text-blue-500 transition-colors">{{ $description }}</p>
        @endif
    </div>
    
    <div class="w-6 h-6 border-2 border-slate-200 rounded-full flex items-center justify-center group-hover:border-blue-600 transition-all">
        <div class="w-3 h-3 bg-blue-600 rounded-full opacity-0 peer-checked:opacity-100 transition-opacity"></div>
    </div>
    
    <div class="absolute inset-0 border-2 border-blue-600 rounded-2xl opacity-0 peer-checked:opacity-100 pointer-events-none"></div>
</label>
