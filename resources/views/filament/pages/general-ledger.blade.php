<x-filament-panels::page>
    {{-- Filter Form --}}
    <div class="mb-6">
        <x-filament::section>
            <x-slot name="heading">Filter</x-slot>
            {{ $this->form }}

            <div class="mt-4">
                <x-filament::button wire:click="generate" icon="heroicon-m-magnifying-glass">
                    Generate Ledger
                </x-filament::button>
            </div>
        </x-filament::section>
    </div>

    {{-- Summary Cards --}}
    @if(count($ledgerLines) > 0)
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
            {{-- Total Debits --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-blue-500 mb-1">Total Debits (DR)</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white font-mono">
                    ZMW {{ number_format($totalDebits, 2) }}
                </p>
            </div>
            {{-- Total Credits --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-green-500 mb-1">Total Credits (CR)</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white font-mono">
                    ZMW {{ number_format($totalCredits, 2) }}
                </p>
            </div>
            {{-- Net Balance --}}
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wider text-amber-500 mb-1">Net Balance</p>
                <p class="text-2xl font-bold {{ $runningBalance >= 0 ? 'text-green-600' : 'text-red-600' }} font-mono">
                    ZMW {{ number_format(abs($runningBalance), 2) }}
                    <span class="text-sm font-normal">({{ $runningBalance >= 0 ? 'DR' : 'CR' }})</span>
                </p>
            </div>
        </div>

        {{-- Ledger Table --}}
        <x-filament::section>
            <x-slot name="heading">
                @if($selectedAccountCode)
                    Ledger for [{{ $selectedAccountCode }}] {{ $selectedAccountName }}
                @else
                    All Accounts Ledger
                @endif
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="py-3 px-4 text-left font-semibold text-gray-600 dark:text-gray-400">Date</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-600 dark:text-gray-400">Entry #</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-600 dark:text-gray-400">Account</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-600 dark:text-gray-400">Description</th>
                            <th class="py-3 px-4 text-right font-semibold text-blue-600">Debit (DR)</th>
                            <th class="py-3 px-4 text-right font-semibold text-green-600">Credit (CR)</th>
                            <th class="py-3 px-4 text-right font-semibold text-gray-600 dark:text-gray-400">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ledgerLines as $line)
                            <tr
                                class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="py-3 px-4 text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                    {{ $line['entry_date'] }}
                                </td>
                                <td class="py-3 px-4">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono font-medium bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400">
                                        {{ $line['entry_number'] }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center gap-1">
                                        <span
                                            class="text-xs font-mono font-semibold text-gray-500">{{ $line['account_code'] }}</span>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $line['account_name'] }}</span>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-gray-600 dark:text-gray-400 max-w-xs truncate">
                                    {{ $line['description'] }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-medium text-blue-600 dark:text-blue-400">
                                    @if($line['type'] === 'debit')
                                        {{ number_format($line['amount'], 2) }}
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600">—</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-medium text-green-600 dark:text-green-400">
                                    @if($line['type'] === 'credit')
                                        {{ number_format($line['amount'], 2) }}
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600">—</span>
                                    @endif
                                </td>
                                <td
                                    class="py-3 px-4 text-right font-mono font-semibold {{ $line['running_balance'] >= 0 ? 'text-gray-800 dark:text-gray-200' : 'text-red-600 dark:text-red-400' }}">
                                    {{ number_format(abs($line['running_balance']), 2) }}
                                    <span
                                        class="text-xs font-normal text-gray-400">{{ $line['running_balance'] >= 0 ? 'DR' : 'CR' }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr
                            class="border-t-2 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800/50 font-bold">
                            <td colspan="4" class="py-3 px-4 text-gray-700 dark:text-gray-300">TOTALS</td>
                            <td class="py-3 px-4 text-right font-mono text-blue-600">{{ number_format($totalDebits, 2) }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-green-600">{{ number_format($totalCredits, 2) }}
                            </td>
                            <td
                                class="py-3 px-4 text-right font-mono {{ $runningBalance >= 0 ? 'text-gray-800 dark:text-gray-200' : 'text-red-600' }}">
                                {{ number_format(abs($runningBalance), 2) }}
                                <span
                                    class="text-xs font-normal text-gray-400">{{ $runningBalance >= 0 ? 'DR' : 'CR' }}</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-filament::section>

    @elseif($hasGenerated)
        <x-filament::section>
            <div class="flex flex-col items-center py-12 text-gray-400">
                <x-heroicon-o-document-magnifying-glass class="w-16 h-16 mb-4 opacity-40" />
                <p class="text-lg font-medium">No transactions found for the selected filters.</p>
                <p class="text-sm mt-1">Try adjusting the date range or account selection.</p>
            </div>
        </x-filament::section>
    @else
        <x-filament::section>
            <div class="flex flex-col items-center py-12 text-gray-400">
                <x-heroicon-o-table-cells class="w-16 h-16 mb-4 opacity-40" />
                <p class="text-lg font-medium">Select filters above and click <strong>Generate Ledger</strong></p>
                <p class="text-sm mt-1">You can filter by account and date range.</p>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>