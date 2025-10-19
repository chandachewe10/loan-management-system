<x-filament::page>
    <h2 class="text-xl font-bold mb-4">Statement of Financial Position</h2>

    <table class="min-w-full border rounded-lg bg-white dark:bg-gray-800">
        <thead>
            <tr class="bg-gray-50 dark:bg-gray-700 font-semibold">
                <th class="border px-4 py-2 text-left border-gray-300 dark:border-gray-600">Assets</th>
                <th class="border px-4 py-2 text-right border-gray-300 dark:border-gray-600">Amount (K)</th>
                <th class="border px-4 py-2 text-left border-gray-300 dark:border-gray-600">Liabilities & Equity</th>
                <th class="border px-4 py-2 text-right border-gray-300 dark:border-gray-600">Amount (K)</th>
            </tr>
        </thead>
        <tbody>
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">Cash</td>
                <td class="border px-4 py-2 text-right border-gray-300 dark:border-gray-600">{{ number_format($cashAmount, 2) }}</td>
                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">Expenses</td>
                <td class="border px-4 py-2 text-right border-gray-300 dark:border-gray-600">{{ number_format($totalLiabilities, 2) }}</td>
            </tr>

            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">Equipment</td>
                <td class="border px-4 py-2 text-right border-gray-300 dark:border-gray-600">{{ number_format($equipmentAmount, 2) }}</td>
                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">Total Equity</td>
                <td class="border px-4 py-2 text-right border-gray-300 dark:border-gray-600">{{ number_format($totalEquity, 2) }}</td>
            </tr>
            
            <tr class="font-bold bg-gray-50 dark:bg-gray-700">
                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">Total Assets</td>
                <td class="border px-4 py-2 text-right border-gray-300 dark:border-gray-600">{{ number_format($totalAssets, 2) }}</td>
                <td class="border px-4 py-2 border-gray-300 dark:border-gray-600">Total Liabilities + Equity</td>
                <td class="border px-4 py-2 text-right border-gray-300 dark:border-gray-600">{{ number_format($totalAssets, 2) }}</td>
            </tr>
        </tbody>
    </table>
</x-filament::page>