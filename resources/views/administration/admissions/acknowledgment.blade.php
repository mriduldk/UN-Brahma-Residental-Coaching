<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admission Acknowledgment - {{ $admission->admission_code }}</title>
    <style>
        @page {
            margin: 0.5cm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #334155;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .container {
            padding: 10px 20px;
        }
        .header {
            text-align: center;
            border-bottom: 1.5px solid #e2e8f0;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .institution-name {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .organization-name {
            font-size: 11px;
            color: #64748b;
            margin: 2px 0;
        }
        .header-address {
            font-size: 9px;
            color: #94a3b8;
            margin: 0;
        }
        .doc-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            margin: 10px 0;
            text-transform: uppercase;
        }
        .main-info-grid {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }
        .info-card {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 15px;
        }
        .section-header {
            background: #f8fafc;
            padding: 5px 10px;
            font-weight: bold;
            font-size: 11px;
            color: #1e293b;
            border-bottom: 1px solid #e2e8f0;
        }
        .data-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .data-grid td {
            padding: 6px 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        .label {
            color: #64748b;
            font-weight: 500;
            width: 130px;
        }
        .value {
            color: #1e293b;
            font-weight: 600;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        .status-completed { background: #dcfce7; color: #15803d; }
        .status-active { background: #dbeafe; color: #1d4ed8; }
        .status-unsettled { background: #fef2f2; color: #b91c1c; }
        .status-settled { background: #f0fdf4; color: #15803d; }

        .table-container {
            margin-top: 10px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table th {
            background: #f8fafc;
            padding: 8px 10px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
        }
        .data-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 9px;
        }
        .amount-col { text-align: right; font-weight: bold; }
        
        .totals-section {
            width: 250px;
            float: right;
            margin-top: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .total-row {
            padding: 6px 10px;
            display: flex;
            justify-content: space-between;
        }
        .total-row.final {
            background: #f8fafc;
            border-top: 1.5px solid #e2e8f0;
            color: #2563eb;
            font-size: 11px;
        }
        
        .footer {
            margin-top: 30px;
            clear: both;
            display: table;
            width: 100%;
        }
        .footer-left {
            display: table-cell;
            vertical-align: bottom;
            font-size: 8px;
            color: #94a3b8;
            width: 60%;
        }
        .footer-right {
            display: table-cell;
            width: 40%;
            text-align: center;
        }
        .sig-line {
            border-top: 1px solid #334155;
            margin-top: 40px;
            margin-bottom: 4px;
        }
        .sig-label {
            font-weight: bold;
            font-size: 10px;
            color: #1e293b;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: #f1f5f9;
            z-index: -1;
            font-weight: bold;
            opacity: 0.6;
            text-transform: uppercase;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    @if($admission->pending_amount <= 0)
        <div class="watermark">PAID FULL</div>
    @endif

    <div class="container">
        <div class="header">
            <h1 class="institution-name">{{ $institution->institution_name }}</h1>
            <p class="organization-name">{{ $organization->organization_name ?? '' }}</p>
            <p class="header-address">{{ $institution->address ?? '' }}</p>
        </div>

        <div class="doc-title">Admission Acknowledgment</div>

        <!-- Quick Summary -->
        <div class="info-card">
            <div class="section-header">Enrollment Overview</div>
            <table class="data-grid">
                <tr>
                    <td class="label">Admission Code:</td>
                    <td class="value">{{ $admission->admission_code }}</td>
                    <td class="label">Admission Date:</td>
                    <td class="value">{{ $admission->created_at->format('d M, Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Academic Session:</td>
                    <td class="value">{{ $admission->academic_session }}</td>
                    <td class="label">Payment Plan:</td>
                    <td class="value">{{ strtoupper($admission->payment_type) }} Enrollment</td>
                </tr>
                <tr>
                    <td class="label">Enrollment Status:</td>
                    <td class="value">
                        <span class="status-badge {{ $admission->pending_amount <= 0 ? 'status-completed' : 'status-active' }}">
                            {{ $admission->pending_amount <= 0 ? 'Completed' : 'Active (EMI)' }}
                        </span>
                        @php
                            $hasUnsettledCash = $payments->where('payment_type', 'cash')->where('is_settled', false)->isNotEmpty();
                        @endphp
                        @if($hasUnsettledCash)
                            <span class="status-badge status-unsettled" style="margin-left: 5px;">
                                Pending Settlement
                            </span>
                        @endif
                    </td>
                    <td class="label">Course / Trade:</td>
                    <td class="value">{{ $application->application_form->application_name }}</td>
                </tr>
            </table>
        </div>

        <div class="info-card">
            <div class="section-header">Student Details</div>
            <table class="data-grid">
                <tr>
                    <td class="label">Full Name:</td>
                    <td class="value" colspan="3">{{ $student->full_name }}</td>
                </tr>
                <tr>
                    <td class="label">Application ID:</td>
                    <td class="value">{{ $application->application_code }}</td>
                    <td class="label">Contact Number:</td>
                    <td class="value">{{ $student->phone_number }}</td>
                </tr>
                <tr>
                    <td class="label">Father's Name:</td>
                    <td class="value">{{ $application->application_parent->father_name ?? '-' }}</td>
                    <td class="label">Email Address:</td>
                    <td class="value">{{ $student->email }}</td>
                </tr>
            </table>
        </div>

        <!-- Payment History Table -->
        <div class="table-container">
            <div class="section-header" style="border: 1px solid #e2e8f0; border-radius: 6px 6px 0 0;">Transaction History</div>
            <table class="data-table" style="border: 1px solid #e2e8f0; border-top: none;">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Transaction Info</th>
                        <th>Description</th>
                        <th class="amount-col">Paid Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                        <tr>
                            <td>
                                {{ $payment->created_at->format('d M, Y') }}<br>
                                <span style="color: #94a3b8; font-size: 8px;">{{ $payment->created_at->format('h:i A') }}</span>
                            </td>
                            <td>
                                @php
                                    $txn = $payment->order ? $payment->order->latest_transaction : null;
                                @endphp
                                <span style="color: #0f172a; font-weight: 600;">{{ $txn ? $txn->razorpay_payment_id : ($payment->transaction_id ?? 'CASH_REC') }}</span><br>
                                <span style="color: #64748b; font-size: 8px;">{{ strtoupper($txn ? ($txn->method ?? 'Online') : 'Cash') }}</span>
                                @if($payment->payment_type == 'cash')
                                    <span class="status-badge {{ $payment->is_settled ? 'status-settled' : 'status-unsettled' }}" style="padding: 1px 4px; font-size: 7px; margin-left: 4px;">
                                        {{ $payment->is_settled ? 'Settled' : 'Unsettled' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                {{ ucfirst($payment->payment_type) }} Payment
                                @if($payment->payment_type == 'emi' && $payment->emi_months)
                                    <div style="color: #94a3b8; font-size: 8px;">{{ $payment->emi_months }} Months Plan</div>
                                @endif
                            </td>
                            <td class="amount-col">&#8377; {{ number_format($payment->paid_amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals Summary -->
        <div class="clearfix">
            <div class="totals-section">
                <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
                    <tr>
                        <td style="padding: 6px 10px; color: #64748b;">Total Course Fee:</td>
                        <td style="padding: 6px 10px; text-align: right; font-weight: bold;">&#8377; {{ number_format($admission->total_payable_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 10px; color: #16a34a;">Amount Paid to Date:</td>
                        <td style="padding: 6px 10px; text-align: right; font-weight: bold; color: #16a34a;">&#8377; {{ number_format($admission->total_paid_amount, 2) }}</td>
                    </tr>
                    <tr class="final">
                        <td style="padding: 8px 10px; font-weight: bold; border-top: 1px solid #e2e8f0; border-radius: 0 0 0 6px;">Remaining Balance:</td>
                        <td style="padding: 8px 10px; text-align: right; font-weight: bold; border-top: 1px solid #e2e8f0; border-radius: 0 0 6px 0; color: {{ $admission->pending_amount > 0 ? '#dc2626' : '#16a34a' }};">
                            &#8377; {{ number_format($admission->pending_amount, 2) }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="footer">
            <div class="footer-left">
                <strong>Disclaimer:</strong> This is a computer-generated admission acknowledgment for the student's reference. It serves as proof of admission and partial/full fee collection as recorded in the institution's information system.<br>
                <div style="margin-top: 5px;">Generated on: {{ now()->format('d M, Y h:i A') }}</div>
            </div>
            <div class="footer-right">
                <div class="sig-line"></div>
                <div class="sig-label">Authorized Signatory</div>
                <div style="font-size: 8px; color: #64748b; margin-top: 2px;">{{ $institution->institution_name }}</div>
            </div>
        </div>
    </div>
</body>
</html>
