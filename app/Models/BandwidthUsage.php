<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BandwidthUsage extends Model
{
    protected $table = 'bandwidth_usage';
    protected $fillable = ['customer_unique_id','router_id','usage_date','download_gb','upload_gb','cost'];
    protected $casts = ['usage_date'=>'date','download_gb'=>'decimal:3','upload_gb'=>'decimal:3','cost'=>'decimal:2'];
}
