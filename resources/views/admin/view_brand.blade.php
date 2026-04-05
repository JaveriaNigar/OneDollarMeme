@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Brand Request Details</h2>
    <a href="{{ route('admin.brands') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Requests
    </a>
</div>

<div class="row g-4">
    <!-- Basic Information -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="card-title mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted fw-normal">Company Name</dt>
                    <dd class="col-sm-7 fw-medium">{{ $request->company_name }}</dd>

                    <dt class="col-sm-5 text-muted fw-normal">Contact Email</dt>
                    <dd class="col-sm-7">{{ $request->contact_email }}</dd>

                    <dt class="col-sm-5 text-muted fw-normal">Website</dt>
                    <dd class="col-sm-7">
                        @if($request->website)
                            <a href="{{ $request->website }}" target="_blank" class="text-decoration-none">{{ $request->website }}</a>
                        @else
                            N/A
                        @endif
                    </dd>

                    <dt class="col-sm-5 text-muted fw-normal">Product Category</dt>
                    <dd class="col-sm-7">{{ $request->product_category ?? 'N/A' }}</dd>

                    <dt class="col-sm-5 text-muted fw-normal">Requested By</dt>
                    <dd class="col-sm-7">{{ $request->user->name ?? 'N/A' }}</dd>

                    <dt class="col-sm-5 text-muted fw-normal">Status</dt>
                    <dd class="col-sm-7">
                        <span class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'pending' ? 'warning text-dark' : 'danger') }}">
                            {{ ucfirst($request->status) }}
                        </span>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Campaign Details -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100 border-0">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="card-title mb-0">Campaign Details</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted fw-normal">Campaign Title</dt>
                    <dd class="col-sm-7 fw-medium">{{ $request->campaign_title }}</dd>

                    <dt class="col-sm-5 text-muted fw-normal">Campaign Goal</dt>
                    <dd class="col-sm-7">{{ $request->campaign_goal ?? 'N/A' }}</dd>

                    <dt class="col-sm-5 text-muted fw-normal">Prize</dt>
                    <dd class="col-sm-7">{{ $request->prize_type ?? 'N/A' }} - ${{ number_format($request->prize_amount ?? 0, 2) }}</dd>

                    <dt class="col-sm-5 text-muted fw-normal">Audience Location</dt>
                    <dd class="col-sm-7">{{ $request->audience_location ?? 'N/A' }}</dd>

                    <dt class="col-sm-5 text-muted fw-normal">Duration</dt>
                    <dd class="col-sm-7">
                        {{ $request->start_date ? $request->start_date->format('M d, Y') : 'N/A' }}
                        to
                        {{ $request->end_date ? $request->end_date->format('M d, Y') : 'N/A' }}
                    </dd>
                    
                    <dt class="col-sm-5 text-muted fw-normal">Theme Color</dt>
                    <dd class="col-sm-7">
                        @if($request->theme_color)
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded border" style="width: 30px; height: 30px; background-color: {{ $request->theme_color }};"></div>
                                <span class="font-monospace">{{ $request->theme_color }}</span>
                            </div>
                        @else
                            <span class="text-muted">Default (Purple)</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Descriptions and Content -->
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title border-bottom pb-2 mb-3">Product Description</h5>
                <p class="text-muted {{ !$request->product_content ? 'fst-italic' : '' }} mb-4" style="white-space: pre-wrap;">{{ $request->product_content ?: 'No product description provided' }}</p>

                @if($request->dos)
                <h5 class="card-title border-bottom pb-2 mb-3 mt-4 text-success">✓ DO's</h5>
                <p class="text-muted" style="white-space: pre-wrap;">{{ $request->dos }}</p>
                @endif

                @if($request->donts)
                <h5 class="card-title border-bottom pb-2 mb-3 mt-4 text-danger">✗ DON'T's</h5>
                <p class="text-muted" style="white-space: pre-wrap;">{{ $request->donts }}</p>
                @endif

                @if($request->rules && !$request->dos && !$request->donts)
                <h5 class="card-title border-bottom pb-2 mb-3 mt-4">Campaign Guidelines</h5>
                <p class="text-muted" style="white-space: pre-wrap;">{{ $request->rules }}</p>
                @endif
                
                @if($request->tags && is_array($request->tags) && count($request->tags) > 0)
                <h5 class="card-title border-bottom pb-2 mb-3 mt-4">Tags</h5>
                <div>
                    @foreach($request->tags as $tag)
                        <span class="badge bg-secondary me-1">{{ $tag }}</span>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Files & Media -->
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="card-title mb-0">Files & Media</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    @if($request->brand_logo)
                        <div class="col-md-3">
                            <label class="form-label text-muted small">Brand Logo</label>
                            <div class="border rounded p-2 text-center bg-light">
                                <img src="{{ asset('storage/' . $request->brand_logo) }}" alt="Brand Logo" class="img-fluid rounded" style="max-height: 150px;">
                            </div>
                        </div>
                    @endif

                    @if($request->product_images && is_array($request->product_images))
                        @foreach($request->product_images as $image)
                            @if($image)
                                <div class="col-md-3">
                                    <label class="form-label text-muted small">Campaign Image</label>
                                    <div class="border rounded p-2 text-center bg-light">
                                        <img src="{{ asset('storage/' . $image) }}" alt="Campaign Image" class="img-fluid rounded" style="max-height: 150px;">
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endif
                    
                     @if($request->other_files && is_array($request->other_files))
                        @foreach($request->other_files as $file)
                            @if($file)
                            <div class="col-md-3">
                                <label class="form-label text-muted small">Other File</label>
                                <div class="border rounded p-3 text-center bg-light h-100 d-flex align-items-center justify-content-center">
                                    <a href="{{ asset('storage/' . $file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Actions -->
    <div class="col-12 mb-5">
        <div class="card shadow-sm border-0 bg-light">
            <div class="card-body d-flex flex-wrap gap-2">
                @if($request->status !== 'approved')
                <form action="{{ route('admin.brands.approve', $request) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Approve Request</button>
                </form>
                @endif
                
                @if($request->status !== 'rejected')
                <form action="{{ route('admin.brands.reject', $request) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-warning"><i class="bi bi-x-circle"></i> Reject Request</button>
                </form>
                @endif
                
                <form action="{{ route('admin.brands.delete', $request) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this completely?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i> Delete Request</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection