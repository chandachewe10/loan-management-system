<?php
use App\Http\Controllers\{BorrowersController,
SubscriptionsController, CustomerStatementController, BorrowerApplicationController};
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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/borrower-application/{id}/preview', [BorrowerApplicationController::class, 'preview'])->name('borrower.application.preview');
    Route::get('/borrower-application/{id}/download', [BorrowerApplicationController::class, 'download'])->name('borrower.application.download');
});

