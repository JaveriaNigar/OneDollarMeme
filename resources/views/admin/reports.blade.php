@extends('admin.layout')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">User Reports</h1>

    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Reports</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Meme / Reason</th>
                            <th>Reporter</th>
                            <th>Details</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                        <tr>
                            <td>{{ $report->created_at->format('M d, Y') }}</td>
                            <td>
                                <strong>{{ $report->meme->title ?? 'Deleted Meme' }}</strong><br>
                                <span class="text-danger">{{ $report->reason }}</span>
                            </td>
                            <td>{{ $report->user->name ?? 'Unknown' }}</td>
                            <td>{{ $report->details }}</td>
                            <td>
                                <span class="badge bg-{{ $report->status == 'pending' ? 'warning' : ($report->status == 'resolved' ? 'success' : 'secondary') }}">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </td>
                            <td>
                                @if($report->status == 'pending')
                                <div class="btn-group">
                                    <button onclick="resolveReport({{ $report->id }}, 'resolved')" class="btn btn-success btn-sm">Resolve</button>
                                    <button onclick="resolveReport({{ $report->id }}, 'dismissed')" class="btn btn-secondary btn-sm">Dismiss</button>
                                </div>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No reports found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $reports->links() }}
            </div>
        </div>
    </div>

    <script>
        function resolveReport(reportId, action) {
            $.ajax({
                url: '/admin/reports/' + reportId + '/resolve',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    action: action
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    if (window.showToast) window.showToast('Report updated successfully', 'success');
                }
            });
        }
    </script>
@endsection
