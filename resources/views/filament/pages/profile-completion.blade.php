<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Profile Completeness Indicator -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Profile Completeness
                </h3>
                <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                    {{ $this->getProfileCompleteness() }}%
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                <div 
                    class="bg-primary-600 h-2.5 rounded-full transition-all duration-300" 
                    style="width: {{ $this->getProfileCompleteness() }}%"
                ></div>
            </div>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Complete your profile to unlock all features
            </p>
        </div>

        <!-- Profile Completion Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 relative">
            <x-filament-panels::form wire:submit="save">
                {{ $this->form }}

                <div class="flex justify-end gap-3 mt-6">
                    <x-filament::button
                        color="gray"
                        wire:click="completeLater"
                        wire:loading.attr="disabled"
                        wire:target="completeLater"
                    >
                        <span wire:loading.remove wire:target="completeLater">
                            Complete Later
                        </span>
                        <span wire:loading wire:target="completeLater" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </x-filament::button>
                    <x-filament::button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="save"
                    >
                        <span wire:loading.remove wire:target="save">
                            Save & Continue
                        </span>
                        <span wire:loading wire:target="save" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Saving...
                        </span>
                    </x-filament::button>
                </div>
                
                <!-- Loading overlay for form submission -->
                <div wire:loading wire:target="save" class="absolute inset-0 bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 rounded-lg">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 flex flex-col items-center gap-4">
                        <svg class="animate-spin h-8 w-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Saving your profile...</p>
                    </div>
                </div>
            </x-filament-panels::form>
        </div>
    </div>
</x-filament-panels::page>

