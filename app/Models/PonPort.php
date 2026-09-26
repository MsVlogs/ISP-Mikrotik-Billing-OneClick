<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PonPort extends Model
{
    protected $fillable = ['olt_id','port_no','status','onu_capacity','onu_online','rx_power'];
    protected $casts = ['rx_power'=>'decimal:2'];
    public function olt() { return $this->belongsTo(NetworkInventoryDevice::class, 'olt_id'); }
}
