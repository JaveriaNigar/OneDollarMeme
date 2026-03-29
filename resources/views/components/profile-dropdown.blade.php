@php
    $dropdownId = 'profileDropdown-' . uniqid();
@endphp

<div class="dropdown">
    <button class="btn dropdown-toggle d-flex align-items-center gap-1 border-0 p-0"
            type="button"
            id="{{ $dropdownId }}"
            data-bs-toggle="dropdown"
            aria-expanded="false">
        <img src="{{ $user->profile_photo_url }}" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;" alt="{{ $user->name }}">
    </button>

    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="{{ $dropdownId }}">
        <li>
            <a class="dropdown-item" href="/profile">
                Profile
            </a>
        </li>
        @if($user->isAdmin())
        <li>
            <a class="dropdown-item" href="/admin">
                Admin Panel
            </a>
        </li>
        @endif
        <li>
            <a class="dropdown-item" href="{{ route('account.settings') }}">
                Account Settings
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#termsModal">
                Terms & Conditions
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <form method="POST" action="/logout" class="mb-0">
                @csrf
                <button type="submit" class="dropdown-item">
                    Logout
                </button>
            </form>
        </li>
    </ul>
</div>