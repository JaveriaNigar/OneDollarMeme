@extends('admin.layout')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">{{ $title ?? 'Moderation Queue' }}</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                {{ $title ?? 'Memes Pending Review' }}
            </h6>
        </div>
        
        <!-- Filter Form -->
        <div class="card-body border-bottom bg-light">
            <form action="{{ route('admin.moderation') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="reported" {{ request('status') == 'reported' ? 'selected' : '' }}>Reported</option>
                        <option value="hidden" {{ request('status') == 'hidden' ? 'selected' : '' }}>Hidden</option>
                        <option value="removed" {{ request('status') == 'removed' ? 'selected' : '' }}>Removed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Type</label>
                    <select name="is_contest" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="1" {{ request('is_contest') == '1' ? 'selected' : '' }}>Contest</option>
                        <option value="0" {{ request('is_contest') == '0' ? 'selected' : '' }}>Regular</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Week</label>
                    <select name="week_id" class="form-select form-select-sm">
                        <option value="">All Weeks</option>
                        @foreach($weeks as $weekId)
                            <option value="{{ $weekId }}" {{ request('week_id') == $weekId ? 'selected' : '' }}>{{ $weekId }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-sm me-2"><i class="bi bi-filter"></i> Filter</button>
                    <a href="{{ route('admin.moderation') }}" class="btn btn-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Reports</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($memes as $meme)
                        <tr>
                            <td class="text-center">
                                <img src="{{ asset($meme->image_path) }}" alt="Meme" style="height: 80px; width: auto; max-width: 150px; object-fit: contain;">
                            </td>
                            <td>{{ $meme->title ?? 'Untitled' }}</td>
                            <td>{{ $meme->user->name }}<br><small class="text-muted">{{ $meme->user->email }}</small></td>
                            <td>
                                <span class="badge bg-{{ $meme->status == 'reported' ? 'danger' : 'warning' }}">
                                    {{ $meme->status }}
                                </span>
                            </td>
                            <td>
                                @if($meme->reports->count() > 0)
                                    <button class="btn btn-sm btn-info" data-bs-toggle="collapse" data-bs-target="#reports-{{ $meme->id }}">
                                        View {{ $meme->reports->count() }} Reports
                                    </button>
                                    <div class="collapse mt-2" id="reports-{{ $meme->id }}">
                                        <ul class="list-group list-group-flush small">
                                            @foreach($meme->reports as $report)
                                                <li class="list-group-item bg-light p-1">
                                                    <strong>{{ $report->reason }}</strong>: {{ $report->details }}
                                                    <span class="text-muted fst-italic">- {{ $report->user->name }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <span class="text-muted">Manually Flagged</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button onclick="updateStatus({{ $meme->id }}, 'published')" class="btn btn-success btn-sm">Approve</button>
                                    <button onclick="updateStatus({{ $meme->id }}, 'hidden')" class="btn btn-secondary btn-sm">Hide</button>
                                    <button onclick="updateStatus({{ $meme->id }}, 'removed')" class="btn btn-danger btn-sm">Remove</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">No memes currently require moderation.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex justify-content-center">
                {{ $memes->links('vendor.pagination.admin-simple') }}
            </div>
        </div>
    </div>

    <script>
        function updateStatus(memeId, status) {
            if(!confirm('Are you sure you want to change status to ' + status + '?')) return;

            $.ajax({
                url: '/admin/memes/' + memeId + '/status',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error updating status');
                }
            });
        }
    </script>
@endsection
