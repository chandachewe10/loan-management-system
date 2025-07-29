<?php

namespace App\Providers;
use App\Observers\ActivityLogObserver;
use App\Observers\LoanAgreementFormsObserver;
use App\Observers\TransferObserver;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use App\Filament\CustomLogOutResponse;
use App\Models\ActivityLogs;
use App\Models\Borrower;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Loan;
use App\Models\LoanAgreementForms;
use App\Models\LoanSettlementForms;
use App\Models\LoanType;
use App\Models\Messages;
use App\Models\Repayments;
use App\Models\ThirdParty;
use App\Models\Transaction;
use App\Models\Transfer;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use App\Models\User;
use App\Observers\BorrowerObserver;
use App\Observers\ExpenseCategoryObserver;
use App\Observers\ExpenseObserver;
use App\Observers\LoanObserver;
use App\Observers\LoanSettlementFormsObserver;
use App\Observers\LoanTypesObserver;
use App\Observers\MessagesObserver;
use App\Observers\RepaymentsObserver;
use App\Observers\ThirdyPartyObserver;
use App\Observers\TransactionObserver;
use App\Observers\UserObserver;
use App\Observers\WalletObserver;
use App\Models\Wallet;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        $this->app->bind(LogoutResponseContract::class, CustomLogOutResponse::class);
        Model::unguard();
        Filament::registerNavigationGroups([
            'Customers',
            'Loan Agreement Forms',
            'Wallets',
            'Loans',
            'Expenses',
            'Repayments',
            'Addons',
        ]);

        User::observe(UserObserver::class);
        ThirdParty::observe(ThirdyPartyObserver::class);
        Repayments::observe(RepaymentsObserver::class);
        Messages::observe(MessagesObserver::class);
        LoanType::observe(LoanTypesObserver::class);
        LoanSettlementForms::observe(LoanSettlementFormsObserver::class);
        Loan::observe(LoanObserver::class);
        LoanAgreementForms::observe(LoanAgreementFormsObserver::class);
        Expense::observe(ExpenseObserver::class);
        ExpenseCategory::observe(ExpenseCategoryObserver::class);
        Borrower::observe(BorrowerObserver::class);
        ActivityLogs::observe(ActivityLogObserver::class);
        Wallet::observe(WalletObserver::class);
        Transfer::observe(TransferObserver::class);
        Transaction::observe(TransactionObserver::class);
    }


}
