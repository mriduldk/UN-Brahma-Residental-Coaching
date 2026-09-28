@props([
    'disabled' => false,
    'searchable' => false,
    'name' => '',
    'id' => '',
    'placeholder' => 'Select an option',
])

@if($searchable)
    <div 
        x-data="{
            open: false,
            search: '',
            selectedVal: '',
            selectedLabel: '{{ $placeholder }}',
            options: [],
            init() {
                this.syncOptions();
                
                // Set up MutationObserver to re-sync options when JS changes native options
                const observer = new MutationObserver(() => this.syncOptions());
                observer.observe(this.$refs.nativeSelect, { childList: true, subtree: true });
                
                // Watch selectedVal to sync back to the hidden/native select
                this.$watch('selectedVal', (val) => {
                    if (this.$refs.nativeSelect.value !== val) {
                        this.$refs.nativeSelect.value = val;
                        // Trigger native change event so standard form submit/JS event listeners fire
                        this.$refs.nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            },
            syncOptions() {
                const select = this.$refs.nativeSelect;
                const parsed = [];
                for (let i = 0; i < select.options.length; i++) {
                    const opt = select.options[i];
                    parsed.push({
                        value: opt.value,
                        label: opt.text,
                        selected: opt.selected
                    });
                }
                this.options = parsed;
                // Update selectedVal and selectedLabel if standard select changed
                const active = this.options.find(o => o.value === select.value);
                if (active) {
                    this.selectedVal = active.value;
                    this.selectedLabel = active.label;
                } else if (select.options.length > 0) {
                    this.selectedVal = select.options[0].value;
                    this.selectedLabel = select.options[0].text;
                } else {
                    this.selectedVal = '';
                    this.selectedLabel = '{{ $placeholder }}';
                }
            },
            get filteredOptions() {
                if (!this.search) return this.options;
                const q = this.search.toLowerCase();
                return this.options.filter(o => o.label.toLowerCase().includes(q));
            },
            selectOption(opt) {
                this.selectedVal = opt.value;
                this.selectedLabel = opt.label;
                this.open = false;
                this.search = '';
            }
        }"
        class="relative w-full"
        @click.away="open = false"
    >
        <!-- Native select element hidden from view but kept for form submissions and JS listeners -->
        <select 
            x-ref="nativeSelect"
            name="{{ $name }}"
            id="{{ $id }}"
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->except(['class', 'name', 'id']) }}
            style="display: none;"
        >
            {{ $slot }}
        </select>

        <!-- Custom Dropdown Trigger Button -->
        <div class="relative group">
            <button
                type="button"
                :disabled="{{ $disabled ? 'true' : 'false' }}"
                @click="open = !open"
                class="w-full appearance-none text-left bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-3 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer group-hover:bg-slate-100 group-hover:border-slate-300 shadow-sm disabled:opacity-50 disabled:bg-slate-100 disabled:cursor-not-allowed flex items-center justify-between"
            >
                <span x-text="selectedLabel" class="truncate"></span>
                <span class="text-slate-400 group-hover:text-indigo-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </span>
            </button>
        </div>

        <!-- Dropdown Menu -->
        <div
            x-show="open"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute left-0 mt-2 w-full bg-white border border-slate-200 rounded-2xl shadow-xl z-50 overflow-hidden"
            style="display: none;"
        >
            <!-- Search Input -->
            <div class="p-3 border-b border-slate-100">
                <div class="relative">
                    <input
                        type="text"
                        x-model="search"
                        placeholder="Search..."
                        class="w-full bg-slate-50 border border-slate-200 text-slate-700 rounded-xl pl-9 pr-4 py-2.5 text-xs focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all"
                        @keydown.escape="open = false"
                        x-ref="searchInput"
                        x-effect="if(open) { $nextTick(() => $refs.searchInput.focus()); }"
                    >
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Options List -->
            <ul class="max-h-60 overflow-y-auto py-1">
                <template x-for="opt in filteredOptions" :key="opt.value + opt.label">
                    <li>
                        <button
                            type="button"
                            @click="selectOption(opt)"
                            class="w-full text-left px-4 py-2.5 text-xs text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors flex items-center justify-between"
                            :class="{'bg-indigo-50/50 font-bold text-indigo-600': selectedVal === opt.value}"
                        >
                            <span x-text="opt.label" class="truncate"></span>
                            <span x-show="selectedVal === opt.value">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </span>
                        </button>
                    </li>
                </template>
                <template x-if="filteredOptions.length === 0">
                    <li class="px-4 py-6 text-center text-xs text-slate-400">
                        No results found
                    </li>
                </template>
            </ul>
        </div>
    </div>
@else
    <!-- Standard non-searchable premium styling -->
    <div class="relative group w-full">
        <select 
            name="{{ $name }}"
            id="{{ $id }}"
            {{ $disabled ? 'disabled' : '' }}
            {!! $attributes->merge(['class' => 'w-full appearance-none bg-slate-50 border border-slate-200 text-slate-700 font-medium rounded-xl px-4 py-3 pr-10 text-sm focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-400 outline-none transition-all cursor-pointer group-hover:bg-slate-100 group-hover:border-slate-300 shadow-sm disabled:opacity-50 disabled:bg-slate-100 disabled:cursor-not-allowed']) !!}
        >
            {{ $slot }}
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 group-hover:text-indigo-500 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </div>
@endif
