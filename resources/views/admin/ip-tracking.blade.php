@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4"><i class="bi bi-globe me-2"></i>IP Tracking & Device Monitoring</h1>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card card-stat bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total IP Records</h5>
                    <h2 class="mb-0">{{ $ipRecords->total() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Suspicious IPs (2+ users)</h5>
                    <h2 class="mb-0">{{ $suspiciousCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stat bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Unique IPs Today</h5>
                    <h2 class="mb-0">{{ \App\Models\IpAddress::whereDate('last_login_at', today())->distinct('ip_address')->count('ip_address') }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.ip-tracking') }}" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Search by IP Address</label>
                    <input type="text" name="ip" class="form-control" placeholder="192.168.1.1" value="{{ $ipFilter }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                    <a href="{{ route('admin.ip-tracking') }}" class="btn btn-secondary">Clear</a>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="suspicious" value="1" class="form-check-input" id="suspiciousCheck" {{ $suspiciousOnly ? 'checked' : '' }}>
                        <label class="form-check-label" for="suspiciousCheck">
                            Show Suspicious Only
                        </label>
                    </div>
                    <button type="submit" class="btn btn-warning ms-3">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- IP Records Table -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                @if($suspiciousOnly)
                    <i class="bi bi-exclamation-triangle me-2"></i>Suspicious IP Addresses (Multiple Users)
                @elseif($ipFilter)
                    <i class="bi bi-search me-2"></i>Results for: {{ $ipFilter }}
                @else
                    <i class="bi bi-list me-2"></i>All IP Records
                @endif
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>IP Address</th>
                            <th>User</th>
                            <th>Browser</th>
                            <th>OS</th>
                            <th>Device Fingerprint</th>
                            <th>Login Count</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ipRecords as $record)
                            <tr>
                                <td>
                                    <code>{{ $record->ip_address }}</code>
                                    @php
                                        $sameIpCount = \App\Models\IpAddress::where('ip_address', $record->ip_address)->distinct('user_id')->count('user_id');
                                    @endphp
                                    @if($sameIpCount > 1)
                                        <span class="badge bg-warning ms-1">{{ $sameIpCount }} users</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->user)
                                        <div class="d-flex align-items-center">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($record->user->name) }}&background=random" 
                                                 class="rounded-circle me-2" width="32" height="32">
                                            <div>
                                                <div class="fw-bold">{{ $record->user->name }}</div>
                                                <small class="text-muted">{{ $record->user->email }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Deleted User</span>
                                    @endif
                                </td>
                                <td>{{ $record->browser_version ?? 'Unknown' }}</td>
                                <td>{{ $record->operating_system ?? 'Unknown' }}</td>
                                <td><code>{{ substr($record->device_fingerprint ?? 'N/A', 0, 16) }}...</code></td>
                                <td><span class="badge bg-secondary">{{ $record->login_count }}</span></td>
                                <td>{{ $record->last_login_at ? $record->last_login_at->diffForHumans() : 'N/A' }}</td>
                                <td>
                                    @if($record->user)
                                        <form method="POST" action="{{ route('admin.delete-user') }}" class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $record->user_id }}">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mt-2">No IP records found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bulk Delete by IP -->
    @if($suspiciousOnly || ($ipRecords->groupBy('ip_address')->filter(fn($items) => $items->unique('user_id')->count() > 1)->count() > 0))
    <div class="card mt-4 border-warning">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Bulk Delete Users by IP</h5>
        </div>
        <div class="card-body">
            <p class="text-muted mb-3">Delete all users sharing the same IP address (useful for removing multi-account abusers).</p>
            <form method="POST" action="{{ route('admin.delete-users-by-ip') }}" class="row g-3" 
                  onsubmit="return confirm('WARNING: This will permanently delete ALL users sharing this IP address. Are you sure?');">
                @csrf
                <div class="col-md-6">
                    <input type="text" name="ip_address" class="form-control" placeholder="Enter IP address to delete all users" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-trash me-1"></i> Delete All Users by IP
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Pagination -->
    <div class="mt-4">
        {{ $ipRecords->links() }}
    </div>
</div>
@endsection
