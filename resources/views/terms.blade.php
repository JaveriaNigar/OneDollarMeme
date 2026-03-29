@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-5">
                    <h1 class="fw-bold mb-4">Terms & Conditions</h1>
                    <p class="text-muted">Welcome to OneDollarMeme. By using our service, you agree to these terms.</p>
                    <hr>
                    <div class="mt-4">
                        <h5>1. Membership</h5>
                        <p>User must be 18+ to enter battles.</p>
                        
                        <h5>2. Payments</h5>
                        <p>All battle entries are $1 and non-refundable.</p>
                        
                        <h5>3. Community Guidelines</h5>
                        <p>No hate speech or illegal content.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
