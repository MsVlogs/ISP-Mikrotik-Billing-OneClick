<?php

use Illuminate\Support\Facades\Schedule;
use App\Jobs\Billing\GenerateMonthlyInvoices;
use App\Jobs\Router\RecordRouterHealth;
use App\Models\RouterList;

Schedule::job(new GenerateMonthlyInvoices)->monthlyOn(1, '00:10');
Schedule::call(function () { RouterList::pluck('id')->each(fn ($id) => RecordRouterHealth::dispatch($id)); })->everyFiveMinutes();
