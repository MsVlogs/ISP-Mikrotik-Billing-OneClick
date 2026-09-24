<x-app-layout>
@push('styles')
<link rel="stylesheet" href="{{ asset('xlink-broadband/customers.css') }}">
<link rel="stylesheet" href="{{ asset('xlink-broadband/ui-polish-20260826.css') }}">
<style>
.broadband-customer-table th{white-space:nowrap;font-size:.78rem;text-transform:uppercase;letter-spacing:.03em}
.broadband-customer-table td{vertical-align:middle}
.customer-actions{min-width:145px}.customer-actions form{display:inline}
.customer-actions .btn{margin:.12rem}
.customer-meta{font-size:.78rem}.filter-card .form-label{font-size:.78rem;font-weight:600;color:#6c757d}
</style>
@endpush

@php
    $title = match($mode) {
        'due' => 'Due Customers',
        'inactive' => 'Inactive Customers',
        'unverified' => 'Unverified Customers',
        'online' => 'Online Customers',
        default => 'Customer List',
    };
@endphp

<div class="container-fluid py-2">
    @if(session('broadband_message'))
        <div class="alert alert-success py-2">{{ session('broadband_message') }}</div>
    @endif
    @if($errors->has('customer'))
        <div class="alert alert-danger py-2">{{ $errors->first('customer') }}</div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <span class="text-uppercase small text-muted fw-semibold">Broadband</span>
            <h3 class="mb-0">{{ $title }}</h3>
            <small class="text-muted">Search and manage broadband customers without changing router configuration unless an action is explicitly requested.</small>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="{{ route('customer-add') }}"><i class="bi bi-person-plus me-1"></i>Add Customer</a>
            <a class="btn btn-primary" href="{{ route('broadband-packages') }}"><i class="bi bi-speedometer2 me-1"></i>Packages</a>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-3 filter-card">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-12 col-lg-5">
                    <label class="form-label mb-1">Search customer</label>
                    <input class="form-control" name="q" value="{{ request('q') }}" placeholder="CID, name, mobile, username, IP, NID, address, MAC/caller ID, router or package">
                </div>
                <div class="col-6 col-lg-2">
                    <label class="form-label mb-1">Router</label>
                    <select class="form-select" name="router_id">
                        <option value="">All Routers</option>
                        @foreach($routers as $r)
                            <option value="{{ $r->router_name }}" @selected(request('router_id') === $r->router_name)>{{ $r->router_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-lg-2">
                    <label class="form-label mb-1">Rows</label>
                    <select class="form-select" name="rows">
                        @foreach([50,100,250,500] as $size)
                            <option value="{{ $size }}" @selected((int)request('rows',50) === $size)>{{ $size }} rows</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-lg-3 d-flex align-items-end gap-2">
                    <button class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i>Search</button>
                    <a class="btn btn-outline-secondary" href="{{ url()->current() }}">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <strong>Customer List</strong>
            <span class="text-muted small">{{ number_format($customers->total()) }} records</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 broadband-customer-table">
                <thead>
                    <tr>
                        <th>CID</th><th>Customer</th><th>Connection</th><th>Mobile</th><th>Package</th>
                        <th>Router</th><th>Billing</th><th>Address / Area</th><th>Status</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($customers as $c)
                    @php
                        $customerId = encrypt($c->customer_unique_id);
                        $address = $c->customerAddress->map(fn($a) => implode(', ', array_filter([$a->input_type_text, $a->input_type_dropdown, $a->input_type_textarea])))->filter()->implode(', ');
                        $connection = $c->pppUser->username ?? $c->pppUser->ppp_remote_ip ?? '—';
                        $status = $c->status ?: 'unknown';
                        $statusClass = in_array($status, ['active','online']) ? 'success' : (in_array($status, ['inactive','disable']) ? 'secondary' : 'warning');
                    @endphp
                    <tr>
                        <td><a class="fw-semibold text-decoration-none" href="{{ route('customers.show', $customerId) }}">{{ $c->customer_unique_id }}</a></td>
                        <td>
                            <strong>{{ $c->customer_name ?: 'Unnamed' }}</strong>
                            <div class="customer-meta text-muted">{{ $c->email ?: 'No email' }}</div>
                        </td>
                        <td>
                            <span class="badge text-bg-{{ $c->pppUser?->service === 'static' ? 'info' : 'secondary' }}">{{ strtoupper($c->pppUser?->service ?: 'N/A') }}</span>
                            <div class="customer-meta">{{ $connection }}</div>
                        </td>
                        <td>{{ $c->mobile ?: '—' }}</td>
                        <td>{{ $c->package->package ?? $c->pppUser->package_name ?? '—' }}</td>
                        <td>{{ $c->pppUser->router_name ?? '—' }}</td>
                        <td>
                            <div>Bill: {{ number_format($c->billing?->total_amount ?? 0, 2) }} ৳</div>
                            <div class="{{ ($c->billing?->due_amount ?? 0) > 0 ? 'text-danger' : 'text-success' }}">Due: {{ number_format($c->billing?->due_amount ?? 0, 2) }} ৳</div>
                        </td>
                        <td>
                            <div>{{ $address ?: ($c->address ?: '—') }}</div>
                            @if($c->pppUser?->router_name)<div class="customer-meta text-muted">{{ $c->pppUser->router_name }}</div>@endif
                        </td>
                        <td><span class="badge text-bg-{{ $statusClass }}">{{ ucfirst($status) }}</span></td>
                        <td class="customer-actions">
                            <a class="btn btn-outline-secondary btn-sm" title="View" href="{{ route('customers.show', $customerId) }}"><i class="bi bi-eye"></i></a>
                            @if(auth()->user()->hasRole('Super Admin') || hasAccess(['Super Admin'], ['edit-customer']))
                                <a class="btn btn-primary btn-sm" title="Edit" href="{{ route('customers.edit', $customerId) }}"><i class="bi bi-pencil-square"></i></a>
                            @endif
                            @if($status === 'active' && (auth()->user()->hasRole('Super Admin') || hasAccess(['Super Admin'], ['disable-customer'])))
                                <form method="POST" action="{{ route('broadband-customer-disable', $customerId) }}" onsubmit="return confirm('Disable this customer?')">
                                    @csrf
                                    <button class="btn btn-warning btn-sm" title="Disable"><i class="bi bi-pause-circle"></i></button>
                                </form>
                            @endif
                            @if($status !== 'active' && (auth()->user()->hasRole('Super Admin') || hasAccess(['Super Admin'], ['delete-customer'])))
                                <form method="POST" action="{{ route('broadband-customer-destroy', $customerId) }}" onsubmit="return confirm('Delete this customer record? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center py-5 text-muted">No customers found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span class="text-muted small">Showing {{ $customers->firstItem() ?: 0 }}–{{ $customers->lastItem() ?: 0 }} of {{ $customers->total() }}</span>
            {{ $customers->links() }}
        </div>
    </div>
</div>
</x-app-layout>
