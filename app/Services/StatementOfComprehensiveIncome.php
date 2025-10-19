<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\Expense;

class StatementOfComprehensiveIncome
{
    // Interest income from loans
    public function interestIncome(): float
    {
        return (float) Loan::where('branch_id', "=", auth()->user()->branch_id)->whereIn('loan_status', ['approved','partially_paid','fully_paid','defaulted'])->sum('interest_amount');
    }

    // Service fee income from loans
    public function serviceFeeIncome(): float
    {
        return (float) Loan::where('branch_id', "=", auth()->user()->branch_id)->whereIn('loan_status', ['approved','partially_paid','fully_paid','defaulted'])->sum('service_fee');
    }

    // Total income
    public function totalIncome(): float
    {
        return $this->interestIncome() + $this->serviceFeeIncome();
    }

    // Total regular expenses
    public function totalExpenses(): float
    {
        return (float) Expense::where('branch_id', "=", auth()->user()->branch_id)->sum('expense_amount') + $this->badLoans();
    }

    // Total bad loans / defaulters
    public function badLoans(): float
    {
        return (float) Loan::where('branch_id', "=", auth()->user()->branch_id)->where('loan_status', 'defaulted')->sum('balance');
    }

    // Net profit
    public function netProfit(): float
    {
        return $this->totalIncome() - $this->totalExpenses();
    }

    // Return all data for Blade
    public function getReportData(): array
    {
        return [
            'interestIncome' => $this->interestIncome(),
            'serviceFeeIncome' => $this->serviceFeeIncome(),
            'totalIncome' => $this->totalIncome(),
            'totalExpenses' => $this->totalExpenses(),
            'badLoans' => $this->badLoans(),
            'netProfit' => $this->netProfit(),
        ];
    }
}
