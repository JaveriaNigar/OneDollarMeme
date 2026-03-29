@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="display-4 fw-bold text-dark mb-1">Your Campaign Drafts</h1>
                    <p class="text-muted mb-0">Manage and continue your unpublished brand campaigns</p>
                </div>
                <a href="{{ route('brands.create') }}" class="btn btn-purple rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-plus-lg me-2"></i>New Campaign
                </a>
            </div>

            @if($drafts->isEmpty())
                <div class="card border-0 shadow-sm rounded-4 py-5 text-center">
                    <div class="card-body py-5">
                        <div class="mb-4">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold text-dark">No drafts found</h3>
                        <p class="text-muted mb-4">You haven't saved any campaign drafts yet.</p>
                        <a href="{{ route('brands.create') }}" class="btn btn-purple rounded-pill px-5 fw-bold shadow-sm">
                            Create First Campaign
                        </a>
                    </div>
                </div>
            @else
                <div class="row g-4">
                    @foreach($drafts as $draft)
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden hover-lift transition-all">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="bg-purple-light text-purple rounded-pill px-3 py-1 small fw-bold">
                                            Last saved: {{ $draft->updated_at->diffForHumans() }}
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-link text-muted p-0" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3">
                                                <li>
                                                    <button type="button"
                                                        class="dropdown-item text-danger d-flex align-items-center gap-2"
                                                        onclick="showDeleteConfirm({{ $draft->id }}, '{{ addslashes($draft->campaign_title ?: 'Untitled Campaign') }}')">
                                                        <i class="bi bi-trash"></i> Delete Draft
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <h4 class="fw-bold text-dark mb-2">
                                        {{ $draft->campaign_title ?: 'Untitled Campaign' }}
                                    </h4>

                                    <p class="text-muted small mb-4 line-clamp-2" style="min-height: 3rem;">
                                        {{ $draft->product_content ?: 'No description provided yet...' }}
                                    </p>

                                    <div class="d-flex align-items-center justify-content-between pt-3 border-top border-light">
                                        <div class="text-muted small d-flex align-items-center gap-2">
                                            <i class="bi bi-building"></i>
                                            {{ $draft->company_name ?: 'Unknown Brand' }}
                                        </div>
                                        <a href="{{ route('brands.create', ['draft' => $draft->id]) }}" class="btn btn-purple-outline rounded-pill px-4 fw-bold btn-sm">
                                            Resume <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Delete Confirmation Card --}}
<div id="deleteConfirmOverlay" style="
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    z-index: 9999;
    align-items: center;
    justify-content: center;
">
    <div style="
        background: white;
        border-radius: 20px;
        padding: 2rem 2rem 1.5rem;
        max-width: 380px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        text-align: center;
        animation: popIn 0.2s ease;
    ">
        <div style="
            width: 64px; height: 64px;
            background: #fef2f2;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        ">
            <i class="bi bi-trash3-fill" style="font-size: 1.6rem; color: #ef4444;"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">Delete Draft?</h5>
        <p class="text-muted small mb-0" id="deleteConfirmTitle" style="font-size: 0.9rem;"></p>
        <p class="text-muted small mt-1 mb-4" style="font-size: 0.8rem;">This action cannot be undone.</p>

        <div class="d-flex gap-2 justify-content-center">
            <button
                onclick="hideDeleteConfirm()"
                style="
                    flex: 1;
                    padding: 0.6rem 1.2rem;
                    border-radius: 50px;
                    border: 2px solid #e5e7eb;
                    background: white;
                    color: #374151;
                    font-weight: 600;
                    font-size: 0.9rem;
                    cursor: pointer;
                    transition: all 0.2s;
                "
                onmouseover="this.style.borderColor='#9ca3af'"
                onmouseout="this.style.borderColor='#e5e7eb'"
            >
                Cancel
            </button>
            <form id="deleteConfirmForm" method="POST" style="flex: 1; margin: 0;">
                @csrf
                @method('DELETE')
                <button type="submit" style="
                    width: 100%;
                    padding: 0.6rem 1.2rem;
                    border-radius: 50px;
                    border: none;
                    background: #ef4444;
                    color: white;
                    font-weight: 600;
                    font-size: 0.9rem;
                    cursor: pointer;
                    transition: all 0.2s;
                "
                onmouseover="this.style.background='#dc2626'"
                onmouseout="this.style.background='#ef4444'"
                >
                    <i class="bi bi-trash me-1"></i> Yes, Delete
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes popIn {
        from { transform: scale(0.85); opacity: 0; }
        to   { transform: scale(1);    opacity: 1; }
    }
    .bg-purple-light { background-color: rgba(124, 58, 237, 0.1); }
    .text-purple { color: #7c3aed; }
    .btn-purple {
        background-color: #7c3aed;
        color: white;
        border: none;
    }
    .btn-purple:hover {
        background-color: #6d28d9;
        color: white;
    }
    .btn-purple-outline {
        color: #7c3aed;
        border: 2px solid #7c3aed;
        background: transparent;
    }
    .btn-purple-outline:hover {
        background-color: #7c3aed;
        color: white;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<script>
    function showDeleteConfirm(draftId, draftTitle) {
        const overlay = document.getElementById('deleteConfirmOverlay');
        const form    = document.getElementById('deleteConfirmForm');
        const titleEl = document.getElementById('deleteConfirmTitle');

        form.action = '/drafts/' + draftId;
        titleEl.textContent = '"' + draftTitle + '"';

        overlay.style.display = 'flex';
    }

    function hideDeleteConfirm() {
        document.getElementById('deleteConfirmOverlay').style.display = 'none';
    }

    // Close on overlay backdrop click
    document.getElementById('deleteConfirmOverlay').addEventListener('click', function(e) {
        if (e.target === this) hideDeleteConfirm();
    });
</script>
@endsection
