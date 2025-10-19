<x-filament-panels::page>
    @if($record && $record->ai_scored_at)
        <div class="space-y-6">
            <!-- Header -->
            <x-filament::section>
                <x-slot name="heading">
                    AI Credit Assessment Results
                </x-slot>
                <x-slot name="description">
                    Assessment completed on {{ $record->ai_scored_at->format('M j, Y g:i A') }}
                </x-slot>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Credit Score -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 p-4 text-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">AI Credit Score</div>
                        <div class="text-3xl font-bold {{ $this->getScoreColor($record->ai_credit_score) }}">
                            {{ number_format($record->ai_credit_score) }}
                        </div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">300-850 Scale</div>
                    </div>

                    <!-- Default Probability -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 p-4 text-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">Default Probability</div>
                        <div class="text-3xl font-bold {{ $this->getProbabilityColor($record->default_probability) }}">
                            {{ number_format($record->default_probability * 100, 1) }}%
                        </div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">Likelihood of Default</div>
                    </div>

                    <!-- Recommendation -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600 p-4 text-center">
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">AI Recommendation</div>
                        <div class="text-2xl font-bold {{ $this->getRecommendationColor($record->ai_recommendation) }}">
                            {{ $record->ai_recommendation }}
                        </div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">Based on Risk Analysis</div>
                    </div>
                </div>
            </x-filament::section>

            <!-- Risk Factors -->
            @if(!empty($record->risk_factors))
            <x-filament::section>
                <x-slot name="heading">
                    Identified Risk Factors
                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">({{ count($record->risk_factors) }} found)</span>
                </x-slot>
                
                <div class="space-y-2">
                    @foreach($record->risk_factors as $factor)
                        <div class="flex items-center p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                            <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-500 dark:text-red-400 mr-3" />
                            <span class="text-red-700 dark:text-red-300">{{ $factor }}</span>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
            @endif

            <!-- AI Analysis -->
            @if($record->ai_decision_reason)
            <x-filament::section>
                <x-slot name="heading">AI Analysis</x-slot>
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <p class="text-blue-800 dark:text-blue-200">{{ $record->ai_decision_reason }}</p>
                </div>
            </x-filament::section>
            @endif

            <!-- Loan Details -->
            <x-filament::section>
                <x-slot name="heading">Loan Details</x-slot>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded">
                        <span class="font-medium text-gray-900 dark:text-gray-100">Borrower:</span>
                        <span class="text-gray-600 dark:text-gray-400">{{ $record->borrower->full_name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded">
                        <span class="font-medium text-gray-900 dark:text-gray-100">Principal Amount:</span>
                        <span class="text-gray-600 dark:text-gray-400">ZMW {{ number_format($record->principal_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded">
                        <span class="font-medium text-gray-900 dark:text-gray-100">Loan Duration:</span>
                        <span class="text-gray-600 dark:text-gray-400">{{ $record->loan_duration }} months</span>
                    </div>
                    <div class="flex justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded">
                        <span class="font-medium text-gray-900 dark:text-gray-100">Interest Rate:</span>
                        <span class="text-gray-600 dark:text-gray-400">{{ $record->interest_rate }}%</span>
                    </div>
                </div>
            </x-filament::section>

            <!-- Borrower Financials -->
            <x-filament::section>
                <x-slot name="heading">Borrower Financial Information</x-slot>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded">
                        <span class="font-medium text-gray-900 dark:text-gray-100">Monthly Income:</span>
                        <span class="text-gray-600 dark:text-gray-400">ZMW {{ number_format($record->borrower_monthly_income, 2) }}</span>
                    </div>
                    <div class="flex justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded">
                        <span class="font-medium text-gray-900 dark:text-gray-100">Employment Duration:</span>
                        <span class="text-gray-600 dark:text-gray-400">{{ $record->borrower_employment_months }} months</span>
                    </div>
                    <div class="flex justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded">
                        <span class="font-medium text-gray-900 dark:text-gray-100">Existing Debts:</span>
                        <span class="text-gray-600 dark:text-gray-400">ZMW {{ number_format($record->borrower_existing_debts, 2) }}</span>
                    </div>
                    <div class="flex justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded">
                        <span class="font-medium text-gray-900 dark:text-gray-100">Credit History:</span>
                        <span class="text-gray-600 dark:text-gray-400">{{ $record->borrower_credit_history_months }} months</span>
                    </div>
                    <div class="flex justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded">
                        <span class="font-medium text-gray-900 dark:text-gray-100">Previous Defaults:</span>
                        <span class="text-gray-600 dark:text-gray-400">{{ $record->borrower_previous_defaults }}</span>
                    </div>
                </div>
            </x-filament::section>

        </div>
    @else
        <!-- No Assessment Data -->
        <x-filament::section>
            <x-slot name="heading">No AI Assessment Available</x-slot>
            <div class="text-center py-8">
                <x-heroicon-o-document-magnifying-glass class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-4" />
                <p class="text-gray-600 dark:text-gray-400 mb-4">This loan hasn't been assessed by the AI system yet.</p>
                <a 
                    href="{{ \App\Filament\Resources\LoanResource::getUrl('view', ['record' => $record->id]) }}"
                    class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 focus:bg-primary-500 active:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:focus:ring-offset-gray-800"
                >
                    <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                    Back to Loan
                </a>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>