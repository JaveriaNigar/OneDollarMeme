@extends('admin.layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Brand Memes</h1>
        <div class="small text-muted">Showing all memes generated for brand campaigns</div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Brand Submissions</h6>
            <span class="badge bg-primary">{{ $memes->total() }} Total</span>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover border" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 150px;">Meme</th>
                            <th>Caption</th>
                            <th>Brand Campaign</th>
                            <th>Creator</th>
                            <th>Status</th>
                            <th>Generated Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($memes as $meme)
                        <tr>
                            <td>
                                @if($meme->image_path)
                                    <div class="rounded border p-1 bg-light text-center">
                                        <img src="{{ asset('storage/' . $meme->image_path) }}" alt="Meme" class="img-fluid rounded" style="max-height: 80px; object-fit: contain;">
                                    </div>
                                @else
                                    <div class="bg-light rounded p-3 text-center text-muted small">
                                        <i class="bi bi-fonts fs-4 d-block mb-1"></i>
                                        Text Only
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold fs-6">{{ $meme->title ?? 'Untitled' }}</div>
                                @if($meme->template)
                                    <span class="badge bg-secondary opacity-75" style="font-size: 0.7rem;">Template: {{ $meme->template }}</span>
                                @endif
                            </td>
                            <td>
                                @if($meme->brand)
                                    <div class="d-flex align-items-center gap-2">
                                        @if($meme->brand->logo)
                                            <img src="{{ asset('storage/' . $meme->brand->logo) }}" width="24" height="24" class="rounded-circle border">
                                        @endif
                                        <span class="fw-bold">{{ $meme->brand->company_name }}</span>
                                    </div>
                                    <div class="small text-muted mt-1">{{ Str::limit($meme->brand->campaign_title, 30) }}</div>
                                @else
                                    <span class="text-danger">Brand Missing</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $meme->user->profile_photo_url }}" width="24" height="24" class="rounded-circle border">
                                    <span>{{ $meme->user->name }}</span>
                                </div>
                                <div class="small text-muted">{{ $meme->user->email }}</div>
                            </td>
                            <td>
                                @php
                                    $statusColor = match($meme->status) {
                                        'published', 'active' => 'success',
                                        'removed', 'hidden', 'rejected' => 'danger',
                                        default => 'warning'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">
                                    {{ ucfirst($meme->status) }}
                                </span>
                                @if($meme->score > 0)
                                    <div class="small mt-1 text-primary"><i class="bi bi-graph-up me-1"></i>Score: {{ $meme->score }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="small fw-bold">{{ $meme->created_at->format('M d, Y') }}</div>
                                <div class="text-muted small">{{ $meme->created_at->format('h:i A') }}</div>
                            </td>
                            <td>
                                <div class="btn-group shadow-sm">
                                    @if($meme->status !== 'published' && $meme->status !== 'active')
                                        <button onclick="updateStatus({{ $meme->id }}, 'published')" class="btn btn-outline-success btn-sm" title="Approve">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    @endif
                                    @if($meme->status !== 'removed')
                                        <button onclick="updateStatus({{ $meme->id }}, 'removed')" class="btn btn-outline-danger btn-sm" title="Remove">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('brands.show', $meme->brand_id) }}" target="_blank" class="btn btn-outline-primary btn-sm" title="View Campaign">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="bi bi-images text-muted" style="font-size: 3rem;"></i>
                                <p class="mt-3 text-muted">No memes have been generated for brands yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($memes->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $memes->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        function updateStatus(memeId, status) {
            if(!confirm('Are you sure you want to change this meme status to ' + status + '?')) return;

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
                    if (window.showToast) window.showToast('Error updating status: ' + (xhr.responseJSON?.message || 'Server error'), 'error');
                }
            });
        }
    </script>
@endsection
