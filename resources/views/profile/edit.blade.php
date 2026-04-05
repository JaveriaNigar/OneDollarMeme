<x-app-layout>
    <style>
        :root {
            --brand-purple: #5B2E91;
            --brand-purple-light: #F5F3FF;
            --brand-purple-soft: #EDE9FE;
            --brand-purple-accent: #7C3AED;
        }
        .profile-page-wrapper {
            background: linear-gradient(135deg, var(--brand-purple-light) 0%, var(--brand-purple-soft) 100%);
            min-height: calc(100vh - 64px); /* Subtract nav height if exists */
        }
        .post-card { 
            background: #E2D8FF; 
            border-radius: 1rem; 
            padding: 1rem; 
            margin-bottom: 1.5rem; 
            box-shadow: 0 4px 12px rgba(91, 46, 145, 0.1); 
            border: 1px solid rgba(91, 46, 145, 0.05);
        }
        .avatar-circle { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; }
        .winner-badge { 
            position: absolute; 
            top: -10px; 
            left: -10px; 
            z-index: 10;
            padding: 4px 10px;
            font-weight: bold;
            border-radius: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            font-size: 0.7rem;
        }
        .rank-1 { background: linear-gradient(45deg, #ffd700, #ffae00); color: black; }
        .rank-2 { background: linear-gradient(45deg, #c0c0c0, #a0a0a0); color: black; }
        .rank-3 { background: linear-gradient(45deg, #cd7f32, #b87333); color: white; }

        .btn-purple-outline {
            background-color: transparent;
            color: var(--brand-purple);
            border: 1px solid var(--brand-purple);
            transition: all 0.2s;
        }
        .btn-purple-outline:hover {
            background-color: var(--brand-purple-light);
            color: var(--brand-purple-accent);
        }
        .btn-purple-solid {
            background-color: var(--brand-purple);
            color: white;
            transition: all 0.2s;
        }
        .btn-purple-solid:hover {
            background-color: var(--brand-purple-accent);
            opacity: 0.9;
        }
    </style>
    <div class="profile-page-wrapper py-5">
        <div class="container">
            <!-- Profile Header Section -->
            <div class="bg-white shadow-sm rounded-4 border border-purple-100 p-4 p-md-5 mb-5 mt-4">
                <div class="d-flex flex-column flex-md-row gap-4 align-items-center align-items-md-start">
                    <!-- Avatar Section -->
                    <div class="flex-shrink-0">
                        <div class="relative group" id="avatar-container" style="width: 154px; height: 154px;">
                            <img
                                id="avatar-img"
                                src="{{ $user->profile_photo_url }}"
                                alt="User Avatar"
                                class="w-full h-full rounded-full object-cover border-4 border-purple-100 shadow-sm"
                            >
                            <div class="absolute inset-0 bg-brand-purple bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none" id="avatar-overlay" style="display: none;">
                                <i class="fas fa-camera text-white fs-2"></i>
                            </div>
                        </div>
                    </div>

                    <!-- User Info & Stats -->
                    <div class="flex-grow-1 text-center text-md-start">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center align-items-md-start mb-4 gap-3">
                            <div>
                                <h4 class="h4 fw-black italic tracking-tighter mb-1 uppercase">{{ $user->name }}</h4>

                                <!-- Role Badge -->
                                @if($user->isAdmin())
                                    <span class="badge bg-danger mb-2">
                                        <i class="bi bi-shield-check me-1"></i> Admin
                                    </span>
                                    <span class="badge bg-warning text-dark mb-2">
                                        <i class="bi bi-pen me-1"></i> Blogger
                                    </span>
                                @else
                                    <span class="badge bg-purple mb-2" style="background-color: var(--brand-purple);">
                                        <i class="bi bi-image me-1"></i> Meme Creator
                                    </span>
                                @endif

                                <!-- Stats Row (Now below Username) -->
                                <div class="d-flex justify-content-center justify-content-md-start gap-4 py-2">
                                    @if($user->isAdmin())
                                        <div class="text-center text-md-start">
                                            <span class="fw-black">{{ $user->memes->count() }}</span>
                                            <span class="small text-purple fw-bold uppercase tracking-wider ms-1">Memes</span>
                                        </div>
                                        <div class="text-center text-md-start">
                                            <span class="fw-black">{{ $user->blogs->count() }}</span>
                                            <span class="small text-purple fw-bold uppercase tracking-wider ms-1">Blogs</span>
                                        </div>
                                        <div class="text-center text-md-start">
                                            <span class="fw-black">{{ $user->blogs->sum('views_count') }}</span>
                                            <span class="small text-purple fw-bold uppercase tracking-wider ms-1">Views</span>
                                        </div>
                                    @else
                                        <div class="text-center text-md-start">
                                            <span class="fw-black">{{ $user->memes->count() }}</span>
                                            <span class="small text-purple fw-bold uppercase tracking-wider ms-1">Memes</span>
                                        </div>
                                        <div class="text-center text-md-start">
                                            <span class="fw-black">{{ $user->memes->sum('score') }}</span>
                                            <span class="small text-purple fw-bold uppercase tracking-wider ms-1">Points</span>
                                        </div>
                                    @endif
                                    <div class="text-center text-md-start">
                                        <span class="fw-black">0</span>
                                        <span class="small text-purple fw-bold uppercase tracking-wider ms-1">Followers</span>
                                    </div>
                                    <div class="text-center text-md-start">
                                        <span class="fw-black">0</span>
                                        <span class="small text-purple fw-bold uppercase tracking-wider ms-1">Following</span>
                                    </div>
                                </div>

                                <!-- Bio (Now below Stats) -->
                                <div id="bio-display" class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 mt-2">
                                    <p class="text-muted italic mb-0">
                                        {{ $user->bio ?? 'Meme lover, caffeine addict, and proud owner of 3 cats.' }}
                                    </p>
                                    <button onclick="editBio()" class="text-purple-400 hover:text-purple-600 transition-colors bio-edit-icon" style="display: none;">
                                        <i class="fas fa-pencil-alt small"></i>
                                    </button>
                                </div>
                                <div id="bio-edit" class="hidden mt-2">
                                    <textarea
                                        id="bio-input"
                                        maxlength="120"
                                        rows="2"
                                        class="form-control text-muted italic border-purple-200 focus:ring-purple-400 focus:border-purple-400"
                                        style="border-radius: 12px; resize: none;"
                                    >{{ $user->bio ?? 'Meme lover, caffeine addict, and proud owner of 3 cats.' }}</textarea>
                                    <div class="d-flex gap-2 mt-2">
                                        <button onclick="saveBio()" class="btn btn-sm btn-purple-solid px-3 rounded-pill">Save</button>
                                        <button onclick="cancelBioEdit()" class="btn btn-sm btn-light px-3 rounded-pill border">Cancel</button>
                                    </div>
                                    <small class="text-muted small mt-1 d-block"><span id="bio-char-count">{{ strlen($user->bio ?? '') }}</span>/120</small>
                                </div>
                                <div id="join-date-display" class="text-muted small mt-2" style="display: none;">
                                    <i class="far fa-calendar-alt me-1"></i> Joined {{ $user->created_at ? $user->created_at->format('F Y') : 'January 2023' }}
                                </div>

                                <!-- Action Buttons (Now below Bio) -->
                                @if($isOwner ?? false)
                                <div class="d-flex justify-content-center justify-content-md-start gap-2 mt-3" style="margin-left: -3px;">
                                    <button class="btn btn-purple-outline rounded-pill px-4 fw-bold" onclick="toggleEditMode()">
                                        Edit Profile
                                    </button>
                                    <button class="btn btn-purple-outline rounded-pill px-4 fw-bold" onclick="shareProfile(event)">
                                        Share
                                    </button>
                                    @if($user->isAdmin())
                                        <a href="{{ route('memes.index') }}" class="btn btn-purple-outline rounded-pill px-4 fw-bold" style="text-decoration: none;">
                                            My Memes
                                        </a>
                                        <a href="{{ route('blogs.my-blogs') }}" class="btn btn-purple-outline rounded-pill px-4 fw-bold" style="text-decoration: none;">
                                            My Blogs
                                        </a>
                                    @else
                                        <a href="{{ route('drafts.index') }}" class="btn btn-purple-outline rounded-pill px-4 fw-bold" style="text-decoration: none;">
                                            Drafts
                                        </a>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Grid Section (Memes for Users, Both for Admins) -->
            <div class="px-2">
                @if($user->isAdmin())
                    <!-- Admins see BOTH sections -->
                    
                    <!-- Memes Section -->
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-purple text-white p-2 rounded-3 me-3">
                            <i class="bi bi-grid-3x3-gap-fill fs-5"></i>
                        </div>
                        <h3 class="h4 fw-black italic uppercase mb-0 tracking-tight">{{ ($isOwner ?? false) ? 'My Memes' : $user->name . "'s Memes" }}</h3>
                    </div>

                    <div class="row g-4 mb-5">
                        @forelse($user->memes->sortByDesc('created_at') as $index => $meme)
                            <div class="col-sm-6 col-lg-4 col-xl-3">
                                <div class="post-card position-relative h-100 d-flex flex-column border-0 shadow-sm hover-translate-y" style="transition: transform 0.3s ease;">
                                    @if($meme->is_contest)
                                        <div class="winner-badge rank-1 shadow-sm">
                                            @if($meme->status === 'winner')
                                                WINNER 🏆
                                            @else
                                                BATTLE 💰
                                            @endif
                                        </div>
                                    @elseif($meme->brand_id)
                                        <div class="winner-badge shadow-sm" style="background: linear-gradient(45deg, #7c3aed, #5b2e91); color: white;">
                                            {{ $meme->brand->company_name ?? 'BRAND' }} 🚀
                                        </div>
                                    @endif

                                    <div class="d-flex align-items-center mb-3">
                                        <img src="{{ $user->profile_photo_url }}" class="rounded-circle border border-purple-100" style="width: 32px; height: 32px; object-fit: cover;" alt="{{ $user->name }}">
                                        <div class="ms-2">
                                            <span class="fw-bold small d-block text-dark">{{ $user->name }}</span>
                                            <span class="text-muted" style="font-size: 0.65rem;">{{ $meme->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>

                                    <div class="flex-grow-1 mb-3 rounded-4 overflow-hidden bg-light d-flex flex-column position-relative shadow-sm" style="min-height: 200px;">
                                        @if($meme->image_path)
                                            <img src="{{ asset('storage/' . $meme->image_path) }}" class="img-fluid w-100 h-100" style="object-fit: cover; position: absolute; inset: 0;" alt="Meme">
                                            @if($meme->title && $meme->title !== 'Untitled')
                                                <div class="position-absolute bottom-0 start-0 end-0 w-100 p-2 text-center" style="background: rgba(0,0,0,0.6); color: white; font-size: 0.8rem; font-weight: 600;">
                                                    {{ $meme->title }}
                                                </div>
                                            @endif
                                        @else
                                            <div class="d-flex align-items-center justify-content-center p-3 h-100 w-100">
                                                <p class="mb-0 fw-bold text-center text-dark" style="font-size: 1.1rem; line-height: 1.4;">
                                                    {{ $meme->title }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-auto pt-2 d-flex justify-content-between align-items-center">
                                        <div class="badge bg-purple/10 text-purple rounded-pill px-3 py-2 fw-bold" style="font-size: 0.75rem;">
                                            Score: {{ $meme->calculated_score ?? 0 }}
                                        </div>
                                        @if($meme->brand_id && $meme->brand)
                                            <a href="{{ url('/brands/' . $meme->brand_id) }}?highlight={{ $meme->id }}#meme-{{ $meme->id }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold" style="font-size: 0.75rem;">View</a>
                                        @else
                                            <a href="{{ url('/') }}?highlight={{ $meme->id }}#meme-{{ $meme->id }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold" style="font-size: 0.75rem;">View</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5 bg-white rounded-4 border border-dashed border-purple-200">
                                    <div class="mb-3 text-purple opacity-50">
                                        <i class="bi bi-images fs-1"></i>
                                    </div>
                                    <h5 class="text-muted mb-0">No memes yet! Upload your first meme.</h5>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Blogs Section -->
                    <div class="d-flex align-items-center mb-4 mt-5">
                        <div class="bg-purple text-white p-2 rounded-3 me-3">
                            <i class="bi bi-journal-text fs-5"></i>
                        </div>
                        <h3 class="h4 fw-black italic uppercase mb-0 tracking-tight">{{ ($isOwner ?? false) ? 'My Blogs' : $user->name . "'s Blogs" }}</h3>
                    </div>

                    <div class="row g-4">
                        @forelse($user->blogs->sortByDesc('created_at') as $blog)
                            <div class="col-sm-6 col-lg-4 col-xl-3">
                                <div class="post-card position-relative h-100 d-flex flex-column border-0 shadow-sm hover-translate-y" style="transition: transform 0.3s ease;">
                                    <!-- Status Badge -->
                                    @if($blog->status === 'draft')
                                        <div class="winner-badge shadow-sm" style="background: #6c757d; color: white; top: 5px; left: 5px; font-size: 0.6rem;">
                                            DRAFT
                                        </div>
                                    @elseif($blog->status === 'published')
                                        <div class="winner-badge shadow-sm" style="background: linear-gradient(45deg, #28a745, #20c997); color: white; top: 5px; left: 5px; font-size: 0.6rem;">
                                            PUBLISHED
                                        </div>
                                    @endif

                                    <div class="d-flex align-items-center mb-3">
                                        <img src="{{ $user->profile_photo_url }}" class="rounded-circle border border-purple-100" style="width: 32px; height: 32px; object-fit: cover;" alt="{{ $user->name }}">
                                        <div class="ms-2">
                                            <span class="fw-bold small d-block text-dark">{{ $user->name }}</span>
                                            <span class="text-muted" style="font-size: 0.65rem;">{{ $blog->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>

                                    <div class="flex-grow-1 mb-3 rounded-4 overflow-hidden bg-light d-flex flex-column position-relative shadow-sm" style="min-height: 200px;">
                                        @if($blog->featured_image)
                                            <img src="{{ asset('storage/' . $blog->featured_image) }}" class="img-fluid w-100 h-100" style="object-fit: cover; position: absolute; inset: 0;" alt="Blog">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center p-3 h-100 w-100 bg-purple-light">
                                                <i class="bi bi-journal-text text-purple" style="font-size: 3rem; opacity: 0.3;"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <h4 class="h6 fw-bold mb-2 text-dark line-clamp-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $blog->title }}
                                    </h4>

                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <div class="d-flex gap-2 small text-muted">
                                            <span title="Views"><i class="bi bi-eye"></i> {{ $blog->views_count }}</span>
                                            <span title="Reading Time"><i class="bi bi-clock"></i> {{ $blog->reading_time }} min</span>
                                        </div>
                                        <a href="{{ route('blogs.show', $blog->slug) }}" class="btn btn-sm btn-purple-outline rounded-pill px-3" style="text-decoration: none; font-size: 0.75rem;">
                                            Read
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5 bg-white rounded-4 border">
                                    <i class="bi bi-journal-x text-purple" style="font-size: 4rem; opacity: 0.3;"></i>
                                    <h5 class="mt-3 text-muted">No blogs yet</h5>
                                    @if($isOwner ?? false)
                                        <a href="{{ route('blogs.create') }}" class="btn btn-purple mt-2">Create Your First Blog</a>
                                    @endif
                                </div>
                            </div>
                        @endforelse
                    </div>
                @else
                    <!-- Memes Grid for Meme Users -->
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-purple text-white p-2 rounded-3 me-3">
                            <i class="bi bi-grid-3x3-gap-fill fs-5"></i>
                        </div>
                        <h3 class="h4 fw-black italic uppercase mb-0 tracking-tight">{{ ($isOwner ?? false) ? 'My Memes' : $user->name . "'s Memes" }}</h3>
                    </div>

                <div class="row g-4">
                    @forelse($user->memes->sortByDesc('created_at') as $index => $meme)
                        <div class="col-sm-6 col-lg-4 col-xl-3">
                            <div class="post-card position-relative h-100 d-flex flex-column border-0 shadow-sm hover-translate-y" style="transition: transform 0.3s ease;">
                                @if($meme->is_contest)
                                    <div class="winner-badge rank-1 shadow-sm">
                                        @if($meme->status === 'winner')
                                            WINNER 🏆
                                        @else
                                            BATTLE 💰
                                        @endif
                                    </div>
                                @elseif($meme->brand_id)
                                    <div class="winner-badge shadow-sm" style="background: linear-gradient(45deg, #7c3aed, #5b2e91); color: white;">
                                        {{ $meme->brand->company_name ?? 'BRAND' }} 🚀
                                    </div>
                                @endif

                                <div class="d-flex align-items-center mb-3">
                                    <img src="{{ $user->profile_photo_url }}" class="rounded-circle border border-purple-100" style="width: 32px; height: 32px; object-fit: cover;" alt="{{ $user->name }}">
                                    <div class="ms-2">
                                        <span class="fw-bold small d-block text-dark">{{ $user->name }}</span>
                                        <span class="text-muted" style="font-size: 0.65rem;">{{ $meme->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>

                                <div class="flex-grow-1 mb-3 rounded-4 overflow-hidden bg-light d-flex flex-column position-relative shadow-sm" style="min-height: 200px;">
                                    @if($meme->image_path)
                                        <img src="{{ asset('storage/' . $meme->image_path) }}" class="img-fluid w-100 h-100" style="object-fit: cover; position: absolute; inset: 0;" alt="Meme">
                                        <!-- Caption overlay for images -->
                                        @if($meme->title && $meme->title !== 'Untitled')
                                            <div class="position-absolute bottom-0 start-0 end-0 w-100 p-2 text-center" style="background: rgba(0,0,0,0.6); color: white; font-size: 0.8rem; font-weight: 600;">
                                                {{ $meme->title }}
                                            </div>
                                        @endif
                                    @else
                                        <!-- Full text display for text-only memes -->
                                        <div class="d-flex align-items-center justify-content-center p-3 h-100 w-100">
                                            <p class="mb-0 fw-bold text-center text-dark" style="font-size: 1.1rem; line-height: 1.4;">
                                                {{ $meme->title }}
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-auto pt-2 d-flex justify-content-between align-items-center">
                                    <div class="badge bg-purple/10 text-purple rounded-pill px-3 py-2 fw-bold" style="font-size: 0.75rem;">
                                        Score: {{ $meme->calculated_score ?? 0 }}
                                    </div>
                                    @if($meme->brand_id && $meme->brand)
                                        <a href="{{ url('/brands/' . $meme->brand_id) }}?highlight={{ $meme->id }}#meme-{{ $meme->id }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold" style="font-size: 0.75rem;">View</a>
                                    @else
                                        <a href="{{ url('/') }}?highlight={{ $meme->id }}#meme-{{ $meme->id }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold" style="font-size: 0.75rem;">View</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5 bg-white rounded-4 border border-dashed border-purple-200">
                                <div class="mb-3 text-purple opacity-50">
                                    <i class="bi bi-images fs-1"></i>
                                </div>
                                <h5 class="text-muted mb-0">Start your journey! Upload your first meme.</h5>
                                @if($isOwner ?? false)
                                <a href="{{ route('upload-meme.create') }}" class="btn btn-purple-solid rounded-pill px-4 mt-3">Upload Now</a>
                                @endif
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
                @endif
            <!-- Hidden stuff -->
            <input type="file" id="avatar-input" class="hidden" accept="image/*" onchange="updateAvatar(this)">
        </div>
    </div>
    
    <script>
        // Edit mode state
        let isEditMode = false;

        // Initialize profile in view-only mode
        document.addEventListener('DOMContentLoaded', function() {
            lockProfileEditing();

            // Initially hide the join date when page loads
            const joinDateDisplay = document.getElementById('join-date-display');
            if (joinDateDisplay) {
                joinDateDisplay.style.display = 'none';
            }
        });

        // Toggle edit mode when "Edit Profile" button is clicked
        function toggleEditMode() {
            isEditMode = !isEditMode;

            if (isEditMode) {
                unlockProfileEditing();
            } else {
                lockProfileEditing();
            }
        }

        // Lock all profile editing features
        function lockProfileEditing() {
            // Disable avatar click functionality
            const avatarContainer = document.getElementById('avatar-container');
            avatarContainer.classList.remove('cursor-pointer');
            avatarContainer.onclick = null;

            // Hide avatar overlay
            const avatarOverlay = document.getElementById('avatar-overlay');
            avatarOverlay.style.display = 'none';

            // Hide bio edit pencil icon
            const bioEditIcons = document.querySelectorAll('.bio-edit-icon');
            bioEditIcons.forEach(icon => {
                icon.style.display = 'none';
            });

            // Hide country edit pencil icon
            const countryEditIcons = document.querySelectorAll('.country-edit-icon');
            countryEditIcons.forEach(icon => {
                icon.style.display = 'none';
            });

            // Hide join date
            const joinDateDisplay = document.getElementById('join-date-display');
            if (joinDateDisplay) {
                joinDateDisplay.style.display = 'none';
            }

            // Change button text to "Done Editing"
            const editButton = document.querySelector('button[onclick*="toggleEditMode"]');
            if (editButton) {
                editButton.textContent = 'Edit Profile';
            }
        }

        // Unlock profile editing features
        function unlockProfileEditing() {
            // Enable avatar click functionality
            const avatarContainer = document.getElementById('avatar-container');
            avatarContainer.classList.add('cursor-pointer');
            avatarContainer.onclick = function() {
                document.getElementById('avatar-input').click();
            };

            // Show avatar overlay
            const avatarOverlay = document.getElementById('avatar-overlay');
            avatarOverlay.style.display = 'flex';

            // Show bio edit pencil icon
            const bioEditIcons = document.querySelectorAll('.bio-edit-icon');
            bioEditIcons.forEach(icon => {
                icon.style.display = 'inline';
            });

            // Show country edit pencil icon
            const countryEditIcons = document.querySelectorAll('.country-edit-icon');
            countryEditIcons.forEach(icon => {
                icon.style.display = 'inline';
            });

            // Show join date
            const joinDateDisplay = document.getElementById('join-date-display');
            if (joinDateDisplay) {
                joinDateDisplay.style.display = 'block';
            }

            // Change button text to "Done Editing"
            const editButton = document.querySelector('button[onclick*="toggleEditMode"]');
            if (editButton) {
                editButton.textContent = 'Done Editing';
            }
        }

        function editBio() {
            document.getElementById('bio-display').classList.add('hidden');
            document.getElementById('bio-edit').classList.remove('hidden');
        }

        function cancelBioEdit() {
            document.getElementById('bio-edit').classList.add('hidden');
            document.getElementById('bio-display').classList.remove('hidden');
        }

        async function saveBio() {
            const bioInput = document.getElementById('bio-input').value;
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch('{{ route('profile.update.info') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ bio: bioInput })
                });

                const data = await response.json();

                if (data.success) {
                    // Update display with new bio
                    document.querySelector('#bio-display p').textContent = bioInput;
                    document.getElementById('bio-edit').classList.add('hidden');
                    document.getElementById('bio-display').classList.remove('hidden');

                    // Automatically lock editing after saving
                    isEditMode = false;
                    lockProfileEditing();
                } else {
                    alert('Failed to update bio. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            }
        }

        // Share Profile function
        function shareProfile(event) {
            event.preventDefault();

            // Get the current profile URL
            const profileUrl = window.location.origin + '/profile/{{ urlencode($user->name) }}';
            const profileTitle = 'Check out my profile on OneDollarMeme!';

            // Create the share modal HTML with same design as main feed
            const shareOptions = `
                <div id="share-modal" style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: rgba(0, 0, 0, 0.5);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 9999;
                ">
                    <div style="
                        background: white;
                        border-radius: 0.5rem;
                        padding: 1.5rem;
                        width: 90%;
                        max-width: 24rem;
                        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                    ">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                            <h3 style="font-size: 1.125rem; font-weight: 700; color: #000; margin: 0;">Share Profile</h3>
                            <button onclick="closeShareModal()" style="color: #6b7280; background: transparent; border: none; cursor: pointer; font-size: 1.25rem;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div style="display: flex; flex-direction: column; gap: 0.5rem; max-height: 400px; overflow-y: auto; padding-right: 5px;" class="custom-scrollbar">
                            <!-- Copy Link -->
                            <button onclick="copyToClipboard('${profileUrl}')" style="
                                width: 100%;
                                display: flex;
                                align-items: center;
                                gap: 0.75rem;
                                padding: 0.6rem;
                                text-align: left;
                                background: transparent;
                                border: none;
                                border-radius: 0.5rem;
                                cursor: pointer;
                                transition: background-color 0.2s;
                            " onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                                <i class="fas fa-copy" style="color: #4b5563; width: 20px; text-align: center;"></i>
                                <span style="color: #374151; font-weight: 500; font-size: 0.9rem;">Copy Link</span>
                            </button>

                            <!-- Twitter / X -->
                            <a href="https://twitter.com/intent/tweet?url=${encodeURIComponent(profileUrl)}&text=${encodeURIComponent(profileTitle)}" target="_blank" style="
                                width: 100%;
                                display: flex;
                                align-items: center;
                                gap: 0.75rem;
                                padding: 0.6rem;
                                text-align: left;
                                text-decoration: none;
                                border-radius: 0.5rem;
                                transition: background-color 0.2s;
                            " onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                                <i class="fab fa-x-twitter" style="color: #000; width: 20px; text-align: center;"></i>
                                <span style="color: #374151; font-weight: 500; font-size: 0.9rem;">Twitter / X</span>
                            </a>

                            <!-- Facebook -->
                            <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(profileUrl)}" target="_blank" style="
                                width: 100%;
                                display: flex;
                                align-items: center;
                                gap: 0.75rem;
                                padding: 0.6rem;
                                text-align: left;
                                text-decoration: none;
                                border-radius: 0.5rem;
                                transition: background-color 0.2s;
                            " onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                                <i class="fab fa-facebook" style="color: #1877F2; width: 20px; text-align: center;"></i>
                                <span style="color: #374151; font-weight: 500; font-size: 0.9rem;">Facebook</span>
                            </a>

                            <!-- Instagram -->
                            <a href="https://www.instagram.com/" target="_blank" onclick="alert('Instagram does not support direct link sharing. Please copy the link and paste it in your post or story: ' + '${profileUrl}')" style="
                                width: 100%;
                                display: flex;
                                align-items: center;
                                gap: 0.75rem;
                                padding: 0.6rem;
                                text-align: left;
                                text-decoration: none;
                                border-radius: 0.5rem;
                                transition: background-color 0.2s;
                            " onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                                <i class="fab fa-instagram" style="color: #E4405F; width: 20px; text-align: center;"></i>
                                <span style="color: #374151; font-weight: 500; font-size: 0.9rem;">Instagram</span>
                            </a>

                            <!-- WhatsApp -->
                            <a href="https://wa.me/?text=${encodeURIComponent(profileTitle + ': ' + profileUrl)}" target="_blank" style="
                                width: 100%;
                                display: flex;
                                align-items: center;
                                gap: 0.75rem;
                                padding: 0.6rem;
                                text-align: left;
                                text-decoration: none;
                                border-radius: 0.5rem;
                                transition: background-color 0.2s;
                            " onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                                <i class="fab fa-whatsapp" style="color: #25D366; width: 20px; text-align: center;"></i>
                                <span style="color: #374151; font-weight: 500; font-size: 0.9rem;">WhatsApp</span>
                            </a>

                            <!-- Telegram -->
                            <a href="https://t.me/share/url?url=${encodeURIComponent(profileUrl)}&text=${encodeURIComponent(profileTitle)}" target="_blank" style="
                                width: 100%;
                                display: flex;
                                align-items: center;
                                gap: 0.75rem;
                                padding: 0.6rem;
                                text-align: left;
                                text-decoration: none;
                                border-radius: 0.5rem;
                                transition: background-color 0.2s;
                            " onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                                <i class="fab fa-telegram" style="color: #0088cc; width: 20px; text-align: center;"></i>
                                <span style="color: #374151; font-weight: 500; font-size: 0.9rem;">Telegram</span>
                            </a>

                            <!-- Reddit -->
                            <a href="https://reddit.com/submit?url=${encodeURIComponent(profileUrl)}&title=${encodeURIComponent(profileTitle)}" target="_blank" style="
                                width: 100%;
                                display: flex;
                                align-items: center;
                                gap: 0.75rem;
                                padding: 0.6rem;
                                text-align: left;
                                text-decoration: none;
                                border-radius: 0.5rem;
                                transition: background-color 0.2s;
                            " onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                                <i class="fab fa-reddit" style="color: #FF4500; width: 20px; text-align: center;"></i>
                                <span style="color: #374151; font-weight: 500; font-size: 0.9rem;">Reddit</span>
                            </a>

                            <!-- Pinterest -->
                            <a href="https://pinterest.com/pin/create/button/?url=${encodeURIComponent(profileUrl)}&description=${encodeURIComponent(profileTitle)}" target="_blank" style="
                                width: 100%;
                                display: flex;
                                align-items: center;
                                gap: 0.75rem;
                                padding: 0.6rem;
                                text-align: left;
                                text-decoration: none;
                                border-radius: 0.5rem;
                                transition: background-color 0.2s;
                            " onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                                <i class="fab fa-pinterest" style="color: #BD081C; width: 20px; text-align: center;"></i>
                                <span style="color: #374151; font-weight: 500; font-size: 0.9rem;">Pinterest</span>
                            </a>

                            <!-- LinkedIn -->
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(profileUrl)}" target="_blank" style="
                                width: 100%;
                                display: flex;
                                align-items: center;
                                gap: 0.75rem;
                                padding: 0.6rem;
                                text-align: left;
                                text-decoration: none;
                                border-radius: 0.5rem;
                                transition: background-color 0.2s;
                            " onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                                <i class="fab fa-linkedin" style="color: #0A66C2; width: 20px; text-align: center;"></i>
                                <span style="color: #374151; font-weight: 500; font-size: 0.9rem;">LinkedIn</span>
                            </a>
                        </div>
                    </div>
                </div>
            `;

            // Remove any existing modals
            const existingModal = document.getElementById('share-modal');
            if (existingModal) {
                existingModal.remove();
            }

            // Add the new modal to the page
            document.body.insertAdjacentHTML('beforeend', shareOptions);
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                if (window.showToast) {
                    window.showToast('Profile link copied!', 'success');
                } else {
                    alert('Profile link copied!');
                }
                closeShareModal();
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                if (window.showToast) {
                    window.showToast('Profile link copied!', 'success');
                } else {
                    alert('Profile link copied!');
                }
                closeShareModal();
            });
        }

        function closeShareModal() {
            const modal = document.getElementById('share-modal');
            if (modal) {
                modal.remove();
            }
        }

        // Avatar upload function
        async function updateAvatar(input) {
            if (!input.files || !input.files[0]) return;

            const file = input.files[0];

            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Please select an image file.');
                return;
            }

            // Validate file size (2MB max)
            if (file.size > 2 * 1024 * 1024) {
                alert('Image must be less than 2MB.');
                return;
            }

            // Show preview immediately
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-img').src = e.target.result;
            };
            reader.readAsDataURL(file);

            // Upload to server
            const formData = new FormData();
            formData.append('avatar', file);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const response = await fetch('{{ route('profile.update.avatar') }}', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    console.log('Avatar updated successfully');

                    // Automatically lock editing after saving
                    isEditMode = false;
                    lockProfileEditing();
                } else {
                    alert('Failed to update avatar. ' + (data.message || 'Please try again.'));
                    // Reload to restore old avatar
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while uploading. Please try again.');
                window.location.reload();
            }
        }


    </script>
</x-app-layout>
