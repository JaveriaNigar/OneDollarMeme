@extends('layouts.app')

@section('title', 'My Drafts - OneDollarMeme')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-purple/10 p-3 rounded-3">
                    <svg class="w-8 h-8 text-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="h3 fw-black uppercase italic tracking-tight mb-0">My Drafts</h1>
                    <p class="text-muted small mb-0">Continue where you left off</p>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 d-flex align-items-start gap-3">
                <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-green-800 fw-medium mb-0">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 d-flex align-items-start gap-3">
                <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-red-800 fw-medium mb-0">{{ session('error') }}</p>
            </div>
        @endif

        <!-- Drafts List -->
        @if($drafts->count() > 0)
            <div class="row g-4">
                @foreach($drafts as $draft)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 hover-translate-y" style="transition: transform 0.3s ease; background: white;">
                            <div class="card-body p-4">
                                <!-- Campaign Image or Placeholder -->
                                <div class="rounded-3 mb-3 overflow-hidden bg-light d-flex align-items-center justify-content-center" style="height: 160px;">
                                    @if($draft->campaign_image)
                                        <img src="{{ asset('storage/' . $draft->campaign_image) }}" class="w-100 h-100" style="object-fit: cover;" alt="Campaign">
                                    @else
                                        <div class="text-center text-muted">
                                            <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="small">No Image</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Campaign Title -->
                                <h5 class="fw-bold mb-2 text-dark text-truncate">
                                    {{ $draft->campaign_title ?? 'Untitled Campaign' }}
                                </h5>

                                <!-- Company Name -->
                                <p class="text-muted small mb-3">
                                    <svg class="w-4 h-4 me-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    {{ $draft->company_name ?? 'Not specified' }}
                                </p>

                                <!-- Last Updated -->
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <svg class="w-4 h-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-muted small">
                                        Updated {{ $draft->updated_at->diffForHumans() }}
                                    </span>
                                </div>

                                <!-- Progress Indicator -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted small">Completion</span>
                                        <span class="text-purple fw-bold small">{{ $draft->completion_percentage ?? 0 }}%</span>
                                    </div>
                                    <div class="progress" style="height: 6px; background: #e9ecef; border-radius: 10px;">
                                        <div class="progress-bar bg-purple" role="progressbar" 
                                             style="width: {{ $draft->completion_percentage ?? 0 }}%; border-radius: 10px;" 
                                             aria-valuenow="{{ $draft->completion_percentage ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2">
                                    <a href="{{ route('brands.create', ['draft' => $draft->id]) }}" class="btn btn-purple-solid rounded-pill fw-bold py-2">
                                        <svg class="w-4 h-4 me-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Continue Editing
                                    </a>
                                    <button onclick="confirmDelete({{ $draft->id }})" class="btn btn-outline-danger rounded-pill fw-bold py-2">
                                        <svg class="w-4 h-4 me-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete Draft
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-5 bg-white rounded-4 border border-dashed border-purple-200">
                <div class="mb-3 text-purple opacity-50">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                </div>
                <h5 class="text-muted mb-2">No Drafts Yet</h5>
                <p class="text-muted small mb-4">Start creating your first sponsored campaign!</p>
                <a href="{{ route('brands.create') }}" class="btn btn-purple-solid rounded-pill px-5 py-3 fw-bold">
                    Create Campaign
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full d-flex align-items-center justify-content-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </div>
                <h5 class="fw-bold mb-2">Delete Draft?</h5>
                <p class="text-muted small mb-0">This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.fw-black { font-weight: 900; }
.uppercase { text-transform: uppercase; }
.italic { font-style: italic; }
.tracking-tight { letter-spacing: -1.5px; }
.rounded-4 { border-radius: 1.5rem; }
.bg-purple\/10 { background-color: rgba(111, 66, 193, 0.1); }
.text-purple { color: #6f42c1; }
.bg-purple { background-color: #6f42c1; }
.bg-green-50 { background-color: #f0fdf4; }
.bg-red-50 { background-color: #fef2f2; }
.border-green-200 { border-color: #bbf7d0; }
.border-red-200 { border-color: #fee2e2; }
.hover-translate-y { transition: transform 0.3s ease; }
.hover-translate-y:hover { transform: translateY(-5px); }
.btn-purple-solid {
    background-color: #6f42c1;
    color: white;
    border: none;
}
.btn-purple-solid:hover {
    background-color: #5b34a0;
    color: white;
}
</style>

<script>
function confirmDelete(draftId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const form = document.getElementById('deleteForm');
    form.action = `/drafts/${draftId}`;
    modal.show();
}
</script>
@endsection
