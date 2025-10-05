<x-filament::page>
    <h2 class="text-xl font-bold mb-4">Statement of Financial Position</h2>

    <table class="min-w-full bg-white border rounded">
        <thead>
            <tr class="bg-gray-100 font-semibold">
                <th class="border px-4 py-2 text-left">Assets</th>
                <th class="border px-4 py-2 text-right">Amount (K)</th>
                <th class="border px-4 py-2 text-left">Liabilities & Equity</th>
                <th class="border px-4 py-2 text-right">Amount (K)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-4 py-2">Cash</td>
                <td class="border px-4 py-2 text-right">{{ number_format($cashAmount, 2) }}</td>
                <td class="border px-4 py-2">Loans Payable</td>
                <td class="border px-4 py-2 text-right">{{ number_format($totalLiabilities, 2) }}</td>
            </tr>
            <tr>
                <td class="border px-4 py-2">Loans to Clients</td>
                <td class="border px-4 py-2 text-right">{{ number_format($loansAmount, 2) }}</td>
                <td class="border px-4 py-2 font-bold">Equity</td>
                <td class="border px-4 py-2 text-right font-bold">{{ number_format($totalEquity, 2) }}</td>
            </tr>
            <tr>
                <td class="border px-4 py-2">Equipment</td>
                <td class="border px-4 py-2 text-right">{{ number_format($equipmentAmount, 2) }}</td>
                <td class="border px-4 py-2 bg-gray-100 font-bold">Total Liabilities + Equity</td>
                <td class="border px-4 py-2 text-right bg-gray-100 font-bold">{{ number_format($totalAssets, 2) }}</td>
            </tr>
            <tr class="font-bold">
                <td class="border px-4 py-2">Total Assets</td>
                <td class="border px-4 py-2 text-right">{{ number_format($totalAssets, 2) }}</td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</x-filament::page>
