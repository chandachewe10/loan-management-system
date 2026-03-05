<x-filament-panels::page>
    {{-- Filter Form --}}
    <div class="mb-6">
        <x-filament::section>
            <x-slot name="heading">Filter</x-slot>
            {{ $this->form }}

            <div class="mt-4">
                <x-filament::button wire:click="generate" icon="heroicon-m-magnifying-glass">
                    Generate Trial Balance
                </x-filament::button>
            </div>
        </x-filament::section>
    </div>

    @if($accounts->isNotEmpty())
    {{-- Balance Status --}}
    <div class="mb-6">
        @if($isBalanced)
            <div class="rounded-xl border-2 border-green-300 dark:border-green-700 bg-green-50 dark:bg-green-900/20 p-4 flex items-center gap-3">
                <x-heroicon-o-check-circle class="w-8 h-8 text-green-500" />
                <div>
                    <p class="font-bold text-green-700 dark:text-green-400 text-lg">Books are in Balance ✓</p>
                    <p class="text-sm text-green-600 dark:text-green-500">Total Debits equal Total Credits. Your entries are clean.</p>
                </div>
            </div>
        @else
            <div class="rounded-xl border-2 border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/20 p-4 flex items-center gap-3">
                <x-heroicon-o-exclamation-triangle class="w-8 h-8 text-red-500" />
                <div>
                    <p class="font-bold text-red-700 dark:text-red-400 text-lg">Books are Out of Balance ✗</p>
                    <p class="text-sm text-red-600 dark:text-red-500">
                        Difference: ZMW {{ number_format(abs($totalDebits - $totalCredits), 2) }}.
                        Check recent journal entries for errors.
                    </p>
                </div>
            </div>
        @endif
    </div>

    {{-- Trial Balance Table --}}
    <x-filament::section>
        <x-slot name="heading">Trial Balance as of {{ \Carbon\Carbon::parse($as_of_date)->format('d M Y') }}</x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b-2 border-gray-300 dark:border-gray-600">
                        <th class="py-3 px-4 text-left font-semibold text-gray-600 dark:text-gray-400">Code</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600 dark:text-gray-400">Account Name</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600 dark:text-gray-400">Type</th>
                        <th class="py-3 px-4 text-right font-semibold text-blue-600">Debit (DR)</th>
                        <th class="py-3 px-4 text-right font-semibold text-green-600">Credit (CR)</th>
                    </tr>
                </thead>
                <tbody>
                    @php $currentType = null; @endphp
                    @foreach($accounts as $account)
                        @if($currentType !== $account['type'])
                            @php $currentType = $account['type']; @endphp
                            <tr class="bg-gray-50 dark:bg-gray-800/50">
                                <td colspan="5" class="py-2 px-4 text-xs font-bold uppercase tracking-wider
                                    @switch($account['type'])
                                        @case('asset') text-blue-600 @break
                                        @case('liability') text-amber-600 @break
                                        @case('equity') text-emerald-600 @break
                                        @case('revenue') text-purple-600 @break
                                        @case('expense') text-red-600 @break
                                    @endswitch
                                ">
                                    {{ ucfirst($account['type']) }} Accounts
                                </td>
                            </tr>
                        @endif
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <td class="py-2.5 px-4 font-mono text-xs font-semibold text-gray-500">{{ $account['code'] }}</td>
                            <td class="py-2.5 px-4 text-gray-700 dark:text-gray-300">{{ $account['name'] }}</td>
                            <td class="py-2.5 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    @switch($account['type'])
                                        @case('asset') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 @break
                                        @case('liability') bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 @break
                                        @case('equity') bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 @break
                                        @case('revenue') bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 @break
                                        @case('expense') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 @break
                                    @endswitch
                                ">
                                    {{ ucfirst($account['type']) }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono font-medium text-blue-600 dark:text-blue-400">
                                @if($account['debit_balance'] > 0)
                                    {{ number_format($account['debit_balance'], 2) }}
                                @else
                                    <span class="text-gray-300 dark:text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-right font-mono font-medium text-green-600 dark:text-green-400">
                                @if($account['credit_balance'] > 0)
                                    {{ number_format($account['credit_balance'], 2) }}
                                @else
                                    <span class="text-gray-300 dark:text-gray-600">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-gray-400 dark:border-gray-500 bg-gray-100 dark:bg-gray-800 font-bold text-base">
                        <td colspan="3" class="py-3 px-4 text-gray-700 dark:text-gray-200">TOTALS</td>
                        <td class="py-3 px-4 text-right font-mono text-blue-700 dark:text-blue-400">{{ number_format($totalDebits, 2) }}</td>
                        <td class="py-3 px-4 text-right font-mono text-green-700 dark:text-green-400">{{ number_format($totalCredits, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-filament::section>

    @else
        <x-filament::section>
            <div class="flex flex-col items-center py-12 text-gray-400">
                <x-heroicon-o-scale class="w-16 h-16 mb-4 opacity-40" />
                <p class="text-lg font-medium">Select a date and click <strong>Generate Trial Balance</strong></p>
                <p class="text-sm mt-1">Shows all account balances as of the selected date.</p>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
