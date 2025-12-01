<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payslip - {{ $payslip->payslip_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .payslip-title {
            font-size: 16px;
            margin-top: 10px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px 25px;
            margin-bottom: 15px;
            align-items: baseline;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
            min-width: 150px;
        }
        .info-value {
            flex: 1;
        }
        .info-item {
            display: inline-flex;
            align-items: baseline;
            white-space: nowrap;
        }
        .info-item-label {
            font-weight: bold;
            margin-right: 5px;
        }
        .info-item-value {
            margin-right: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .net-pay {
            font-size: 16px;
            font-weight: bold;
            color: #0066cc;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        @php
            $user = auth()->user();
            $logoPath = null;
            $isProfileComplete = $user && $user->profile_completeness >= 100;
            
            if ($isProfileComplete && $user && $user->hasMedia('company_logo')) {
                $media = $user->getFirstMedia('company_logo');
                $logoPath = $media ? $media->getPath() : null;
            } elseif (file_exists(public_path('companyLogo.png'))) {
                $logoPath = public_path('companyLogo.png');
            } elseif (file_exists(public_path('logo.png'))) {
                $logoPath = public_path('logo.png');
            } elseif (file_exists(public_path('logo.jpg'))) {
                $logoPath = public_path('logo.jpg');
            }
        @endphp
        
        @if($logoPath && file_exists($logoPath))
            <div style="margin-bottom: 10px;">
                <img src="{{ $logoPath }}" alt="Company Logo" style="max-height: 80px; max-width: 200px;">
            </div>
        @endif
        
        <div class="company-name">{{ $user->name ?? 'Company Name' }}</div>
        
        @if($isProfileComplete && $user && $user->company_address)
            <div style="font-size: 10px; margin-top: 5px; margin-bottom: 10px;">
                {{ $user->company_address }}
            </div>
            @if($user->company_phone)
                <div style="font-size: 10px; margin-bottom: 5px;">
                    Phone: {{ $user->company_phone }}
                </div>
            @endif
        @endif
        
        <div class="payslip-title">PAYSLIP</div>
        <div>Period: {{ $payrollRun->period_name }}</div>
    </div>

    <div class="info-section">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-item-label">Employee Name:</div>
                <div class="info-item-value">{{ $employee->full_name }}</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Employee Number:</div>
                <div class="info-item-value">{{ $employee->employee_number }}</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Position:</div>
                <div class="info-item-value">{{ $employee->position ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Department:</div>
                <div class="info-item-value">{{ $employee->department ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Payslip Number:</div>
                <div class="info-item-value">{{ $payslip->payslip_number }}</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Pay Period:</div>
                <div class="info-item-value">{{ $payrollRun->pay_period_start->format('M d, Y') }} - {{ $payrollRun->pay_period_end->format('M d, Y') }}</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Payment Date:</div>
                <div class="info-item-value">{{ $payrollRun->payment_date->format('F d, Y') }}</div>
            </div>
        </div>
    </div>

    <h3>EARNINGS</h3>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Amount (ZMW)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Basic Salary</td>
                <td class="text-right">{{ number_format($payslip->basic_salary, 2) }}</td>
            </tr>
            @if($payslip->housing_allowance > 0)
            <tr>
                <td>Housing Allowance</td>
                <td class="text-right">{{ number_format($payslip->housing_allowance, 2) }}</td>
            </tr>
            @endif
            @if($payslip->transport_allowance > 0)
            <tr>
                <td>Transport Allowance</td>
                <td class="text-right">{{ number_format($payslip->transport_allowance, 2) }}</td>
            </tr>
            @endif
            @if($payslip->medical_allowance > 0)
            <tr>
                <td>Medical Allowance</td>
                <td class="text-right">{{ number_format($payslip->medical_allowance, 2) }}</td>
            </tr>
            @endif
            @if($payslip->other_allowances > 0)
            <tr>
                <td>Other Allowances</td>
                <td class="text-right">{{ number_format($payslip->other_allowances, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td><strong>Gross Salary</strong></td>
                <td class="text-right"><strong>{{ number_format($payslip->gross_salary, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <h3>DEDUCTIONS</h3>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Amount (ZMW)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>PAYE (Tax)</td>
                <td class="text-right">{{ number_format($payslip->paye, 2) }}</td>
            </tr>
            <tr>
                <td>NAPSA (Pension)</td>
                <td class="text-right">{{ number_format($payslip->napsa, 2) }}</td>
            </tr>
            <tr>
                <td>NHIMA (Health Insurance)</td>
                <td class="text-right">{{ number_format($payslip->nhima, 2) }}</td>
            </tr>
            @if($payslip->other_deductions > 0)
            <tr>
                <td>Other Deductions</td>
                <td class="text-right">{{ number_format($payslip->other_deductions, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td><strong>Total Deductions</strong></td>
                <td class="text-right"><strong>{{ number_format($payslip->total_deductions, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <table>
        <tr class="net-pay">
            <td><strong>NET PAY</strong></td>
            <td class="text-right"><strong>ZMW {{ number_format($payslip->net_pay, 2) }}</strong></td>
        </tr>
    </table>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
        <p>For inquiries, please contact your HR department.</p>
    </div>
</body>
</html>

