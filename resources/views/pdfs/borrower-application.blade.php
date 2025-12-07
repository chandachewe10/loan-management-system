<!DOCTYPE html>
<html>
<head>
    <title>Pre-Approval Form</title>
    <style>
        @page {
            margin: 15mm;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .header-container {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .logo-section {
            display: table-cell;
            width: 30%;
            vertical-align: top;
        }

        .logo-section img {
            max-width: 150px;
            height: auto;
        }

        .company-info {
            display: table-cell;
            width: 70%;
            vertical-align: top;
            text-align: right;
            padding-left: 20px;
        }

        .company-name {
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .company-legal {
            font-size: 9px;
            font-style: italic;
            margin-bottom: 8px;
            color: #333;
        }

        .contact-info {
            font-size: 10px;
            line-height: 1.4;
            text-align: right;
        }

        .contact-info div {
            margin-bottom: 2px;
        }

        .registration-bar {
            background-color: #000;
            color: #fff;
            padding: 5px 10px;
            font-size: 9px;
            text-align: center;
            margin: 10px 0;
        }

        .form-title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            margin: 15px 0;
            text-transform: uppercase;
        }

        .section-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .section-header {
            background-color: #000;
            color: #fff;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }

        .section-table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        .field-label {
            font-weight: bold;
            width: 40%;
        }

        .field-value {
            border-bottom: 1px solid #000;
            min-height: 15px;
            padding-bottom: 2px;
            font-weight: bold;
        }

        .requirements-list {
            margin: 10px 0;
            padding-left: 20px;
        }

        .requirements-list li {
            margin-bottom: 5px;
            font-size: 11px;
        }

        .parameter-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .parameter-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            width: 33.33%;
        }

        .admin-section {
            margin: 15px 0;
            padding: 10px;
            border: 1px solid #000;
        }

        .admin-section-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .admin-section-text {
            font-size: 10px;
            line-height: 1.4;
            text-align: justify;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .signature-table td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
            width: 33.33%;
        }

        .signature-field {
            min-height: 50px;
            border-bottom: 1px solid #000;
            margin-bottom: 40px;
        }

        .confirmation-section {
            margin-top: 20px;
            font-size: 11px;
        }

        .confirmation-item {
            margin-bottom: 8px;
        }

        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin: 0 5px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header-container">
        <div class="logo-section">
            @php
                $logoPath = null;
                if ($user && $user->hasMedia('company_logo')) {
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
                <img src="{{ $logoPath }}" alt="Company Logo">
            @endif
        </div>
        <div class="company-info">
            <div class="company-name">{{ $companyName }}</div>
            <div class="contact-info">
                <div><strong>Address:</strong> {{ $user->company_address ?? ($branch->address ?? 'Lusaka, Zambia') }}</div>
                <div><strong>Tel:</strong> {{ $user->company_phone ?? ($branch->mobile ?? '+260') }}</div>
                <div><strong>Email:</strong> {{ $user->company_representative_email ?? ($branch->email ?? 'info@company.co.zm') }}</div>
                <div><strong>Website:</strong> {{ parse_url(config('app.url'), PHP_URL_HOST) }}</div>
            </div>
        </div>
    </div>

    <div class="form-title">Pre-Approval Form</div>

    <!-- Applicants Personal Details -->
    <table class="section-table">
        <thead>
            <tr>
                <th class="section-header" colspan="2">APPLICANTS PERSONAL DETAILS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="field-label">Client Name:</td>
                <td class="field-value">{{ $borrower->first_name ?? '' }}</td>
            </tr>
            <tr>
                <td class="field-label">Other Names:</td>
                <td class="field-value">{{ $borrower->last_name ?? '' }}</td>
            </tr>
            <tr>
                <td class="field-label">NRC#:</td>
                <td class="field-value">{{ $borrower->identification ?? '' }}</td>
            </tr>
            <tr>
                <td class="field-label">Employer:</td>
                <td class="field-value">{{ ucfirst($borrower->occupation ?? '') }}</td>
            </tr>
            <tr>
                <td class="field-label">Payroll Area Code:</td>
                <td class="field-value"></td>
            </tr>
            <tr>
                <td class="field-label">Applicants Signature:</td>
                <td class="field-value"></td>
            </tr>
            <tr>
                <td class="field-label">Employee No:</td>
                <td class="field-value"></td>
            </tr>
            <tr>
                <td class="field-label">Division/District:</td>
                <td class="field-value">{{ $borrower->city ?? '' }}, {{ $borrower->province ?? '' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Requirements -->
    <table class="section-table">
        <thead>
            <tr>
                <th class="section-header">REQUIREMENTS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <ol class="requirements-list">
                        <li>Original National Registration Card</li>
                        <li>Two Latest Pay slips</li>
                        <li>Latest 3 Months Bank Statement of Salary Account</li>
                        <li>Stamped Pre-Approval Form</li>
                    </ol>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Applicants Bank Account Details -->
    <table class="section-table">
        <thead>
            <tr>
                <th class="section-header" colspan="2">APPLICANTS BANK ACCOUNT DETAILS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="field-label">Bank Name:</td>
                <td class="field-value">{{ $borrower->bank_name ?? '' }}</td>
            </tr>
            <tr>
                <td class="field-label">Branch Name:</td>
                <td class="field-value">{{ $borrower->bank_branch ?? '' }}</td>
            </tr>
            <tr>
                <td class="field-label">Account#:</td>
                <td class="field-value">{{ $borrower->bank_account_number ?? '' }}</td>
            </tr>
            <tr>
                <td class="field-label">Account Name:</td>
                <td class="field-value">{{ $borrower->bank_account_name ?? '' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Parameter Section -->
    <table class="parameter-table">
        <thead>
            <tr>
                <th class="section-header" colspan="3">PARAMETER</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>60% of Basic Pay</td>
                <td>Add 100% of all Recurring Allowances</td>
                <td>Less (Subtract) PAYE</td>
            </tr>
            <tr>
                <td>Less (Subtract) Pension/NAPSA</td>
                <td>Less (Subtract) all other statutory deductions</td>
                <td>Less (Subtract) all other recurring deductions (inclusive of 3rd Party Deductions)</td>
            </tr>
        </tbody>
    </table>

    <!-- Maximum Recoverable Installment Amount -->
    <table class="parameter-table">
        <thead>
            <tr>
                <th class="section-header" colspan="3">MAXIMUM RECOVERABLE INSTALLMENT AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Primary Loan Amount Applied for</td>
                <td>Tenure</td>
                <td>Monthly Installment</td>
            </tr>
        </tbody>
    </table>

    <!-- GRZ / Employer Administrative Official Use Only -->
    <div class="admin-section">
        <div class="admin-section-title">>FOR GRZ / EMPLOYER ADMINISTRATIVE OFFICIAL USE ONLY</div>
        <div class="admin-section-text">
            The GRZ / Employer administrator / official hereby confirms that the above given details for the employee are correct and that the individual is still an active GRZ employee. By appending signature and endorsing the official stamp below, the authorized signatory hereby authorizes the aforementioned employee to obtain a loan from {{ $companyName }}.
        </div>
    </div>

    <!-- Signatories Details -->
    <table class="signature-table">
        <thead>
            <tr>
                <th class="section-header" colspan="3">SIGNATORIES DETAILS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Signatories Full Names:</strong>
                    <div class="signature-field"></div>
                </td>
                <td>
                    <strong>Designation:</strong>
                    <div class="signature-field"></div>
                </td>
                <td>
                    <strong>Contact & <span style="color: red;">NRC Numbers</span>:</strong>
                    <div class="signature-field"></div>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Signatories Signature:</strong>
                    <div class="signature-field"></div>
                </td>
                <td>
                    <strong>Stamp:</strong>
                    <div class="signature-field"></div>
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <!-- Confirmation Section -->
    <div class="confirmation-section">
        <div><strong>Kindly confirm</strong></div>
        <div class="confirmation-item">
            > Is client working in a rural set up? <span class="checkbox"></span> Yes <span class="checkbox"></span> No
        </div>
        <div class="confirmation-item">
            > Is client staying in an institutional house? <span class="checkbox"></span> Yes <span class="checkbox"></span> No
        </div>
    </div>
</body>
</html>

