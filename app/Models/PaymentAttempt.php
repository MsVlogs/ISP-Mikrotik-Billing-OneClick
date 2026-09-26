<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentAttempt extends Model
{
    protected $fillable = ['provider','provider_transaction_id','reference','customer_unique_id','amount','currency','status','payload','failure_reason','processed_at'];
    protected $casts = ['amount' => 'decimal:2', 'payload' => 'array', 'processed_at' => 'datetime'];
    public function customer() { return $this->belongsTo(CustomersInfo::class, 'customer_unique_id', 'customer_unique_id'); }
}
