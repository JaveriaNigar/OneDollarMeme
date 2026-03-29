@extends('layouts.brands_app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-black uppercase italic tracking-tight" style="color: var(--primary-purple);">How It Works for Brands</h1>
                <p class="lead text-muted">Create memes, participate in brand campaigns, and win amazing prizes!</p>
            </div>

            <!-- Steps Section -->
            <div class="row g-4 mb-5">
                <!-- Step 1 -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-4 transition-transform hover:-translate-y-1">
                        <div class="mb-4 d-flex justify-content-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 80px; height: 80px; background-color: var(--primary-purple); font-size: 2rem;">
                                1
                            </div>
                        </div>
                        <h3 class="h5 fw-bold mb-3">Find a Campaign</h3>
                        <p class="text-muted small">Browse the active brand campaigns on our platform. Each brand offers unique challenges and huge prize pools for the best memes.</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-4 transition-transform hover:-translate-y-1">
                        <div class="mb-4 d-flex justify-content-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 80px; height: 80px; background-color: var(--primary-orange); font-size: 2rem;">
                                2
                            </div>
                        </div>
                        <h3 class="h5 fw-bold mb-3">Create & Submit</h3>
                        <p class="text-muted small">Design a creative, funny, and engaging meme following the brand's guidelines. Submit it directly through the brand's campaign page.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-4 transition-transform hover:-translate-y-1">
                        <div class="mb-4 d-flex justify-content-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 80px; height: 80px; background-color: #28a745; font-size: 2rem;">
                                3
                            </div>
                        </div>
                        <h3 class="h5 fw-bold mb-3">Win Big Prizes</h3>
                        <p class="text-muted small">Get votes, reactions, and shares from the community. Top trending memes will win the prize money set by the brand at the end of the campaign!</p>
                    </div>
                </div>
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
    .rounded-\[40px\] { border-radius: 40px; }
    .hover\:-translate-y-1:hover { transform: translateY(-0.25rem); }
    .transition-transform { transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
</style>
@endsection
