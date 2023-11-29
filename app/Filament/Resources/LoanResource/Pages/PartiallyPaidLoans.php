<?php
namespace App\Filament\Resources\LoanResource\Pages;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\LoanResource;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\View\View;
use App\Models\Loan;
use Filament\Resources\Pages\Page;

class PartiallyPaidLoans extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;
   
    protected static string $resource = LoanResource::class;

    protected static string $view = 'filament.resources.loan-resource.pages.partially-paid-loans';
    protected static ?string $navigationIcon = 'fas-dollar-sign';
    protected static ?string $navigationLabel = 'Partially Paid Loans';
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('loan_status',"=",'partially_paid')->count();
    }
    public function getBreadcrumb(): ?string
    {
        return static::$breadcrumb ?? __('Partially Paid Loans');
    }

    public function table(Table $table): Table
    {
        return $table
        ->query(Loan::query()->where('loan_status', 'partially_paid'))
        ->columns([
            Tables\Columns\TextColumn::make('borrower.full_name')
            ->searchable(),
        Tables\Columns\TextColumn::make('loan_type.loan_name')
            ->searchable(),
        Tables\Columns\TextColumn::make('loan_status')
            ->searchable(),
        Tables\Columns\TextColumn::make('principal_amount')
            ->label('Principle Amount')
            ->money('ZMW')
            ->sortable()
            ->searchable(),
            Tables\Columns\TextColumn::make('loan_due_date')
            ->label('Due Date')
            ->searchable(),
        ])
        ->filters([
            // ...
        ])
        ->actions([
            // ...
        ])
        ->bulkActions([
            // ...
        ]);
    }



}
