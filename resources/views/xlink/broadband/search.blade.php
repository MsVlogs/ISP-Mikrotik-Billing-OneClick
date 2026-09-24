<x-app-layout>
@push('styles')<link rel="stylesheet" href="{{ asset('xlink-broadband/customers.css') }}">@endpush
<div class="container-fluid py-2">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div><span class="text-uppercase small text-muted fw-semibold">Broadband</span><h3 class="mb-0">Customer Search</h3><small class="text-muted">Search across customer identity, contact, address, PPPoE/IP, router, package and NID fields.</small></div>
        <a class="btn btn-primary" href="{{ route('customer-add') }}">Add Customer</a>
    </div>
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <form class="row g-2">
                <div class="col-12 col-lg-9"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="CID, name, mobile, username, IP, NID, address, caller ID/MAC, router or package"></div>
                <div class="col-12 col-lg-3 d-flex gap-2"><button class="btn btn-primary flex-grow-1">Search</button><a class="btn btn-outline-secondary" href="{{ url()->current() }}">Clear</a></div>
            </form>
        </div>
    </div>
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Customer ID</th><th>Customer</th><th>Username / IP</th><th>Mobile</th><th>Package</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                @forelse($customers as $c)
                    @php $customerId = encrypt($c->customer_unique_id); @endphp
                    <tr>
                        <td><a class="fw-semibold text-decoration-none" href="{{ route('customers.show', $customerId) }}">{{ $c->customer_unique_id }}</a></td>
                        <td><strong>{{ $c->customer_name ?: 'Unnamed' }}</strong><div class="small text-muted">{{ $c->email ?: '—' }}</div></td>
                        <td>{{ $c->pppUser->username ?? $c->pppUser->ppp_remote_ip ?? '—' }}</td>
                        <td>{{ $c->mobile ?: '—' }}</td>
                        <td>{{ $c->package->package ?? $c->pppUser->package_name ?? '—' }}</td>
                        <td><span class="badge text-bg-{{ $c->status==='active'?'success':'secondary' }}">{{ ucfirst($c->status ?: 'unknown') }}</span></td>
                        <td class="text-nowrap">
                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('customers.show', $customerId) }}"><i class="bi bi-eye"></i> Select</a>
                            @if(auth()->user()->hasRole('Super Admin') || hasAccess(['Super Admin'], ['edit-customer']))
                                <a class="btn btn-primary btn-sm" href="{{ route('customers.edit', $customerId) }}"><i class="bi bi-pencil-square"></i></a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">No customer found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $customers->links() }}</div>
    </div>
</div>
</x-app-layout>
