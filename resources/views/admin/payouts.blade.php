@extends('admin.layout')

@section('content')
    <h1 class="h3 mb-4 text-gray-800">Payout Management</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Winner Payouts</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Challenge</th>
                            <th>Winner</th>
                            <th>Meme</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Admin Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payouts as $payout)
                        <tr>
                            <td>{{ $payout->challenge_id }}</td>
                            <td>{{ $payout->user->name }}<br><small>{{ $payout->user->email }}</small></td>
                            <td><a href="{{ route('memes.show', $payout->meme_id) }}" target="_blank">View Meme</a></td>
                            <td class="text-success fw-bold">${{ number_format($payout->winner_payout_cents/100, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $payout->status == 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($payout->status) }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('admin.payout.update', $payout->id) }}" method="POST" class="d-flex">
                                    @csrf
                                    <input type="text" name="notes" class="form-control form-control-sm me-2" value="{{ $payout->notes ?? '' }}" placeholder="Tx ID / Notes">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                                </form>
                            </td>
                            <td>
                                @if($payout->status != 'paid')
                                <form action="{{ route('admin.payout.markPaid', $payout->id) }}" method="POST" onsubmit="return confirm('Confirm payment sent?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Mark Paid</button>
                                </form>
                                @else
                                <span class="text-muted"><i class="bi bi-check-circle-fill text-success"></i> Paid</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center">No payouts found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $payouts->links() }}
            </div>
        </div>
    </div>
@endsection
