<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentCreateRequest;
use App\Models\Payment;
use Exception;

class PaymentController extends Controller
{
    public function create(PaymentCreateRequest $request)
    {
        try {

            Payment::create($request->only(
                'transaction_id',
                'amount',
                'paid_on',
                'details'
            ));
            // ** Updating transaction status in PaymentObserver
            
            return response()->json(['message' => 'Payment created successfully'], 201);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
}
