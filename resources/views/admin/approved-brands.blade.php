@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Approved Campaigns</h2>
    <a href="{{ route('admin.brands') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Brand Requests
    </a>
</div>


<div class="table-responsive">
    <table class="table table-hover align-middle shadow-sm bg-white rounded">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Company</th>
                <th>Campaign</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($campaigns as $campaign)
            <tr>
                <td>{{ $campaign->id }}</td>
                <td>{{ $campaign->company_name }}</td>
                <td>{{ $campaign->campaign_title }}</td>
                <td>
                    <span class="badge bg-success">Approved</span>
                </td>
                <td>
                    <form action="{{ route('admin.approved-brands.delete', $campaign->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Note: This will remove the campaign from approved list. Continue?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    No approved campaigns found
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($campaigns->hasPages())
<div class="mt-3">
    {{ $campaigns->links('pagination::bootstrap-5') }}
</div>
@endif
@endsection
