<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay for Meme</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('image/my-logo.jpg') }}">
    
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #f1f5f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .payment-card {
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
        }

        .meme-preview-container {
            width: 100%;
            border-radius: 0.75rem;
            overflow: hidden;
            background: #f8fafc;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .meme-image {
            width: 100%;
            height: auto;
            max-height: 400px;
            object-fit: contain;
        }

        .meme-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 1rem;
            word-wrap: break-word;
        }

        .pay-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2rem;
        }

        .proceed-btn {
            width: 100%;
            background: #4f46e5;
            color: white;
            padding: 1rem;
            border: none;
            border-radius: 0.75rem;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .proceed-btn:hover {
            background: #4338ca;
            opacity: 0.95;
            color: white;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 1rem;
            color: #64748b;
            text-decoration: none;
        }
        
        .back-link:hover {
            color: #334155;
            text-decoration: underline;
        }

        /* Fake Payment Form Styles */
        .payment-form {
            display: none; /* Initially hidden */
            text-align: left;
            margin-top: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #475569;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #cbd5e0;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-size: 1rem;
            outline: none;
        }

        .form-input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1);
        }

        .form-row {
            display: flex;
            gap: 1rem;
        }

        .col-half {
            flex: 1;
        }
    </style>
</head>
<body>

    <div class="payment-card">
        
        @if($meme->is_contest)
            <h1 class="h3 mb-4">Congratulations</h1>
        @else
            <h1 class="h3 mb-4">Pay $1 for Competition</h1>
        @endif

        @if($meme->title && $meme->title !== 'Untitled')
            <div class="meme-title">{{ $meme->title }}</div>
        @endif

        @if($meme->image_path)
            <div class="meme-preview-container">
                <img src="{{ asset('storage/' . $meme->image_path) }}" 
                     alt="Meme Preview" 
                     class="meme-image">
            </div>
        @endif

        @if(!$meme->is_contest)
            <div class="pay-text">Pay $1 to enter Weekly Challenge</div>
        @endif

        @if($meme->is_contest)
            <div class="alert alert-success mt-3 mb-3">
                🏆 This meme is entered in the Weekly Challenge!
            </div>
            <div class="badge bg-warning text-dark mb-4" style="font-size: 1.2rem;">
                ⭐ Official Entry
            </div>
        @else

            <!-- Initial Proceed Button -->
            <button type="button" id="proceedBtn" class="proceed-btn">
                👉 Proceed to Payment
            </button>

            <!-- Fake Payment Form -->
            <div id="paymentFormContainer" class="payment-form">
                <form action="{{ route('memes.pay.dummy', $meme->id) }}" method="POST">
                    @csrf
                    
                    <label class="form-label">Card Number</label>
                    <input type="text" class="form-input" placeholder="0000 0000 0000 0000" maxlength="19" required>

                    <div class="form-row">
                        <div class="col-half">
                            <label class="form-label">Expiry Date</label>
                            <input type="text" class="form-input" placeholder="MM/YY" maxlength="5" required>
                        </div>
                        <div class="col-half">
                            <label class="form-label">CVC</label>
                            <input type="text" class="form-input" placeholder="123" maxlength="3" required>
                        </div>
                    </div>

                    <button type="submit" class="proceed-btn">
                        Pay $1 for Competition
                    </button>
                </form>
            </div>
        @endif

        
        @if($meme->is_contest)
            <a href="{{ route('home') }}" class="back-link">Continue to Home</a>
        @else
            <a href="{{ route('upload-meme.create', ['title' => $meme->title === 'Untitled' ? '' : $meme->title, 'image_preview' => asset('storage/' . $meme->image_path)]) }}" class="back-link">Cancel & Go to Upload Meme</a>
        @endif

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const proceedBtn = document.getElementById('proceedBtn');
            const paymentFormContainer = document.getElementById('paymentFormContainer');

            if (proceedBtn) {
                proceedBtn.addEventListener('click', function() {
                    proceedBtn.style.display = 'none';
                    paymentFormContainer.style.display = 'block';
                });
            }
        });
    </script>
</body>
</html>
