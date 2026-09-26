<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['customer_unique_id','package_id','status','starts_at','ends_at','grace_days','suspended_at','suspension_reason'];
    protected $casts = ['starts_at'=>'date','ends_at'=>'date','suspended_at'=>'datetime'];
    public function customer() { return $this->belongsTo(CustomersInfo::class, 'customer_unique_id', 'customer_unique_id'); }
    public function package() { return $this->belongsTo(PackageList::class, 'package_id'); }
    public function invoices() { return $this->hasMany(Invoice::class); }
}
