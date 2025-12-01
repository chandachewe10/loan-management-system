<!DOCTYPE html>
<html>
<head>
    <title>Direct Debit Mandate - FORM DD8</title>
    <style>
        @page {
            margin: 15mm;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11px;
            font-weight: bold;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        .classification {
            color: #ff6600;
            font-size: 10px;
            margin-bottom: 10px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-title {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 5px 0;
        }

        .form-number {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .main-container {
            border: 2px solid #ccc;
            padding: 20px;
            margin: 10px 0;
            position: relative;
        }

        .service-provider-header {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }

        .logo-section {
            display: table-cell;
            width: 25%;
            vertical-align: top;
            padding-right: 15px;
        }

        .logo-section img {
            max-width: 120px;
            height: auto;
        }

        .title-section {
            display: table-cell;
            width: 75%;
            vertical-align: top;
            padding-left: 10px;
        }

        .mandate-title {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .info-field {
            margin-bottom: 8px;
        }

        .field-label {
            font-weight: bold;
            display: block;
            margin-bottom: 3px;
        }

        .field-value {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 400px;
            padding-bottom: 2px;
            margin-top: 2px;
        }

        .reference-boxes {
            display: inline-block;
            margin-left: 5px;
            margin-top: 5px;
        }

        .ref-box {
            display: inline-block;
            width: 22px;
            height: 22px;
            border: 1px solid #000;
            text-align: center;
            line-height: 22px;
            margin-right: 2px;
            font-size: 10px;
            vertical-align: middle;
        }

        .section-container {
            position: relative;
            margin: 30px 0;
            padding-left: 40px;
        }

        .section-label {
            position: absolute;
            left: 0;
            top: 0;
            writing-mode: vertical-rl;
            text-orientation: upright;
            font-size: 10px;
            font-weight: bold;
            color: #000;
            padding: 10px 5px;
            width: 30px;
        }

        .service-details-grid {
            display: table;
            width: 100%;
            margin-top: 10px;
        }

        .service-column {
            display: table-cell;
            width: 33.33%;
            vertical-align: top;
            padding-right: 15px;
        }

        .service-column:last-child {
            padding-right: 0;
        }

        .checkbox-group {
            margin: 8px 0;
            display: inline-block;
        }

        .checkbox-row {
            margin-bottom: 5px;
        }

        .checkbox-item {
            display: inline-block;
            margin-right: 12px;
            margin-top: 3px;
        }

        .checkbox {
            display: inline-block;
            width: 11px;
            height: 11px;
            border: 1px solid #000;
            margin-right: 4px;
            vertical-align: middle;
        }

        .checkbox.checked {
            background-color: #000;
        }

        .checkbox-label {
            vertical-align: middle;
            font-size: 10px;
        }

        .legend {
            font-size: 9px;
            margin-top: 5px;
            font-style: italic;
        }

        .amount-box {
            display: inline-block;
            width: 90px;
            height: 22px;
            border: 1px solid #000;
            text-align: center;
            line-height: 22px;
            margin-left: 5px;
            font-size: 10px;
        }

        .date-boxes {
            display: inline-block;
            margin-left: 5px;
            margin-top: 3px;
        }

        .date-box {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 1px solid #000;
            text-align: center;
            line-height: 18px;
            margin-right: 2px;
            font-size: 9px;
        }

        .date-separator {
            margin: 0 2px;
            font-size: 10px;
        }

        .payer-details-box {
            border: 1px solid #ccc;
            padding: 12px;
            margin: 10px 0;
            background-color: #f9f9f9;
        }

        .payer-field {
            margin-bottom: 8px;
        }

        .payer-label {
            font-weight: bold;
            display: inline-block;
            min-width: 140px;
        }

        .payer-value {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 350px;
            padding-bottom: 2px;
        }

        .sortcode-boxes {
            display: inline-block;
            margin-left: 5px;
        }

        .sortcode-box {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 1px solid #000;
            text-align: center;
            line-height: 18px;
            margin-right: 2px;
            font-size: 9px;
        }

        .sortcode-dash {
            margin: 0 1px;
            font-size: 10px;
        }

        .account-boxes {
            display: inline-block;
            margin-left: 5px;
        }

        .account-box {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 1px solid #000;
            text-align: center;
            line-height: 18px;
            margin-right: 2px;
            font-size: 9px;
        }

        .instruction-box {
            border: 1px solid #ccc;
            padding: 12px;
            margin: 15px 0;
            background-color: #f9f9f9;
        }

        .instruction-text {
            font-size: 10px;
            line-height: 1.5;
            text-align: justify;
            margin: 12px 0;
        }

        .signature-field {
            border-bottom: 1px solid #000;
            min-height: 35px;
            margin: 8px 0;
            padding-bottom: 3px;
            display: inline-block;
            min-width: 300px;
        }

        .guarantee-box {
            border: 1px solid #ccc;
            padding: 12px;
            margin: 15px 0;
            background-color: #f9f9f9;
        }

        .guarantee-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .guarantee-list {
            margin: 8px 0;
            padding-left: 18px;
        }

        .guarantee-list li {
            margin-bottom: 6px;
            font-size: 10px;
            line-height: 1.4;
        }

        .dotted-line {
            border-bottom: 1px dotted #000;
            min-height: 20px;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="classification">Classification: Customer Confidential</div>
    
    <div class="form-header">
        <div class="form-title">DIRECT DEBIT MANDATE & DIRECT DEBIT GUARANTEE</div>
        <div class="form-number">FORM DD8</div>
    </div>

    <div class="main-container">
        <!-- Service Provider Information -->
        <div class="service-provider-header">
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
                    <div style="width: 100px; height: 70px; border: 2px solid #000; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 13px;">{{ substr($companyName, 0, 3) }}</div>
                @endif
            </div>
            <div class="title-section">
                <div class="mandate-title">MANDATE TO YOUR BANK TO PAY BY DIRECT DEBIT</div>
                
                <div class="info-field">
                    <div class="field-label">Name and full postal address of the Service Provider:</div>
                    <div class="field-value" style="margin-left: 0;">{{ $companyName }}</div>
                </div>
                <div style="margin-top: 3px;">
                    <div class="field-value" style="margin-left: 0;">{{ $user->company_address ?? ($branch->address ?? 'Lusaka, Zambia') }}</div>
                </div>

                <div class="info-field" style="margin-top: 12px;">
                    <div class="field-label">Service Provider's Reference Number:</div>
                    <div class="reference-boxes">
                        @php
                            $refNumber = $settings->service_provider_reference_number ?? '000000000';
                            // Ensure we have exactly 9 digits, pad with zeros if needed
                            $refNumber = str_pad($refNumber, 9, '0', STR_PAD_LEFT);
                            $refDigits = str_split(substr($refNumber, 0, 9));
                        @endphp
                        @foreach($refDigits as $digit)
                            <span class="ref-box">{{ $digit }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="info-field" style="margin-top: 12px;">
                    <div class="field-label">Payer's Account Number with Service Provider:</div>
                    <div class="reference-boxes">
                        @php
                            $payerAccount = str_pad($loan->borrower_id ?? '000000000', 9, '0', STR_PAD_LEFT);
                            $payerDigits = str_split($payerAccount);
                        @endphp
                        @foreach($payerDigits as $digit)
                            <span class="ref-box">{{ $digit }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Details Section -->
        <div class="section-container">
            <div class="section-label">Service Details</div>
            <div style="margin-left: 0;">
                <div class="service-details-grid">
                    <div class="service-column">
                        <div class="info-field">
                            <div class="field-label">Payment Date (DD/MM/YYYY):</div>
                            <div class="date-boxes">
                                @php
                                    $paymentDate = null;
                                    if ($settings->payment_date_calculation === 'loan_release_date' && $loan->loan_release_date) {
                                        $paymentDate = \Carbon\Carbon::parse($loan->loan_release_date);
                                    } elseif ($settings->payment_date_calculation === 'loan_due_date' && $loan->loan_due_date) {
                                        $paymentDate = \Carbon\Carbon::parse($loan->loan_due_date);
                                    } elseif ($loan->loan_release_date) {
                                        $paymentDate = \Carbon\Carbon::parse($loan->loan_release_date);
                                    }
                                @endphp
                                @if($paymentDate)
                                    <span class="date-box">{{ substr($paymentDate->format('d'), 0, 1) }}</span>
                                    <span class="date-box">{{ substr($paymentDate->format('d'), 1, 1) }}</span>
                                    <span class="date-separator">/</span>
                                    <span class="date-box">{{ substr($paymentDate->format('m'), 0, 1) }}</span>
                                    <span class="date-box">{{ substr($paymentDate->format('m'), 1, 1) }}</span>
                                    <span class="date-separator">/</span>
                                    <span class="date-box">{{ substr($paymentDate->format('Y'), 0, 1) }}</span>
                                    <span class="date-box">{{ substr($paymentDate->format('Y'), 1, 1) }}</span>
                                    <span class="date-box">{{ substr($paymentDate->format('Y'), 2, 1) }}</span>
                                    <span class="date-box">{{ substr($paymentDate->format('Y'), 3, 1) }}</span>
                                @else
                                    <span class="date-box"></span>
                                    <span class="date-box"></span>
                                    <span class="date-separator">/</span>
                                    <span class="date-box"></span>
                                    <span class="date-box"></span>
                                    <span class="date-separator">/</span>
                                    <span class="date-box"></span>
                                    <span class="date-box"></span>
                                    <span class="date-box"></span>
                                    <span class="date-box"></span>
                                @endif
                            </div>
                        </div>

                        <div class="info-field" style="margin-top: 12px;">
                            <div class="field-label">Expiry Date (DD/MM/YYYY):</div>
                            <div class="date-boxes">
                                @if($loan->loan_due_date)
                                    @php
                                        $dueDate = \Carbon\Carbon::parse($loan->loan_due_date);
                                    @endphp
                                    <span class="date-box">{{ substr($dueDate->format('d'), 0, 1) }}</span>
                                    <span class="date-box">{{ substr($dueDate->format('d'), 1, 1) }}</span>
                                    <span class="date-separator">/</span>
                                    <span class="date-box">{{ substr($dueDate->format('m'), 0, 1) }}</span>
                                    <span class="date-box">{{ substr($dueDate->format('m'), 1, 1) }}</span>
                                    <span class="date-separator">/</span>
                                    <span class="date-box">{{ substr($dueDate->format('Y'), 0, 1) }}</span>
                                    <span class="date-box">{{ substr($dueDate->format('Y'), 1, 1) }}</span>
                                    <span class="date-box">{{ substr($dueDate->format('Y'), 2, 1) }}</span>
                                    <span class="date-box">{{ substr($dueDate->format('Y'), 3, 1) }}</span>
                                @else
                                    <span class="date-box"></span>
                                    <span class="date-box"></span>
                                    <span class="date-separator">/</span>
                                    <span class="date-box"></span>
                                    <span class="date-box"></span>
                                    <span class="date-separator">/</span>
                                    <span class="date-box"></span>
                                    <span class="date-box"></span>
                                    <span class="date-box"></span>
                                    <span class="date-box"></span>
                                @endif
                            </div>
                        </div>

                        <div class="info-field" style="margin-top: 12px;">
                            <div class="field-label">Payment Frequency (Tick as applicable):</div>
                            <div class="checkbox-group">
                                <div class="checkbox-row">
                                    <span class="checkbox-item">
                                        <span class="checkbox @if($settings->default_payment_frequency == 'D') checked @endif"></span>
                                        <span class="checkbox-label">D</span>
                                    </span>
                                    <span class="checkbox-item">
                                        <span class="checkbox @if($settings->default_payment_frequency == 'W') checked @endif"></span>
                                        <span class="checkbox-label">W</span>
                                    </span>
                                    <span class="checkbox-item">
                                        <span class="checkbox @if($settings->default_payment_frequency == 'FN') checked @endif"></span>
                                        <span class="checkbox-label">FN</span>
                                    </span>
                                    <span class="checkbox-item">
                                        <span class="checkbox @if($settings->default_payment_frequency == 'M') checked @endif"></span>
                                        <span class="checkbox-label">M</span>
                                    </span>
                                </div>
                                <div class="checkbox-row" style="margin-top: 3px;">
                                    <span class="checkbox-item">
                                        <span class="checkbox @if($settings->default_payment_frequency == 'Q') checked @endif"></span>
                                        <span class="checkbox-label">Q</span>
                                    </span>
                                    <span class="checkbox-item">
                                        <span class="checkbox @if($settings->default_payment_frequency == 'H') checked @endif"></span>
                                        <span class="checkbox-label">H</span>
                                    </span>
                                    <span class="checkbox-item">
                                        <span class="checkbox @if($settings->default_payment_frequency == 'A') checked @endif"></span>
                                        <span class="checkbox-label">A</span>
                                    </span>
                                </div>
                            </div>
                            <div class="legend">*D=Daily W=Weekly FN=Fortnightly M=Monthly Q=Quarterly H=Half Yearly A=Annually</div>
                        </div>
                    </div>

                    <div class="service-column">
                        <div class="info-field">
                            <div class="field-label">How many days can the Direct Debit be processed <strong>before</strong> Payment Date?</div>
                            <div class="amount-box">{{ $settings->days_before_payment_date }}</div>
                        </div>

                        <div class="info-field" style="margin-top: 12px;">
                            <div class="field-label">How many days can the Direct Debit be processed <strong>after</strong> Payment Date?</div>
                            <div class="amount-box">{{ $settings->days_after_payment_date }}</div>
                        </div>
                    </div>

                    <div class="service-column">
                        @php
                            $monthlyInstallment = 0;
                            if ($loan->loan_duration && $loan->loan_duration > 0) {
                                $repaymentAmount = is_numeric($loan->repayment_amount) ? $loan->repayment_amount : str_replace(',', '', $loan->repayment_amount);
                                $monthlyInstallment = $repaymentAmount / $loan->loan_duration;
                            }
                        @endphp
                        <div class="info-field">
                            <div class="field-label">Fixed amount to be debited:</div>
                            <div class="amount-box">K {{ number_format($monthlyInstallment, 0) }}</div>
                        </div>

                        <div class="info-field" style="margin-top: 12px;">
                            <div class="field-label">Variable amount to be debited subject to maximum of:</div>
                            <div class="amount-box">K {{ number_format($loan->principal_amount ?? 0, 0) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payer's Personal Details -->
        <div class="section-container">
            <div class="section-label">Payer's Personal Details</div>
            <div class="payer-details-box" style="margin-left: 0;">
                @php
                    $borrower = $loan->borrower;
                @endphp
                <div class="payer-field">
                    <span class="payer-label">Name:</span>
                    <span class="payer-value">{{ $borrower->first_name ?? '' }} {{ $borrower->last_name ?? '' }}</span>
                </div>

                <div class="payer-field">
                    <span class="payer-label">Telephone Number:</span>
                    <span class="payer-value">{{ $borrower->mobile ?? '' }}</span>
                </div>

                <div class="payer-field">
                    <span class="payer-label">Email:</span>
                    <span class="payer-value">{{ $borrower->email ?? '' }}</span>
                </div>

                <div class="payer-field">
                    <span class="payer-label">Address:</span>
                    <span class="payer-value" style="min-width: 450px;">{{ $borrower->address ?? '' }}, {{ $borrower->city ?? '' }}, {{ $borrower->province ?? '' }}</span>
                </div>
            </div>
        </div>

        <!-- Payer's Bank Details -->
        <div class="section-container">
            <div class="section-label">Payer's Bank Details</div>
            <div class="payer-details-box" style="margin-left: 0;">
                <div class="payer-field">
                    <span class="payer-label">Bank Name:</span>
                    <span class="payer-value">{{ $borrower->bank_name ?? '' }}</span>
                </div>

                <div class="payer-field">
                    <span class="payer-label">Branch Name:</span>
                    <span class="payer-value">{{ $borrower->bank_branch ?? '' }}</span>
                </div>

                <div class="payer-field">
                    <span class="payer-label">Sortcode:</span>
                    <div class="sortcode-boxes">
                        @php
                            $sortCode = str_pad($borrower->bank_sort_code ?? '000000', 6, '0', STR_PAD_LEFT);
                            $sortDigits = str_split($sortCode);
                        @endphp
                        @foreach($sortDigits as $index => $digit)
                            <span class="sortcode-box">{{ $digit }}</span>
                            @if($index == 1 || $index == 3)
                                <span class="sortcode-dash">-</span>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="payer-field">
                    <span class="payer-label">Bank Account Number:</span>
                    <div class="account-boxes">
                        @php
                            $accountNumber = str_pad($borrower->bank_account_number ?? '0000000000', 10, '0', STR_PAD_LEFT);
                            $accountDigits = str_split($accountNumber);
                        @endphp
                        @foreach($accountDigits as $digit)
                            <span class="account-box">{{ $digit }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Instruction to Bank/NBFI -->
        <div class="section-container">
            <div class="section-label">Instruction to your Bank/NBFI</div>
            <div class="instruction-box" style="margin-left: 0;">
                <div style="margin-bottom: 8px;">
                    <strong>To: The Manager</strong><br>
                    <span style="font-size: 10px;">(Name and full postal address of your Bank)</span>
                </div>
                <div class="dotted-line"></div>
                <div class="dotted-line"></div>
                <div class="dotted-line"></div>

                <div class="instruction-text">
                    <strong>Instruction to Debit My Account</strong><br><br>
                    Please pay <strong>{{ $companyName }}</strong> Direct Debits from my account detailed in this mandate subject to safeguards assured by the Direct Debits Guarantee. I/we understand that this mandate may remain with <strong>{{ $companyName }}</strong> and, if so, details will be passed electronically to my Bank/NBFI.
                </div>

                <div class="payer-field" style="margin-top: 15px;">
                    <span class="payer-label">Signatures:</span>
                    <div class="signature-field"></div>
                </div>

                <div class="payer-field">
                    <span class="payer-label">Date:</span>
                    <div class="signature-field" style="min-width: 200px;"></div>
                </div>

                <div style="font-size: 9px; margin-top: 8px; font-style: italic;">
                    Banks/NBFIs may not accept Direct Debit Mandates for some types of accounts
                </div>
            </div>
        </div>

        <!-- Direct Debit Guarantee -->
        <div class="guarantee-box">
            <div class="guarantee-title">The Direct Debit Guarantee</div>
            <ol class="guarantee-list">
                <li>This Guarantee is offered by all Banks/NBFI that take part in the DDACC System. The efficiency and security of the Direct Debit is monitored and protected by your own Bank/NBFI.</li>
                <li>If the amounts to be paid or the payment dates change, <strong>{{ $companyName }}</strong> will notify you 14 working days in advance of your account being debited or as otherwise agreed.</li>
                <li>If an error is made by <strong>{{ $companyName }}</strong>, you are guaranteed a full and immediate refund of the amount paid from <strong>{{ $companyName }}</strong>.</li>
                <li>If an error is made by your bank/NBFI, you are guaranteed a full and immediate refund from your branch of the amount paid.</li>
                <li>You can cancel a Direct Debit at any time by writing to your Bank/NBFI. Please also send a copy of your letter to us.</li>
            </ol>
        </div>
    </div>
</body>
</html>
