<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        $paid_amount = round($this?->payments->sum('amount'), 2);
        $payable_amount = round($this->payable_amount, 2);
        $balance_amount = $payable_amount - $paid_amount;

        return [
            'id' => $this->id,
            'amount' => round($this->amount, 2),
            'user_id' => $this->user_id,
            'due_on' => $this->due_on,
            'vat' => round($this->vat, 2),
            'payable_amount' => $payable_amount,
            'paid_amount' => $paid_amount,
            'balance_amount' => $balance_amount,
            'is_vat_inclusive' => $this->is_vat_inclusive,
            'payment_status' => ucfirst(strtolower($this->paymentStatus())),
            'payments' => isset($this->payments) && !empty($this->payments) ? PaymentResource::collection($this->payments) : []
        ];
    }

    private function paymentStatus()
    {
        $status = $this->status == "PENDING" ? ($this->due_on >= now() ? "Outstanding" : "Overdue") : $this->status;

        return $status;
    }
}
