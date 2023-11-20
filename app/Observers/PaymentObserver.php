<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\Transaction;

class PaymentObserver
{
    public function created(Payment $payment): void
    {
        $transaction_id = $payment->transaction_id;
        $transaction = Transaction::find($transaction_id);
        $total_paid_amount = Payment::where('transaction_id', $transaction_id)->sum('amount');
        
        if($transaction->payable_amount <= $total_paid_amount){
            $transaction->status = 'PAID';
            $transaction->save();
        }
    }
}
