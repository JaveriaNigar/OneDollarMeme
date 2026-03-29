@extends('admin.layout')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

    <div class="row mb-4">
        <!-- Total Memes -->
        <div class="col-xl-3 col-md-6 mb-4">
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
        <div class="col-xl-3 col-md-6 mb-4">
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
        <div class="col-xl-3 col-md-6 mb-4">
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
            </div>
        </div>

        <!-- Prize Pool -->
        <div class="col-xl-3 col-md-6 mb-4">
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

    <!-- Security Stats -->
    <div class="row mb-4">
        <!-- Suspicious IPs -->
        <div class="col-xl-3 col-md-6 mb-4">
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
        <div class="col-xl-3 col-md-6 mb-4">
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
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Active Weekly Challenge</h6>
                </div>
                <div class="card-body">
                    @if($stats['active_challenge'])
                        <p><strong>ID:</strong> {{ $stats['active_challenge']->challenge_id }}</p>
                        <p><strong>Status:</strong> <span class="badge bg-success">{{ $stats['active_challenge']->status }}</span></p>
                        <p><strong>Entries:</strong> {{ $stats['active_challenge']->entries->count() }}</p>
                        <p><strong>Starts:</strong> {{ \Carbon\Carbon::parse($stats['active_challenge']->start_at)->format('M d, Y') }}</p>
                        <p><strong>Ends:</strong> {{ \Carbon\Carbon::parse($stats['active_challenge']->end_at)->format('M d, Y') }}</p>
                    @else
                        <p>No active challenge found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
