<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Expense;
use App\Models\Loan; 
use App\Models\Wallet; 

class StatementOfFinancialPosition
{
    
    public function cashAmount(): float
    {
        return (float) Wallet::where('branch_id', "=", auth()->user()->branch_id)->sum('balance'); 
    }




    public function equipmentAmount(): float
    {
        return (float) Asset::where('branch_id', "=", auth()->user()->branch_id)->whereIn('status', ['active'])->sum('net_book_value');
    }

   
    public function totalAssets(): float
    {
        return $this->cashAmount() + $this->equipmentAmount();
    }

    
    public function totalLiabilities(): float
    {
        return (float) Expense::where('branch_id', "=", auth()->user()->branch_id)->sum('expense_amount');
    }

    
    public function totalEquity(): float
    {
        return $this->totalAssets() - $this->totalLiabilities();
    }

   
    public function getReportData(): array
    {
        return [
            'cashAmount' => $this->cashAmount(),
            'equipmentAmount' => $this->equipmentAmount(),
            'totalAssets' => $this->totalAssets(),
            'totalLiabilities' => $this->totalLiabilities(),
            'totalEquity' => $this->totalEquity(),
        ];
    }
}
