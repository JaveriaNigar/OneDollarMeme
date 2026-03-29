@extends('layouts.brands_app')

@section('content')
<div class="container py-5">
    <!-- Brand Header -->
    <div class="brand-header p-5 rounded-[40px] text-white mb-5 position-relative overflow-hidden" 
         style="background: linear-gradient(135deg, {{ $brand->theme_color ?? '#6f42c1' }} 0%, #000 100%); min-height: 300px;">
        
        <!-- Background Pattern -->
        <div class="position-absolute top-0 end-0 opacity-10 w-100 h-100" 
             style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;"></div>
        
        <div class="position-relative z-1 d-flex flex-column align-items-center text-center">
            @if($brand->logo)
                <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->company_name }}"
                     class="rounded-circle shadow-lg mb-4 border border-4 border-white" style="width: 120px; height: 120px; object-fit: cover;">
            @else
                <div class="rounded-circle shadow-lg mb-4 bg-white d-flex align-items-center justify-center text-dark fw-black"
                     style="width: 100px; height: 100px; font-size: 2rem;">
                    {{ substr($brand->company_name, 0, 1) }}
                </div>
            @endif

            <h1 class="display-4 fw-black uppercase italic tracking-tight mb-2">{{ $brand->company_name }}</h1>
            <p class="lead opacity-75 max-w-2xl">{{ $brand->brand_description ?? 'Participate in our brand campaign and win amazing prizes!' }}</p>
            
            @if($brand->website)
                <a href="{{ $brand->website }}" target="_blank" class="btn btn-outline-light rounded-pill px-4 py-2 mt-3 tracking-widest uppercase small fw-bold">
                    Visit Website <i class="bi bi-box-arrow-up-right ms-2"></i>
                </a>
            @endif
        </div>
    </div>

    <!-- Campaign Section -->
    <div class="row mb-5">
        <div class="col-lg-8">
            <h2 class="h3 fw-bold italic uppercase mb-4">Campaign Memes</h2>
            
            @if($memes->count() > 0)
                <div class="row g-4" id="meme-grid">
                    @foreach($memes as $meme)
                    <div class="col-md-6 col-lg-4" id="meme-{{ $meme->id }}" style="scroll-margin-top: 80px;">
                        <div class="meme-card h-100" style="
                            background: white;
                            border-radius: 20px;
                            overflow: hidden;
                            box-shadow: 0 4px 20px rgba(0,0,0,0.07);
                            border: 1px solid rgba(0,0,0,0.05);
                            transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
                            display: flex;
                            flex-direction: column;
                        " onmouseover="this.style.transform='translateY(-6px)';this.style.boxShadow='0 20px 40px rgba(0,0,0,0.12)';"
                           onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 20px rgba(0,0,0,0.07)';">

                            {{-- Image Section --}}
                            @if($meme->image_path)
                            <div class="position-relative" style="aspect-ratio: 4/3; overflow: hidden; background: #0f0f0f;">
                                <img src="{{ asset('storage/' . $meme->image_path) }}"
                                     alt="{{ $meme->title ?? 'Meme' }}"
                                     style="width:100%; height:100%; object-fit: cover; display: block; transition: transform 0.4s ease;"
                                     onmouseover="this.style.transform='scale(1.05)'"
                                     onmouseout="this.style.transform='scale(1)'">

                                {{-- Overlay Badges --}}
                                @if($loop->first)
                                    <div style="position:absolute; top:10px; left:10px; background:linear-gradient(135deg,#f59e0b,#d97706); color:white; font-size:0.7rem; font-weight:800; padding:4px 10px; border-radius:20px; text-transform:uppercase; letter-spacing:0.5px; box-shadow:0 2px 8px rgba(0,0,0,0.2);">
                                        ⭐ TOP
                                    </div>
                                @endif
                                @if($meme->score >= 20)
                                    <div style="position:absolute; top:10px; right:10px; background:linear-gradient(135deg,#ef4444,#dc2626); color:white; font-size:0.7rem; font-weight:800; padding:4px 10px; border-radius:20px; text-transform:uppercase; box-shadow:0 2px 8px rgba(0,0,0,0.2);">
                                        🔥 TRENDING
                                    </div>
                                @endif
                            </div>
                            @else
                            {{-- Text-Only Meme Card --}}
                            <div style="
                                min-height: 180px;
                                background: linear-gradient(135deg, {{ $brand->theme_color ?? '#6f42c1' }}22, {{ $brand->theme_color ?? '#6f42c1' }}44);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                padding: 24px;
                                position: relative;
                                overflow: hidden;
                            ">
                                <div style="position:absolute; inset:0; background-image: radial-gradient({{ $brand->theme_color ?? '#6f42c1' }}33 1px, transparent 1px); background-size: 20px 20px; opacity: 0.5;"></div>
                                <p style="
                                    font-size: 1.05rem;
                                    font-weight: 700;
                                    color: #1e1e2e;
                                    text-align: center;
                                    line-height: 1.5;
                                    position: relative;
                                    z-index: 1;
                                    margin: 0;
                                ">"{{ $meme->title }}"</p>
                            </div>
                            @endif

                            {{-- Card Body --}}
                            <div style="padding: 16px 18px 18px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                                {{-- User Row --}}
                                <div style="display:flex; align-items:center; gap:10px; margin-bottom: 10px;">
                                    <img src="{{ $meme->user->profile_photo_url }}"
                                         alt="{{ $meme->user->name }}"
                                         style="width:32px; height:32px; border-radius:50%; object-fit:cover; border: 2px solid #f0f0f0;">
                                    <div>
                                        <div style="font-weight:700; font-size:0.82rem; color:#1e1e2e;">{{ $meme->user->name ?? 'Anonymous' }}</div>
                                        <div style="font-size:0.72rem; color:#9ca3af;">{{ $meme->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>

                                {{-- Title (if has image) --}}
                                @if($meme->image_path && $meme->title)
                                <p style="font-size: 0.88rem; font-weight: 600; color: #374151; margin-bottom: 12px; line-height: 1.4; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                    {{ $meme->title }}
                                </p>
                                @endif

                                {{-- Stats Row --}}
                                <div style="display:flex; align-items:center; justify-content:space-between; border-top: 1px solid #f3f4f6; padding-top: 12px; margin-top: auto;">
                                    <div style="display:flex; gap:14px; font-size:0.8rem; color:#6b7280; font-weight:600;">
                                        <span><i class="bi bi-heart-fill" style="color:#ef4444;"></i> {{ $meme->reactions->count() }}</span>
                                        <span><i class="bi bi-chat-fill" style="color:#3b82f6;"></i> {{ $meme->comments->count() }}</span>
                                    </div>
                                    <span style="
                                        background: {{ $brand->theme_color ?? '#6f42c1' }}22;
                                        color: {{ $brand->theme_color ?? '#6f42c1' }};
                                        font-size: 0.75rem;
                                        font-weight: 800;
                                        padding: 3px 10px;
                                        border-radius: 20px;
                                    ">Score: {{ $meme->calculated_score }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-5 d-flex justify-content-center">
                    {{ $memes->links() }}
                </div>
            @else
                <div class="text-center py-5 rounded-4" style="background: #f8fafc; border: 2px dashed #e2e8f0;">
                    <div style="font-size: 3rem; margin-bottom: 16px;">🎭</div>
                    <h5 class="fw-bold text-dark mb-2">No memes yet!</h5>
                    <p class="text-muted small mb-4">Be the first to submit a meme for this campaign</p>
                    <a href="{{ route('sponsored.submit.form', $brand->slug ?? $brand->id) }}" class="btn rounded-pill px-4 fw-bold text-white" style="background-color: {{ $brand->theme_color ?? '#6f42c1' }};">
                        Submit First Meme
                    </a>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4" style="position: sticky; top: 20px; max-height: calc(100vh - 40px); overflow-y: auto;">
                <h3 class="h5 fw-bold italic uppercase mb-4" style="color: {{ $brand->theme_color ?? '#6f42c1' }};">Campaign Info</h3>

                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold tracking-wider mb-1">Campaign Title</label>
                    <p class="fw-bold text-dark">{{ $brand->campaign_title ?? 'The Great Meme War' }}</p>
                </div>

                <div class="mb-4">
                    <label class="text-muted small text-uppercase fw-bold tracking-wider mb-1">Prize Pool</label>
                    <p class="h4 fw-black" style="color: {{ $brand->theme_color ?? '#6f42c1' }};">${{ number_format($brand->prize_amount ?? 100, 2) }}</p>
                </div>

                <div class="row mb-4">
                    <div class="col-6">
                        <label class="text-muted small text-uppercase fw-bold tracking-wider mb-1">Start Date</label>
                        <p class="small fw-bold text-dark">{{ $brand->start_date ? $brand->start_date->format('M d, Y h:i A') : '-' }}</p>
                    </div>
                    <div class="col-6">
                        <label class="text-muted small text-uppercase fw-bold tracking-wider mb-1">End Date</label>
                        <p class="small fw-bold text-dark">{{ $brand->end_date ? $brand->end_date->format('M d, Y h:i A') : '-' }}</p>
                    </div>
                </div>

                <div class="mb-4">
                    @if($brand->dos_guidelines)
                        <label class="text-muted small text-uppercase fw-bold tracking-wider mb-2" style="color: {{ $brand->theme_color ?? '#6f42c1' }};">✓ Do's Guidelines</label>
                        <div class="small text-dark opacity-75 mb-3">
                            {!! nl2br(e($brand->dos_guidelines)) !!}
                        </div>
                    @else
                        <label class="text-muted small text-uppercase fw-bold tracking-wider mb-2" style="color: {{ $brand->theme_color ?? '#6f42c1' }};">✓ Do's Guidelines</label>
                        <p class="small text-muted mb-3">No Do's guidelines specified</p>
                    @endif

                    @if($brand->donts_guidelines)
                        <label class="text-muted small text-uppercase fw-bold tracking-wider mb-2" style="color: {{ $brand->theme_color ?? '#6f42c1' }};">✗ Don'ts Guidelines</label>
                        <div class="small text-dark opacity-75">
                            {!! nl2br(e($brand->donts_guidelines)) !!}
                        </div>
                    @else
                        <label class="text-muted small text-uppercase fw-bold tracking-wider mb-2" style="color: {{ $brand->theme_color ?? '#6f42c1' }};">✗ Don'ts Guidelines</label>
                        <p class="small text-muted">No Don'ts guidelines specified</p>
                    @endif
                </div>

                <!-- Campaign Images -->
                @if($brand->product_images && is_array($brand->product_images) && count($brand->product_images) > 0)
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase fw-bold tracking-wider mb-2" style="color: {{ $brand->theme_color ?? '#6f42c1' }};">Campaign Images</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            @foreach($brand->product_images as $image)
                                <div class="relative aspect-square rounded-lg overflow-hidden border-2 border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                                    <img src="{{ asset('storage/' . $image) }}" class="w-full h-full object-cover" alt="Campaign Image">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Image Description -->
                @if($brand->image_description)
                    <div class="mb-4">
                        <label class="text-muted small text-uppercase fw-bold tracking-wider mb-1">Image Description</label>
                        <p class="small text-dark opacity-75">{{ $brand->image_description }}</p>
                    </div>
                @endif

                <hr class="my-4 opacity-10">

                <a href="{{ route('sponsored.submit.form', $brand->slug ?? $brand->id) }}" class="btn w-100 rounded-pill py-3 fw-black uppercase italic tracking-widest text-white" style="background: linear-gradient(135deg, {{ $brand->theme_color ?? '#6f42c1' }} 0%, {{ $brand->theme_color ?? '#6f42c1' }}cc 100%);">
                    Submit a Meme
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .uppercase { text-transform: uppercase; }
    .italic { font-style: italic; }
    .tracking-tight { letter-spacing: -1.5px; }
    .tracking-widest { letter-spacing: 2px; }
    .rounded-4 { border-radius: 1.5rem; }
    .hover\:-translate-y-1:hover { transform: translateY(-0.25rem); }
    .transition-transform { transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
    
    /* Grid for campaign images */
    .grid { display: grid; }
    .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .gap-2 { gap: 0.5rem; }
    .aspect-square { aspect-ratio: 1 / 1; }
    .overflow-hidden { overflow: hidden; }
    .border-2 { border-width: 2px; }
    .border-gray-200 { border-color: #e5e7eb; }
    .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
    .shadow-md { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
    .transition-shadow { transition: box-shadow 0.2s ease-in-out; }
    .object-cover { object-fit: cover; }
    .w-full { width: 100%; }
    .h-full { height: 100%; }
@keyframes meme-pulse {
    0%   { box-shadow: 0 0 0 0 rgba(91,46,145,0.7); border-color: #5B2E91; }
    50%  { box-shadow: 0 0 0 12px rgba(91,46,145,0.15), 0 0 25px 5px rgba(91,46,145,0.2); border-color: #5B2E91; }
    100% { box-shadow: 0 0 0 0 rgba(91,46,145,0); border-color: #5B2E91; }
}
.meme-highlighted > .meme-card {
    border: 3px solid #5B2E91 !important;
    animation: meme-pulse 1s ease-in-out 3;
    border-radius: 20px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    @php $highlightId = request()->query('highlight') ?? session('highlight_meme_id'); @endphp
    @if($highlightId ?? false)
    const el = document.getElementById('meme-{{ $highlightId }}');
    if (el) {
        setTimeout(() => {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            el.classList.add('meme-highlighted');
            setTimeout(() => el.classList.remove('meme-highlighted'), 4000);
        }, 400);
    }
    @endif
});
</script>

@endsection
