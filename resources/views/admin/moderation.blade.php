@extends('admin.layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0 text-gray-800">{{ $title ?? 'Moderation Queue' }}</h1>
        <span class="badge bg-primary">{{ $memes->total() }} items</span>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="m-0 font-weight-bold text-primary">
                {{ $title ?? 'Memes Pending Review' }}
            </h6>
        </div>

        <!-- Filter Form -->
        <div class="card-body border-bottom bg-light">
            <form action="{{ route('admin.moderation') }}" method="GET" class="row g-2 g-md-3 align-items-end">
                <div class="col-6 col-md-3">
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
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-bold">Type</label>
                    <select name="is_contest" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="1" {{ request('is_contest') == '1' ? 'selected' : '' }}>Contest</option>
                        <option value="0" {{ request('is_contest') == '0' ? 'selected' : '' }}>Regular</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-bold">Week</label>
                    <select name="week_id" class="form-select form-select-sm">
                        <option value="">All Weeks</option>
                        @foreach($weeks as $weekId)
                            <option value="{{ $weekId }}" {{ request('week_id') == $weekId ? 'selected' : '' }}>{{ $weekId }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <button type="submit" class="btn btn-primary btn-sm w-100 w-md-auto me-md-2"><i class="bi bi-filter"></i> Filter</button>
                    <a href="{{ route('admin.moderation') }}" class="btn btn-secondary btn-sm w-100 w-md-auto">Reset</a>
                </div>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 100px;">Image</th>
                            <th style="min-width: 150px;">Title</th>
                            <th style="min-width: 150px;">User</th>
                            <th style="min-width: 100px;">Status</th>
                            <th style="min-width: 120px;">Reports</th>
                            <th style="min-width: 200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($memes as $meme)
                        <tr>
                            <td class="text-center">
                                <img src="{{ asset($meme->image_path) }}" alt="Meme" style="height: 60px; width: auto; max-width: 100px; object-fit: contain;" class="img-thumbnail">
                            </td>
                            <td class="align-middle">{{ Str::limit($meme->title ?? 'Untitled', 50) }}</td>
                            <td class="align-middle">
                                <div>{{ $meme->user->name }}</div>
                                <small class="text-muted">{{ $meme->user->email }}</small>
                            </td>
                            <td class="align-middle">
                                <span class="badge bg-{{ $meme->status == 'reported' ? 'danger' : 'warning' }}">
                                    {{ $meme->status }}
                                </span>
                            </td>
                            <td class="align-middle">
                                @if($meme->reports->count() > 0)
                                    <button class="btn btn-sm btn-info text-white" data-bs-toggle="collapse" data-bs-target="#reports-{{ $meme->id }}">
                                        <i class="bi bi-exclamation-triangle"></i> {{ $meme->reports->count() }}
                                    </button>
                                    <div class="collapse mt-2" id="reports-{{ $meme->id }}">
                                        <div class="card card-body p-2 bg-light">
                                            <ul class="list-group list-group-flush small">
                                                @foreach($meme->reports as $report)
                                                    <li class="list-group-item bg-transparent p-1">
                                                        <strong>{{ $report->reason }}</strong>: {{ Str::limit($report->details, 30) }}
                                                        <span class="text-muted fst-italic d-block">- {{ $report->user->name }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-secondary">Manually Flagged</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div class="btn-group btn-group-sm flex-wrap" role="group">
                                    <button onclick="updateStatus({{ $meme->id }}, 'published')" class="btn btn-success" title="Approve">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button onclick="updateStatus({{ $meme->id }}, 'hidden')" class="btn btn-secondary" title="Hide">
                                        <i class="bi bi-eye-slash"></i>
                                    </button>
                                    <button onclick="updateStatus({{ $meme->id }}, 'removed')" class="btn btn-danger" title="Remove">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1"></i>
                                    <p class="mt-2">No memes currently require moderation.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($memes->hasPages())
            <div class="mt-3 d-flex justify-content-center flex-wrap gap-2">
                {{ $memes->links() }}
            </div>
            @endif
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

<style>
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    @media (max-width: 575.98px) {
        .btn-group .btn {
            padding: 0.5rem;
        }
    }
</style>
@endsection
