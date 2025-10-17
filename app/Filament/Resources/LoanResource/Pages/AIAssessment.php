<?php

namespace App\Filament\Resources\LoanResource\Pages;

use App\Filament\Resources\LoanResource;
use Filament\Resources\Pages\Page;

class AIAssessment extends Page
{
    protected static string $resource = LoanResource::class;

    protected static string $view = 'filament.resources.loan-resource.pages.a-i-assessment';

    public $record;

    public function mount($record): void
    {
        $this->record = \App\Models\Loan::find($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('back')
                ->label('Back to Loan')
                ->url(LoanResource::getUrl('view', ['record' => $this->record]))
                ->icon('heroicon-o-arrow-left'),
        ];
    }

    // Helper methods for styling
    public function getScoreColor($score): string
    {
        return match(true) {
            $score >= 700 => 'text-green-600',
            $score >= 600 => 'text-yellow-600',
            $score >= 500 => 'text-orange-600',
            default => 'text-red-600',
        };
    }

    public function getProbabilityColor($probability): string
    {
        return match(true) {
            $probability <= 0.1 => 'text-green-600',
            $probability <= 0.2 => 'text-yellow-600',
            $probability <= 0.3 => 'text-orange-600',
            default => 'text-red-600',
        };
    }

    public function getRecommendationColor($recommendation): string
    {
        return match($recommendation) {
            'APPROVE' => 'text-green-600',
            'REVIEW' => 'text-yellow-600',
            'REJECT' => 'text-red-600',
            default => 'text-gray-600',
        };
    }
}
