<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Roll Numbers - {{ $centerTitle }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 5mm 6mm 5mm 6mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #ffffff;
            color: #0f172a;
        }

        .page-break {
            page-break-after: always;
        }

        table.grid-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.grid-table td {
            width: 33.333%;
            height: 24.8mm;
            padding: 1.5mm;
            vertical-align: middle;
            text-align: center;
        }

        .roll-box {
            border: 1.5px dashed #334155;
            border-radius: 4px;
            height: 21.8mm;
            text-align: center;
            background-color: #ffffff;
            padding: 2mm 3mm;
            display: block;
        }

        .roll-text {
            font-family: 'Courier New', Courier, monospace, 'DejaVu Sans';
            font-size: 17pt;
            font-weight: bold;
            color: #0f172a;
            line-height: 21.8mm;
            letter-spacing: 1.5px;
            text-align: center;
        }

        .empty-message {
            text-align: center;
            padding-top: 60mm;
            font-size: 14pt;
            color: #64748b;
            font-weight: bold;
        }
    </style>
</head>
<body>

@php
    $chunks = $applications->chunk(30);
@endphp

@forelse($chunks as $pIdx => $pageApplications)
    @php
        $rows = $pageApplications->chunk(3);
        $isLastPage = $loop->last;
    @endphp
    <table class="grid-table {{ !$isLastPage ? 'page-break' : '' }}">
        <tbody>
            @for($r = 0; $r < 10; $r++)
                @php
                    $rowItems = $rows->get($r, collect());
                @endphp
                <tr>
                    @for($c = 0; $c < 3; $c++)
                        @php
                            $app = $rowItems->get($c);
                        @endphp
                        @if($app)
                            @php
                                $rollNo = $app->roll_no ?: $app->roll_number ?: $app->application_code;
                            @endphp
                            <td>
                                <div class="roll-box">
                                    <div class="roll-text">{{ $rollNo }}</div>
                                </div>
                            </td>
                        @else
                            <td></td>
                        @endif
                    @endfor
                </tr>
            @endfor
        </tbody>
    </table>
@empty
    <div class="empty-message">
        No candidate roll numbers found for the selected examination center and stream.
    </div>
@endforelse

</body>
</html>
