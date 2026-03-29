@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Brand Request Management</h2>
</div>

<div class="mb-3 d-flex justify-content-between">
    <div>
        <a href="{{ route('admin.brands') }}" class="btn btn-sm {{ !isset($status) || $status === '' ? 'btn-primary' : 'btn-outline-primary' }}">All Requests</a>
        <a href="{{ route('admin.brands') }}?status=pending" class="btn btn-sm {{ isset($status) && $status === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">Pending</a>
        <a href="{{ route('admin.brands') }}?status=approved" class="btn btn-sm {{ isset($status) && $status === 'approved' ? 'btn-success' : 'btn-outline-success' }}">Approved</a>
        <a href="{{ route('admin.brands') }}?status=rejected" class="btn btn-sm {{ isset($status) && $status === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">Rejected</a>
    </div>
    <a href="{{ route('admin.approved-brands') }}" class="btn btn-sm btn-dark">Manage Approved Brands</a>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Company</th>
                <th>User</th>
                <th>Campaign</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($brandRequests as $request)
            <tr>
                <td>{{ $request->id }}</td>
                <td>{{ \Illuminate\Support\Str::limit($request->company_name, 20) }}</td>
                <td>{{ $request->user->name ?? 'N/A' }}</td>
                <td>{{ \Illuminate\Support\Str::limit($request->campaign_title, 20) }}</td>
                <td>
                    <span class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'pending' ? 'warning text-dark' : 'danger') }}">
                        {{ ucfirst($request->status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.brands.view', $request) }}" class="btn btn-sm btn-info text-white">View</a>
                    @if($request->status !== 'approved')
                        <form action="{{ route('admin.brands.approve', $request) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                        </form>
                        <form action="{{ route('admin.brands.reject', $request) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                        </form>
                    @endif
                    <form action="{{ route('admin.brands.delete', $request) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this brand request?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">No brand requests found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    {{ $brandRequests->links('pagination::bootstrap-5') }}
</div>
@endsection