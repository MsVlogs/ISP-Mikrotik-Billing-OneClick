<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouterHealthSnapshot extends Model
{
    protected $fillable = ['router_id','status','latency_ms','active_sessions','cpu_percent','memory_percent','error','checked_at'];
    protected $casts = ['cpu_percent'=>'decimal:2','memory_percent'=>'decimal:2','checked_at'=>'datetime'];
    public function router() { return $this->belongsTo(RouterList::class, 'router_id'); }
}
