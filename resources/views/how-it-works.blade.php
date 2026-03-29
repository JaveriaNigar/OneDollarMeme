@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
                <div class="card-body p-5">
                    <div class="text-center mb-5">
                        <h1 style="color: var(--brand-purple); font-weight: 900; font-size: 3.5rem; text-transform: uppercase; letter-spacing: 2px; font-family: 'bold', sans-serif;">How It Works</h1>
                        <p class="lead text-muted">Join the battle in 3 simple steps!</p>
                    </div>

                    <div class="row g-4 mb-5">
                        <div class="col-md-4">
                            <div class="text-center h-100 p-4" style="background: #f8f0fc; border-radius: 15px;">
                                <div class="mb-3" style="font-size: 2.5rem; color: var(--brand-purple);">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                </div>
                                <h4 class="fw-bold mb-2">1. Upload Meme</h4>
                                <p class="text-muted small mb-0">Post your best meme for just $1 to enter the weekly battle.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center h-100 p-4" style="background: #f8f0fc; border-radius: 15px;">
                                <div class="mb-3" style="font-size: 2.5rem; color: var(--brand-purple);">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                                <h4 class="fw-bold mb-2">2. Enter Battle</h4>
                                <p class="text-muted small mb-0">Your meme goes live on the leaderboard. Get reactions and shares!</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center h-100 p-4" style="background: #f8f0fc; border-radius: 15px;">
                                <div class="mb-3" style="font-size: 2.5rem; color: var(--brand-purple);">
                                    <i class="bi bi-trophy"></i>
                                </div>
                                <h4 class="fw-bold mb-2">3. Win Prizes</h4>
                                <p class="text-muted small mb-0">The highest-scoring meme winners the weekly cash prize pool!</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-top pt-5">
                        <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
                            <a href="{{ route('upload-meme.create') }}" class="btn btn-hero-purple px-5 py-3 fs-5">Upload Meme</a>
                            <a href="{{ route('upload-meme.create') }}" class="btn btn-hero-orange px-5 py-3 fs-5">Enter Battle $1</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --brand-purple: #702cb0;
        --brand-orange: #ff7a2d;
    }
    .btn-hero-purple,
    .btn-hero-purple:hover,
    .btn-hero-purple:focus,
    .btn-hero-purple:active {
        background-color: var(--brand-purple) !important;
        color: white !important;
        font-weight: 700;
        border-radius: 10px;
        border: none;
        box-shadow: none !important;
        transform: none !important;
        opacity: 1 !important;
    }
    .btn-hero-orange,
    .btn-hero-orange:hover,
    .btn-hero-orange:focus,
    .btn-hero-orange:active {
        background-color: var(--brand-orange) !important;
        color: white !important;
        font-weight: 700;
        border-radius: 10px;
        border: none;
        box-shadow: none !important;
        transform: none !important;
        opacity: 1 !important;
    }
</style>
@endsection
