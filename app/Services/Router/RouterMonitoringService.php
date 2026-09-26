<?php

namespace App\Services\Router;

use App\Models\RouterList;
use RouterOS\Query;

class RouterMonitoringService
{
    public function getHealth(RouterList $router): array
    {
        try {
            if ($router->action !== 'connected') {
                return ['status' => 'offline', 'latency_ms' => null, 'error' => 'Not connected'];
            }

            return [
                'status' => 'online',
                'latency_ms' => 0,
                'active_sessions' => 0,
                'cpu_percent' => 0,
                'memory_percent' => 0,
                'error' => null,
            ];
        } catch (\Throwable $e) {
            return ['status' => 'error', 'error' => $e->getMessage()];
        }
    }
}
