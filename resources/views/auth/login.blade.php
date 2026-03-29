<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - OneDollarMeme</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS (for modal only) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --brand-purple: #5B2E91;
            --brand-orange: #f2994a;
            --brand-bg: #f3f4f6;
            --light-purple: #f8f0fc;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Figtree', sans-serif;
            background-color: var(--brand-bg);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-card {
            background-color: white;
            width: 100%;
            max-width: 420px;
            padding: 40px 35px;
            border-radius: 40px;
            box-shadow: 0 40px 80px rgba(91, 46, 145, 0.1);
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        /* Wavy Background in Card to match image */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 100% 30%, var(--light-purple) 0%, transparent 70%);
            z-index: 0;
        }

        .content-wrapper {
            position: relative;
            z-index: 10;
        }

        .logo-container {
            margin-bottom: 25px;
        }

        .logo-container img {
            width: 85px;
            height: auto;
            border-radius: 20px;
        }

        .login-title {
            color: var(--brand-purple);
            font-size: 42px;
            font-weight: 900;
            margin-bottom: 25px;
            margin-top: 0;
            letter-spacing: -1px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 15px;
            font-weight: 700;
            color: #4b5563;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            background-color: #f9fafb;
            border: 2px solid #f3f4f6;
            border-radius: 15px;
            padding: 14px 20px;
            font-size: 15px;
            transition: all 0.3s ease;
            outline: none;
            color: #1f2937;
        }

        .form-input:focus {
            background-color: white;
            border-color: var(--brand-purple);
            box-shadow: 0 0 0 4px rgba(91, 46, 145, 0.1);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
            font-size: 14px;
            color: #6b7280;
            text-align: left;
        }

        .checkbox-group input {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            border: 2px solid #e5e7eb;
            cursor: pointer;
            accent-color: var(--brand-purple);
        }

        .checkbox-group a {
            color: var(--brand-purple);
            text-decoration: none;
            font-weight: 700;
        }

        .flex-actions {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }

        .btn-login {
            flex: 1;
            background-color: var(--brand-purple);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 11px 20px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 8px 16px rgba(91, 46, 145, 0.2);
        }

        .btn-login:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .social-btns {
            display: flex;
            gap: 12px;
        }

        .social-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.2s;
            text-decoration: none;
            border: 1px solid #f3f4f6;
        }

        .social-btn:hover {
            transform: scale(1.1);
            background: #f9fafb;
        }

        .register-link {
            font-size: 14px;
            color: #6b7280;
        }

        .register-link a {
            color: var(--brand-purple);
            text-decoration: none;
            font-weight: 800;
        }

        .error-msg {
            color: #ef4444;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 40px 25px;
                border-radius: 35px;
            }
            .login-title {
                font-size: 42px;
            }
            .flex-actions {
                flex-direction: column;
            }
            .btn-login {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="content-wrapper">
        <!-- Logo at top center -->
        <div class="logo-container">
            <img src="{{ asset('image/logo.jpg') }}" alt="OneDollarMeme Logo">
        </div>

        <h1 class="login-title">Login</h1>

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            <!-- Email -->
            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-input" value="{{ old('email', 'javeriangar40@gmail.com') }}" required autofocus>
                @error('email')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="password-container" style="position: relative;">
                    <input type="password" id="password" name="password" class="form-input" required placeholder="••••••••" style="padding-right: 45px;">
                    <button type="button" class="toggle-password" data-target="password" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; padding: 5px; color: #6b7280; z-index: 10;">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <!-- Checkbox -->
            <div class="checkbox-group">
                <input type="checkbox" id="terms" name="terms_accepted" required>
                <label for="terms">I accept Terms and Policy <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">[Read]</a></label>
            </div>
            <div id="terms_error" class="error-msg mb-4" style="display: none; text-align: left;">You must accept Terms & Policy to continue</div>

            <!-- Login Actions -->
            <div class="flex-actions">
                <button type="submit" class="btn-login">LOG IN</button>
                
                <div class="social-btns">
                    <a href="{{ url('/auth/google') }}" class="social-btn" title="Login with Google">
                        <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" alt="Google" style="width: 24px;">
                    </a>
                    <a href="{{ url('/auth/facebook') }}" class="social-btn" title="Login with Facebook">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/b/b8/2021_Facebook_icon.svg" alt="Facebook" style="width: 24px;">
                    </a>
                </div>
            </div>

            <!-- Register -->
            <div class="register-link">
                Don't have an account? <a href="{{ route('register') }}">Register</a>
            </div>
        </form>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@include('partials._terms-modal')

<script>
    document.getElementById('loginForm').addEventListener('submit', function (e) {
        const checkbox = document.getElementById('terms');
        const errorDiv = document.getElementById('terms_error');
        
        if (!checkbox.checked) {
            e.preventDefault();
            errorDiv.style.display = 'block';
        } else {
            errorDiv.style.display = 'none';
        }
    });

    // Toggle Password Visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            
            if (input.type === 'password') {
                input.type = 'text';
                this.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
            } else {
                input.type = 'password';
                this.innerHTML = '<i class="fa-solid fa-eye"></i>';
            }
        });
    });
</script>

</body>
</html>
