@extends('admin.layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <span class="text-muted small">{{ now()->format('M d, Y') }}</span>
    </div>

    <!-- Stats Row 1 -->
    <div class="row mb-4">
        <!-- Total Memes -->
        <div class="col-12 col-sm-6 col-xl-3 mb-4">
            <div class="card card-stat border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Memes Used</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_memes'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-images fa-2x text-gray-300 fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contest Entries -->
        <div class="col-12 col-sm-6 col-xl-3 mb-4">
            <div class="card card-stat border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Contest Entries</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['contest_memes'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-trophy fa-2x text-gray-300 fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Reports -->
        <div class="col-12 col-sm-6 col-xl-3 mb-4">
            <div class="card card-stat border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Reports</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_reports'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-flag fa-2x text-gray-300 fs-1"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="{{ route('admin.reports') }}" class="text-xs text-warning text-decoration-none">View Details <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Prize Pool -->
        <div class="col-12 col-sm-6 col-xl-3 mb-4">
            <div class="card card-stat border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Active Prize Pool</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format(($stats['active_challenge']->prize_pool_cents ?? 0) / 100, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-currency-dollar fa-2x text-gray-300 fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row 2 - Security -->
    <div class="row mb-4">
        <!-- Suspicious IPs -->
        <div class="col-12 col-sm-6 col-xl-3 mb-4">
            <div class="card card-stat border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Suspicious IPs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['suspicious_ips'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-geo-alt fa-2x text-gray-300 fs-1"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="{{ route('admin.ip-tracking', ['suspicious' => 1]) }}" class="text-xs text-danger text-decoration-none">View Details <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Flagged Engagements -->
        <div class="col-12 col-sm-6 col-xl-3 mb-4">
            <div class="card card-stat border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Flagged Engagements</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['flagged_engagements'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-shield-exclamation fa-2x text-gray-300 fs-1"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="{{ route('admin.engagement-audit', ['filter' => 'flagged']) }}" class="text-xs text-warning text-decoration-none">View Details <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Challenge Info -->
    <div class="row">
        <div class="col-12 col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="m-0 font-weight-bold text-primary">Active Weekly Challenge</h6>
                    @if($stats['active_challenge'])
                        <span class="badge bg-success">{{ $stats['active_challenge']->status }}</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($stats['active_challenge'])
                        <div class="row g-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong class="text-muted">Challenge ID:</strong></p>
                                <p class="fs-5">{{ $stats['active_challenge']->challenge_id }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong class="text-muted">Total Entries:</strong></p>
                                <p class="fs-5">{{ $stats['active_challenge']->entries->count() }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong class="text-muted">Start Date:</strong></p>
                                <p class="fs-5">{{ \Carbon\Carbon::parse($stats['active_challenge']->start_at)->format('M d, Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong class="text-muted">End Date:</strong></p>
                                <p class="fs-5">{{ \Carbon\Carbon::parse($stats['active_challenge']->end_at)->format('M d, Y') }}</p>
                            </div>
                            <div class="col-12">
                                <p class="mb-1"><strong class="text-muted">Prize Pool:</strong></p>
                                <p class="fs-4 text-success fw-bold">${{ number_format(($stats['active_challenge']->prize_pool_cents ?? 0) / 100, 2) }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">No active challenge found.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-12 col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.moderation') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-exclamation-diamond me-2"></i>Review Content Queue
                        </a>
                        <a href="{{ route('admin.brands') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-briefcase me-2"></i>Review Brand Requests
                        </a>
                        <a href="{{ route('admin.challenge') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-trophy me-2"></i>Manage Challenge
                        </a>
                        <a href="{{ route('admin.payouts') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-cash-stack me-2"></i>Process Payouts
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
    .border-left-primary { border-left: 4px solid #4e73df; }
    .border-left-success { border-left: 4px solid #1cc88a; }
    .border-left-info { border-left: 4px solid #36b9cc; }
    .border-left-warning { border-left: 4px solid #f6c23e; }
    .border-left-danger { border-left: 4px solid #e74a3b; }
    
    .text-primary { color: #4e73df !important; }
    .text-success { color: #1cc88a !important; }
    .text-info { color: #36b9cc !important; }
    .text-warning { color: #f6c23e !important; }
    .text-danger { color: #e74a3b !important; }
    
    .text-gray-300 { color: #dddfeb !important; }
    .text-gray-800 { color: #5a5c69 !important; }
    
    .font-weight-bold { font-weight: 700 !important; }
    .text-xs { font-size: 0.7rem; }
    .text-uppercase { text-transform: uppercase; }
    .letter-spacing { letter-spacing: 0.05em; }
    
    .card-footer {
        background-color: #f8f9fc;
        border-top: 1px solid #e3e6f0;
    }
</style>
