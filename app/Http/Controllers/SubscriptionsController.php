<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payments;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class SubscriptionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }




public function completeSubscription(Request $request, $amount)
{
    try {

       // Log raw form data
    Log::info('Received payment callback', [
        'data' => $request->input('data'),
        'amount' => $amount,
    ]);

     $paymentData = json_decode($request->input('data'), true);
     $reference = $paymentData['reference'] ?? null;
        $payment = Payments::create([
            'organization_id' => auth()->user()->organization_id,
            'payer_id' => auth()->id(),
            'payment_amount' => $amount,
            'transaction_reference' => $reference,
            'gateway' => 'LENCO PAYMENT GATEWAY',
            'payment_made_at' => Carbon::now(),
            'payment_expires_at' => Carbon::now()->addMonth(),
        ]);


        return response()->json([
            'status' => 'success',
            'payment' => $payment,

        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}


}
