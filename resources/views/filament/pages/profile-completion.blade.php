<x-filament-panels::page>
    <div class="w-[80%] mx-auto space-y-6">
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
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <x-filament-panels::form wire:submit="save">
                {{ $this->form }}

                <div class="flex justify-end gap-3 mt-6">
                    <x-filament::button
                        color="gray"
                        wire:click="completeLater"
                    >
                        Complete Later
                    </x-filament::button>
                    <x-filament::button
                        type="submit"
                    >
                        Save & Continue
                    </x-filament::button>
                </div>
            </x-filament-panels::form>
        </div>
    </div>
</x-filament-panels::page>

