<?php

namespace App\Jobs\Router;

use App\Models\RouterHealthSnapshot;
use App\Models\RouterList;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecordRouterHealth implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function __construct(public int $routerId) {}
    public function handle(): void
    {
        $router = RouterList::find($this->routerId);
        if (!$router) return;
        $started = microtime(true);
        $status = 'offline'; $error = null;
        try { $status = $router->action === 'connected' ? 'online' : 'degraded'; } catch (\Throwable $e) { $error = $e->getMessage(); }
        RouterHealthSnapshot::create(['router_id'=>$router->id,'status'=>$status,'latency_ms'=>(int)((microtime(true)-$started)*1000),'error'=>$error,'checked_at'=>now()]);
    }
}
