<?php
use App\Http\Controllers\{BorrowersController,
SubscriptionsController, CustomerStatementController, BorrowerApplicationController, LoanApplicationController, DirectDebitMandateController, PayslipController};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/subscription/{amount}', function ($amount) {
    return view('gateways.lenco.lencoPayments', ['amount' => decrypt($amount)]);
})->name('subscription.lenco');

Route::post('completeSubscription/{amount}',[SubscriptionsController::class,'completeSubscription'])
->name('completeSubscription');



Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('borrower',BorrowersController::class);
});

Route::get('/statement/{record}', [CustomerStatementController::class, 'download'])->name('statement.download');

Route::get('/payslip/{payslip}/download', [\App\Http\Controllers\PayslipController::class, 'download'])
    ->middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->name('payslip.download');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/borrower-application/{id}/preview', [BorrowerApplicationController::class, 'preview'])->name('borrower.application.preview');
    Route::get('/borrower-application/{id}/download', [BorrowerApplicationController::class, 'download'])->name('borrower.application.download');
    Route::get('/loan-application/{id}/preview', [LoanApplicationController::class, 'preview'])->name('loan.application.preview');
    Route::get('/loan-application/{id}/download', [LoanApplicationController::class, 'download'])->name('loan.application.download');
    Route::get('/direct-debit-mandate/{id}/preview', [DirectDebitMandateController::class, 'preview'])->name('direct.debit.mandate.preview');
    Route::get('/direct-debit-mandate/{id}/download', [DirectDebitMandateController::class, 'download'])->name('direct.debit.mandate.download');
});

