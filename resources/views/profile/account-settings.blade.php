@extends('layouts.app')

@section('content')
<div class="settings-container py-5" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); min-height: calc(100vh - 64px);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <!-- Settings Card -->
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <!-- Header -->
                    <div class="p-4 bg-white border-b border-gray-100">
                        <div class="d-flex align-items-center gap-3">
                            <div class="w-12 h-12 bg-purple/10 rounded-2xl flex items-center justify-center text-purple">
                                <i class="bi bi-person-gear fs-4"></i>
                            </div>
                            <div>
                                <h1 class="h4 fw-black uppercase italic tracking-tight mb-0">Account Settings</h1>
                                <p class="text-muted small mb-0">Manage your account credentials</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <!-- Info Box -->
                        <div class="bg-gray-50 rounded-3 p-4 mb-5 border border-gray-100">
                            <label class="text-muted small text-uppercase fw-bold tracking-wider mb-2 d-block">Registered Email</label>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-dark fs-5">{{ $user->email }}</span>
                                <span class="badge bg-purple/10 text-purple rounded-pill px-3">Primary</span>
                            </div>
                            <p class="text-muted small mt-2 mb-0 italic">Note: Email address cannot be changed for security reasons.</p>
                        </div>

                        <!-- Username Form -->
                        <div class="mb-5">
                            <h2 class="h6 fw-bold text-dark uppercase tracking-wider mb-4">Display Information</h2>
                            <form method="post" action="{{ route('account.settings.username.update') }}">
                                @csrf
                                @method('patch')
                                
                                <label class="text-muted small fw-bold mb-2">Display Name</label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control border-gray-200 py-3 px-4 @error('name') is-invalid @enderror" 
                                           name="name" 
                                           value="{{ old('name', $user->name) }}" 
                                           placeholder="Display Name" 
                                           required 
                                           style="border-radius: 12px 0 0 12px;">
                                    <button class="btn btn-purple px-4 fw-bold uppercase italic tracking-widest text-white" 
                                            type="submit" 
                                            style="background-color: #6f42c1; border-radius: 0 12px 12px 0;">
                                        Update
                                    </button>
                                </div>

                                @error('name')
                                    <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror

                                @if (session('status') === 'name-updated')
                                    <div class="alert alert-success mt-3 py-2 small border-0 rounded-3 bg-green-50 text-green-700" role="alert">
                                        <i class="bi bi-check-circle-fill me-2"></i> Username updated successfully.
                                    </div>
                                @endif
                            </form>
                        </div>

                        <hr class="my-5 opacity-10">

                        <!-- Password Form -->
                        <div>
                            <h2 class="h6 fw-bold text-dark uppercase tracking-wider mb-4">Security & Password</h2>
                            <form method="post" action="{{ route('account.settings.update') }}" id="accountForm">
                                @csrf
                                @method('patch')

                                <div class="mb-4">
                                    <label class="text-muted small fw-bold mb-2">Current Password</label>
                                    <div class="input-group password-container">
                                        <input type="password" 
                                               class="form-control border-gray-200 py-3 px-4 @error('current_password') is-invalid @enderror" 
                                               name="current_password" 
                                               id="current_password"
                                               placeholder="Enter current password" 
                                               required
                                               style="border-radius: 12px;">
                                        <button class="toggle-password" type="button" data-target="current_password" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; padding: 5px; color: #6b7280; z-index: 10;">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('current_password')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="text-muted small fw-bold mb-2">New Password</label>
                                    <div class="input-group password-container">
                                        <input type="password" 
                                               class="form-control border-gray-200 py-3 px-4 @error('password') is-invalid @enderror" 
                                               name="password" 
                                               id="password" 
                                               placeholder="Min. 8 characters" 
                                               required
                                               style="border-radius: 12px;">
                                        <button class="toggle-password" type="button" data-target="password" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; padding: 5px; color: #6b7280; z-index: 10;">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                    <div id="passwordError" class="text-danger small mt-2" style="display: none;">Password must be at least 8 characters.</div>
                                </div>

                                <div class="mb-5">
                                    <label class="text-muted small fw-bold mb-2">Confirm New Password</label>
                                    <div class="input-group password-container">
                                        <input type="password" 
                                               class="form-control border-gray-200 py-3 px-4" 
                                               name="password_confirmation" 
                                               id="password_confirmation" 
                                               placeholder="Repeat new password" 
                                               required
                                               style="border-radius: 12px;">
                                        <button class="toggle-password" type="button" data-target="password_confirmation" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; padding: 5px; color: #6b7280; z-index: 10;">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                    <div id="passwordMatchError" class="text-danger small mt-2" style="display: none;">Passwords do not match.</div>
                                </div>

                                <div class="d-flex flex-column flex-md-row gap-3">
                                    <a href="{{ route('profile.edit') }}" class="btn btn-light py-3 px-4 rounded-3 fw-bold flex-grow-1 border">
                                        Go to Profile
                                    </a>
                                    <button type="submit" class="btn btn-orange py-3 px-5 rounded-3 fw-black uppercase italic tracking-widest text-white flex-grow-1 shadow-sm" style="background: linear-gradient(135deg, #fd7e14 0%, #f97316 100%);">
                                        Update Password
                                    </button>
                                </div>

                                @if (session('status') === 'password-updated')
                                    <div class="alert alert-success mt-4 mb-0 py-2 small border-0 rounded-3 bg-green-50 text-green-700" role="alert">
                                        <i class="bi bi-shield-check me-2"></i> Password updated successfully.
                                    </div>
                                @endif
                            </form>
                        </div>
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
    .bg-purple\/10 { background-color: rgba(111, 66, 193, 0.1); }
    .text-purple { color: #6f42c1; }
    .bg-gray-50 { background-color: #f8fafc; }
    .border-gray-100 { border-color: #f1f5f9; }
    .border-gray-200 { border-color: #e2e8f0; }
    
    .form-control:focus {
        border-color: #6f42c1;
        box-shadow: 0 0 0 4px rgba(111, 66, 193, 0.1);
    }

    .password-container {
        position: relative;
    }

    .password-container .form-control {
        padding-right: 45px; /* Make space for the toggle button */
    }

    .toggle-password {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6b7280;
        transition: color 0.2s;
        background: none;
        border: none;
        padding: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10; /* Ensure button is above input */
    }

    .toggle-password:hover {
        color: #6f42c1; /* Use the purple color for hover */
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('accountForm');
    const password = document.getElementById('password');
    const confirm = document.getElementById('password_confirmation');
    const passwordError = document.getElementById('passwordError');
    const passwordMatchError = document.getElementById('passwordMatchError');

    const validate = () => {
        const pass = password.value;
        const conf = confirm.value;
        const isValid = pass.length >= 8;
        const isMatch = pass === conf;

        if (pass.length > 0) {
            passwordError.style.display = !isValid ? 'block' : 'none';
        } else {
            passwordError.style.display = 'none';
        }

        if (conf.length > 0) {
            passwordMatchError.style.display = !isMatch ? 'block' : 'none';
        } else {
            passwordMatchError.style.display = 'none';
        }
    };

    password.addEventListener('input', validate);
    confirm.addEventListener('input', validate);

    form.addEventListener('submit', (e) => {
        if (password.value.length < 8 || password.value !== confirm.value) {
            e.preventDefault();
            validate();
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
});
</script>
@endsection
