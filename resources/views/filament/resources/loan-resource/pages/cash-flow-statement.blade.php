<x-filament-panels::page>
    <div class="space-y-6">

     <!-- Transaction Details -->
        <x-filament::section>
            <x-slot name="heading">Transaction Details (All Accounts)</x-slot>
            {{ $this->table }}
        </x-filament::section>
        <!-- Total Cash Position -->
        <x-filament::section>
            <x-slot name="heading">Total Cash Position Across All Accounts</x-slot>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <div class="text-sm text-gray-500">Total Balance</div>
                    <div class="text-2xl font-bold text-success">
                        ZMW {{ number_format($this->totalBalance, 2) }}
                    </div>
                </div>
              

            </div>
        </x-filament::section>


    </div>
</x-filament-panels::page>