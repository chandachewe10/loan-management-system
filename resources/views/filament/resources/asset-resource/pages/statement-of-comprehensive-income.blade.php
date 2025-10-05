<x-filament::page>
    <h2 class="text-xl font-bold mb-4">Statement of Comprehensive Income</h2>

    <table class="min-w-full bg-white border rounded">
        <thead>
            <tr class="bg-gray-100 font-semibold">
                <th class="border px-4 py-2 text-left">Description</th>
                <th class="border px-4 py-2 text-right">Amount (K)</th>
            </tr>
        </thead>
        <tbody>
            <!-- Income -->
            <tr>
                <td class="border px-4 py-2">Interest Income</td>
                <td class="border px-4 py-2 text-right">{{ number_format($interestIncome, 2) }}</td>
            </tr>
            <tr>
                <td class="border px-4 py-2">Service Fees</td>
                <td class="border px-4 py-2 text-right">{{ number_format($serviceFeeIncome, 2) }}</td>
            </tr>
            <tr class="font-bold bg-gray-50">
                <td class="border px-4 py-2">Total Income</td>
                <td class="border px-4 py-2 text-right font-bold">{{ number_format($totalIncome, 2) }}</td>
            </tr>

            <!-- Expenses -->
            <tr>
                <td class="border px-4 py-2">Bad Loans / Defaulters</td>
                <td class="border px-4 py-2 text-right">{{ number_format($badLoans, 2) }}</td>
            </tr>
            <tr>
                <td class="border px-4 py-2">Other Expenses</td>
                <td class="border px-4 py-2 text-right">{{ number_format($totalExpenses - $badLoans, 2) }}</td>
            </tr>
            <tr class="font-bold bg-gray-50">
                <td class="border px-4 py-2">Total Expenses</td>
                <td class="border px-4 py-2 text-right font-bold">{{ number_format($totalExpenses, 2) }}</td>
            </tr>

            <!-- Net Profit -->
            <tr class="font-bold bg-gray-100">
                <td class="border px-4 py-2">Net Profit / Loss</td>
                <td class="border px-4 py-2 text-right font-bold">{{ number_format($netProfit, 2) }}</td>
            </tr>
        </tbody>
    </table>
</x-filament::page>
