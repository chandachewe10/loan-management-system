<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Loan Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Loan Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Loan Number</p>
                    <p class="text-lg font-bold">{{ $record->loan_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Principal Amount</p>
                    <p class="text-lg font-bold">ZMW {{ number_format($record->principal_amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Monthly EMI</p>
                    <p class="text-lg font-bold text-green-600">ZMW {{ number_format($monthlyEMI, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total EMI Amount</p>
                    <p class="text-lg font-bold">ZMW {{ number_format($totalEMI, 2) }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Paid Installments</p>
                    <p class="text-lg font-bold text-green-600">{{ $paidInstallments }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Remaining Installments</p>
                    <p class="text-lg font-bold text-yellow-600">{{ $remainingInstallments }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Installments</p>
                    <p class="text-lg font-bold text-blue-600">{{ count($schedule) }}</p>
                </div>
            </div>
        </div>

        <!-- EMI Schedule Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold">EMI Schedule</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Monthly payment schedule until loan completion</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Installment #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Payment Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">EMI Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Principal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Interest</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Outstanding Principal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Remaining Balance</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($schedule as $installment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $installment['installment_number'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $installment['payment_date_formatted'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">ZMW {{ number_format($installment['emi_amount'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">ZMW {{ number_format($installment['principal_component'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">ZMW {{ number_format($installment['interest_component'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">ZMW {{ number_format($installment['outstanding_principal'], 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($installment['is_paid'])
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Paid</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $installment['remaining_balance'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                ZMW {{ number_format($installment['remaining_balance'], 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>

