<x-filament::page>
    <h2 class="text-xl font-bold mb-4">Statement of Comprehensive Income</h2>

    <table class="min-w-full border rounded-lg bg-white dark:bg-gray-800">
        <thead>
            <tr class="bg-gray-50 dark:bg-gray-700 font-semibold">
                <th class="border px-4 py-2 text-left border-gray-300 dark:border-gray-600">Description</th>
                <th class="border px-4 py-2 text-right border-gray-300 dark:border-gray-600">Amount (K)</th>
            </tr>
        </thead>
        <tbody>
            <!-- Income -->
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">Interest Income</td>
                <td class="border px-4 py-2 text-right border-gray-300 dark:border-gray-600">{{ number_format($interestIncome, 2) }}</td>
            </tr>
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">Admin Fees</td>
                <td class="border px-4 py-2 text-right border-gray-300 dark:border-gray-600">{{ number_format($serviceFeeIncome, 2) }}</td>
            </tr>
            <tr class="font-bold bg-gray-50 dark:bg-gray-700">
                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">Total Income</td>
                <td class="border px-4 py-2 text-right font-bold border-gray-300 dark:border-gray-600">{{ number_format($totalIncome, 2) }}</td>
            </tr>

            <!-- Expenses -->
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">Bad Loans / Defaulters</td>
                <td class="border px-4 py-2 text-right border-gray-300 dark:border-gray-600">{{ number_format($badLoans, 2) }}</td>
            </tr>
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">Other Expenses</td>
                <td class="border px-4 py-2 text-right border-gray-300 dark:border-gray-600">{{ number_format($totalExpenses - $badLoans, 2) }}</td>
            </tr>
            <tr class="font-bold bg-gray-50 dark:bg-gray-700">
                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">Total Expenses</td>
                <td class="border px-4 py-2 text-right font-bold border-gray-300 dark:border-gray-600">{{ number_format($totalExpenses, 2) }}</td>
            </tr>

            <!-- Net Profit -->
            <tr class="font-bold bg-gray-100 dark:bg-gray-600">
                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">Net Profit / Loss</td>
                <td class="border px-4 py-2 text-right font-bold border-gray-300 dark:border-gray-600">{{ number_format($netProfit, 2) }}</td>
            </tr>
        </tbody>
    </table>
</x-filament::page>