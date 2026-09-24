<?php

namespace App\Http\Controllers;

use App\Models\BillingInfo;
use App\Models\CustomersInfo;
use App\Models\PackageList;
use App\Models\PPPSecrets;
use App\Models\Reseller;
use App\Models\RouterList;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BroadbandController extends Controller
{
    private function baseQuery(Request $request)
    {
        return CustomersInfo::query()
            ->with(['billing', 'pppUser', 'customerAddress', 'package', 'reseller'])
            ->when($request->q, fn ($q, $v) => $q->where(function ($x) use ($v) {
                $like = "%{$v}%";
                $x->where('customer_name', 'like', $like)
                  ->orWhere('customer_unique_id', 'like', $like)
                  ->orWhere('mobile', 'like', $like)
                  ->orWhere('alternative_mobile', 'like', $like)
                  ->orWhere('email', 'like', $like)
                  ->orWhere('contact_person', 'like', $like)
                  ->orWhere('parents_name', 'like', $like)
                  ->orWhere('spouse_name', 'like', $like)
                  ->orWhere('address', 'like', $like)
                  ->orWhere('identification_no', 'like', $like)
                  ->orWhere('profession', 'like', $like)
                  ->orWhereHas('customerAddress', function ($address) use ($like) {
                      $address->where('label_name', 'like', $like)
                          ->orWhere('input_type_text', 'like', $like)
                          ->orWhere('input_type_dropdown', 'like', $like)
                          ->orWhere('input_type_textarea', 'like', $like);
                  })
                  ->orWhereHas('pppUser', function ($ppp) use ($like) {
                      $ppp->where('username', 'like', $like)
                          ->orWhere('router_name', 'like', $like)
                          ->orWhere('ppp_remote_ip', 'like', $like)
                          ->orWhere('ip_address', 'like', $like)
                          ->orWhere('caller_id', 'like', $like)
                          ->orWhere('comment', 'like', $like);
                  })
                  ->orWhereHas('package', function ($package) use ($like) {
                      $package->where('package', 'like', $like);
                  });
            }))
            ->when($request->router_id, fn ($q, $v) => $q->whereHas('pppUser', fn ($x) => $x->where('router_name', $v)))
            ->when($request->reseller_id, fn ($q, $v) => $q->where('reseller_id', $v));
    }

    public function list(Request $request)
    {
        $status = $request->status;
        $query = $this->baseQuery($request);
        if ($status === 'online') $query->where('status', 'active')->whereHas('pppUser', fn ($q) => $q->where('status', 'active'));
        if ($status === 'inactive') $query->whereIn('status', ['inactive', 'disable']);
        if ($status === 'unverified') $query->where(function ($q) { $q->whereNull('mobile')->orWhereNull('identification_no'); });
        if ($status === 'due') $query->whereHas('billing', fn ($q) => $q->where('due_amount', '>', 0));
        if ($request->from) $query->whereDate('created_at', '>=', $request->from);
        if ($request->to) $query->whereDate('created_at', '<=', $request->to);
        $rows = min(max((int) $request->input('rows', 50), 10), 500);
        $customers = $query->latest('id')->paginate($rows)->withQueryString();
        return view('xlink.broadband.customer-list', ['customers' => $customers, 'mode' => $status ?: 'all', 'routers' => RouterList::orderBy('router_name')->get(), 'resellers' => Reseller::with('user')->get(), 'packages' => PackageList::orderBy('package')->get()]);
    }

    public function search(Request $request)
    {
        $customers = $this->baseQuery($request)->latest('id')->paginate(50)->withQueryString();
        return view('xlink.broadband.search', compact('customers'));
    }

    public function due(Request $request)
    { $request->merge(['status' => 'due']); return $this->list($request); }
    public function inactive(Request $request)
    { $request->merge(['status' => 'inactive']); return $this->list($request); }
    public function newCustomers(Request $request)
    { $request->merge(['from' => Carbon::now()->toDateString()]); return $this->list($request); }
    public function unverified(Request $request)
    { $request->merge(['status' => 'unverified']); return $this->list($request); }

    public function disableCustomer(string $id)
    {
        abort_unless(auth()->user()?->hasRole('Super Admin') || hasAccess(['Super Admin'], ['disable-customer']), 403);

        try {
            $uniqueId = decrypt($id);
            $customer = CustomersInfo::where('customer_unique_id', $uniqueId)->with('pppUser')->firstOrFail();

            if ($customer->pppUser && $customer->pppUser->router_name) {
                app(MikrotikController::class)->disablePPPSecret(
                    $uniqueId,
                    $customer->pppUser->router_name,
                    $customer->pppUser->username
                );
            }

            \DB::transaction(function () use ($customer) {
                $customer->status = 'disable';
                $customer->save();

                if ($customer->pppUser) {
                    PPPSecrets::whereKey($customer->ppp_user_id)->update(['status' => 'disable']);
                }
            });

            return back()->with('broadband_message', 'Customer disabled successfully.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['customer' => 'Failed to disable customer. Please try again.']);
        }
    }

    public function destroyCustomer(string $id)
    {
        abort_unless(auth()->user()?->hasRole('Super Admin') || hasAccess(['Super Admin'], ['delete-customer']), 403);

        try {
            $uniqueId = decrypt($id);
            $customer = CustomersInfo::where('customer_unique_id', $uniqueId)->with('pppUser')->firstOrFail();

            if ($customer->status === 'active') {
                return back()->withErrors(['customer' => 'Disable the customer before deleting the customer record.']);
            }

            $pppUser = $customer->pppUser;
            if ($pppUser && $pppUser->router_name) {
                app(MikrotikController::class)->removePPPSecret(
                    $uniqueId,
                    $pppUser->router_name,
                    $pppUser->username
                );
            }

            \DB::transaction(function () use ($customer, $pppUser) {
                if ($pppUser) {
                    $pppUser->delete();
                }
                $customer->delete();
            });

            return redirect()->route('broadband-customers')->with('broadband_message', 'Customer deleted successfully.');
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['customer' => 'Operation failed. Please try again.']);
        }
    }

    public function packages(Request $request)
    {
        $packages = PackageList::query()->orderBy('package')->paginate(50)->withQueryString();
        return view('xlink.broadband.packages', compact('packages'));
    }

    public function import()
    {
        return view('xlink.broadband.import', ['routers' => RouterList::orderBy('router_name')->get(), 'packages' => PackageList::orderBy('package')->get()]);
    }

    public function storePackage(Request $request)
    {
        $data = $request->validate(['package' => 'required|string|max:120', 'price' => 'required|numeric|min:0']);
        PackageList::create($data + ['status' => 'active']);
        return back()->with('broadband_message', 'Package created successfully.');
    }
}
