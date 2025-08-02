<?php

namespace App\Filament\Resources\LoanResource\Pages;

use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\LoanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoans extends ListRecords
{
    protected static string $resource = LoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs():array{
        return [
       'Active' => Tab::make('Active')
       ->icon('heroicon-m-home')
       ->modifyQueryUsing(fn (Builder $query) => $query->where('loan_status', 'approved')->orWhere('loan_status', 'partially_paid')),
       'Settled' => Tab::make('Settled')
       ->icon('heroicon-m-home')
       ->modifyQueryUsing(fn (Builder $query) => $query->where('loan_status', 'fully_paid')),
       'Processing' => Tab::make('Processing')
       ->icon('heroicon-m-home')
       ->modifyQueryUsing(fn (Builder $query) => $query->where('loan_status', 'processing')->orWhere('loan_status', 'requested')),
       'Over Due' => Tab::make('Over Due')
       ->icon('heroicon-m-home')
       ->modifyQueryUsing(fn (Builder $query) => $query->where('loan_status', 'defaulted')),
       'Failed' => Tab::make('Failed')
       ->icon('heroicon-m-home')
       ->modifyQueryUsing(fn (Builder $query) => $query->where('loan_status', 'denied')),



        ];



}
}
