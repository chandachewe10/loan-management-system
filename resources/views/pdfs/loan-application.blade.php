<!DOCTYPE html>
<html>
<head>
    <title>LMS Application Form</title>
    <style>
        @page {
            margin: 15mm;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            font-weight: bold;
            line-height: 1.5;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        .header-container {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .logo-section {
            display: table-cell;
            width: 20%;
            vertical-align: top;
            padding-right: 15px;
        }

        .logo-section img {
            max-width: 120px;
            height: auto;
        }

        .company-info {
            display: table-cell;
            width: 80%;
            vertical-align: top;
        }

        .company-name {
            font-size: 17px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2d5016;
        }

        .contact-info {
            font-size: 11px;
            font-weight: bold;
            line-height: 1.6;
            color: #666;
            margin-bottom: 5px;
        }

        .slogan {
            font-style: italic;
            font-size: 12px;
            font-weight: bold;
            color: #8b6914;
            margin-top: 5px;
        }

        .decorative-line {
            height: 2px;
            margin: 5px 0;
        }

        .line-green {
            background-color: #2d5016;
        }

        .line-gold {
            background-color: #8b6914;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #000;
        }

        .subsection-title {
            font-size: 13px;
            font-weight: bold;
            text-decoration: underline;
            margin: 15px 0 10px 0;
        }

        .form-field {
            margin-bottom: 12px;
        }

        .field-label {
            font-weight: bold;
            display: inline-block;
            margin-right: 5px;
        }

        .field-line {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 300px;
            margin-left: 5px;
            padding-bottom: 2px;
        }

        .field-line-short {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 50px;
            margin: 0 2px;
            padding-bottom: 2px;
        }

        .checkbox-group {
            margin: 8px 0;
        }

        .checkbox-item {
            display: inline-block;
            margin-right: 15px;
            margin-top: 5px;
        }

        .checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            margin-right: 5px;
            vertical-align: middle;
        }

        .checkbox-label {
            vertical-align: middle;
        }

        .section-divider {
            border-top: 1px solid #000;
            margin: 20px 0;
        }

        .signature-section {
            margin: 20px 0;
        }

        .signature-field {
            border-bottom: 1px solid #000;
            min-height: 40px;
            margin: 10px 0;
            padding-bottom: 5px;
        }

        .office-use-section {
            margin-top: 30px;
            padding: 15px;
            border: 2px solid #000;
        }

        .office-use-title {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .watermark {
            position: fixed;
            bottom: 50px;
            right: 50px;
            font-size: 120px;
            color: rgba(0, 0, 0, 0.05);
            z-index: -1;
            font-weight: bold;
        }

        .declaration-text {
            font-size: 11px;
            font-weight: bold;
            line-height: 1.6;
            text-align: justify;
            margin: 15px 0;
        }

        ol {
            margin: 10px 0;
            padding-left: 25px;
        }

        ol li {
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <!-- Watermark -->
    <div class="watermark">LMS</div>

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
            @else
                <div style="width: 100px; height: 100px; border: 2px solid #8b6914; border-radius: 50px; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #2d5016; font-size: 24px;">LMS</div>
            @endif
        </div>
        <div class="company-info">
            <div class="company-name">{{ $companyName }}</div>
            <div class="contact-info">
                <div><strong>Phone:</strong> {{ $user->company_phone ?? ($branch->mobile ?? '0778 451 086') }}, {{ $user->company_address ?? ($branch->city ?? 'Lusaka') }}-Zambia.</div>
                <div><strong>Email:</strong> {{ $user->company_representative_email ?? ($branch->email ?? 'limelightmoneylink@gmail.com') }}</div>
            </div>
            <div class="slogan">Transforming your financial vision into reality</div>
            <div class="decorative-line line-green"></div>
            <div class="decorative-line line-gold"></div>
        </div>
    </div>

    @php
        $borrower = $loan->borrower;
        $monthlyInstallment = 0;
        if ($loan->loan_duration && $loan->loan_duration > 0) {
            $repaymentAmount = is_numeric($loan->repayment_amount) ? $loan->repayment_amount : str_replace(',', '', $loan->repayment_amount);
            $monthlyInstallment = $repaymentAmount / $loan->loan_duration;
        }
    @endphp

    <!-- SECTION A: APPLICANT INFORMATION -->
    <div class="section-title">SECTION A: APPLICANT INFORMATION</div>
    
    <div class="subsection-title">Personal Details</div>

    <div class="form-field">
        <span class="field-label">1. Full Name:</span>
        <span class="field-line">{{ $borrower->first_name ?? '' }} {{ $borrower->last_name ?? '' }}</span>
    </div>

    <div class="form-field">
        <span class="field-label">2. National Registration Card (NRC) Number:</span>
        <span class="field-line">{{ $borrower->identification ?? '' }}</span>
    </div>

    <div class="form-field">
        <span class="field-label">3. Date of Birth:</span>
        @if($borrower->dob)
            @php
                $dob = \Carbon\Carbon::parse($borrower->dob);
            @endphp
            <span class="field-line-short">{{ $dob->format('d') }}</span> / 
            <span class="field-line-short">{{ $dob->format('m') }}</span> / 
            <span class="field-line-short">{{ $dob->format('Y') }}</span>
        @else
            <span class="field-line-short"></span> / 
            <span class="field-line-short"></span> / 
            <span class="field-line-short"></span>
        @endif
    </div>

    <div class="form-field">
        <span class="field-label">4. Gender:</span>
        <div class="checkbox-group">
            <span class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">Male</span>
            </span>
            <span class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">Female</span>
            </span>
        </div>
        @if($borrower->gender)
            <script>
                // Gender will be checked manually on printed form
            </script>
        @endif
    </div>

    <div class="form-field">
        <span class="field-label">5. Marital Status:</span>
        <div class="checkbox-group">
            <span class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">Single</span>
            </span>
            <span class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">Married</span>
            </span>
            <span class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">Divorced</span>
            </span>
            <span class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">Widowed</span>
            </span>
        </div>
    </div>

    <div class="form-field">
        <span class="field-label">6. Residential Address:</span>
        <span class="field-line">{{ $borrower->address ?? '' }}</span>
    </div>

    <div class="form-field">
        <span class="field-label">7. Contact Number(s):</span>
        <span class="field-line">{{ $borrower->mobile ?? '' }}</span>
    </div>

    <div class="form-field">
        <span class="field-label">8. Email Address (if applicable):</span>
        <span class="field-line">{{ $borrower->email ?? '' }}</span>
    </div>

    <div class="form-field">
        <span class="field-label">4. Physical address:</span>
        <span class="field-line">{{ $borrower->address ?? '' }}, {{ $borrower->city ?? '' }}, {{ $borrower->province ?? '' }}</span>
    </div>

    <!-- SECTION B: EMPLOYMENT INFORMATION -->
    <div class="section-title">SECTION B: EMPLOYMENT INFORMATION</div>

    <div class="form-field">
        <span class="field-label">1. Employer/Institution Name:</span>
        <span class="field-line">{{ ucfirst($borrower->occupation ?? '') }}</span>
    </div>

    <div class="form-field">
        <span class="field-label">2. Department/Unit:</span>
        <span class="field-line"></span>
    </div>

    <div class="form-field">
        <span class="field-label">3. Designation/Position:</span>
        <span class="field-line"></span>
    </div>

    <div class="form-field">
        <span class="field-label">4. Employee Number:</span>
        <span class="field-line"></span>
    </div>

    <div class="form-field">
        <span class="field-label">5. Work Address:</span>
        <span class="field-line"></span>
    </div>

    <div class="form-field">
        <span class="field-label">6. Work Phone Number:</span>
        <span class="field-line"></span>
    </div>

    <div class="form-field">
        <span class="field-label">7. Length of Employment:</span>
        <div class="checkbox-group">
            <span class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">Less than 1 year</span>
            </span>
            <span class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">1–3 years</span>
            </span>
            <span class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">Over 3 years</span>
            </span>
        </div>
    </div>

    <div class="form-field">
        <span class="field-label">8. Monthly Net Salary (ZMW):</span>
        <span class="field-line">{{ number_format($loan->borrower_monthly_income ?? 0, 2) }}</span>
    </div>

    <!-- SECTION C: LOAN DETAILS -->
    <div class="section-title">SECTION C: LOAN DETAILS</div>

    <div class="form-field">
        <span class="field-label">• Loan Amount Requested (ZMW):</span>
        <span class="field-line">{{ number_format($loan->principal_amount ?? 0, 2) }}</span>
    </div>

    <div class="form-field">
        <span class="field-label">• Purpose of Loan:</span>
        <div class="checkbox-group">
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">Emergency</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">Education</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">Personal Development</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">Business</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">Other (please specify):</span>
                <span class="field-line" style="min-width: 200px; margin-left: 5px;"></span>
            </div>
        </div>
    </div>

    <div class="form-field">
        <span class="field-label">• Preferred Repayment Period:</span>
        <div class="checkbox-group">
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">1 month</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">3 months</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">6 months</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">12 months</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">Other (please specify):</span>
                <span class="field-line" style="min-width: 200px; margin-left: 5px;">{{ $loan->loan_duration ?? '' }} {{ $loan->duration_period ?? 'Months' }}</span>
            </div>
        </div>
    </div>

    <div class="form-field">
        <span class="field-label">• Proposed Monthly Repayment Amount (ZMW):</span>
        <span class="field-line">{{ number_format($monthlyInstallment, 2) }}</span>
    </div>

    <!-- SECTION D: SUPPORTING DOCUMENTS CHECKLIST -->
    <div class="section-title">SECTION D: SUPPORTING DOCUMENTS CHECKLIST</div>
    <div style="font-size: 11px; font-weight: bold; margin-bottom: 10px;">(Please attach copies of the following documents)</div>

    <div class="checkbox-group">
        <div class="checkbox-item">
            <span class="checkbox"></span>
            <span class="checkbox-label">NRC (Copy)</span>
        </div>
        <div class="checkbox-item">
            <span class="checkbox"></span>
            <span class="checkbox-label">Recent Payslip (Last 3 months)</span>
        </div>
        <div class="checkbox-item">
            <span class="checkbox"></span>
            <span class="checkbox-label">Recent Bank statement (Last 3 months)</span>
        </div>
        <div class="checkbox-item">
            <span class="checkbox"></span>
            <span class="checkbox-label">Letter of Introduction from Employer</span>
        </div>
    </div>

    <!-- SECTION E: DECLARATION & CONSENT -->
    <div class="section-divider"></div>
    <div class="section-title">SECTION E: DECLARATION & CONSENT</div>
    
    <div class="declaration-text">
        I, the undersigned, hereby declare that the information provided above is true and correct to the best of my knowledge. I authorize {{ $companyName }} to verify the information provided, contact my employer, and access my credit history for loan assessment purposes.
    </div>
    <div class="declaration-text">
        I acknowledge and agree to the terms and conditions that will be set out in the loan agreement and MOU.
    </div>

    <!-- Next of Kin Information -->
    <div style="margin-top: 30px;">
        <div class="subsection-title" style="text-decoration: underline; font-size: 13px; font-weight: bold;">Next of kin information</div>
        
        <div class="form-field">
            <span class="field-label">1. Full names:</span>
            <span class="field-line">{{ ($borrower->next_of_kin_first_name ?? '') . ' ' . ($borrower->next_of_kin_last_name ?? '') }}</span>
        </div>

        <div class="form-field">
            <span class="field-label">2. Relationship:</span>
            <span class="field-line">{{ ucfirst($borrower->relationship_next_of_kin ?? '') }}</span>
        </div>

        <div class="form-field">
            <span class="field-label">3. Physical place of work:</span>
            <span class="field-line"></span>
        </div>

        <div class="form-field">
            <span class="field-label">4. Residential Address:</span>
            <span class="field-line">{{ $borrower->address_next_of_kin ?? '' }}</span>
        </div>

        <div class="form-field">
            <span class="field-label">5. Contact Number:</span>
            <span class="field-line">{{ $borrower->phone_next_of_kin ?? '' }}</span>
        </div>
    </div>

    <!-- Friend/Workmate Information -->
    <div style="margin-top: 30px;">
        <div class="subsection-title" style="text-decoration: underline; font-size: 13px; font-weight: bold;">Friend/workmate information</div>
        
        <div class="form-field">
            <span class="field-label">1. Full Names:</span>
            <span class="field-line"></span>
        </div>

        <div class="form-field">
            <span class="field-label">2. Occupation:</span>
            <span class="field-line"></span>
        </div>

        <div class="form-field">
            <span class="field-label">3. Contact number:</span>
            <span class="field-line"></span>
        </div>
    </div>

    <!-- Applicant's Signature Section -->
    <div class="signature-section">
        <div class="form-field">
            <span class="field-label"><strong>Applicant's Full Name:</strong></span>
            <span class="field-line" style="min-width: 400px;">{{ $borrower->first_name ?? '' }} {{ $borrower->last_name ?? '' }}</span>
        </div>

        <div class="form-field">
            <span class="field-label"><strong>Signature:</strong></span>
            <span class="field-line" style="min-width: 300px;"></span>
        </div>

        <div class="form-field">
            <span class="field-label"><strong>Date:</strong></span>
            <span class="field-line-short"></span> / 
            <span class="field-line-short"></span> / 
            <span class="field-line-short"></span>
        </div>
    </div>

    <!-- For Office Use Only Section -->
    <div class="office-use-section">
        <div class="office-use-title">For Office Use Only</div>
        
        <div class="checkbox-group">
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">Application Received</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">Documents Complete</span>
            </div>
        </div>

        <div class="checkbox-group" style="margin-top: 10px;">
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">Loan Approved</span>
            </div>
            <div class="checkbox-item">
                <span class="checkbox"></span>
                <span class="checkbox-label">Not Approved</span>
            </div>
        </div>

        <div class="form-field" style="margin-top: 15px;">
            <span class="field-label">Loan Amount Approved: ZMW</span>
            <span class="field-line">{{ number_format($loan->principal_amount ?? 0, 2) }}</span>
        </div>

        <div class="form-field">
            <span class="field-label">Repayment Term:</span>
            <span class="field-line">{{ $loan->loan_duration ?? '' }} {{ $loan->duration_period ?? 'Months' }}</span>
        </div>

        <div class="form-field">
            <span class="field-label">Interest Rate:</span>
            <span class="field-line">{{ $loan->interest_rate ?? 0 }}</span> %
        </div>

        <div class="form-field">
            <span class="field-label">Loan Processing Officer:</span>
            <span class="field-line">{{ auth()->user()->name ?? '' }}</span>
        </div>

        <div class="form-field">
            <span class="field-label">Date:</span>
            <span class="field-line-short"></span> / 
            <span class="field-line-short"></span> / 
            <span class="field-line-short"></span>
        </div>
    </div>
</body>
</html>
