<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Timetable - {{ $class->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .institution-name {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }
        .document-type {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 5px;
        }
        .info-grid {
            width: 100%;
            margin-top: 15px;
        }
        .info-item {
            width: 50%;
        }
        .label {
            font-size: 10px;
            color: #999;
            text-transform: uppercase;
            font-weight: bold;
        }
        .value {
            font-size: 16px;
            font-weight: bold;
            color: #4f46e5;
        }
        table.timetable {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        table.timetable th {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
        }
        table.timetable td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            vertical-align: top;
            height: 100px;
        }
        .day-column {
            width: 14.28%;
        }
        .slot-card {
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #f1f5f9;
            border-radius: 4px;
            background-color: #fff;
        }
        .slot-time {
            font-size: 8px;
            font-weight: bold;
            color: #94a3b8;
            margin-bottom: 4px;
        }
        .slot-subject {
            font-size: 11px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 2px;
        }
        .slot-teacher {
            font-size: 9px;
            color: #64748b;
        }
        .slot-section {
            font-size: 7px;
            display: inline-block;
            border: 1px solid #e2e8f0;
            padding: 1px 4px;
            border-radius: 2px;
            margin-top: 4px;
            color: #94a3b8;
        }
        .no-slots {
            font-size: 9px;
            color: #cbd5e1;
            font-style: italic;
            text-align: center;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <h1 class="institution-name">{{ auth()->user()->currentOrganization()->organization_name ?? 'Institution Name' }}</h1>
                    <p class="document-type">Official Academic Timetable</p>
                </td>
                <td style="text-align: right;">
                    <p class="label">Academic Year</p>
                    <p style="font-size: 14px; font-weight: bold;">{{ $activeYear->name ?? 'N/A' }}</p>
                </td>
            </tr>
        </table>

        <table class="info-grid">
            <tr>
                <td class="info-item">
                    <p class="label">Class</p>
                    <p class="value">{{ $class->name }}</p>
                </td>
                <td class="info-item">
                    <p class="label">Section</p>
                    <p class="value">{{ $section ? $section->name : 'All Sections' }}</p>
                </td>
            </tr>
        </table>
    </div>

    <table class="timetable">
        <thead>
            <tr>
                @foreach($days as $day)
                    <th class="day-column">{{ $day }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach($days as $day)
                    <td>
                        @php
                            $daySlots = collect($timetable->get($day, []))->sortBy('start_time');
                        @endphp
                        
                        @forelse($daySlots as $slot)
                            <div class="slot-card">
                                <div class="slot-time">
                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - 
                                    {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                </div>
                                <div class="slot-subject">{{ $slot->subject_name }}</div>
                                <div class="slot-teacher">{{ $slot->staff->full_name }}</div>
                                @if(!$section && $slot->section)
                                    <div class="slot-section">{{ $slot->section->name }}</div>
                                @endif
                            </div>
                        @empty
                            <div class="no-slots">No Classes</div>
                        @endforelse
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>
</body>
</html>
