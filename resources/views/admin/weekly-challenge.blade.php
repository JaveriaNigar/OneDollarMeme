@extends('admin.layout')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Weekly Challenge Manager</h1>

    <div class="row">
        <!-- Challenge Stats -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Current Week ({{ $challenge->challenge_id ?? 'N/A' }})</h6>
                </div>
                <div class="card-body">
                    @if($challenge)
                        <table class="table table-sm">
                            <tr><th>Status:</th><td><span class="badge bg-{{ $challenge->status == 'active' ? 'success' : 'secondary' }}">{{ $challenge->status }}</span></td></tr>
                            <tr><th>Start:</th><td>{{ \Carbon\Carbon::parse($challenge->start_at)->format('M d H:i') }}</td></tr>
                            <tr><th>End:</th><td>{{ \Carbon\Carbon::parse($challenge->end_at)->format('M d H:i') }}</td></tr>
                            <tr><th>Prize Pool:</th><td class="fw-bold text-success">${{ number_format($challenge->getTotalPrizePoolCents()/100, 2) }}</td></tr>
                            <tr><th>Entries:</th><td>{{ $challenge->entries->count() }}</td></tr>
                        </table>
                        
                        @if($challenge->status == 'active')
                            <hr>
                            <form action="{{ route('admin.challenge.close', $challenge->id) }}" method="POST" onsubmit="return confirm('WARNING: This will close the week, PICK A WINNER, and generate payouts. This cannot be undone. Proceed?');">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="bi bi-trophy-fill me-2"></i>Close Week & Pick Winner
                                </button>
                            </form>
                        @endif
                    @else
                        <p>No active challenge.</p>
                        <form action="{{ route('admin.challenge.create') }}" method="POST">
                            @csrf
                            <button class="btn btn-primary">Start New Week</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Leaderboard Preview -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Live Leaderboard (Top 10)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Meme</th>
                                    <th>User</th>
                                    <th>Score</th>
                                    <th>Breakdown</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaderboard as $index => $meme)
                                <tr>
                                    <td>#{{ $index + 1 }}</td>
                                    <td>{{ $meme->title }}</td>
                                    <td>{{ $meme->user->name }}</td>
                                    <td class="fw-bold">{{ $meme->score }}</td>
                                    <td class="small text-muted">
                                        R: {{ $meme->reactions->count() }} | C: {{ $meme->comments->count() }} | S: {{ $meme->shareEvents->count() }}
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center">No entries yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
