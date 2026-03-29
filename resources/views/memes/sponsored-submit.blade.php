<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit for {{ $campaign->company_name ?? $campaign->campaign_title }}</title>
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('image/my-logo.jpg') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">

    <!-- Emoji Picker -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>

    <style>
        :root {
            --brand-purple: {{ $campaign->theme_color ?? '#6f42c1' }};
            --brand-purple-dark: #3e1e86;
            --brand-orange: #fd7e14;
            --brand-yellow: #ffc107;
            --bg-gradient: linear-gradient(135deg, #fdfbf7 0%, #f4f7fa 100%);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            color: #333;
            padding-bottom: 40px;
        }

        .navbar {
            background-color: var(--brand-purple-dark);
            padding: 0.8rem 2rem;
        }
        .navbar-brand {
            color: white !important;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Brand Header Section */
        .campaign-header {
            background: linear-gradient(135deg, var(--brand-purple) 0%, #000 100%);
            color: white;
            padding: 60px 0;
            border-radius: 0 0 40px 40px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }
        .campaign-header-bg {
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            opacity: 0.1;
            background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0);
            background-size: 24px 24px;
        }

        .main-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .upload-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
        }

        .upload-btn-wrapper {
            border: 2px dashed #e2e8f0;
            border-radius: 20px;
            padding: 50px 20px;
            text-align: center;
            background-color: #f8fafc;
            transition: all 0.3s;
            cursor: pointer;
            margin-bottom: 2rem;
            position: relative;
        }
        .upload-btn-wrapper:hover {
            background-color: #f1f5f9;
            border-color: var(--brand-purple);
        }
        .file-input {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
            z-index: 10;
        }
        .choose-btn {
            background-color: var(--brand-purple);
            color: white;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 700;
            border: none;
            margin-top: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .custom-label {
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .form-control-custom {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 1rem;
            transition: all 0.2s;
        }
        .form-control-custom:focus {
            background-color: #fff;
            border-color: var(--brand-purple);
            box-shadow: 0 0 0 4px rgba(0,0,0,0.05);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--brand-purple) 0%, var(--brand-purple-dark) 100%);
            color: white;
            font-weight: 800;
            padding: 16px 32px;
            border-radius: 14px;
            border: none;
            width: 100%;
            margin-top: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
            transition: all 0.3s;
        }
        .btn-submit:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.2);
        }
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            filter: grayscale(1);
        }

        .sidebar-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        #previewArea {
            margin-top: 15px;
            border-radius: 16px;
            overflow: hidden;
            display: none;
            width: 100%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        #previewImage {
            width: 100%;
            height: auto;
            max-height: 450px;
            object-fit: contain;
            background: #000;
        }

        .brand-logo-circle {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            border: 3px solid white;
        }

        .rules-badge {
            background: rgba(255,255,255,0.15);
            padding: 6px 15px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.3);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
    </style>
</head>
<body>

    <nav class="navbar text-white">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('image/my-logo.jpg') }}" width="35" height="35" class="rounded-circle shadow-sm" alt="Logo">
                <span class="fw-bold">OneDollarMeme</span>
            </a>
        </div>
    </nav>

    <!-- Campaign Header -->
    <header class="campaign-header">
        <div class="campaign-header-bg"></div>
        <div class="container main-container position-relative z-1">
            <div class="d-flex flex-column align-items-center text-center">
                <div class="brand-logo-circle">
                    @if($campaign->logo)
                        <img src="{{ asset('storage/' . $campaign->logo) }}" alt="" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                    @else
                        <span class="fs-1 fw-bold" style="color: var(--brand-purple);">{{ substr($campaign->company_name ?? $campaign->campaign_title, 0, 1) }}</span>
                    @endif
                </div>
                <h1 class="fw-900 italic text-uppercase tracking-tighter mb-2" style="font-weight: 900; font-style: italic;">{{ $campaign->campaign_title ?? 'Brand Campaign' }}</h1>
                <p class="lead opacity-75 mb-4" style="max-width: 600px;">{{ $campaign->campaign_description ?? 'Submit your creative meme for ' . ($campaign->company_name ?? 'this brand') . ' and win amazing prizes!' }}</p>
                
                <div class="d-flex gap-3 flex-wrap justify-content-center">
                    <div class="rules-badge" style="background: linear-gradient(135deg, var(--brand-purple) 0%, var(--brand-purple-dark) 100%);">
                        <i class="bi bi-trophy"></i> Prize Pool: ${{ number_format($campaign->prize_amount ?? 100, 0) }}
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="main-container px-3">
        <div class="row g-4">
            <!-- Left Column -->
            <div class="col-lg-8">
                <div class="upload-card">
                    <form action="{{ route('sponsored.submit', $campaign->slug) }}" method="POST" enctype="multipart/form-data" id="submitForm">
                        @csrf
                        
                        <div class="custom-label">
                            <i class="bi bi-image"></i> Step 1: Upload your Meme
                        </div>
                        <div class="upload-btn-wrapper">
                            <input type="file" name="image" id="imageInput" class="file-input" accept="image/*">
                            <div id="uploadPlaceholder">
                                <i class="bi bi-cloud-upload" style="font-size: 3rem; color: var(--brand-purple); opacity: 0.5;"></i>
                                <h6 class="fw-bold mt-3">Click or Drag Image Here</h6>
                                <p class="text-muted small">Max size: 2MB (JPG, PNG, WebP)</p>
                            </div>
                            <div id="previewArea">
                                <img id="previewImage" src="" alt="Preview">
                                <button type="button" class="btn btn-sm btn-danger mt-3 rounded-pill px-3" id="removeImageBtn">
                                    <i class="bi bi-trash"></i> Remove
                                </button>
                            </div>
                        </div>

                        <div class="mb-4 position-relative">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="custom-label mb-0"><i class="bi bi-chat-text"></i> Step 2: Add Caption</label>
                                <span class="text-muted small" id="charCount">0 / 140</span>
                            </div>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary border-end-0" type="button" id="emojiToggleBtn" style="border-radius: 12px 0 0 12px; background: #fff;">
                                    😀
                                </button>
                                <input type="text" name="title" id="titleInput" class="form-control form-control-custom border-start-0" style="border-radius: 0 12px 12px 0;" placeholder="Write something funny...">
                            </div>
                            <div id="emojiPickerContainer" style="display:none; position: absolute; top: 100%; left: 0; z-index: 1000; margin-top: 5px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); border-radius: 12px; overflow: hidden;">
                                <emoji-picker id="captionEmojiPicker"></emoji-picker>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit" id="submitBtn" disabled>
                            Submit Meme for Review
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-lg-4">
                <div class="sidebar-card">
                    <h5 class="fw-bold mb-4 text-uppercase italic" style="font-style: italic;">Campaign Rules</h5>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 32px; height: 32px; color: var(--brand-purple);">
                                    <i class="bi bi-check2"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Stay On Theme</h6>
                                <p class="text-muted small mb-0">Your meme must be related to {{ $campaign->company_name ?? 'the brand' }}.</p>
                            </div>
                        </div>
                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 32px; height: 32px; color: var(--brand-purple);">
                                    <i class="bi bi-check2"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Be Creative</h6>
                                <p class="text-muted small mb-0">Funny, witty, and high-quality memes get more votes!</p>
                            </div>
                        </div>
                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width: 32px; height: 32px; color: var(--brand-purple);">
                                    <i class="bi bi-check2"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Win Prizes</h6>
                                <p class="text-muted small mb-0">Top-voted memes win a share of the ${{ number_format($campaign->prize_amount ?? 100, 0) }} prize pool.</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($memesWithScores) && count($memesWithScores) > 0)
                <div class="sidebar-card">
                    <h5 class="fw-bold mb-4 text-uppercase italic" style="font-style: italic;">Current Top Entries</h5>
                    <div class="d-flex flex-column gap-3">
                        @foreach($memesWithScores as $index => $meme)
                        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge {{ $index == 0 ? 'bg-warning' : ($index == 1 ? 'bg-secondary' : 'bg-danger') }} rounded-circle" style="width: 24px; height: 24px;">{{ $index+1 }}</span>
                                <div class="fw-bold small">{{ $meme->user->name ?? 'Anonymous' }}</div>
                            </div>
                            <div class="fw-bold text-orange" style="color: var(--brand-orange);">Score: {{ $meme->calculated_score }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('imageInput');
            const previewArea = document.getElementById('previewArea');
            const previewImage = document.getElementById('previewImage');
            const uploadPlaceholder = document.getElementById('uploadPlaceholder');
            const removeBtn = document.getElementById('removeImageBtn');
            const titleInput = document.getElementById('titleInput');
            const submitBtn = document.getElementById('submitBtn');
            const charCount = document.getElementById('charCount');
            const emojiToggle = document.getElementById('emojiToggleBtn');
            const emojiContainer = document.getElementById('emojiPickerContainer');
            const emojiPicker = document.getElementById('captionEmojiPicker');

            // Image Preview
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        previewArea.style.display = 'block';
                        uploadPlaceholder.style.display = 'none';
                        validateForm();
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Remove Image
            removeBtn.addEventListener('click', function() {
                imageInput.value = '';
                previewArea.style.display = 'none';
                uploadPlaceholder.style.display = 'block';
                validateForm();
            });

            // Emoji Toggle
            emojiToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                emojiContainer.style.display = emojiContainer.style.display === 'none' ? 'block' : 'none';
            });

            document.addEventListener('click', (e) => {
                if (!emojiContainer.contains(e.target) && e.target !== emojiToggle) {
                    emojiContainer.style.display = 'none';
                }
            });

            // Emoji Insert
            emojiPicker.addEventListener('emoji-click', event => {
                const emoji = event.detail.unicode;
                const start = titleInput.selectionStart;
                const end = titleInput.selectionEnd;
                titleInput.value = titleInput.value.substring(0, start) + emoji + titleInput.value.substring(end);
                titleInput.focus();
                titleInput.setSelectionRange(start + emoji.length, start + emoji.length);
                validateForm();
            });

            // Char Count & Validation
            titleInput.addEventListener('input', () => {
                charCount.textContent = `${titleInput.value.length} / 140`;
                validateForm();
            });

            function validateForm() {
                const hasImage = imageInput.files.length > 0;
                const hasTitle = titleInput.value.trim().length >= 3;
                
                if (hasImage || hasTitle) {
                    submitBtn.disabled = false;
                } else {
                    submitBtn.disabled = true;
                }
            }
        });
    </script>
</body>
</html>
