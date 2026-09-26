<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\ChannelPackage;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\PaymentAttempt;
use App\Models\Subscription;
use Illuminate\Http\Request;

class PlatformFeatureController extends Controller
{
    public function payments() { return PaymentAttempt::latest()->paginate(25); }
    public function invoices() { return Invoice::with('customer')->latest()->paginate(25); }
    public function subscriptions() { return Subscription::with('customer','package')->latest()->paginate(25); }
    public function employees() { return Employee::with('user')->latest()->paginate(25); }
    public function channels() { return Channel::with('packages')->where('is_active', true)->get(); }
    public function channelPackages() { return ChannelPackage::with('channels')->where('is_active', true)->get(); }
    public function storePayment(Request $request) { return PaymentAttempt::create($request->validate(['provider'=>'required|string|max:40','provider_transaction_id'=>'required|string|max:120','amount'=>'required|numeric|min:0','customer_unique_id'=>'nullable|string','reference'=>'nullable|string'])); }
}
