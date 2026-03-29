@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4"><i class="bi bi-shield-check me-2"></i>Engagement Audit System</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card card-stat bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Engagements</h5>
                    <h2 class="mb-0">{{ $stats['total'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Flagged as Fraud</h5>
                    <h2 class="mb-0">{{ $stats['flagged'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Suspicious</h5>
                    <h2 class="mb-0">{{ $stats['suspicious'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Today's Engagements</h5>
                    <h2 class="mb-0">{{ $stats['today'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Suspicious IPs -->
    @if($suspiciousIps->count() > 0)
    <div class="card mb-4 border-danger">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Suspicious IP Addresses</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>IP Address</th>
                            <th>Total Engagements</th>
                            <th>Unique Users</th>
                            <th>Flagged Count</th>
                            <th>Risk Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suspiciousIps as $ip)
                            <tr>
                                <td><code>{{ $ip->ip_address }}</code></td>
                                <td>{{ $ip->engagement_count }}</td>
                                <td>{{ $ip->user_count }}</td>
                                <td>{{ $ip->flagged_count }}</td>
                                <td>
                                    @if($ip->engagement_count >= 20 || $ip->user_count >= 5)
                                        <span class="badge bg-danger">High</span>
                                    @elseif($ip->engagement_count >= 10 || $ip->user_count >= 3)
                                        <span class="badge bg-warning">Medium</span>
                                    @else
                                        <span class="badge bg-info">Low</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.engagement-audit') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Filter</label>
                    <select name="filter" class="form-select">
                        <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>All Engagements</option>
                        <option value="flagged" {{ $filter === 'flagged' ? 'selected' : '' }}>Flagged Only</option>
                        <option value="suspicious" {{ $filter === 'suspicious' ? 'selected' : '' }}>Suspicious (Risk >= 30)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Engagement Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="like" {{ $engagementType === 'like' ? 'selected' : '' }}>Likes</option>
                        <option value="comment" {{ $engagementType === 'comment' ? 'selected' : '' }}>Comments</option>
                        <option value="share" {{ $engagementType === 'share' ? 'selected' : '' }}>Shares</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Meme ID</label>
                    <input type="number" name="meme_id" class="form-control" placeholder="Enter meme ID" value="{{ $memeId }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.engagement-audit') }}" class="btn btn-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Cleanup Button -->
    <div class="alert alert-info d-flex justify-content-between align-items-center mb-4">
        <div>
            <i class="bi bi-info-circle me-2"></i>
            <strong>Auto-Cleanup Enabled:</strong> Flagged engagements are auto-deleted after 24 hours. Old records (30+ days) are cleaned daily.
        </div>
        <div>
            <form method="POST" action="{{ route('admin.engagement.cleanup') }}" class="d-inline"
                  onsubmit="return confirm('This will permanently delete all flagged engagements. Continue?');">
                @csrf
                <button type="submit" class="btn btn-warning me-2">
                    <i class="bi bi-trash me-1"></i> Cleanup Flagged Now
                </button>
            </form>
            <a href="{{ route('admin.ip-tracking') }}" class="btn btn-primary">
                <i class="bi bi-globe me-1"></i> IP Tracking
            </a>
        </div>
    </div>

    <!-- Engagements Table -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                @if($filter === 'flagged')
                    <i class="bi bi-flag me-2"></i>Flagged Engagements
                @elseif($filter === 'suspicious')
                    <i class="bi bi-exclamation-triangle me-2"></i>Suspicious Engagements
                @else
                    <i class="bi bi-list me-2"></i>All Engagements
                @endif
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>User</th>
                            <th>Meme</th>
                            <th>IP Address</th>
                            <th>Device Fingerprint</th>
                            <th>Risk Score</th>
                            <th>Status</th>
                            <th>Flag Reason</th>
                            <th>Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($engagements as $engagement)
                            <tr class="{{ $engagement->is_flagged ? 'table-danger' : '' }}">
                                <td>{{ $engagement->id }}</td>
                                <td>
                                    @if($engagement->engagement_type === 'like')
                                        <span class="badge bg-primary">👍 Like</span>
                                    @elseif($engagement->engagement_type === 'comment')
                                        <span class="badge bg-success">💬 Comment</span>
                                    @else
                                        <span class="badge bg-info">🔗 Share</span>
                                    @endif
                                </td>
                                <td>
                                    @if($engagement->user)
                                        <div class="d-flex align-items-center">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($engagement->user->name) }}&background=random" 
                                                 class="rounded-circle me-2" width="32" height="32">
                                            <div>
                                                <div class="fw-bold">{{ $engagement->user->name }}</div>
                                                <small class="text-muted">{{ $engagement->user->email }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Deleted</span>
                                    @endif
                                </td>
                                <td>
                                    @if($engagement->meme)
                                        <a href="{{ route('memes.show', $engagement->meme) }}" target="_blank">
                                            #{{ $engagement->meme->id }} - {{ Str::limit($engagement->meme->title, 30) }}
                                        </a>
                                    @else
                                        <span class="text-muted">Deleted</span>
                                    @endif
                                </td>
                                <td><code>{{ $engagement->ip_address }}</code></td>
                                <td><code>{{ substr($engagement->device_fingerprint ?? 'N/A', 0, 16) }}...</code></td>
                                <td>
                                    @if($engagement->risk_score >= 50)
                                        <span class="badge bg-danger">{{ $engagement->risk_score }}</span>
                                    @elseif($engagement->risk_score >= 30)
                                        <span class="badge bg-warning">{{ $engagement->risk_score }}</span>
                                    @else
                                        <span class="badge bg-success">{{ $engagement->risk_score }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($engagement->is_flagged)
                                        <span class="badge bg-danger">Flagged</span>
                                    @elseif($engagement->is_verified)
                                        <span class="badge bg-success">Verified</span>
                                    @else
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($engagement->flag_reason)
                                        <small class="text-danger">{{ Str::limit($engagement->flag_reason, 40) }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $engagement->created_at->diffForHumans() }}</td>
                                <td>
                                    @if($engagement->is_flagged)
                                        <form method="POST" action="{{ route('admin.engagement.verify', $engagement) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Verify">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.engagement.remove', $engagement) }}" class="d-inline"
                                          onsubmit="return confirm('Remove this engagement?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mt-2">No engagements found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $engagements->links() }}
    </div>
</div>
@endsection
