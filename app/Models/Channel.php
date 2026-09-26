<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    protected $fillable = ['name','stream_url','category','quality','epg_url','is_active'];
    protected $casts = ['is_active'=>'boolean'];
    public function packages() { return $this->belongsToMany(ChannelPackage::class); }
}
