<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'event_id',
        'amount',
        'currency',
        'card_ends_with',
        'transaction_key'
    ];

    public function getAmountAttribute()
    {
        if (!empty($this->attributes['amount'])) {
            return (float) $this->attributes['amount'];
        } else {
            return 0;
        }
    }
}
