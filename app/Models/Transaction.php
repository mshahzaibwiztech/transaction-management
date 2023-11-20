<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    protected $fillable = [
        'amount',
        'user_id',
        'due_on',
        'vat',
        'payable_amount',
        'is_vat_inclusive',
        'status',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
