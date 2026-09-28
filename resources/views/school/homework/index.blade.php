@extends('layouts.school')

@section('content')
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <x-ui.page-header title="Homework & Assignments" subtitle="Manage homework for your classes and sections"
                class="mb-0" />
            <a href="{{ route('school.homework.create') }}">
                <x-ui.button variant="primary" icon="plus">
                    Assign New
                </x-ui.button>
            </a>
        </div>

        <!-- Filters -->
        <x-ui.card padding="p-4" class="mb-6">
            <form method="GET" action="{{ route('school.homework.index') }}"
                class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Class</label>
                    <x-ui.select name="class_id" id="classSelect" searchable placeholder="All Classes"
                        onchange="updateSections()">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}
                                data-sections='{{ json_encode($class->sections) }}'>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </x-ui.select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Section</label>
                    <x-ui.select name="section_id" id="sectionSelect" searchable placeholder="All Sections">
                        <option value="">All Sections</option>
                    </x-ui.select>
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Type</label>
                    <x-ui.select name="type" placeholder="All Types">
                        <option value="">All Types</option>
                        <option value="homework" {{ request('type') == 'homework' ? 'selected' : '' }}>Homework</option>
                        <option value="assignment" {{ request('type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                    </x-ui.select>
                </div>
                <div>
                    <x-ui.button variant="secondary" type="submit" class="w-full md:w-auto">
                        Filter
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>

        <!-- Homework List -->
        <x-ui.card padding="p-0" class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Title
                                / Subject</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Class
                                (Section)</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Date
                            </th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Due
                                Date</th>
                            <th scope="col"
                                class="px-6 py-4 class text-right text-xs font-bold text-slate-400 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($homeworks as $homework)
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="text-sm font-bold text-foreground">{{ $homework->title }}</div>
                                        @if(($homework->type ?? 'homework') === 'assignment')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-wider">
                                                Assignment
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 uppercase tracking-wider">
                                                Homework
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-muted font-medium mt-0.5">{{ $homework->subject_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-foreground">{{ $homework->schoolClass->name }}</div>
                                    <div class="text-xs text-muted font-medium mt-0.5">
                                        {{ $homework->section ? $homework->section->name : 'All Sections' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-muted font-medium">
                                    {{ $homework->homework_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-muted font-medium">
                                    {{ $homework->due_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('school.homework.show', $homework->id) }}"
                                        class="text-primary hover:text-primary-hover font-bold mr-4">View</a>
                                    <a href="{{ route('school.homework.edit', $homework->id) }}"
                                        class="text-info hover:text-info-hover font-bold mr-4">Edit</a>
                                    <form action="{{ route('school.homework.destroy', $homework->id) }}" method="POST"
                                        class="inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-danger hover:text-danger-hover font-bold">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 whitespace-nowrap text-center text-sm text-muted font-medium">
                                    No homework assigned yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($homeworks->hasPages())
                <div class="px-6 py-4 border-t border-border">
                    {{ $homeworks->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
@endsection

@push('scripts')
    <script>
        function updateSections() {
            const classSelect = document.getElementById('classSelect');
            const sectionSelect = document.getElementById('sectionSelect');
            if (!classSelect || !sectionSelect) return;

            const selectedOption = classSelect.options[classSelect.selectedIndex];

            // Clear existing sections
            sectionSelect.innerHTML = '<option value="">All Sections</option>';

            if (selectedOption && selectedOption.value && selectedOption.hasAttribute('data-sections')) {
                try {
                    const sections = JSON.parse(selectedOption.getAttribute('data-sections'));
                    const selectedSection = '{{ request('section_id') }}';

                    sections.forEach(section => {
                        const option = document.createElement('option');
                        option.value = section.id;
                        option.textContent = section.name;
                        if (selectedSection == section.id) {
                            option.selected = true;
                        }
                        sectionSelect.appendChild(option);
                    });
                } catch (e) {
                    console.error('Error parsing sections json');
                }
            }
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', updateSections);
    </script>
@endpush