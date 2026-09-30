<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $transaction->order->razorpay_order_id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            color: #334155;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }
        .container {
            padding: 40px;
        }
        .header {
            margin-bottom: 40px;
        }
        .header-table {
            width: 100%;
        }
        .logo-text {
            font-size: 24px;
            font-weight: 900;
            color: #0f172a;
        }
        .branding-line {
            height: 4px;
            background-color: #2563eb;
            margin-bottom: 15px;
        }
        .invoice-title {
            font-size: 40px;
            font-weight: 900;
            color: #f1f5f9;
            text-transform: uppercase;
            text-align: right;
            margin: 0;
        }
        .section-table {
            width: 100%;
            margin-top: 30px;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }
        .label {
            font-size: 10px;
            font-weight: bold;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .value-bold {
            font-size: 18px;
            font-weight: 900;
            color: #0f172a;
        }
        .value-small {
            font-size: 12px;
            color: #64748b;
        }
        .item-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }
        .item-table th {
            background-color: #f8fafc;
            padding: 12px;
            font-size: 10px;
            font-weight: bold;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: left;
        }
        .item-table td {
            padding: 20px 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer-table {
            width: 100%;
            margin-top: 40px;
        }
        .total-box {
            width: 250px;
            float: right;
        }
        .total-row {
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .grand-total {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #0f172a;
        }
        .notes-section {
            margin-top: 60px;
        }
        .signature-section {
            margin-top: -30px;
            text-align: right;
        }
        .mono {
            font-family: 'Courier', monospace;
        }
        .blue { color: #2563eb; }
    </style>
</head>
<body>
    <div class="branding-line"></div>
    <div class="container">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td>
                        <img src="{{ public_path('image/logo/logo.png') }}" alt="Logo" style="width: 50px; height: 50px; border-radius: 10px; margin-bottom: 10px;">
                        <div class="logo-text">Director of Education, BTC</div>
                        <div class="value-small">
                            Digital Management System for Institutions<br>
                            Support: info.jypko@gmail.com<br>
                        </div>
                    </td>
                    <td class="text-right">
                        <h1 class="invoice-title">Invoice</h1>
                        <div class="label">Invoice Date</div>
                        <div style="font-size: 16px; font-weight: 900;">{{ $transaction->created_at->format('d M, Y') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="section-table">
            <tr>
                <td width="50%" valign="top">
                    <div class="label">Billed To</div>
                    <div class="value-bold">{{ $org->organization_name }}</div>
                    <div class="value-small" style="font-style: italic; font-weight: bold;">Authorized Organization</div>
                    
                    <div style="margin-top: 15px;">
                        @if($org->organization_code)
                            <div class="value-small">Org Code: <span class="mono" style="font-weight: bold; color: #334155;">{{ $org->organization_code }}</span></div>
                        @endif
                        @if($org->registration_no)
                            <div class="value-small">Reg No: <span style="font-weight: bold; color: #334155;">{{ $org->registration_no }}</span></div>
                        @endif
                        @if($org->gst_no)
                            <div class="value-small">GST No: <span style="font-weight: bold; color: #334155;">{{ $org->gst_no }}</span></div>
                        @endif
                        @if($org->pan_no)
                            <div class="value-small">PAN No: <span style="font-weight: bold; color: #334155;">{{ $org->pan_no }}</span></div>
                        @endif
                    </div>
                </td>
                <td width="50%" class="text-right" valign="top">
                    <div class="label">Order Details</div>
                    <div class="value-small">Order ID: <span class="mono" style="font-weight: bold;">{{ $transaction->order->razorpay_order_id }}</span></div>
                    <div class="value-small">Payment ID: <span class="mono" style="font-weight: bold;">{{ $transaction->razorpay_payment_id }}</span></div>
                    <div class="value-small">Status: <span style="color: #16a34a; font-weight: bold; text-transform: uppercase;">Paid</span></div>
                </td>
            </tr>
        </table>

        @php
            $totalAmount = (float)$transaction->amount;
            $baseCreationFee = (float)$globalConfig->form_creation_charge;
            $regFeeSurcharge = (float)$globalConfig->registration_fee_charge;
            
            $isIntegrated = !empty($transaction->order->application_form_id);
            $hasRegFee = $isIntegrated && $totalAmount >= ($baseCreationFee + $regFeeSurcharge);
            
            $creationFeeToDisplay = $hasRegFee ? $baseCreationFee : $totalAmount;
        @endphp

        <table class="item-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        @if($isIntegrated)
                            <div style="font-weight: bold; color: #0f172a;">Form Creation: {{ $transaction->order->applicationForm->application_name }}</div>
                            <div class="value-small" style="color: #2563eb;">Form Code: {{ $transaction->order->applicationForm->application_code }}</div>
                        @else
                            <div style="font-weight: bold; color: #0f172a;">Form Creation Quota Slot</div>
                            <div class="value-small">Expansion of organization form capacity</div>
                        @endif
                    </td>
                    <td class="text-center" style="font-weight: bold;">1</td>
                    <td class="text-right" style="font-weight: bold;">&#8377;{{ number_format($creationFeeToDisplay, 2) }}</td>
                    <td class="text-right" style="font-weight: bold; color: #0f172a;">&#8377;{{ number_format($creationFeeToDisplay, 2) }}</td>
                </tr>
                @if($hasRegFee)
                <tr>
                    <td>
                        <div style="font-weight: bold; color: #0f172a;">Registration Fee Service</div>
                        <div class="value-small" style="color: #f97316;">System enabled for taking online registration fees</div>
                    </td>
                    <td class="text-center" style="font-weight: bold;">1</td>
                    <td class="text-right" style="font-weight: bold;">&#8377;{{ number_format($regFeeSurcharge, 2) }}</td>
                    <td class="text-right" style="font-weight: bold; color: #0f172a;">&#8377;{{ number_format($regFeeSurcharge, 2) }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="footer-table">
            <div class="total-box" style="width: 320px;">
                <table width="100%">
                    <tr>
                        <td colspan="2"><div class="grand-total" style="border-top: 2px solid #0f172a; margin-top: 10px; padding-top: 10px;"></div></td>
                    </tr>
                    <tr>
                        <td style="font-size: 13px; font-weight: bold; color: #334155; text-transform: uppercase;">Grand Total <br><span style="font-size: 9px; font-weight: normal; color: #64748b; text-transform: none; font-style: italic;">(Inclusive of 18% GST)</span></td>
                        <td class="text-right" style="font-size: 22px; font-weight: bold; color: #2563eb;">₹{{ number_format((float)$transaction->amount, 2) }}</td>
                    </tr>
                </table>
            </div>
            <div style="clear: both;"></div>
        </div>

        <div class="notes-section">
            <div class="label">Notes</div>
            <div class="value-small" style="width: 60%; font-weight: bold;">
                This is a computer-generated invoice and does not require a physical signature. The quota slots have been automatically activated upon successful payment confirmation.
            </div>
        </div>

        <div class="signature-section">
            <div style="width: 150px; border-top: 1px solid #e2e8f0; margin-left: auto; margin-bottom: 5px;"></div>
            <div class="label" style="color: #0f172a;">System Authorized</div>
        </div>
    </div>
</body>
</html>
