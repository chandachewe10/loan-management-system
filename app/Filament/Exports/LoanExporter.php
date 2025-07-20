<?php

namespace App\Filament\Exports;

use App\Models\{Loan,Repayments};
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Carbon\Carbon;

class LoanExporter extends Exporter
{
    protected static ?string $model = Loan::class;

    public static function getColumns(): array
    {
        return [
              ExportColumn::make('id')
                ->label('ID'),
              ExportColumn::make('loan_number')
             ->label('Loan ID'),
              ExportColumn::make('borrower_id')
             ->label('Customer ID'),
              ExportColumn::make('borrower.first_name')
             ->label('First Name'),
               ExportColumn::make('borrower.last_name')
             ->label('Last Name'),
               ExportColumn::make('borrower.mobile')
             ->label('Main Phone Number'),
               ExportColumn::make('borrower.identification')
             ->label('NRC Number'),
             ExportColumn::make('principal_amount')
            ->label('Loan Amount Obtained (K)'),
             ExportColumn::make('disbursed_amount')
            ->label('Disbursed Amount (K)'),
             ExportColumn::make('service_fee')
            ->label('Processing Fee'),
            ExportColumn::make('interest_rate')
             ->label('Interest rate'),
            ExportColumn::make('repayment_amount')
             ->label('Loan Repayment Amount(K)'),

               ExportColumn::make('repaid_amount')
             ->label('Loan Repaid Amount (K)')
               ->state(function (Loan $loanRecord): float {
                return ($loanRecord->repayment_amount - $loanRecord->balance);
             }),
            

              ExportColumn::make('balance')
             ->label('Loan Balance (K)'),
             
              ExportColumn::make('loan_type.loan_name')
             ->label('Loan Name'),

              ExportColumn::make('loan_duration')
             ->label('Loan Duration'),
             
             
            ExportColumn::make('outstanding_balance') 
    ->label('Total Outstanding Balance')
    ->state(fn ($record) => $record->balance),

              ExportColumn::make('borrower.tpin')
             ->label('TPIN'),

               ExportColumn::make('borrower.dob')
             ->label('Date of Birth'),


               ExportColumn::make('borrower.gender')
             ->label('Gender'),

              ExportColumn::make('borrower.marital')
             ->label('Marital Status'),

               ExportColumn::make('borrower.email')
             ->label('Email Address'),

              ExportColumn::make('borrower.address')
             ->label('Residential Address'),

              ExportColumn::make('borrower.city')
             ->label('Town'),

            

          

            ExportColumn::make('disbursement_date')     
            ->label('Disbursement Date')
            ->state(fn (Loan $loan) => $loan->created_at   
            ->setTimezone('Africa/Lusaka')            
            ->format('d‑M‑Y')
            ),


           ExportColumn::make('disbursement_time')
           ->label('Disbursement Time')
           ->state(fn (Loan $loan) => $loan->created_at
           ->setTimezone('Africa/Lusaka')
           ->format('H:i')                           
             ),
             

             ExportColumn::make('loan_due_date')
             ->label('Due Date'),

    //        ExportColumn::make('days_past_due')
    // ->label('Days Past Due')
    // ->state(function (Loan $loan): int {
       
    //     $due = Carbon::createFromFormat('yy mm dd', $loan->loan_due_date);
    //     $overdue = $due->diffInDays(Carbon::today(), false);
    //     if($overdue < 0) {
    //     return 0;
    //     }
    //   else{
    //    return $overdue;
    //      }
        
    // }),

            
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your loan export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
