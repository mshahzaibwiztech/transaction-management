<?php

namespace App\Observers;

use App\Models\Transaction;

class TransactionObserver
{
    public function created(Transaction $transaction): void
    {
        $payable_amount = $transaction->amount;
        $vat_amount = 0;

        // ** If VAT is exclusive from amount and VAT is greater then zero
        if(!$transaction->is_vat_inclusive && $transaction->vat > 0){
            $vat_amount = (($transaction->amount * $transaction->vat) / 100);
        }
            
        $transaction->payable_amount = $payable_amount + $vat_amount;
        $transaction->save();
    }
}
