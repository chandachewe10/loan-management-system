<!DOCTYPE html>
<html>
<head>
    <title>Customer Statement</title>
    <style>
    body {
        font-family: sans-serif;
        font-size: 12px;
    }

    h1, h2 {
        text-align: center;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table, th, td {
        border: 1px solid #000;
    }

    th, td {
        padding: 6px;
        text-align: left;
    }

    th {
        background-color: #001F3F; 
        color: white;
    }

    .summary {
        margin-top: 30px;
        font-weight: bold;
    }
</style>

</head>
<body>

    <h1>Loan Statement</h1>
    
    <p><strong>Name:</strong> {{ $customer->first_name }} {{ $customer->last_name }}</p>
    <p><strong>NRC:</strong> {{ $customer->identification }}</p>
    <p><strong>Disbursement Date:</strong> {{ \Carbon\Carbon::parse($loan->loan_release_date)->toFormattedDateString() }}</p>
    <p><strong>Loan Balance:</strong> ZK{{ number_format($loan->balance, 2) }}</p>

    @php
        $repayments_histories = \App\Models\Repayments::where('loan_number', $loan->loan_number)->get();
        $running_balance = $loan->balance;
        $initialBalance = ($loan->principal_amount + $loan->interest_amount)
    @endphp

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            <!-- Initial Loan Disbursement -->
            <tr>
                <td>{{ \Carbon\Carbon::parse($loan->loan_release_date)->toFormattedDateString() }}</td>
                <td>Loan disbursed</td>
                <td>ZK{{ number_format($loan->principal_amount, 2) }}</td>
                <td>-</td>
                <td>ZK{{ number_format( $initialBalance, 2) }}</td>
            </tr>

            <!-- Repayments -->
            @foreach($repayments_histories as $repayment)
               
                <tr>
                    <td>{{ \Carbon\Carbon::parse($repayment->updated_at)->toFormattedDateString() }}</td>
                    <td>{{ $repayment->payments_method }}</td>
                    <td>-</td>
                    <td>ZK{{ number_format($repayment->payments, 2) }}</td>
                    <td>ZK{{ number_format($repayment->balance, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="summary">Total Outstanding Balance: ZK{{ number_format($running_balance, 2) }}</p>

</body>
</html>
