<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChannelPackage extends Model
{
    protected $fillable = ['name','monthly_price','is_active'];
    protected $casts = ['monthly_price'=>'decimal:2','is_active'=>'boolean'];
    public function channels() { return $this->belongsToMany(Channel::class); }
}
