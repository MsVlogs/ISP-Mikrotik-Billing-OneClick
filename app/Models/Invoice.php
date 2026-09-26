<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['invoice_no','customer_unique_id','subscription_id','billing_period','subtotal','discount','tax','total','paid','status','due_at'];
    protected $casts = ['billing_period'=>'date','due_at'=>'date','subtotal'=>'decimal:2','discount'=>'decimal:2','tax'=>'decimal:2','total'=>'decimal:2','paid'=>'decimal:2'];
    public function customer() { return $this->belongsTo(CustomersInfo::class, 'customer_unique_id', 'customer_unique_id'); }
    public function subscription() { return $this->belongsTo(Subscription::class); }
    public function balance() { return max(0, (float) $this->total - (float) $this->paid); }
}
